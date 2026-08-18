<?php

namespace cinghie\traits;

use Yii;
use cinghie\traits\services\AttachmentService;
use cinghie\traits\services\RuntimeConfig;
use cinghie\traits\ui\AttachmentUi;
use yii\base\InvalidConfigException;

/**
 * Trait AttachmentTrait
 *
 * Model-facing attachment behavior. Filesystem/media operations are delegated
 * to AttachmentService, runtime configuration to RuntimeConfig, and
 * presentation helpers to AttachmentUi.
 *
 * @property string $extension
 * @property string $filename
 * @property string $mimetype
 */
trait AttachmentTrait
{
    /** Validation rules contributed by this trait. */
    public function getAttachmentRules()
    {
        return [
            [['size'], 'integer'],
            [['extension'], 'string', 'max' => 12],
            [['alias', 'filename', 'mimetype', 'title'], 'string', 'max' => 255],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getAttachmentAttributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'extension' => Yii::t('traits', 'Extension'),
            'filename' => Yii::t('traits', 'Filename'),
            'mimetype' => Yii::t('traits', 'MimeType'),
            'size' => Yii::t('traits', 'Size'),
            'title' => Yii::t('traits', 'Title'),
        ];
    }

    protected function getAttachmentService()
    {
        return new AttachmentService();
    }

    protected function getAttachmentPathConfig()
    {
        $path = RuntimeConfig::get($this, 'attachPath', null, 'attachPath');
        if (!$path) {
            throw new InvalidConfigException('Attachment path is not configured.');
        }

        return Yii::getAlias($path);
    }

    protected function getAttachmentUrlConfig()
    {
        $url = RuntimeConfig::get($this, 'attachURL', '', 'attachURL');
        return $url ? Yii::getAlias($url) : '';
    }

    protected function getFfmpegOptions()
    {
        $configured = RuntimeConfig::get($this, 'ffmpegOptions', null);
        if (is_array($configured)) {
            return $configured;
        }

        $ffmpeg = RuntimeConfig::get($this, 'ffmpegBinary', null);
        $ffprobe = RuntimeConfig::get($this, 'ffprobeBinary', null);
        if ($ffmpeg || $ffprobe) {
            $options = [];
            if ($ffmpeg) {
                $options['ffmpeg.binaries'] = $ffmpeg;
            }
            if ($ffprobe) {
                $options['ffprobe.binaries'] = $ffprobe;
            }
            return $options;
        }

        return [];
    }

    public function getFileUrl()
    {
        $baseUrl = $this->getAttachmentUrlConfig();
        return $baseUrl ? $baseUrl . $this->filename : '';
    }

    public function getVideoThumb()
    {
        return str_replace('media/', 'media/thumbs/video/', $this->fileUrl).'.jpg';
    }

    public function deleteFile()
    {
        return $this->getAttachmentService()->deleteFile($this->getAttachmentPathConfig(), $this->filename);
    }

    public static function getID3Info($attachPath)
    {
        return (new AttachmentService())->getID3Info($attachPath);
    }

    public function getVideoDuration($attachPath)
    {
        return $this->getAttachmentService()->getVideoDuration($attachPath);
    }

    public function createVideoThumb($attachPath, $sec = 3)
    {
        return $this->getAttachmentService()->createVideoThumb($attachPath, $sec, $this->getFfmpegOptions());
    }

    public function getAttachmentType()
    {
        return explode('/', $this->mimetype);
    }

    public function purgeAttachmentName($attachName)
    {
        return $this->getAttachmentService()->purgeAttachmentName($attachName);
    }

    public function formatSize($precision = 2)
    {
        return $this->formatFileSize($this->size, $precision);
    }

    public function formatFileSize($size, $precision = 2)
    {
        return $this->getAttachmentService()->formatFileSize($size, $precision);
    }

    public function generateMd5FileName($filename, $extension)
    {
        return $this->getAttachmentService()->generateMd5FileName($filename, $extension);
    }

    public function getAttachmentPreview($class = 'img-responsive', $style = '')
    {
        return AttachmentUi::preview($this, $class, $style);
    }

    public function getAttachmentTypeIcon()
    {
        return AttachmentUi::typeIcon($this);
    }

    public function getFileWidget($form, $attachType)
    {
        return AttachmentUi::fileWidget($this, $form, $attachType);
    }

    public function getFilesWidget($attachType)
    {
        return AttachmentUi::filesWidget($this, $attachType);
    }

    public function getExtensionWidget($form)
    {
        return AttachmentUi::extensionWidget($this, $form);
    }

    public function getMimeTypeWidget($form)
    {
        return AttachmentUi::mimeTypeWidget($this, $form);
    }

    public function getSizeWidget($form)
    {
        return AttachmentUi::sizeWidget($this, $form);
    }
}
