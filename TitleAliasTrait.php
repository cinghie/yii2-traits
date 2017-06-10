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
 * @property string $alias
 * @property string $title
 */
trait TitleAliasTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alias' => \Yii::t('traits', 'Alias'),
            'title' => \Yii::t('traits', 'Title'),
        ];
    }

    /**
     * Generate URL alias by title
     *
     * @param string $title
     * @return string
     */
    public function generateAlias($title)
    {
        // remove any '-' from the string they will be used as concatonater
        $title = str_replace('-', ' ', $title);
        $title = str_replace('_', ' ', $title);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $title = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $title);

        // lowercase and trim
        $title = trim(strtolower($title));

        return $title;
    }

    /**
     * Generate Title Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getTitleWidget($form)
    {
        return $form->field($this, 'title',[
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
        return $form->field($this, 'alias', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-bookmark"></i>'
                ]
            ]
        ] )->textInput(['maxlength' => 255]);
    }

}
