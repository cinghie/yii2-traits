<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.3
 */

namespace cinghie\traits;

use Exception;
use Yii;
use dosamigos\ckeditor\CKEditor;
use dosamigos\tinymce\TinyMce;
use kartik\form\ActiveField;
use kartik\markdown\MarkdownEditor;
use kartik\widgets\ActiveForm;
use vova07\imperavi\Widget as Imperavi;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Trait EditorTrait
 *
 * @property int $access
 */
trait EditorTrait
{
	/**
	 * Generate Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $requestEditor
	 * @param string $value
	 * @param array $options
	 *
	 * @return ActiveField | string
	 * @throws InvalidConfigException
	 * @throws Exception
	 */
    public function getEditorWidget($form, $field, $requestEditor = '', $value = '', $options = [])
    {
        $editor = $requestEditor !== '' ? $requestEditor : Yii::$app->controller->module->editor;

	    switch ($editor)
	    {
		    case 'ckeditor':
			    $options = empty($options) ? ['rows' => 6] : $options;
			    return $this->getCKEditorWidget($form, $field, $value, $options, $preset = 'basic');
			    break;
		    case 'imperavi':
		    	$options = empty($options) ? [] : $options;
			    return $this->getImperaviWidget($form, $field, $value, $options);
			    break;
		    case 'markdown':
			    $options = empty($options) ? ['height' => 250, 'encodeLabels' => true] : $options;
			    return $this->getMarkdownWidget($form, $field, $value, $options);
			    break;
		    case 'tinymce':
			    $options = empty($options) ? ['rows' => 14] : $options;
			    return $this->getTinyMCEWidget($form, $field, $value, $options);
			    break;
		    default:
			    $options = empty($options) ? ['maxLength' => false, 'rows' => 6] : $options;
			    return $this->getNoEditorWidget($form, $field, $value, $options);
	    }
    }

	/**
	 * Get a CKEditor Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $value
	 * @param array $options
	 * @param string $preset
	 *
	 * @return ActiveField | string
	 * @throws Exception
	 */
    public function getCKEditorWidget($form, $field, $value, $options, $preset)
    {
        if($form !== null) {
	        /** @var $this Model */
	        return $form->field($this, $field)->widget(CKEditor::class, [
		        'options' => $options,
		        'preset' => $preset
	        ]);
        }

	    return CKEditor::widget([
	    	'name' => $field,
		    'options' => $options,
		    'preset' => $preset,
		    'value' => $value
	    ]);
    }

	/**
	 * Get a Imperavi Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $value
	 * @param array $options
	 *
	 * @return ActiveField | string
	 * @throws Exception
	 *
	 * @see https://github.com/vova07/yii2-imperavi-widget
	 */
    public function getImperaviWidget($form, $field, $value, $options)
    {
    	$clips     = isset($options['clips']) ? $options['clips'] : '';
    	$minHeight = isset($options['minHeight']) ? $options['minHeight'] : 260;
    	$plugins   = isset($options['plugins']) ? $options['plugins'] : ['clips','fullscreen'];

	    $imageManagerJson = isset($options['imageManagerJson']) ? $options['imageManagerJson'] : '';
	    $imageUpload = isset($options['imageUpload']) ? $options['imageUpload'] : '';

    	$settings = [
		    'lang' => substr(Yii::$app->language, 0, 2),
		    'minHeight' => $minHeight,
		    'imageManagerJson' => $imageManagerJson,
		    'imageUpload' => $imageUpload,
		    'plugins' => $plugins,
		    'clips' => $clips
	    ];

	    if($form !== null) {
		    /** @var $this Model */
		    return $form->field($this, $field)->widget(Imperavi::class, [
			    'settings' => $settings,
		    ]);
	    }

	    return Imperavi::widget([
		    'name' => $field,
		    'settings' => $settings,
		    'value' => $value
	    ]);
    }

	/**
	 * Get a Markdown Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $value
	 * @param array $options
	 *
	 * @return ActiveField | string
	 * @throws Exception
	 */
    public function getMarkdownWidget($form, $field, $value, $options)
    {
	    if($form !== null) {
		    /** @var $this Model */
		    return $form->field($this, $field)->widget(
			    MarkdownEditor::class,
			    $options
		    );
	    }

	    return MarkdownEditor::widget([
		    'name' => $field,
		    'options' => $options,
		    'value' => $value
	    ]);
    }

	/**
	 * Get a No-Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $value
	 * @param array $options
	 *
	 * @return ActiveField | string
	 * @throws InvalidConfigException
	 */
	public function getNoEditorWidget($form, $field, $value, $options)
	{
		if($form !== null) {
			/** @var $this Model */
			return $form->field($this, $field)->textarea($options);
		}

		$options['class'] = 'form-control';

		return Html::textarea($field, $value, $options);
	}

	/**
	 * Get a TinyMCE Editor Widget
	 *
	 * @param ActiveForm $form
	 * @param string $field
	 * @param string $value
	 * @param array $options
	 *
	 * @return ActiveField | string
	 * @throws Exception
	 */
	public function getTinyMCEWidget($form, $field, $value, $options)
	{
		if($form !== null) {
			/** @var $this Model */
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
			'options' => $options,
			'value' => $value
		]);
	}
}
