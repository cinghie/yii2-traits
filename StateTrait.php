<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.0
 */

namespace cinghie\traits;

use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use Yii;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;

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
            [['state'], 'int']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'state' => Yii::t('traits', 'State'),
        ];
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
     */
    public function getStateWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'state')->widget(Select2::class, [
            'data' => $this->getStateSelect2(),
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
     * @return string
     */
    public function getStateGridView()
    {
        if($this->state) {
            return Html::a(
                '<span class="glyphicon glyphicon-ok text-success"></span>',
                ['changestate', 'id' => $this->id],
                ['data-method' => 'post']
            );
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
	            'style'=>'width:30%'
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
    public function getStateSelect2()
    {
        return [
            '1' => Yii::t('traits', 'Actived'),
            '0' => Yii::t('traits', 'Inactived')
        ];
    }

}
