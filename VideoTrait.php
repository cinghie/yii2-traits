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
use kartik\widgets\Select2;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Trait VideoTrait
 *
 * @property string $video
 * @property string $video_caption
 * @property string $video_credits
 * @property string $video_type
 */
trait VideoTrait
{
    public function getVideoRules()
    {
        return [
            [['video_caption', 'video_credits'], 'string', 'max' => 255],
            [['video'], 'string', 'max' => 50],
            [['video_type'], 'string', 'max' => 20],
        ];
    }

    public function getVideoAttributeLabels()
    {
        return [
            'video' => Yii::t('traits', 'Video'),
            'video_caption' => Yii::t('traits', 'Video Caption'),
            'video_credits' => Yii::t('traits', 'Video Credits'),
            'video_type' => Yii::t('traits', 'Video Type'),
        ];
    }

    public function getVideoTypeSelect2()
    {
	    return [
		    'youtube' => Yii::t('traits','YouTube'),
	        'vimeo' => Yii::t('traits','Vimeo'),
	        'dailymotion' => Yii::t('traits','Dailymotion')
	    ];
    }

    public function getVideoIDWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-film"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    public function getVideoTypeWidget($form)
    {
        /** @var $this Model | VideoTrait */
        return $form->field($this, 'video_type')->widget(Select2::class, [
            'data' => $this->getVideoTypeSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-film"></i>'
                ]
            ],
        ]);
    }

    public function getVideoCaptionWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video_caption', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-video-camera fas fa-video"></i>'
                ]
            ]
        ])->textarea(['maxlength' => true,'rows' => 6]);
    }

    public function getVideoCreditsWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video_credits', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-barcode"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }
}
