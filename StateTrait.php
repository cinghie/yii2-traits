<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 0.1.0
 */

namespace cinghie\traits;

/*
 * @property int $state
 */
trait StateTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'state' => \Yii::t('traits', 'State'),
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
    public function inactive()
    {
        return (bool)$this->updateAttributes([
            'state' => 0
        ]);
    }
    /**
     * Return an array with states
     *
     * @return array
     */
    public function getStateSelect2()
    {
        return [
            "1" => \Yii::t('traits', 'Actived'),
            "0" => \Yii::t('traits', 'Inactived')
        ];
    }


    /**
     * Generate State Form Widget
     *
     * @return \kartik\widgets\Select2 widget
     */
    public function getStateWidget($form,$model)
    {
        return $form->field($model, 'state')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $model->getStateSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-check"></i>'
                ]
            ],
        ]);
    }

}
