<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.0.1
 */

namespace cinghie\traits;

use Yii;

/**
 * Trait NameAliasTrait
 *
 * @property string $alias
 * @property string $name
 */
trait NameAliasTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['alias'], 'unique'],
            [['alias','name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'name' => Yii::t('traits', 'Name'),
        ];
    }

    /**
     * Generate Name Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getNameWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'name',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-pencil"></i>'
                ]
            ],
        ])->textInput(['maxlength' => true]);
    }

    /**
     * Generate Alias Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getAliasWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'alias', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-bookmark"></i>'
                ]
            ]
        ] )->textInput(['maxlength' => 255]);
    }

    /**
     * Generate URL alias by string
     *
     * @param string $string
     * @return string
     */
    public function generateAlias($string)
    {
        // remove any '-' from the string they will be used as concatonater
        $string = str_replace(array('-','_'), ' ', $string);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);

        // lowercase and trim
        $string = trim(strtolower($string));

        return $string;
    }

}
