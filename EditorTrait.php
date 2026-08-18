<?php

namespace cinghie\traits;

use Yii;
use cinghie\traits\ui\EditorUi;

/**
 * Backwards-compatible façade for editor rendering.
 *
 * UI implementation lives in ui/EditorUi so models using this trait no longer
 * contain editor-specific rendering logic.
 */
trait EditorTrait
{
    public function getEditorWidget($form, $field, $requestEditor = '', $value = '', $options = [])
    {
        $editor = $requestEditor !== '' ? $requestEditor : Yii::$app->controller->module->editor;
        return EditorUi::editorWidget($this, $form, $field, $editor, $value, $options);
    }

    public function getCKEditorWidget($form, $field, $value, $options, $preset)
    {
        return EditorUi::ckeditor($this, $form, $field, $value, $options, $preset);
    }

    public function getImperaviWidget($form, $field, $value, $options)
    {
        return EditorUi::imperavi($this, $form, $field, $value, $options);
    }

    public function getMarkdownWidget($form, $field, $value, $options)
    {
        return EditorUi::markdown($this, $form, $field, $value, $options);
    }

    public function getNoEditorWidget($form, $field, $value, $options)
    {
        return EditorUi::plain($this, $form, $field, $value, $options);
    }

    public function getTinyMCEWidget($form, $field, $value, $options)
    {
        return EditorUi::tinyMce($this, $form, $field, $value, $options);
    }
}
