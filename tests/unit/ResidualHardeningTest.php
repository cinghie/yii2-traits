<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\AttachmentTrait;
use cinghie\traits\EditorTrait;
use cinghie\traits\GoogleTranslateTrait;
use cinghie\traits\UserHelpersTrait;
use cinghie\traits\components\Mailer as ComponentMailer;
use cinghie\traits\services\AttachmentService;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;

final class ResidualHardeningTest extends TestCase
{
    /** @var mixed */
    private $previousApp;

    /** @var string */
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousApp = Yii::$app;
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'yii2-traits-residual-' . uniqid('', true);
        mkdir($this->tempDir, 0700, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_file($file) || is_link($file)) {
                @unlink($file);
            }
        }
        @rmdir($this->tempDir);
        Yii::$app = $this->previousApp;
        parent::tearDown();
    }

    public function testAttachmentConfigurationWorksWithoutController(): void
    {
        Yii::$app = null;
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'file.txt', 'ok');

        $host = new ResidualAttachmentHost($this->tempDir, '/media/');
        $host->filename = 'file.txt';

        $this->assertSame('/media/file.txt', $host->getFileUrl());
        $this->assertTrue($host->deleteFile());
    }

    public function testEditorConfigurationWorksWithoutController(): void
    {
        Yii::$app = null;
        $host = new ResidualEditorHost();

        $html = $host->getEditorWidget(null, 'body', '', 'hello', ['rows' => 2]);

        $this->assertStringContainsString('textarea', $html);
        $this->assertStringContainsString('hello', $html);
    }

    public function testSecureAttachmentFilenameIsRandomAndPreservesExtension(): void
    {
        $service = new AttachmentService();
        $first = $service->generateMd5FileName('original', 'pdf');
        $second = $service->generateMd5FileName('original', '.pdf');

        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}\.pdf$/', $first);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}\.pdf$/', $second);
        $this->assertNotSame($first, $second);
    }

    public function testUserHelpersAreSafeForGuestIdentity(): void
    {
        Yii::$app = (object)['user' => (object)['identity' => null]];
        $host = new ResidualUserHelpersHost();

        $this->assertNull($host->getCurrentUser());
        $this->assertNull($host->getCurrentUser('id'));
        $this->assertNull($host->getCurrentUserProfile());
        $this->assertSame([], $host->getCurrentUserSelect2());
    }

    public function testGoogleTranslateErrorFormatterHandlesNonJsonMessage(): void
    {
        $host = new ResidualGoogleTranslateHost();

        $this->assertSame('network failure', $host->formatError(new RuntimeException('network failure')));
    }

    public function testGoogleTranslateErrorFormatterHandlesGoogleJsonMessage(): void
    {
        $host = new ResidualGoogleTranslateHost();
        $payload = json_encode([
            'error' => [
                'status' => 'INVALID_ARGUMENT',
                'code' => 400,
                'message' => 'Bad target language',
            ],
        ]);

        $this->assertSame(
            'INVALID_ARGUMENT - Error 400: Bad target language',
            $host->formatError(new RuntimeException($payload))
        );
    }

    public function testLegacyComponentMailerRequiresExplicitAddresses(): void
    {
        $mailer = new ResidualComponentMailer();

        $this->expectException(InvalidConfigException::class);
        $mailer->sendForTest();
    }
}

final class ResidualAttachmentHost
{
    use AttachmentTrait;

    public $filename = '';
    public $extension = '';
    public $mimetype = 'text/plain';
    public $size = 0;

    /** @var array */
    private $traitsConfig;

    public function __construct($path, $url)
    {
        $this->traitsConfig = [
            'attachPath' => $path,
            'attachURL' => $url,
        ];
    }

    public function getTraitsConfig()
    {
        return $this->traitsConfig;
    }
}

final class ResidualEditorHost
{
    use EditorTrait;

    public function getTraitsConfig()
    {
        return ['editor' => 'plain'];
    }
}

final class ResidualUserHelpersHost
{
    use UserHelpersTrait;
}

final class ResidualGoogleTranslateHost
{
    use GoogleTranslateTrait;

    public function formatError(\Throwable $e)
    {
        return $this->formatGoogleTranslateError($e);
    }
}

final class ResidualComponentMailer extends ComponentMailer
{
    public function sendForTest()
    {
        return $this->sendEmail();
    }
}
