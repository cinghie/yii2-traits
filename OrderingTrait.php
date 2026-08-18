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
use yii\base\InvalidArgumentException;
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
	 * committed in one serializable transaction. `$lastOrdering` is retained for
	 * backwards compatibility but move-to-last now calculates the scoped maximum
	 * inside the transaction.
	 *
	 * @param Model|string $class
	 * @param string $fieldOrdering
	 * @param int $oldOrdering
	 * @param int $lastOrdering Deprecated input retained for API compatibility.
	 */
	public function setOrdering($class, $fieldOrdering, $oldOrdering, $lastOrdering)
	{
		$className = is_object($class) ? get_class($class) : $class;
		if (!is_string($className) || !is_a($className, ActiveRecord::class, true)) {
			throw new InvalidArgumentException('Ordering class must be an ActiveRecord class.');
		}
		if ($this instanceof ActiveRecord && !is_a($this, $className)) {
			throw new InvalidArgumentException('Ordering class must be compatible with the current ActiveRecord.');
		}

		$newOrdering = (int)$this->ordering;
		$oldOrdering = (int)$oldOrdering;

		if ($newOrdering === $oldOrdering) {
			return;
		}

		$db = $className::getDb();
		$transaction = $db->beginTransaction(Transaction::SERIALIZABLE);

		try {
			if ($newOrdering === 0 && $oldOrdering === 1) {
				$this->ordering = 1;
			} elseif ($newOrdering === 0) {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<', 'ordering', $oldOrdering],
				];

				$className::updateAll(['ordering' => new Expression('ordering + 1')], $condition);
				$this->setMinOrder();
			} elseif ($newOrdering === 999999999) {
				$scope = [$fieldOrdering => $this->$fieldOrdering];
				$scopedMaximum = (int)$className::find()->where($scope)->max('ordering');

				$condition = ['and',
					$scope,
					['>', 'ordering', $oldOrdering],
				];

				$className::updateAll(['ordering' => new Expression('ordering - 1')], $condition);
				$this->ordering = max(1, $scopedMaximum);
			} elseif ($newOrdering > $oldOrdering) {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['>', 'ordering', $oldOrdering],
					['<=', 'ordering', $newOrdering],
				];

				$className::updateAll(['ordering' => new Expression('ordering - 1')], $condition);
				$this->ordering = $newOrdering;
			} else {
				$condition = ['and',
					[$fieldOrdering => $this->$fieldOrdering],
					['<', 'ordering', $oldOrdering],
					['>=', 'ordering', $newOrdering],
				];

				$className::updateAll(['ordering' => new Expression('ordering + 1')], $condition);
				$this->ordering = max(1, $newOrdering);
			}

			if ($this instanceof ActiveRecord && !$this->isNewRecord) {
				$primaryKey = $this->getPrimaryKey(true);
				if ($primaryKey) {
					$className::updateAll(['ordering' => $this->ordering], $primaryKey);
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

	public function setMaxOrdering($class, $condition)
	{
		$this->ordering = $this->getLastOrdering($class, $condition);
	}

	public function setMinOrder()
	{
		$this->ordering = 1;
	}

	public function getLastOrdering($class, $condition)
	{
		return $class::find()->where($condition)->max('ordering');
	}

	public function getOrderingWidget($form, $class, $orderingField, $selectField, $condition)
	{
		if ($this->isNewRecord) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [-1 => Yii::t('traits', 'Save to order')];
		} elseif (!$this->isNewRecord && !$this->$orderingField) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = [-1 => Yii::t('traits', 'Select a category to order')];
		} else {
			$options = [];
			$orderingSelect = $this->getOrderingSelect2($class, $orderingField, $selectField, $condition);
		}

		return $form->field($this, 'ordering')->widget(Select2::class, [
			'data' => $orderingSelect,
			'options' => $options,
			'addon' => [
				'prepend' => ['content' => '<i class="fa fa-sort"></i>'],
			],
		]);
	}

	public function getOrderingSelect2($class, $orderingField = '', array $selectField = [], array $condition = [])
	{
		$array = [0 => Yii::t('traits', 'First Element')];
		$items = $class::find()->select($selectField)->where($condition)->orderby('ordering ASC')->all();

		if (count($items) === 1) {
			return $array;
		}

		foreach ($items as $item) {
			$array[$item[$selectField[0]]] = $item[$selectField[1]];
		}

		$array[999999999] = Yii::t('traits', 'Last Element');
		return $array;
	}
}
