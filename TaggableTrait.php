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
	 * 
	 * Note: In PHP 8.1+, calling this method statically will generate a deprecation warning.
	 * It's recommended to use getTaggableRules() instance method instead.
	 * 
	 * @return array
	 */
	public static function rules()
	{
		return [
			[['tagNames'], 'safe'],
		];
	}

	/**
	 * Instance method to get rules without deprecation warning
	 * 
	 * @return array
	 */
	public function getTaggableRules()
	{
		return static::rules();
	}

	/**
	 * @inheritdoc
	 * 
	 * Note: In PHP 8.1+, calling this method statically will generate a deprecation warning.
	 * It's recommended to use getTaggableAttributeLabels() instance method instead.
	 * 
	 * @return array
	 */
	public static function attributeLabels()
	{
		return [
			'tagNames' => Yii::t('traits', 'TagNames'),
		];
	}

	/**
	 * Instance method to get attribute labels without deprecation warning
	 * 
	 * @return array
	 */
	public function getTaggableAttributeLabels()
	{
		return static::attributeLabels();
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
