<?php

namespace cinghie\traits\tests\unit;

use cinghie\traits\AttachmentTrait;
use cinghie\traits\EditorTrait;
use cinghie\traits\services\AttachmentService;
use PHPUnit\Framework\TestCase;

final class ConcernSeparationTest extends TestCase
{
    /** @var string */
    private $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'yii2-traits-service-' . uniqid('', true);
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
        parent::tearDown();
    }

    public function testAttachmentServiceDeletesContainedFileWithoutYiiApplication(): void
    {
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'inside.txt', 'ok');
        $service = new AttachmentService();

        $this->assertTrue($service->deleteFile($this->tempDir, 'inside.txt'));
        $this->assertFileDoesNotExist($this->tempDir . DIRECTORY_SEPARATOR . 'inside.txt');
    }

    public function testAttachmentServiceRejectsTraversalWithoutYiiApplication(): void
    {
        $service = new AttachmentService();
        $this->assertFalse($service->deleteFile($this->tempDir, '../outside.txt'));
    }

    public function testAttachmentFormattingLivesInServiceButLegacyWrapperIsPreserved(): void
    {
        $service = new AttachmentService();
        $host = new ConcernAttachmentHost();

        $this->assertSame('1 KB', $service->formatFileSize(1024, 0));
        $this->assertSame('1 KB', $host->formatFileSize(1024, 0));
    }

    public function testAttachmentTraitDelegatesServiceOperations(): void
    {
        $host = new ConcernAttachmentHost();
        $fake = new ConcernAttachmentService();
        $host->service = $fake;

        $this->assertSame('delegated', $host->purgeAttachmentName('name'));
        $this->assertSame('name:ext', $host->generateMd5FileName('name', 'ext'));
        $this->assertSame(2, $fake->calls);
    }

    public function testEditorTraitDelegatesPlainRenderingToUiLayer(): void
    {
        $host = new ConcernEditorHost();
        $html = $host->getNoEditorWidget(null, 'body', 'hello', ['rows' => 3]);

        $this->assertStringContainsString('textarea', $html);
        $this->assertStringContainsString('hello', $html);
        $this->assertStringContainsString('rows="3"', $html);
    }
}

final class ConcernAttachmentHost
{
    use AttachmentTrait;

    public $extension = 'txt';
    public $filename = '';
    public $mimetype = 'text/plain';
    public $size = 0;
    public $service;

    protected function getAttachmentService()
    {
        return $this->service ?: new AttachmentService();
    }
}

final class ConcernAttachmentService
{
    public $calls = 0;

    public function purgeAttachmentName($attachName)
    {
        $this->calls++;
        return 'delegated';
    }

    public function generateMd5FileName($filename, $extension)
    {
        $this->calls++;
        return $filename . ':' . $extension;
    }
}

final class ConcernEditorHost
{
    use EditorTrait;
}
