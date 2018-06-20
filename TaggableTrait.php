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

use Yii;
use dosamigos\taggable\Taggable;
use yii\base\InvalidParamException;

/**
 * Trait TaggableTrait
 *
 * @property int $tagNames
 */
trait TaggableTrait
{

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			[
				'class' => Taggable::class,
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function rules()
	{
		return [
			[['tagNames'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function attributeLabels()
	{
		return [
			'tagNames' => Yii::t('traits', 'TagNames'),
		];
	}

	/**
	 * Generate DetailView for Tags
	 *
	 * @return array
	 * @throws InvalidParamException
	 */
	public function getTagsDetailView()
	{
		return [
			'attribute' => 'user_id',
			'format' => 'html',
			'value' => $this->tagNames
		];
	}

}
