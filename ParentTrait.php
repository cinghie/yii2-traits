<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.0.0
 */

namespace cinghie\traits;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Trait ParentTrait
 *
 * @property integer $parenn_id
 */
trait ParentTrait
{

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['parent_id'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'parent_id' => Yii::t('traits', 'Parent'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent($className)
	{
		return $this->hasOne($className, ['id' => 'parent_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChild($className)
	{
		return $this->hasOne($className, ['parent_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChilds($className)
	{
		return $this->hasMany($className, ['parent_id' => 'id']);
	}

	/**
	 * Generate GridView for Parent
	 *
	 * @params string $field
	 * @param Url $url
	 * @return string
	 * @throws \yii\base\InvalidParamException
	 */
	public function getParentGridView($field,$url,$hideItem)
	{
		if (isset($this->parent->id) && !$hideItem) {

			$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
			return Html::a($this->parent->$field,$url);

		} elseif (isset($hideItem) && $hideItem ) {

			if($this->parent_id == $hideItem) {

				return '<span class="fa fa-ban text-danger"></span>';

			} else {

				$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
				return Html::a($this->parent->$field,$url);
			}

		} else {
			return '<span class="fa fa-ban text-danger"></span>';
		}
	}

}
