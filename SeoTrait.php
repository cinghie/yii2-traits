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

use Yii;
use kartik\widgets\Select2;

/**
 * Trait SeoTrait
 *
 * @property string $alias
 */
trait SeoTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
        ];
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
     * Generate Robots Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return string
     */
    public function getRobotsWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'robots')->widget(Select2::classname(), [
            'data' => $this->getRobotsOptions(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-globe"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate Author Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return string
     */
    public function getAuthorWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'author', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-user"></i>'
                ]
            ]
        ])->textInput(['maxlength' => 50]);
    }

    /**
     * Generate Copyright Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return string
     */
    public function getCopyrightWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'copyright', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-ban-circle"></i>'
                ]
            ]
        ])->textInput(['maxlength' => 50]);
    }

    /**
     * Generate Meta Description Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return string
     */
    public function getMetaDescriptionWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'metadesc', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-info-sign"></i>'
                ]
            ]
        ])->textarea(['rows' => 4]);
    }

    /**
     * Generate Meta Key Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return string
     */
    public function getMetaKeyWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'metakey', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-tags"></i>'
                ]
            ]
        ])->textarea(['rows' => 4]);
    }

    /**
     * Get Robots Options
     *
     * @return array
     */
    public function getRobotsOptions()
    {
        return [
            "index, follow" => "index, follow",
            "no index, no follow" => "no index, no follow",
            "no index, follow" => "no index, follow",
            "index, no follow" => "index, no follow"
        ];
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
        $string = str_replace('-', ' ', $string);
        $string = str_replace('_', ' ', $string);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);

        // lowercase and trim
        $string = trim(strtolower($string));

        return $string;
    }

}
