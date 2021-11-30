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

use Yii;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Trait SocialTraits
 *
 * @property string $facebook
 * @property string $instagram
 * @property string $linkedin
 * @property string $pinterest
 * @property string $twitter
 * @property string $youtube
 */
trait SocialTrait
{
	/**
	 * @inheritdoc
	 */
	public static function rules()
	{
		return  [
			[['facebook', 'instagram', 'linkedin', 'pinterest', 'twitter', 'youtube'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function attributeLabels()
	{
		return [
			'facebook' => Yii::t('traits', 'Facebook'),
			'instagram' => Yii::t('traits', 'Instagram'),
			'linkedin' => Yii::t('traits', 'Linkedin'),
			'pinterest' => Yii::t('traits', 'Pinterest'),
			'twitter' => Yii::t('traits', 'Twitter'),
			'youtube' => Yii::t('traits', 'YouTube'),
		];
	}

	/**
	 * Get Facebook Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getFacebookWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'facebook', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-facebook"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get Instagram Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getInstagramWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'instagram', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-instagram"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get Linkedin Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getLinkedinWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'linkedin', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-linkedin"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get Pinterest Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getPinterestWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'pinterest', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-pinterest"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get Twitter Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getTwitterWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'twitter', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-twitter"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get Pinterest Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getYouTubeWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'youtube', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fab fa-youtube"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}
}
