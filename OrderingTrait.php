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

use Exception;
use Yii;
use kartik\form\ActiveField;
use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\base\Model;
use yii\db\Expression;

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
	public static function rules()
	{
		return  [
			[['ordering'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function attributeLabels()
	{
		return [
			'ordering' => Yii::t('traits', 'Ordering'),
		];
	}

	/**
	 * Set Model Ordering on Class
	 *
	 * @param Model $class
	 * @param string $fieldOrdering
	 * @param int $oldOrdering
	 * @param int $lastOrdering
	 */
	public function setOrdering($class,$fieldOrdering,$oldOrdering,$lastOrdering)
	{
		$newOrdering = (int)$this->ordering;

		// Verifico se è cambiato l'ordine
		if($newOrdering !== $oldOrdering)
		{
			// If new Orderis 0 (First Element) and the oldOrdering is 1, ordering is 1
			if ( $newOrdering === 0 && $oldOrdering === 1 )
			{
				$this->ordering = 1;

			} elseif ( 0 === $newOrdering ) {

				// If new ordering is 0 (First Element), actual Item ordering = 1 and all other Items ordering + 1
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<','ordering', $oldOrdering],
				];

				$class::updateAll([
					'ordering' => new Expression('ordering + 1')
				], $condition);

				$this->setMinOrder();

			} elseif( $newOrdering === 999999999 ) {

				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['>','ordering', $oldOrdering],
				];

				$class::updateAll([
					'ordering' => new Expression('ordering - 1')
				], $condition);

				$this->ordering = $lastOrdering;

			} elseif ( $newOrdering > $oldOrdering ) {

				// IF newOrdering is > oldOrdering, all items > oldOrdering <= newOrdering are ordering -1 and this->ordering = newOrdering
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['>','ordering', $oldOrdering],
					['<=','ordering', $newOrdering],
				];

				$class::updateAll([
					'ordering' => new Expression('ordering - 1')
				], $condition);

				$this->ordering = $newOrdering;

			} else {

				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<','ordering', $oldOrdering],
					['>=','ordering', $newOrdering],
				];

				$class::updateAll([
					'ordering' => new Expression('ordering + 1')
				], $condition);

				$this->ordering = $newOrdering;
			}

		}


	}

	/**
	 * Set Max Ordering
	 *
	 * @param Model $class
	 * @param array $condition
	 */
	public function setMaxOrdering($class,$condition)
	{
		$this->ordering = $this->getLastOrdering($class,$condition);
	}

	/**
	 * Set Min Ordering
	 */
	public function setMinOrder()
	{
		$this->ordering = 1;
	}

	/**
	 * Get Max ordering in field
	 *
	 * @param Model $class
	 * @param array $condition
	 *
	 * @return mixed
	 */
	public function getLastOrdering($class,$condition)
	{
		return $class::find()->where($condition)->max('ordering');
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
	 * @throws Exception
	 */
	public function getOrderingWidget($form, $class, $orderingField, $selectField, $condition)
	{
		if($this->isNewRecord) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [ -1 => Yii::t('traits', 'Save to order') ];
		} elseif(!$this->isNewRecord && !$this->$orderingField) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [ -1 => Yii::t('traits', 'Select a category to order') ];
		} else {
			$options = [];
			$orderingSelect = $this->getOrderingSelect2($class, $orderingField, $selectField, $condition);
		}

		/** @var $this Model */
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
		$array = [ 0 => Yii::t('traits','First Element') ];
		$items = $class::find()->select($selectField)->where($condition)->orderby('ordering ASC')->all();

		if(count($items) === 1) {
			return $array;
		}

		foreach($items as $item) {
			$array[$item[$selectField[0]]] = $item[$selectField[1]];
		}

		$array[999999999] = Yii::t('traits','Last Element');

		return $array;
	}
}
