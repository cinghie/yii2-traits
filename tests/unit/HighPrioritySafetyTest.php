<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\AttachmentTrait;
use cinghie\traits\models\Mailer;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Module;
use yii\console\Application;
use yii\console\Controller;

final class HighPrioritySafetyTest extends TestCase
{
    /** @var string */
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'yii2-traits-' . uniqid('', true);
        mkdir($this->tempDir, 0700, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_link($file) || is_file($file)) {
                @unlink($file);
            }
        }
        @rmdir($this->tempDir);
        Yii::$app = null;
        parent::tearDown();
    }

    public function testMailerValidatesRecipientSeparatelyFromSender(): void
    {
        $mailer = new Mailer('sender@example.com', 'not-an-email', 'Subject', 'Body');

        $this->assertTrue($mailer->emailFromIsValid());
        $this->assertFalse($mailer->emailToIsValid());
    }

    public function testAttachmentDeletionRejectsTraversalFilename(): void
    {
        $this->bootstrapAttachmentApplication();
        $host = new AttachmentSafetyHost();
        $host->filename = '../outside.txt';

        $this->assertFalse($host->deleteFile());
    }

    public function testAttachmentDeletionDeletesContainedRegularFile(): void
    {
        $this->bootstrapAttachmentApplication();
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'inside.txt', 'ok');

        $host = new AttachmentSafetyHost();
        $host->filename = 'inside.txt';

        $this->assertTrue($host->deleteFile());
        $this->assertFileDoesNotExist($this->tempDir . DIRECTORY_SEPARATOR . 'inside.txt');
    }

    private function bootstrapAttachmentApplication(): void
    {
        new Application([
            'id' => 'traits-test',
            'basePath' => dirname(__DIR__, 2),
        ]);

        $module = new AttachmentSafetyModule('attachments');
        $module->attachPath = $this->tempDir;
        Yii::$app->controller = new Controller('test', $module);
    }
}

final class AttachmentSafetyHost
{
    use AttachmentTrait;

    public $filename;
    public $extension = '';
    public $mimetype = '';
    public $size = 0;
}

final class AttachmentSafetyModule extends Module
{
    public $attachPath;
    public $attachURL = '';
}
