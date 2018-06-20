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

use kartik\widgets\ActiveForm;
use Yii;
use kartik\widgets\Select2;

/**
 * Trait SeoTrait
 *
 * @property string $robots
 * @property string $author
 * @property string $copyright
 * @property string $metadesc
 * @property string $metakey
 */
trait SeoTrait
{

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['metadesc', 'metakey'], 'string'],
            [['author', 'copyright'], 'string', 'max' => 50],
            [['robots'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'author' => Yii::t('traits', 'Author'),
            'copyright' => Yii::t('traits', 'Copyright'),
            'metadesc' => Yii::t('traits', 'Metadesc'),
            'metakey' => Yii::t('traits', 'Metakey'),
            'robots' => Yii::t('traits', 'Robots'),
        ];
    }

    /**
     * Generate Robots Form Widget
     *
     * @param ActiveForm $form
     *
     * @return string
     */
    public function getRobotsWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'robots')->widget(Select2::class, [
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
     * @param ActiveForm $form
     *
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
     * @param ActiveForm $form
     *
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
     * @param ActiveForm $form
     *
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
        ])->textarea(['rows' => 5]);
    }

    /**
     * Generate Meta Key Form Widget
     *
     * @param ActiveForm $form
     *
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
        ])->textarea(['rows' => 5]);
    }

    /**
     * Get Robots Options
     *
     * @return array
     */
    public function getRobotsOptions()
    {
        return [
            'index, follow' => 'index, follow',
            'no index, no follow' => 'no index, no follow',
            'no index, follow' => 'no index, follow',
            'index, no follow' => 'index, no follow'
        ];
    }

}
