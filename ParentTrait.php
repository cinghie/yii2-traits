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
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Trait ParentTrait
 *
 * @property int $parent_id
 */
trait ParentTrait
{

	/**
	 * @inheritdoc
	 */
	public static function rules()
	{
		return [
			[['parent_id'], 'int'],
			//[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => get_called_class(), 'targetAttribute' => [ 'parent_id' => 'id']],
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
	 * @return ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(self::class, ['id' => 'parent_id'])->from(self::tableName() . ' AS parent');
	}

	/**
	 * @return ActiveQuery
	 */
	public function getParents()
	{
		return $this->hasMany(self::class, ['id' => 'parent_id'])->from(self::tableName() . ' AS parent');
	}

	/**
	 * @return ActiveQuery
	 */
	public function getChild()
	{
		return $this->hasOne(self::class, ['parent_id' => 'id'])->from(self::tableName() . ' AS child');
	}

	/**
	 * @return ActiveQuery
	 */
	public function getChilds()
	{
		return $this->hasMany(self::class, ['parent_id' => 'id'])->from(self::tableName() . ' AS child');
	}

	/**
	 * Generate Parent Form Widget
	 *
	 * @param ActiveForm $form
	 * @param [] $items
	 *
	 * @return ActiveField
	 */
	public function getParentWidget($form,$items)
	{
		/** @var $this \yii\base\Model */
		return $form->field($this, 'parent_id')->widget(Select2::class, [
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
	 * @throws InvalidParamException
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
