<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 0.1.0
 */

namespace cinghie\traits;

use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use Yii;
use yii\helpers\Url;

/**
 * Trait AccessTrait
 *
 * @property integer $access
 */
trait AccessTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return  [
            [['access'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access' => Yii::t('traits', 'Access'),
        ];
    }

    /**
     * Generate Access Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getAccessWidget($form)
    {
        /** @var \yii\base\Model $this */

        return $form->field($this, 'access')->widget(Select2::classname(), [
            'data' => $this->getRolesSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-log-in"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate GridView Access
     *
     * @return string
     */
    public function getAccessGridView()
    {
        $url = urldecode(Url::toRoute(['/rbac/role/update', 'name' => $this->access]));;

        return Html::a($this->access,$url);
    }

    /**
     * Generate DetailView Access
     *
     * @return array
     */
    public function getAccessDetailView()
    {
        return [
            'attribute' => 'access',
            'format' => 'html',
            'value' => $this->access ? Html::a($this->access,urldecode(Url::toRoute(['/rbac/role/update', 'name' => $this->access]))) : Yii::t('traits', 'Nobody'),
            'type' => DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

}
