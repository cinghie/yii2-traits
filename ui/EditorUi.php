<?php

namespace cinghie\traits\ui;

use Yii;
use dosamigos\ckeditor\CKEditor;
use dosamigos\tinymce\TinyMce;
use kartik\markdown\MarkdownEditor;
use vova07\imperavi\Widget as Imperavi;
use yii\helpers\Html;

/**
 * Editor rendering used by EditorTrait compatibility wrappers.
 */
final class EditorUi
{
    public static function editorWidget($model, $form, $field, $editor, $value = '', array $options = [])
    {
        switch ($editor) {
            case 'ckeditor':
                return static::ckeditor($model, $form, $field, $value, $options ?: ['rows' => 6], 'basic');
            case 'imperavi':
                return static::imperavi($model, $form, $field, $value, $options);
            case 'markdown':
                return static::markdown($model, $form, $field, $value, $options ?: ['height' => 250, 'encodeLabels' => true]);
            case 'tinymce':
                return static::tinyMce($model, $form, $field, $value, $options ?: ['rows' => 14]);
            default:
                return static::plain($model, $form, $field, $value, $options ?: ['maxLength' => false, 'rows' => 6]);
        }
    }

    public static function ckeditor($model, $form, $field, $value, array $options, $preset)
    {
        if ($form !== null) {
            return $form->field($model, $field)->widget(CKEditor::class, [
                'options' => $options,
                'preset' => $preset,
            ]);
        }

        return CKEditor::widget([
            'name' => $field,
            'options' => $options,
            'preset' => $preset,
            'value' => $value,
        ]);
    }

    public static function imperavi($model, $form, $field, $value, array $options)
    {
        $settings = [
            'lang' => substr(Yii::$app->language, 0, 2),
            'minHeight' => $options['minHeight'] ?? 260,
            'imageManagerJson' => $options['imageManagerJson'] ?? '',
            'imageUpload' => $options['imageUpload'] ?? '',
            'plugins' => $options['plugins'] ?? ['clips', 'fullscreen'],
            'clips' => $options['clips'] ?? '',
        ];

        if ($form !== null) {
            return $form->field($model, $field)->widget(Imperavi::class, ['settings' => $settings]);
        }

        return Imperavi::widget([
            'name' => $field,
            'settings' => $settings,
            'value' => $value,
        ]);
    }

    public static function markdown($model, $form, $field, $value, array $options)
    {
        if ($form !== null) {
            return $form->field($model, $field)->widget(MarkdownEditor::class, $options);
        }

        return MarkdownEditor::widget([
            'name' => $field,
            'options' => $options,
            'value' => $value,
        ]);
    }

    public static function plain($model, $form, $field, $value, array $options)
    {
        if ($form !== null) {
            return $form->field($model, $field)->textarea($options);
        }

        $options['class'] = 'form-control';
        return Html::textarea($field, $value, $options);
    }

    public static function tinyMce($model, $form, $field, $value, array $options)
    {
        $clientOptions = [
            'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        ];

        if ($form !== null) {
            return $form->field($model, $field)->widget(TinyMce::class, [
                'clientOptions' => $clientOptions,
                'language' => Yii::$app->language,
                'options' => $options,
            ]);
        }

        return TinyMce::widget([
            'name' => $field,
            'clientOptions' => $clientOptions,
            'language' => Yii::$app->language,
            'options' => $options,
            'value' => $value,
        ]);
    }
}
