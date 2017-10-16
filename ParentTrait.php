<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.0.1
 */

namespace cinghie\traits;

use kartik\widgets\Select2;
use yii\base\Object;
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
	public function rules()
	{
		return [
			[['parent_id'], 'integer'],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
	 * @param Object $items
	 * @return \kartik\form\ActiveField
	 */
	public function getParentWidget($form,$items)
	{
		return $form->field($this, 'parent_id')->widget(Select2::classname(), [
			'data' => $items,
			'addon' => [
				'prepend' => [
					'content'=>'<i class="glyphicon glyphicon-folder-open"></i>'
				]
			],
		]);;
	}

	/**
	 * Generate GridView for Parent
	 *
	 * @params string $field
	 * @param Url $url
	 * @return string
	 * @throws \yii\base\InvalidParamException
	 */
	public function getParentGridView($field,$url,$hideItem = false)
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
