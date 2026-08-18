<?php

namespace cinghie\traits;

use Exception;
use FFMpeg\Coordinate;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Frame;
use Yii;
use getID3;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Trait AttachmentTrait
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
            [['alias', 'filename', 'mimetype', 'title'], 'string', 'max' => 255]
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

    /**
     * Delete an attached file only when its canonical path is contained in the
     * configured attachment directory.
     *
     * @return bool
     * @throws InvalidParamException
     */
    public function deleteFile()
    {
        if (empty($this->filename) || basename($this->filename) !== $this->filename) {
            return false;
        }

        $basePath = Yii::getAlias(Yii::$app->controller->module->attachPath);
        $baseRealPath = realpath($basePath);
        if ($baseRealPath === false || !is_dir($baseRealPath)) {
            return false;
        }

        $fileRealPath = realpath($baseRealPath . DIRECTORY_SEPARATOR . $this->filename);
        if ($fileRealPath === false || !is_file($fileRealPath)) {
            return false;
        }

        $basePrefix = rtrim($baseRealPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strncmp($fileRealPath, $basePrefix, strlen($basePrefix)) !== 0) {
            return false;
        }

        return unlink($fileRealPath);
    }

    public static function getID3Info($attachPath)
    {
        $getID3 = new getID3;
        return $getID3->analyze($attachPath);
    }

    public function getVideoDuration($attachPath)
    {
        $fileInfo = static::getID3Info($attachPath);
        if (isset($fileInfo['video'], $fileInfo['mime_type']) && strpos($fileInfo['mime_type'], 'video') !== false) {
            return $fileInfo['playtime_string'];
        }
        return null;
    }

    public function createVideoThumb($attachPath, $sec = 3)
    {
        $frame = null;
        $fileInfo = static::getID3Info($attachPath);
        $ffmpegOptions = [];
        $operationSystem = PHP_OS;

        if (strpos($operationSystem, 'Windows') !== false || strpos($operationSystem, 'WIN') !== false) {
            $ffmpegOptions = [
                'ffmpeg.binaries' => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffmpeg.exe'),
                'ffprobe.binaries' => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffprobe.exe')
            ];
        }
        if (strpos($operationSystem, 'Linux') !== false) {
            $ffmpegOptions = ['ffmpeg.binaries' => '/usr/bin/ffmpeg', 'ffprobe.binaries' => '/usr/bin/ffprobe'];
        }
        if (isset($fileInfo['video'], $fileInfo['mime_type']) && strpos($fileInfo['mime_type'], 'video') !== false) {
            $ffmpeg = FFMpeg::create($ffmpegOptions);
            $video = $ffmpeg->open($attachPath);
            $frame = $video->frame(Coordinate\TimeCode::fromSeconds($sec));
        }
        return $frame;
    }

    public function getAttachmentType()
    {
        return explode('/', $this->mimetype);
    }

    public function purgeAttachmentName($attachName)
    {
        return str_replace(["/'/", '’', '"', ':', ';', ',', '.', ' ', '__'], '_', $attachName);
    }

    public function formatSize($precision = 2)
    {
        return $this->formatFileSize($this->size, $precision);
    }

    public function formatFileSize($size, $precision = 2)
    {
        $i = 0;
        $step = 1024;
        $units = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
        while (($size / $step) > 0.9 && $i < count($units) - 1) {
            $size /= $step;
            $i++;
        }
        return round($size, $precision).' '.$units[$i];
    }

    public function generateMd5FileName($filename, $extension)
    {
        return md5(uniqid($filename, false)).'.'.$extension;
    }

    public function getAttachmentPreview($class = 'img-responsive', $style = '')
    {
        if (strpos($this->mimetype, 'image') !== false) {
            return Html::img($this->fileUrl, ['class' => $class, 'style' => $style]);
        }
        if (strpos($this->mimetype, 'video') !== false) {
            return Html::img($this->getVideoThumb(), ['class' => $class, 'style' => $style]);
        }
        return $this->getAttachmentTypeIcon();
    }

    public function getAttachmentTypeIcon()
    {
        $extensions = [
            'csv' => 'file-excel', 'mp4' => 'file-video', 'pdf' => 'file-pdf',
            'plain' => 'file-excel', 'rar' => 'file-archive', 'text' => 'file-text', 'zip' => 'file-archive'
        ];
        if (isset($extensions[$this->extension])) {
            return '<i class="fa fa-'.$extensions[$this->extension].'" aria-hidden="true"></i>';
        }

        $mimetype = $this->getAttachmentType();
        $typeIcons = ['audio' => 'file-audio', 'archive' => 'file-archive', 'image' => 'file-image', 'video' => 'file-video'];
        if (isset($mimetype[0], $typeIcons[$mimetype[0]])) {
            return '<i class="fa fa-'.$typeIcons[$mimetype[0]].'" aria-hidden="true"></i>';
        }

        $applicationIcons = [
            'csv' => 'file-excel', 'pdf' => 'file-pdf', 'plain' => 'file-excel', 'text' => 'file-text',
            'msword' => 'file-word', 'application/x-zip-compressed' => 'file-archive',
            'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file-word',
            'vnd.openxmlformats-officedocument.wordprocessingml.template' => 'file-word',
            'vnd.ms-word.document.macroEnabled.12' => 'file-word', 'vnd.ms-word.template.macroEnabled.12' => 'file-word',
            'vnd.ms-excel' => 'file-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'file-excel',
            'vnd.openxmlformats-officedocument.spreadsheetml.template' => 'file-excel',
            'vnd.ms-excel.sheet.macroEnabled.12' => 'file-excel', 'vnd.ms-excel.template.macroEnabled.12' => 'file-excel',
            'vnd.ms-excel.addin.macroEnabled.12' => 'file-excel', 'vnd.ms-excel.sheet.binary.macroEnabled.12' => 'file-excel',
            'vnd.ms-powerpoint' => 'file-powerpoint', 'vnd.openxmlformats-officedocument.presentationml.presentation' => 'file-powerpoint',
            'vnd.openxmlformats-officedocument.presentationml.template' => 'file-powerpoint',
            'vnd.openxmlformats-officedocument.presentationml.slideshow' => 'file-powerpoint',
            'vnd.ms-powerpoint.addin.macroEnabled.12' => 'file-powerpoint',
            'vnd.ms-powerpoint.presentation.macroEnabled.12' => 'file-powerpoint',
            'vnd.ms-powerpoint.template.macroEnabled.12' => 'file-powerpoint',
            'vnd.ms-powerpoint.slideshow.macroEnabled.12' => 'file-powerpoint'
        ];
        if (isset($mimetype[1], $applicationIcons[$mimetype[1]])) {
            return '<i class="fa fa-'.$applicationIcons[$mimetype[1]].'" aria-hidden="true"></i>';
        }
        return '<i class="fa fa-file" aria-hidden="true"></i>';
    }

    public function getFileWidget($form, $attachType)
    {
        $pluginOptions = [
            'allowedFileExtensions' => $attachType, 'previewFileType' => 'any',
            'showRemove' => false, 'showUpload' => false
        ];
        if ($this->filename) {
            $pluginOptions += [
                'initialPreview' => strpos($this->mimetype, 'image') === 0 ? $this->getFileUrl() : $this->getAttachmentTypeIcon(),
                'initialPreviewAsData' => strpos($this->mimetype, 'image') === 0,
                'initialPreviewConfig' => [['caption' => $this->filename, 'size' => $this->size]],
                'overwriteInitial' => true, 'showPreview' => true, 'showCaption' => true
            ];
        } else {
            $pluginOptions['browseLabel'] = Yii::t('traits', 'Upload');
        }
        return $form->field($this, 'filename')->widget(FileInput::class, [
            'options' => ['multiple' => true], 'pluginOptions' => $pluginOptions
        ]);
    }

    public function getFilesWidget($attachType)
    {
        $attachments = $this->getAttachs();
        $initialPreview = [];
        $initialPreviewConfig = [];
        foreach ($attachments as $attach) {
            $initialPreviewConfig[] = [
                'caption' => $attach['title'], 'size' => $attach['size'],
                'url' => Url::to(['attachments/deleteonfly', 'id' => $attach['id']])
            ];
            $initialPreview[] = strpos($attach->mimetype, 'image') === 0
                ? Html::img($attach->fileUrl, ['class' => 'img-responsive']) : $attach->getAttachmentTypeIcon();
        }
        $pluginOptions = [
            'allowedFileExtensions' => $attachType, 'previewFileType' => 'any',
            'showPreview' => true, 'showCaption' => true, 'showRemove' => false, 'showUpload' => false
        ];
        if ($initialPreview) {
            $pluginOptions += [
                'initialPreview' => $initialPreview, 'initialPreviewAsData' => false,
                'initialPreviewConfig' => $initialPreviewConfig, 'overwriteInitial' => false
            ];
        }
        return '<label class="control-label" for="items-photo_name">'.Yii::t('traits','Upload').'</label>'.FileInput::widget([
            'model' => $this, 'attribute' => 'attachments[]', 'name' => 'attachments[]',
            'options' => ['multiple' => true], 'pluginOptions' => $pluginOptions
        ]);
    }

    public function getExtensionWidget($form)
    {
        return $form->field($this, 'extension', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-file"></i>']]])
            ->textInput(['disabled' => true]);
    }

    public function getMimeTypeWidget($form)
    {
        return $form->field($this, 'mimetype', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-file"></i>']]])
            ->textInput(['disabled' => true]);
    }

    public function getSizeWidget($form)
    {
        return $form->field($this, 'size', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-balance-scale"></i>']]])
            ->textInput(['disabled' => true, 'value' => $this->formatSize()]);
    }
}
