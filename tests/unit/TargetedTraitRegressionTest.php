<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\CacheTrait;
use cinghie\traits\CreatedTrait;
use cinghie\traits\ModifiedTrait;
use cinghie\traits\OrderingTrait;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

final class TargetedTraitRegressionTest extends TestCase
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
        OrderingTraitRecord::$db = null;
        parent::tearDown();
    }

    public function testOrderingMoveDownShiftsSiblingsAndPersistsMovedRow(): void
    {
        $db = $this->createOrderingDatabase();
        $model = OrderingTraitRecord::findOne(2);
        $model->ordering = 4;

        $model->setOrdering(OrderingTraitRecord::class, 'group_id', 2, 4);

        $this->assertSame([
            1 => 1,
            2 => 4,
            3 => 2,
            4 => 3,
        ], $this->fetchOrdering($db));
    }

    public function testOrderingMoveToFirstShiftsOnlyEarlierSiblings(): void
    {
        $db = $this->createOrderingDatabase();
        $model = OrderingTraitRecord::findOne(3);
        $model->ordering = 0;

        $model->setOrdering(OrderingTraitRecord::class, 'group_id', 3, 4);

        $this->assertSame([
            1 => 2,
            2 => 3,
            3 => 1,
            4 => 4,
        ], $this->fetchOrdering($db));
    }

    public function testOrderingRollsBackSiblingShiftWhenMovedRowUpdateFails(): void
    {
        $db = $this->createOrderingDatabase();
        $db->pdo->exec(
            "CREATE TRIGGER fail_moved_row " .
            "BEFORE UPDATE OF ordering ON ordering_test " .
            "WHEN OLD.id = 2 " .
            "BEGIN SELECT RAISE(ABORT, 'boom'); END;"
        );
        $model = OrderingTraitRecord::findOne(2);
        $model->ordering = 4;

        try {
            $model->setOrdering(OrderingTraitRecord::class, 'group_id', 2, 4);
            $this->fail('Expected the database trigger to abort the moved-row update.');
        } catch (\Throwable $e) {
            $this->assertSame([
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
            ], $this->fetchOrdering($db));
        }
    }

    public function testCacheMutationRejectsNonPostRequests(): void
    {
        Yii::$app = new CacheTestApplication(false, true);
        $host = new CacheTraitTestHost();

        $this->expectException(MethodNotAllowedHttpException::class);
        $host->assertMutationAllowed();
    }

    public function testCacheMutationRejectsGuestOrUnauthorizedUser(): void
    {
        Yii::$app = new CacheTestApplication(true, false);
        $host = new CacheTraitTestHost();

        $this->expectException(ForbiddenHttpException::class);
        $host->assertMutationAllowed();
    }

    public function testCacheMutationAcceptsAuthorizedPostAndUsesConfiguredPermission(): void
    {
        $app = new CacheTestApplication(true, true);
        Yii::$app = $app;
        $host = new CacheTraitTestHost();

        $host->assertMutationAllowed();

        $this->assertSame('cache-manage', $app->user->lastPermission);
    }

    public function testCreatedTraitHandlesMissingRelatedUserInGridAndDetailViews(): void
    {
        $model = new CreatedTraitTestHost();
        $model->created_by = 42;
        $model->fakeCreatedBy = null;

        $this->assertSame(Yii::t('traits', 'Nobody'), $model->getCreatedByGridView());
        $detail = $model->getCreatedByDetailView();
        $this->assertSame(Yii::t('traits', 'Nobody'), $detail['value']);
    }

    public function testModifiedTraitHandlesMissingRelatedUserInGridAndDetailViews(): void
    {
        $model = new ModifiedTraitTestHost();
        $model->modified_by = 42;
        $model->fakeModifiedBy = null;

        $this->assertSame(Yii::t('traits', 'Nobody'), $model->getModifiedByGridView());
        $detail = $model->getModifiedByDetailView();
        $this->assertSame(Yii::t('traits', 'Nobody'), $detail['value']);
    }

    public function testCreatedAndModifiedCurrentUserChecksAreSafeForGuests(): void
    {
        Yii::$app = (object)['user' => (object)['identity' => null]];
        $created = new CreatedTraitTestHost();
        $created->created_by = 1;
        $modified = new ModifiedTraitTestHost();
        $modified->modified_by = 1;

        $this->assertFalse($created->isCurrentUserCreator());
        $this->assertFalse($modified->isCurrentUserModifier());
    }

    public function testCreatedAndModifiedCurrentUserChecksMatchAuthenticatedIdentity(): void
    {
        Yii::$app = (object)['user' => (object)['identity' => (object)['id' => 7]]];
        $created = new CreatedTraitTestHost();
        $created->created_by = 7;
        $modified = new ModifiedTraitTestHost();
        $modified->modified_by = 7;

        $this->assertTrue($created->isCurrentUserCreator());
        $this->assertTrue($modified->isCurrentUserModifier());
    }

    public function testModifiedWidgetDoesNotUseMysqlZeroDateForNewRecords(): void
    {
        $model = new ModifiedTraitTestHost();
        $model->newRecord = true;
        $form = new CapturingForm();

        $field = $model->getModifiedWidget($form);

        $this->assertNull($field->widgetConfig['options']['value']);
    }

    private function createOrderingDatabase(): Connection
    {
        $db = new Connection(['dsn' => 'sqlite::memory:']);
        $db->open();
        OrderingTraitRecord::$db = $db;
        $db->createCommand('CREATE TABLE ordering_test (id INTEGER PRIMARY KEY, group_id INTEGER NOT NULL, ordering INTEGER NOT NULL)')->execute();
        foreach ([1, 2, 3, 4] as $ordering) {
            $db->createCommand()->insert('ordering_test', [
                'id' => $ordering,
                'group_id' => 10,
                'ordering' => $ordering,
            ])->execute();
        }
        return $db;
    }

    private function fetchOrdering(Connection $db): array
    {
        $rows = $db->createCommand('SELECT id, ordering FROM ordering_test ORDER BY id')->queryAll();
        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row['id']] = (int)$row['ordering'];
        }
        return $result;
    }
}

