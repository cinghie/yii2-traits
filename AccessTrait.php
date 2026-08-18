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
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Trait AccessTrait
 *
 * @property int $access
 */
trait AccessTrait
{
    /**
     * Trait-specific validation rules to be merged by the consuming Yii model.
     *
     * @return array
     */
    public function getAccessRules()
    {
        return [
            [['access'], 'string', 'max' => 64],
        ];
    }

    /**
     * Trait-specific attribute labels to be merged by the consuming Yii model.
     *
     * @return array
     */
    public function getAccessAttributeLabels()
    {
        return [
            'access' => Yii::t('traits', 'Access'),
        ];
    }

	/**
	 * Generate Access Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws Exception
	 */
    public function getAccessWidget($form)
    {
        /** @var Model $this */
        return $form->field($this, 'access')->widget(Select2::class, [
            'data' => $this->getRolesSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-sign-in fas fa-sign-in-alt"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate GridView Access
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getAccessGridView()
    {
        $url = urldecode(Url::toRoute(['/rbac/role/update', 'name' => $this->access]));

        return Html::a($this->access,$url);
    }

    /**
     * Generate DetailView Access
     *
     * @return array
     * @throws InvalidParamException
     */
    public function getAccessDetailView()
    {
        return [
            'attribute' => 'access',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->access ? Html::a($this->access,urldecode(Url::toRoute(['/rbac/role/update', 'name' => $this->access]))) : Yii::t('traits', 'Nobody'),
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }
}
