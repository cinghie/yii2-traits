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
use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\base\Model;

/**
 * Trait OrderingTrait
 *
 * @property int $ordering
 */
trait OrderingTrait
{

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return  [
			[['ordering'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'ordering' => Yii::t('traits', 'Ordering'),
		];
	}

	/**
	 * Set Ordering on Class
	 */
	public function setOrdering($class)
	{
		$ordering = $this->ordering;

		$items = $class::updateAll(['ordering' => new \yii\db\Expression('@a := @a + 1')], ['cat_id' => $this->cat_id]);

		exit();
	}

	/**
	 * Generate Ordering Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @param Model $class
	 * @param string $orderingField
	 * @param array $selectField
	 * @param array $condition
	 *
	 * @return ActiveField
	 */
	public function getOrderingWidget($form, $class, $orderingField, $selectField, $condition)
	{
		if($this->isNewRecord) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [ -1 => Yii::t('traits', 'Save to order') ];
		} elseif(!$this->isNewRecord && !$this->cat_id) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [ -1 => Yii::t('traits', 'Select a category to order') ];
		} else {
			$options = [];
			$orderingSelect = $this->getOrderingSelect2($class, $orderingField, $selectField, $condition);
		}

		/** @var $this \yii\base\Model */
		return $form->field($this, 'ordering')->widget(Select2::class, [
			'data' => $orderingSelect,
			'options' => $options,
			'addon' => [
				'prepend' => [
					'content'=>'<i class="glyphicon glyphicon-sort"></i>'
				]
			],
		]);
	}

	/**
	 * Return array with all Items by $cat_id
	 *
	 * @param Model $class
	 * @param string $orderingField
	 * @param array $selectField
	 * @param array $condition
	 *
	 * @return array
	 */
	public function getOrderingSelect2($class, $orderingField = '', array $selectField = [], array $condition = [])
	{
		$array = [ 1 => Yii::t('traits','First Element') ];
		$items = $class::find()->select($selectField)->where($condition)->all();

		if(count($items) === 1) {
			return $array;
		}

		foreach($items as $item)
		{
			if($item['id'] !== $this->id) {
				$array[$item['id']] = $item['title'];
			}
		}

		$array[10000000] = Yii::t('traits','Last Element');

		return $array;
	}

}
