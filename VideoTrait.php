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
    /**
     * @inheritdoc
     * 
     * Note: In PHP 8.1+, calling this method statically (e.g., VideoTrait::rules())
     * may generate a deprecation warning. It's recommended to use getVideoRules() instance method instead.
     */
    public static function rules()
    {
        return [
            [['video_caption', 'video_credits'], 'string', 'max' => 255],
            [['video'], 'string', 'max' => 50],
            [['video_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * Instance method to get rules without deprecation warning
     * 
     * @return array
     */
    public function getVideoRules()
    {
        return static::rules();
    }

    /**
     * @inheritdoc
     * 
     * Note: In PHP 8.1+, calling this method statically will generate a deprecation warning.
     * It's recommended to use getVideoAttributeLabels() instance method instead.
     * 
     * @return array
     */
    public static function attributeLabels()
    {
        return [
            'video' => Yii::t('traits', 'Video ID'),
            'video_caption' => Yii::t('traits', 'Video Caption'),
            'video_credits' => Yii::t('traits', 'Video Credits'),
            'video_type' => Yii::t('traits', 'Video Type'),
        ];
    }

    /**
     * Instance method to get attribute labels without deprecation warning
     * 
     * @return array
     */
    public function getVideoAttributeLabels()
    {
        return static::attributeLabels();
    }

    /**
     * Return array for Video Type
     *
     * @return array
     */
    public function getVideoTypeSelect2()
    {
	    return [
		    'youtube' => Yii::t('traits','YouTube'),
	        'vimeo' => Yii::t('traits','Vimeo'),
	        'dailymotion' => Yii::t('traits','Dailymotion')
	    ];
    }

	/**
	 * Generate Video ID Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
    public function getVideoIDWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-film"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

	/**
	 * Generate Video Type Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws Exception
	 */
    public function getVideoTypeWidget($form)
    {
        /** @var $this Model | VideoTrait */
        return $form->field($this, 'video_type')->widget(Select2::class, [
            'data' => $this->getVideoTypeSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-film"></i>'
                ]
            ],
        ]);
    }

	/**
	 * Generate Video Caption Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
    public function getVideoCaptionWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video_caption', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-facetime-video"></i>'
                ]
            ]
        ])->textarea(['maxlength' => true,'rows' => 6]);
    }

	/**
	 * Generate Video Credits Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
    public function getVideoCreditsWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'video_credits', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-barcode"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }
}
