<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.1.1
 */

namespace cinghie\traits;

use Yii;
use dosamigos\ckeditor\CKEditor;
use dosamigos\tinymce\TinyMce;
use kartik\markdown\MarkdownEditor;
use yii\helpers\Html;
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
	 *
	 * @return \kartik\form\ActiveField | string
	 * @throws \Exception
	 */
    public function getEditorWidget($form, $field, $requestEditor = '')
    {
        $editor = $requestEditor !== '' ? $requestEditor : Yii::$app->controller->module->editor;

	    switch ($editor)
	    {
		    case 'ckeditor':
			    return $this->getCKEditorWidget($form, $field, $options = ['rows' => 6], $preset = 'basic');
			    break;
		    case 'imperavi':
			    return $this->getImperaviWidget($form, $field, $options = ['css' => 'wym.css', 'minHeight' => 250], $plugins = ['fullscreen', 'clips']);
			    break;
		    case 'markdown':
			    return $this->getMarkdownWidget($form, $field, $options = ['height' => 250, 'encodeLabels' => true]);
			    break;
		    case 'tinymce':
			    return $this->getTinyMCEWidget($form, $field, $options = ['rows' => 14]);
			    break;
		    default:
			    return $this->getNoEditorWidget($form, $field, $maxLength = false);
	    }
    }

    /**
     * Get a CKEditor Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     *
     * @return \kartik\form\ActiveField | string
     */
    public function getCKEditorWidget($form, $field, $options, $preset)
    {
        if($form !== null) {
	        /** @var $this \yii\base\Model */
	        return $form->field($this, $field)->widget(CKEditor::class, [
		        'options' => $options,
		        'preset' => $preset
	        ]);
        }

	    return CKEditor::widget([
	    	'name' => $field,
		    'options' => $options,
		    'preset' => $preset
	    ]);
    }

	/**
	 * Get a Imperavi Editor Widget
	 *
	 * @param \kartik\widgets\ActiveForm $form
	 * @param string $field
	 *
	 * @return \kartik\form\ActiveField | string
	 * @throws \Exception
	 */
    public function getImperaviWidget($form, $field, $options, $plugins)
    {
	    $options['lang'] = substr(Yii::$app->language, 0, 2);

	    if($form !== null) {
		    /** @var $this \yii\base\Model */
		    return $form->field($this, $field)->widget(Imperavi::class, [
			    'options' => $options,
			    'plugins' => $plugins
		    ]);
	    }

	    return Imperavi::widget([
	    	'attribute' => $field,
		    'options' => $options,
		    'plugins' => $plugins
	    ]);
    }

    /**
     * Get a Markdown Editor Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @param string $field
     *
     * @return \kartik\form\ActiveField | string
     */
    public function getMarkdownWidget($form, $field, $options)
    {
	    if($form !== null) {
		    /** @var $this \yii\base\Model */
		    return $form->field($this, $field)->widget(
			    MarkdownEditor::class,
			    $options
		    );
	    }

	    return MarkdownEditor::widget([
		    'name' => $field,
		    'options' => $options
	    ]);
    }

	/**
	 * Get a No-Editor Widget
	 *
	 * @param \kartik\widgets\ActiveForm $form
	 * @param string $field
	 * @param boolean $maxLength
	 *
	 * @return \kartik\form\ActiveField | string
	 */
	public function getNoEditorWidget($form, $field, $maxLength = false)
	{
		if($form !== null) {
			/** @var $this \yii\base\Model */
			return $form->field($this, $field)->textarea([
				'maxLength' => $maxLength,
				'rows' => 6
			]);
		}

		return Html::textarea($field, '', [
			'class' => 'form-control',
			'maxLength' => $maxLength,
			'rows' => 6
		]);
	}

	/**
	 * Get a TinyMCE Editor Widget
	 *
	 * @param \kartik\widgets\ActiveForm $form
	 * @param string $field
	 *
	 * @return \kartik\form\ActiveField | string
	 */
	public function getTinyMCEWidget($form, $field, $options)
	{
		if($form !== null) {
			/** @var $this \yii\base\Model */
			return $form->field($this, $field)->widget(TinyMce::class, [
				'clientOptions' => [
					'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
				],
				'language' => Yii::$app->language,
				'options' => $options
			]);
		}

		return TinyMce::widget([
			'name' => $field,
			'clientOptions' => [
				'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
			],
			'language' => Yii::$app->language,
			'options' => $options
		]);
	}

}
