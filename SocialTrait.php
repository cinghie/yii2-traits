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
	public function getSocialRules()
	{
		return [
			[['facebook', 'instagram', 'linkedin', 'pinterest', 'twitter', 'youtube'], 'string', 'max' => 255],
		];
	}

	public function getSocialAttributeLabels()
	{
		return [
			'facebook' => Yii::t('traits', 'Facebook'),
			'instagram' => Yii::t('traits', 'Instagram'),
			'linkedin' => Yii::t('traits', 'LinkedIn'),
			'pinterest' => Yii::t('traits', 'Pinterest'),
			'twitter' => Yii::t('traits', 'Twitter'),
			'youtube' => Yii::t('traits', 'YouTube'),
		];
	}

	public function getFacebookWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'facebook', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-facebook"></i>']]])->textInput(['maxlength' => true]);
	}

	public function getInstagramWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'instagram', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-instagram"></i>']]])->textInput(['maxlength' => true]);
	}

	public function getLinkedinWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'linkedin', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-linkedin"></i>']]])->textInput(['maxlength' => true]);
	}

	public function getPinterestWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'pinterest', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-pinterest"></i>']]])->textInput(['maxlength' => true]);
	}

	public function getTwitterWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'twitter', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-twitter"></i>']]])->textInput(['maxlength' => true]);
	}

	public function getYouTubeWidget($form)
	{
		/** @var Model $this */
		return $form->field($this, 'youtube', ['addon' => ['prepend' => ['content'=>'<i class="fab fa-youtube"></i>']]])->textInput(['maxlength' => true]);
	}
}
