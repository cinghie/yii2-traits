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
	 * Generate Ordering Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 */
	public function getOrderingWidget($form)
	{
		if($this->isNewRecord) {
			$options = ['disabled' => 'disabled'];
			$orderingSelect = ['0' => Yii::t('traits', 'Save to order')];
		} else {
			$options = [];
			$orderingSelect = $this->getItemsByCategoriesSelect2($this->cat_id);
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

}
