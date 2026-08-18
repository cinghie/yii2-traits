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
use kartik\detail\DetailView;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\base\Model;
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
	public static function rules()
	{
		return [
			[['parent_id'], 'integer'],
			[['parent_id'], 'exist', 'skipOnEmpty' => true, 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
			[['parent_id'], 'validateParentHierarchy'],
		];
	}

	public function getParentRules()
	{
		return [
			[['parent_id'], 'integer'],
			[['parent_id'], 'exist', 'skipOnEmpty' => true, 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
			[['parent_id'], 'validateParentHierarchy'],
		];
	}

	public static function attributeLabels()
	{
		return [
			'parent_id' => Yii::t('traits', 'Parent'),
		];
	}

	public function getParentAttributeLabels()
	{
		return [
			'parent_id' => Yii::t('traits', 'Parent'),
		];
	}

	/**
	 * Prevent self-parenting and cycles in the ancestor chain.
	 *
	 * @param string $attribute
	 */
	public function validateParentHierarchy($attribute)
	{
		$parentId = $this->$attribute;
		if ($parentId === null || $parentId === '' || (int)$parentId === 0) {
			return;
		}

		$currentId = isset($this->id) ? (int)$this->id : 0;
		if ($currentId && (int)$parentId === $currentId) {
			$this->addError($attribute, Yii::t('traits', 'An item cannot be its own parent.'));
			return;
		}

		$modelClass = static::class;
		$visited = [];
		$ancestorId = (int)$parentId;

		while ($ancestorId > 0) {
			if (isset($visited[$ancestorId])) {
				$this->addError($attribute, Yii::t('traits', 'The selected parent creates a hierarchy cycle.'));
				return;
			}
			$visited[$ancestorId] = true;

			if ($currentId && $ancestorId === $currentId) {
				$this->addError($attribute, Yii::t('traits', 'The selected parent creates a hierarchy cycle.'));
				return;
			}

			$ancestor = $modelClass::find()->select(['id', 'parent_id'])->where(['id' => $ancestorId])->one();
			if ($ancestor === null) {
				return;
			}

			$ancestorId = (int)$ancestor->parent_id;
		}
	}

	public function getParent()
	{
		return $this->hasOne(static::class, ['id' => 'parent_id'])->from(static::tableName() . ' AS parent');
	}

	public function getParents()
	{
		return $this->hasMany(static::class, ['id' => 'parent_id'])->from(static::tableName() . ' AS parent');
	}

	public function getChild()
	{
		return $this->hasOne(static::class, ['parent_id' => 'id'])->from(static::tableName() . ' AS child');
	}

	public function getChilds()
	{
		return $this->hasMany(static::class, ['parent_id' => 'id'])->from(static::tableName() . ' AS child');
	}

	public function getParentWidget($form,$items)
	{
		/** @var $this Model */
		return $form->field($this, 'parent_id')->widget(Select2::class, [
			'data' => $items,
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fa fa-folder-open"></i>'
				]
			],
		]);
	}

	public function getParentGridView($field,$url,$hideItem = false)
	{
		/** @var $this Model */
		$parent = $this->parent;
		if ($parent && !$hideItem) {
			$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
			return Html::a($parent->$field,$url);
		}

		if ($hideItem !== null && $hideItem)
		{
			if($this->parent_id === $hideItem || !$parent) {
				return '<span class="fa fa-ban text-danger"></span>';
			}

			$url = urldecode(Url::toRoute([$url, 'id' => $this->parent_id]));
			return Html::a($parent->$field,$url);
		}

		return '<span class="fa fa-ban text-danger"></span>';
	}

    public function getParentDetailView()
    {
        return [];
    }
}
