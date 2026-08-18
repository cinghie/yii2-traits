<?php

namespace cinghie\traits\ui;

use Yii;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Presentation helpers for AttachmentTrait.
 */
final class AttachmentUi
{
    public static function preview($model, $class = 'img-responsive', $style = '')
    {
        if (strpos($model->mimetype, 'image') !== false) {
            return Html::img($model->fileUrl, ['class' => $class, 'style' => $style]);
        }
        if (strpos($model->mimetype, 'video') !== false) {
            return Html::img($model->getVideoThumb(), ['class' => $class, 'style' => $style]);
        }

        return static::typeIcon($model);
    }

    public static function typeIcon($model)
    {
        $extensions = [
            'csv' => 'file-excel', 'mp4' => 'file-video', 'pdf' => 'file-pdf',
            'plain' => 'file-excel', 'rar' => 'file-archive', 'text' => 'file-text', 'zip' => 'file-archive'
        ];
        if (isset($extensions[$model->extension])) {
            return '<i class="fa fa-'.$extensions[$model->extension].'" aria-hidden="true"></i>';
        }

        $mimetype = $model->getAttachmentType();
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

    public static function fileWidget($model, $form, $attachType)
    {
        $pluginOptions = [
            'allowedFileExtensions' => $attachType,
            'previewFileType' => 'any',
            'showRemove' => false,
            'showUpload' => false,
        ];

        if ($model->filename) {
            $pluginOptions += [
                'initialPreview' => strpos($model->mimetype, 'image') === 0 ? $model->getFileUrl() : static::typeIcon($model),
                'initialPreviewAsData' => strpos($model->mimetype, 'image') === 0,
                'initialPreviewConfig' => [['caption' => $model->filename, 'size' => $model->size]],
                'overwriteInitial' => true,
                'showPreview' => true,
                'showCaption' => true,
            ];
        } else {
            $pluginOptions['browseLabel'] = Yii::t('traits', 'Upload');
        }

        return $form->field($model, 'filename')->widget(FileInput::class, [
            'options' => ['multiple' => true],
            'pluginOptions' => $pluginOptions,
        ]);
    }

    public static function filesWidget($model, $attachType)
    {
        $attachments = $model->getAttachs();
        $initialPreview = [];
        $initialPreviewConfig = [];

        foreach ($attachments as $attach) {
            $initialPreviewConfig[] = [
                'caption' => $attach['title'],
                'size' => $attach['size'],
                'url' => Url::to(['attachments/deleteonfly', 'id' => $attach['id']]),
            ];
            $initialPreview[] = strpos($attach->mimetype, 'image') === 0
                ? Html::img($attach->fileUrl, ['class' => 'img-responsive'])
                : $attach->getAttachmentTypeIcon();
        }

        $pluginOptions = [
            'allowedFileExtensions' => $attachType,
            'previewFileType' => 'any',
            'showPreview' => true,
            'showCaption' => true,
            'showRemove' => false,
            'showUpload' => false,
        ];
        if ($initialPreview) {
            $pluginOptions += [
                'initialPreview' => $initialPreview,
                'initialPreviewAsData' => false,
                'initialPreviewConfig' => $initialPreviewConfig,
                'overwriteInitial' => false,
            ];
        }

        return '<label class="control-label" for="items-photo_name">'.Yii::t('traits','Upload').'</label>'.FileInput::widget([
            'model' => $model,
            'attribute' => 'attachments[]',
            'name' => 'attachments[]',
            'options' => ['multiple' => true],
            'pluginOptions' => $pluginOptions,
        ]);
    }

    public static function extensionWidget($model, $form)
    {
        return $form->field($model, 'extension', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-file"></i>']]])
            ->textInput(['disabled' => true]);
    }

    public static function mimeTypeWidget($model, $form)
    {
        return $form->field($model, 'mimetype', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-file"></i>']]])
            ->textInput(['disabled' => true]);
    }

    public static function sizeWidget($model, $form)
    {
        return $form->field($model, 'size', ['addon' => ['prepend' => ['content'=>'<i class="fa fa-balance-scale"></i>']]])
            ->textInput(['disabled' => true, 'value' => $model->formatSize()]);
    }
}
