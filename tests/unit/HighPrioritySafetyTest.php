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

    /** @var string|null */
    private $outsideFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'yii2-traits-' . uniqid('', true);
        mkdir($this->tempDir, 0700, true);
        $this->outsideFile = null;
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_link($file) || is_file($file)) {
                @unlink($file);
            }
        }
        if ($this->outsideFile && file_exists($this->outsideFile)) {
            @unlink($this->outsideFile);
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

    public function testMailerSendMailStopsBeforeMailerComponentForInvalidRecipient(): void
    {
        new Application([
            'id' => 'traits-mail-test',
            'basePath' => dirname(__DIR__, 2),
        ]);
        $mailer = new Mailer('sender@example.com', 'not-an-email', 'Subject', 'Body');

        $result = $mailer->sendMail();

        $this->assertSame('error', $result['status']);
        $this->assertStringContainsString('not-an-email', $result['message']);
    }

    public function testAttachmentDeletionRejectsTraversalToExistingOutsideFile(): void
    {
        $this->bootstrapAttachmentApplication();
        $this->outsideFile = dirname($this->tempDir) . DIRECTORY_SEPARATOR . basename($this->tempDir) . '-outside.txt';
        file_put_contents($this->outsideFile, 'outside');

        $host = new AttachmentSafetyHost();
        $host->filename = '..' . DIRECTORY_SEPARATOR . basename($this->outsideFile);

        $this->assertFalse($host->deleteFile());
        $this->assertFileExists($this->outsideFile);
    }

    public function testAttachmentDeletionRejectsSymlinkEscapingAttachmentDirectory(): void
    {
        if (!function_exists('symlink')) {
            $this->markTestSkipped('symlink() is unavailable.');
        }

        $this->bootstrapAttachmentApplication();
        $this->outsideFile = dirname($this->tempDir) . DIRECTORY_SEPARATOR . basename($this->tempDir) . '-outside.txt';
        file_put_contents($this->outsideFile, 'outside');
        $link = $this->tempDir . DIRECTORY_SEPARATOR . 'escape.txt';

        if (!@symlink($this->outsideFile, $link)) {
            $this->markTestSkipped('Unable to create symlink on this platform.');
        }

        $host = new AttachmentSafetyHost();
        $host->filename = 'escape.txt';

        $this->assertFalse($host->deleteFile());
        $this->assertFileExists($this->outsideFile);
        $this->assertTrue(is_link($link));
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
