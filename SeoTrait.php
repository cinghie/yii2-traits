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
use Throwable;
use Yii;
use kartik\detail\DetailView;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\base\InvalidConfigException;
use yii\base\Model;

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
    /** Validation rules contributed by this trait. */
    public function getSeoRules()
    {
        return [
            [['metadesc', 'metakey'], 'string'],
            [['author', 'copyright'], 'string', 'max' => 50],
            [['robots'], 'string', 'max' => 20],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getSeoAttributeLabels()
    {
        return [
            'author' => Yii::t('traits', 'Author'),
            'copyright' => Yii::t('traits', 'Copyright'),
            'metadesc' => Yii::t('traits', 'Meta Description'),
            'metakey' => Yii::t('traits', 'Meta Keywords'),
            'robots' => Yii::t('traits', 'Robots'),
        ];
    }

    public function getRobotsWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'robots')->widget(Select2::class, [
            'data' => self::getRobotsOptions(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-globe"></i>'
                ]
            ],
        ]);
    }

    public function getAuthorWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'author', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-user"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    public function getCopyrightWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'copyright', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-ban"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    public function getMetaDescriptionWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'metadesc', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-info-circle"></i>'
                ]
            ]
        ])->textarea(['rows' => 5]);
    }

    public function getMetaKeyWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'metakey', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-tags"></i>'
                ]
            ]
        ])->textarea(['rows' => 5]);
    }

    public static function getRobotsOptions()
    {
        return [
            'index, follow' => 'index, follow',
            'noindex, nofollow' => 'noindex, nofollow',
            'noindex, follow' => 'noindex, follow',
            'index, nofollow' => 'index, nofollow'
        ];
    }

    public function getDetailSeoView()
    {
        return DetailView::widget([
            'model' => $this,
            'condensed' => true,
            'enableEditMode' => false,
            'deleteOptions' => false,
            'hover' => true,
            'mode' => DetailView::MODE_VIEW,
            'panel' => [
                'after' => false,
                'before' => false,
                'footer' => false,
                'heading' => Yii::t('traits', 'SEO Informations'),
                'type' => DetailView::TYPE_INFO,
            ],
            'attributes' => [
                [
                    'attribute' => 'metadesc:ntext',
                    'valueColOptions'=> ['style' => 'width:30%'],
                ],
                [
                    'attribute' => 'metakey:ntext',
                    'valueColOptions'=> ['style' => 'width:30%'],
                ],
                [
                    'attribute' => 'robots',
                    'valueColOptions'=> ['style' => 'width:30%'],
                ],
                [
                    'attribute' => 'author',
                    'valueColOptions'=> ['style' => 'width:30%'],
                ],
                [
                    'attribute' => 'copyright',
                    'valueColOptions'=> ['style' => 'width:30%'],
                ],
            ],
        ]);
    }
}
