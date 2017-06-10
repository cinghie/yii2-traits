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
use kartik\widgets\DateTimePicker;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

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
        if (\Yii::$app->user->identity->id == $this->created_by) {
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
        if ($user_id == $this->created_by) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate Created Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getCreatedWidget($form)
    {
        $created = $this->isNewRecord ? date("Y-m-d H:i:s") : $this->created;

        return $form->field($this, 'created')->widget(DateTimePicker::classname(), [
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
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getCreatedByWidget($form)
    {
        $created_by = $this->isNewRecord ? $this->getCurrentUserSelect2() : [$this->created_by => $this->createdBy->username];

        return $form->field($this, 'created_by')->widget(Select2::classname(), [
            'data' => $created_by,
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-user"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate GridView for CreatedBy
     *
     * @return string
     */
    public function getCreatedGridView()
    {
        $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->created_by]));
        $createdBy = isset($this->createdBy->username) ? $this->createdBy->username : "";

        if($this->created_by) {
            return Html::a($createdBy,$url);
        } else {
            return \Yii::t('traits', 'Nobody');
        }
    }

    /**
     * Generate DetailView for Created
     *
     * @return array
     */
    public function getCreatedDetailView()
    {
        return ['attribute' => 'created'];
    }

    /**
     * Generate DetailView for CreatedBy
     *
     * @return array
     */
    public function getCreatedByDetailView()
    {
        return [
            'attribute' => 'created_by',
            'format' => 'html',
            'value' => $this->created_by ? Html::a($this->createdBy->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->createdBy]))) : \Yii::t('traits', 'Nobody'),
            'type' => DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

}