final class OrderingTraitRecord extends ActiveRecord
{
    use OrderingTrait;

    /** @var Connection|null */
    public static $db;

    public static function tableName()
    {
        return 'ordering_test';
    }

    public static function getDb()
    {
        return static::$db;
    }
}

final class CacheTraitTestHost
{
    use CacheTrait;

    public function assertMutationAllowed(): void
    {
        $this->ensureCacheMutationAllowed();
    }
}

final class CacheTestApplication
{
    /** @var object */
    public $request;

    /** @var CacheTestUser */
    public $user;

    public function __construct(bool $isPost, bool $allowed)
    {
        $this->request = (object)['isPost' => $isPost];
        $this->user = new CacheTestUser($allowed);
    }
}

final class CacheTestUser
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

final class CreatedTraitTestHost extends Model
{
    use CreatedTrait { getCreatedBy as private traitGetCreatedBy; }

    /** @var int|null */
    public $created_by;

    /** @var mixed */
    public $fakeCreatedBy;

    public function getCreatedBy()
    {
        return $this->fakeCreatedBy;
    }
}

final class ModifiedTraitTestHost extends Model
{
    use ModifiedTrait { getModifiedBy as private traitGetModifiedBy; }

    /** @var string|null */
    public $modified;

    /** @var int|null */
    public $modified_by;

    /** @var mixed */
    public $fakeModifiedBy;

    /** @var bool */
    public $newRecord = false;

    public function getIsNewRecord()
    {
        return $this->newRecord;
    }

    public function getModifiedBy()
    {
        return $this->fakeModifiedBy;
    }
}

final class CapturingForm
{
    public function field($model, $attribute)
    {
        return new CapturingField();
    }
}

final class CapturingField
{
    /** @var array */
    public $widgetConfig = [];

    public function widget($class, $config = [])
    {
        $this->widgetConfig = $config;
        return $this;
    }
}
