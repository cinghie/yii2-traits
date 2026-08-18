<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\CacheTrait;
use cinghie\traits\OrderingTrait;
use cinghie\traits\models\Mailer;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\web\BadRequestHttpException;
use yii\web\Request;

final class MediumHardeningTest extends TestCase
{
    /** @var mixed */
    private $previousApp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousApp = Yii::$app;
    }

    protected function tearDown(): void
    {
        Yii::$app = $this->previousApp;
        MediumOrderingRecord::$db = null;
        parent::tearDown();
    }

    public function testMailerAcceptsMultipleRecipientsAsList(): void
    {
        $mailer = new Mailer(
            'sender@example.com',
            ['one@example.com', 'two@example.com'],
            'Subject',
            'Body'
        );

        $this->assertTrue($mailer->emailToIsValid());
    }

    public function testMailerAcceptsYiiRecipientMap(): void
    {
        $mailer = new Mailer(
            'sender@example.com',
            ['one@example.com' => 'One', 'two@example.com' => 'Two'],
            'Subject',
            'Body'
        );

        $this->assertTrue($mailer->emailToIsValid());
    }

    public function testMailerRejectsInvalidAddressInsideRecipientArray(): void
    {
        $mailer = new Mailer(
            'sender@example.com',
            ['one@example.com', 'not-an-email'],
            'Subject',
            'Body'
        );

        $this->assertFalse($mailer->emailToIsValid());
        $this->assertStringContainsString('not-an-email', $mailer->debug);
    }

    public function testOrderingMoveToLastUsesScopedMaximumInsteadOfStaleArgument(): void
    {
        $db = $this->createOrderingDatabase();
        $model = MediumOrderingRecord::findOne(2);
        $model->ordering = 999999999;

        $model->setOrdering(MediumOrderingRecord::class, 'group_id', 2, 999);

        $this->assertSame([
            1 => 1,
            2 => 4,
            3 => 2,
            4 => 3,
        ], $this->fetchOrdering($db));
    }

    public function testOrderingRejectsIncompatibleActiveRecordClass(): void
    {
        $this->createOrderingDatabase();
        $model = MediumOrderingRecord::findOne(2);
        $model->ordering = 3;

        $this->expectException(\InvalidArgumentException::class);
        $model->setOrdering(MediumOtherOrderingRecord::class, 'group_id', 2, 4);
    }

    public function testCacheMutationRejectsDisabledCsrfValidation(): void
    {
        Yii::$app = new MediumCacheApplication(true, true, true, false);
        $host = new MediumCacheHost();

        $this->expectException(BadRequestHttpException::class);
        $host->assertMutationAllowed();
    }

    public function testCacheMutationRejectsInvalidCsrfToken(): void
    {
        Yii::$app = new MediumCacheApplication(true, true, false, true);
        $host = new MediumCacheHost();

        $this->expectException(BadRequestHttpException::class);
        $host->assertMutationAllowed();
    }

    public function testCacheMutationAcceptsValidCsrfTokenAndAuthorizedUser(): void
    {
        Yii::$app = new MediumCacheApplication(true, true, true, true);
        $host = new MediumCacheHost();

        $host->assertMutationAllowed();

        $this->assertSame(1, Yii::$app->request->csrfValidationCalls);
        $this->assertSame('cache-manage', Yii::$app->user->lastPermission);
    }

    private function createOrderingDatabase(): Connection
    {
        $db = new Connection(['dsn' => 'sqlite::memory:']);
        $db->open();
        MediumOrderingRecord::$db = $db;
        $db->createCommand('CREATE TABLE medium_ordering_test (id INTEGER PRIMARY KEY, group_id INTEGER NOT NULL, ordering INTEGER NOT NULL)')->execute();
        foreach ([1, 2, 3, 4] as $ordering) {
            $db->createCommand()->insert('medium_ordering_test', [
                'id' => $ordering,
                'group_id' => 10,
                'ordering' => $ordering,
            ])->execute();
        }
        return $db;
    }

    private function fetchOrdering(Connection $db): array
    {
        $rows = $db->createCommand('SELECT id, ordering FROM medium_ordering_test ORDER BY id')->queryAll();
        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row['id']] = (int)$row['ordering'];
        }
        return $result;
    }
}

final class MediumOrderingRecord extends ActiveRecord
{
    use OrderingTrait;

    /** @var Connection|null */
    public static $db;

    public static function tableName()
    {
        return 'medium_ordering_test';
    }

    public static function getDb()
    {
        return static::$db;
    }
}

final class MediumOtherOrderingRecord extends ActiveRecord
{
    public static function tableName()
    {
        return 'medium_ordering_test';
    }
}

final class MediumCacheHost
{
    use CacheTrait;

    public function assertMutationAllowed(): void
    {
        $this->ensureCacheMutationAllowed();
    }
}

final class MediumCacheApplication
{
    /** @var MediumCacheRequest */
    public $request;

    /** @var MediumCacheUser */
    public $user;

    public function __construct(bool $isPost, bool $allowed, bool $validCsrf, bool $csrfEnabled)
    {
        $this->request = new MediumCacheRequest($isPost, $validCsrf, $csrfEnabled);
        $this->user = new MediumCacheUser($allowed);
    }
}

final class MediumCacheRequest extends Request
{
    /** @var bool */
    private $postRequest;

    /** @var bool */
    private $validCsrf;

    /** @var int */
    public $csrfValidationCalls = 0;

    public function __construct(bool $isPost, bool $validCsrf, bool $csrfEnabled, $config = [])
    {
        $this->postRequest = $isPost;
        $this->validCsrf = $validCsrf;
        parent::__construct($config);
        $this->enableCsrfValidation = $csrfEnabled;
    }

    public function getIsPost()
    {
        return $this->postRequest;
    }

    public function validateCsrfToken($clientSuppliedToken = null)
    {
        $this->csrfValidationCalls++;
        return $this->validCsrf;
    }
}

final class MediumCacheUser
{
    /** @var bool */
    public $isGuest;

    /** @var string|null */
    public $lastPermission;

    /** @var bool */
    private $allowed;

    public function __construct(bool $allowed)
    {
        $this->allowed = $allowed;
        $this->isGuest = !$allowed;
    }

    public function can($permission)
    {
        $this->lastPermission = $permission;
        return $this->allowed;
    }
}
