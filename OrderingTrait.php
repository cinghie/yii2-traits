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
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Transaction;

/**
 * Trait OrderingTrait
 *
 * @property int $ordering
 */
trait OrderingTrait
{
	public static function rules()
	{
		return [[['ordering'], 'integer']];
	}

	public function getOrderingRules()
	{
		return [[['ordering'], 'integer']];
	}

	public static function attributeLabels()
	{
		return ['ordering' => Yii::t('traits', 'Ordering')];
	}

	public function getOrderingAttributeLabels()
	{
		return ['ordering' => Yii::t('traits', 'Ordering')];
	}

	/**
	 * Set Model Ordering on Class.
	 *
	 * Sibling shifts and the persisted ordering of the current ActiveRecord are
	 * committed in one serializable transaction. The caller may still save the
	 * complete model afterwards, as before.
	 *
	 * @param Model|string $class
	 * @param string $fieldOrdering
	 * @param int $oldOrdering
	 * @param int $lastOrdering
	 */
	public function setOrdering($class,$fieldOrdering,$oldOrdering,$lastOrdering)
	{
		$newOrdering = (int)$this->ordering;
		$oldOrdering = (int)$oldOrdering;

		if ($newOrdering === $oldOrdering) {
			return;
		}

		$db = $class::getDb();
		$transaction = $db->beginTransaction(Transaction::SERIALIZABLE);

		try {
			if ($newOrdering === 0 && $oldOrdering === 1) {
				$this->ordering = 1;
			} elseif ($newOrdering === 0) {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<','ordering', $oldOrdering],
				];

				$class::updateAll(['ordering' => new Expression('ordering + 1')], $condition);
				$this->setMinOrder();
			} elseif ($newOrdering === 999999999) {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['>','ordering', $oldOrdering],
				];

				$class::updateAll(['ordering' => new Expression('ordering - 1')], $condition);
				$this->ordering = max(1, (int)$lastOrdering);
			} elseif ($newOrdering > $oldOrdering) {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['>','ordering', $oldOrdering],
					['<=','ordering', $newOrdering],
				];

				$class::updateAll(['ordering' => new Expression('ordering - 1')], $condition);
				$this->ordering = $newOrdering;
			} else {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<','ordering', $oldOrdering],
					['>=','ordering', $newOrdering],
				];

				$class::updateAll(['ordering' => new Expression('ordering + 1')], $condition);
				$this->ordering = max(1, $newOrdering);
			}

			// Persist the moved row inside the same transaction when the trait is
			// hosted by an existing ActiveRecord. This prevents a concurrent request
			// from observing shifted siblings while the moved row still has its old
			// ordering value.
			if ($this instanceof ActiveRecord && !$this->isNewRecord) {
				$primaryKey = $this->getPrimaryKey(true);
				if ($primaryKey) {
					$class::updateAll(['ordering' => $this->ordering], $primaryKey);
				}
			}

			$transaction->commit();
		} catch (\Throwable $e) {
			if ($transaction->isActive) {
				$transaction->rollBack();
			}
			throw $e;
		}
	}

	public function setMaxOrdering($class,$condition)
	{
		$this->ordering = $this->getLastOrdering($class,$condition);
	}

	public function setMinOrder()
	{
		$this->ordering = 1;
	}

	public function getLastOrdering($class,$condition)
	{
		return $class::find()->where($condition)->max('ordering');
	}

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

		return $form->field($this, 'ordering')->widget(Select2::class, [
			'data' => $orderingSelect,
			'options' => $options,
			'addon' => [
				'prepend' => ['content'=>'<i class="fa fa-sort"></i>']
			],
		]);
	}

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
