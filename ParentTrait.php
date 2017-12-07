<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.1.1
 */

namespace cinghie\traits;

use Yii;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Trait ParentTrait
 *
 * @property integer $parent_id
 */
trait ParentTrait
{

	/**
	 * @inheritdoc
	 */
	public static function rules()
	{
		return [
			[['parent_id'], 'integer'],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this::className(), 'targetAttribute' => ['parent_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function attributeLabels()
	{
		return [
			'parent_id' => Yii::t('traits', 'Parent'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(self::className(), ['id' => 'parent_id'])->from(self::tableName() . ' AS parent');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParents()
	{
		return $this->hasMany(self::className(), ['id' => 'parent_id'])->from(self::tableName() . ' AS parent');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChild()
	{
		return $this->hasOne(self::className(), ['parent_id' => 'id'])->from(self::tableName() . ' AS child');
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChilds()
	{
		return $this->hasMany(self::className(), ['parent_id' => 'id'])->from(self::tableName() . ' AS child');
	}

	/**
	 * Generate Parent Form Widget
	 *
	 * @param \kartik\widgets\ActiveForm $form
	 * @param [] $items
	 *
	 * @return \kartik\form\ActiveField
	 */
	public function getParentWidget($form,$items)
	{
		/** @var $this \yii\base\Model */
		return $form->field($this, 'parent_id')->widget(Select2::className(), [
			'data' => $items,
			'addon' => [
				'prepend' => [
					'content'=>'<i class="glyphicon glyphicon-folder-open"></i>'
				]
			],
		]);
	}

	/**
	 * Generate GridView for Parent
	 *
	 * @param string $field
	 * @param string $url
	 * @param bool $hideItem
	 *
	 * @return string
	 * @throws \yii\base\InvalidParamException
	 */
	public function getParentGridView($field,$url,$hideItem = false)
	{
		/** @var $this \yii\base\Model */
		if (isset($this->parent->id) && !$hideItem) {
			$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
			return Html::a($this->parent->$field,$url);
		}

		if (isset($hideItem) && $hideItem)
		{
			if($this->parent_id === $hideItem) {
				return '<span class="fa fa-ban text-danger"></span>';
			}

			$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
			return Html::a($this->parent->$field,$url);
		}

		return '<span class="fa fa-ban text-danger"></span>';
	}

}
