<?php

namespace cinghie\traits;

use Yii;
use cinghie\traits\services\AttachmentService;
use cinghie\traits\ui\AttachmentUi;

/**
 * Trait AttachmentTrait
 *
 * Model-facing attachment behavior. Filesystem/media operations are delegated
 * to AttachmentService and presentation helpers to AttachmentUi.
 *
 * @property string $extension
 * @property string $filename
 * @property string $mimetype
 */
trait AttachmentTrait
{
    public static function rules()
    {
        return [
            [['size'], 'integer'],
            [['extension'], 'string', 'max' => 12],
            [['alias', 'filename', 'mimetype', 'title'], 'string', 'max' => 255],
        ];
    }

    public function getAttachmentRules()
    {
        return static::rules();
    }

    public static function attributeLabels()
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

    public function getAttachmentAttributeLabels()
    {
        return static::attributeLabels();
    }

    protected function getAttachmentService()
    {
        return new AttachmentService();
    }

    public function getFileUrl()
    {
        return Yii::$app->controller->module->attachURL
            ? Yii::getAlias(Yii::$app->controller->module->attachURL).$this->filename
            : '';
    }

    public function getVideoThumb()
    {
        return str_replace('media/', 'media/thumbs/video/', $this->fileUrl).'.jpg';
    }

    public function deleteFile()
    {
        $basePath = Yii::getAlias(Yii::$app->controller->module->attachPath);
        return $this->getAttachmentService()->deleteFile($basePath, $this->filename);
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
        $ffmpegOptions = [];
        $operationSystem = PHP_OS;

        if (strpos($operationSystem, 'Windows') !== false || strpos($operationSystem, 'WIN') !== false) {
            $ffmpegOptions = [
                'ffmpeg.binaries' => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffmpeg.exe'),
                'ffprobe.binaries' => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffprobe.exe'),
            ];
        }
        if (strpos($operationSystem, 'Linux') !== false) {
            $ffmpegOptions = [
                'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
            ];
        }

        return $this->getAttachmentService()->createVideoThumb($attachPath, $sec, $ffmpegOptions);
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
