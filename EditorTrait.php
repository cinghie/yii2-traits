<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.0.2
 */

namespace cinghie\traits;

use Yii;
use dosamigos\ckeditor\CKEditor;
use dosamigos\tinymce\TinyMce;
use kartik\markdown\MarkdownEditor;
use yii\imperavi\Widget as Imperavi;

/**
 * Trait EditorTrait
 *
 * @property integer $access
 */
trait EditorTrait
{

    /**
     * Generate Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     * @param string $requestEditor
     * @return \kartik\form\ActiveField
     * @internal param string $editor
     */
    public function getEditorWidget($form,$field,$requestEditor = '')
    {
        $editor = $requestEditor !== '' ? $requestEditor : Yii::$app->controller->module->editor;

        switch ($editor)
        {
            case 'ckeditor':
                return $this->getCKEditorWidget($form,$field);
                break;
            case 'imperavi':
                return $this->getImperaviWidget($form,$field);
                break;
            case 'markdown':
                return $this->getMarkdownWidget($form,$field);
                break;
            case 'tinymce':
                return $this->getTinyMCEWidget($form,$field);
                break;
            default:
                return $this->getNoEditorWidget($form,$field,$maxLength = false);
        }
    }

    /**
     * Get a CKEditor Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     * @return \kartik\form\ActiveField
     */
    public function getCKEditorWidget($form,$field)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, $field)->widget(CKEditor::className(), [
            'options' => ['rows' => 6],
            'preset' => 'advanced'
        ]);
    }

    /**
     * Get a TinyMCE Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     * @return \kartik\form\ActiveField
     */
    public function getTinyMCEWidget($form,$field)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, $field)->widget(TinyMce::className(), [
            'options' => ['rows' => 6],
            'clientOptions' => [
                'plugins' => [
                    'advlist autolink lists link charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste'
                ],
                'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            ]
        ]);
    }

    /**
     * Get a Imperavi Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     * @return \kartik\form\ActiveField
     */
    public function getImperaviWidget($form,$field)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, $field)->widget(Imperavi::className(), [
            'options' => [
            	'lang' => substr(Yii::$app->language,0,2),
                'css' => 'wym.css',
                'minHeight' => 250,
            ],
            'plugins' => [
                'fullscreen',
                'clips'
            ],
        ]);
    }

    /**
     * Get a Markdown Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     * @return \kartik\form\ActiveField
     */
    public function getMarkdownWidget($form,$field)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, $field)->widget(
            MarkdownEditor::className(),
            ['height' => 300, 'encodeLabels' => true]
        );
    }

	/**
	 * Get a No-Editor Widget
	 *
	 * @param \kartik\widgets\ActiveForm $form
	 * @param string $field
	 * @param boolean $maxLength
	 * @return \kartik\form\ActiveField
	 */
	public function getNoEditorWidget($form,$field,$maxLength = false)
	{
		/** @var $this \yii\base\Model */
		return $form->field($this, $field)->textarea(['maxLength' => $maxLength,'rows' => 6]);
	}

}
