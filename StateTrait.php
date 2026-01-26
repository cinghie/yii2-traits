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
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\base\Model;

/**
 * Trait StateTrait
 *
 * @property int $state
 */
trait StateTrait
{
    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['state'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * 
     * Note: In PHP 8.1+, calling this method statically (e.g., StateTrait::attributeLabels())
     * will generate a deprecation warning. It's recommended to call this as an instance method
     * in your model's attributeLabels() method instead.
     * 
     * @return array
     */
    public static function attributeLabels()
    {
        return [
            'state' => Yii::t('traits', 'State'),
        ];
    }

    /**
     * Instance method to get attribute labels without deprecation warning
     * Use this method in your model's attributeLabels() instead of calling the static method
     * 
     * @return array
     */
    public function getStateAttributeLabels()
    {
        return static::attributeLabels();
    }

    /**
     * Active model state (Set 1)
     *
     * @return bool
     */
    public function active()
    {
        return (bool)$this->updateAttributes([
            'state' => 1
        ]);
    }

    /**
     * Inactive model state (Set 0)
     *
     * @return bool
     */
    public function deactive()
    {
        return (bool)$this->updateAttributes([
            'state' => 0
        ]);
    }

	/**
	 * Generate State Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws Exception
	 */
    public function getStateWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'state')->widget(Select2::class, [
            'data' => StateTrait::getStateSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-check"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate GridView for State
     *
     * @param bool $removeLink
     *
     * @return string
     */
    public function getStateGridView($removeLink = false)
    {
        if($this->state)
        {
        	if($removeLink) {
        		return '<span class="glyphicon glyphicon-ok text-success"></span>';
	        }

            return Html::a(
                '<span class="glyphicon glyphicon-ok text-success"></span>',
                ['changestate', 'id' => $this->id],
                ['data-method' => 'post']
            );
        }

	    if($removeLink) {
		    return '<span class="glyphicon glyphicon-remove text-danger"></span>';
	    }

	    return Html::a(
		    '<span class="glyphicon glyphicon-remove text-danger"></span>',
		    ['changestate', 'id' => $this->id],
		    ['data-method' => 'post']
	    );
    }

    /**
     * Generate DetailView for State
     *
     * @return array
     */
    public function getStateDetailView()
    {
        return [
            'attribute' => 'state',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->state ? '<span class="label label-success">'. Yii::t('traits', 'Actived').'</span>' : '<span class="label label-danger">'. Yii::t('traits', 'Deactivated').'</span>',
            'valueColOptions'=> [
	            'style' => 'width:30%'
            ],
            'widgetOptions' => [
                'pluginOptions' => [
                    'onText' => 'Yes',
                    'offText' => 'No',
                ]
            ]
        ];
    }

    /**
     * Return an array with states
     *
     * @return array
     */
    public static function getStateSelect2()
    {
        return [
            '1' => Yii::t('traits', 'Actived'),
            '0' => Yii::t('traits', 'Inactived')
        ];
    }
}
