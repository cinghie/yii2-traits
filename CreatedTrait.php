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

use dektrium\user\models\User;

/*
 * @property string $created
 * @property int $created_by
 *
 * @property User $createdBy
 */
trait CreatedTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'safe'],
            [['created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'created' => \Yii::t('traits', 'Created'),
            'created_by' => \Yii::t('traits', 'Created By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by'])->from(User::tableName() . ' AS createdBy');
    }

    /**
     * Check if current user is the created_by
     *
     * @return bool
     */
    public function isCurrentUserCreator()
    {
        if ( \Yii::$app->user->identity->id == $this->created_by ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user_id params is the created_by
     *
     * @param User $user_id
     * @return bool
     */
    public function isUserCreator($user_id)
    {
        if ( $user_id == $this->created_by ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate Created Form Widget
     *
     * @return \kartik\widgets\DateTimePicker widget
     */
    public function getCreatedWidget($form,$model)
    {
        $created = $model->isNewRecord ? date("Y-m-d H:i:s") : $model->created;

        return $form->field($model, 'created')->widget(\kartik\widgets\DateTimePicker::classname(), [
            'options' => [
                'value' => $created,
            ],
            'pluginOptions' => [
                'autoclose'      => true,
                'format'         => 'yyyy-mm-dd hh:ii:ss',
                'todayHighlight' => true,
            ]
        ]);
    }

    /**
     * Generate CreatedBy Form Widget
     *
     * @return \kartik\widgets\Select2 widget
     */
    public function getCreatedByWidget($form,$model)
    {
        $created_by = $model->isNewRecord ? $model->getCurrentUserSelect2() : [$model->created_by => $model->createdBy->username];

        return $form->field($model, 'created_by')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $created_by,
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-user"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate DetailView for Created View
     *
     * @return array
     */
    public function getCreatedDetailView($model)
    {
        return ['attribute' => 'created'];
    }

    /**
     * Generate DetailView for CreatedBy View
     *
     * @return array
     */
    public function getCreatedByDetailView($model)
    {
        return [
            'attribute' => 'created_by',
            'format' => 'raw',
            'value' => $model->created_by ? \kartik\helpers\Html::a($model->createdBy->username,urldecode(\yii\helpers\Url::toRoute(['/user/admin/update', 'id' => $model->createdBy]))) : \Yii::t('traits', 'Nobody'),
            'type' => \kartik\detail\DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

}
