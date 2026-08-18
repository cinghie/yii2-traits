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
	/** Attach the taggable behavior. */
	public function behaviors()
	{
		return [
			[
				'class' => Taggable::class,
			],
		];
	}

	/** Validation rules contributed by this trait. */
	public function getTaggableRules()
	{
		return [
			[['tagNames'], 'safe'],
		];
	}

	/** Attribute labels contributed by this trait. */
	public function getTaggableAttributeLabels()
	{
		return [
			'tagNames' => Yii::t('traits', 'TagNames'),
		];
	}

	/** Return the tags configuration for DetailView. */
	public function getTagsDetailView()
	{
		return [
			'attribute' => 'user_id',
			'format' => 'html',
			'value' => $this->tagNames
		];
	}
}
