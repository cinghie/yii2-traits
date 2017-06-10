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
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

/*
 * @property string $modified
 * @property int $modified_by
 *
 * @property User $modifiedBy
 */
trait ModifiedTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modified'], 'safe'],
            [['modified_by'], 'integer'],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['modified_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modified' => \Yii::t('traits', 'Modified'),
            'modified_by' => \Yii::t('traits', 'Modified By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'modified_by'])->from(User::tableName() . ' AS modifiedBy');
    }

    /**
     * check if current user is the modified_by
     * @return bool
     */
    public function isCurrentUserModifier()
    {
        if (\Yii::$app->user->identity->id == $this->modified_by) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user_id params is the modified_by
     *
     * @param User $user_id
     * @return bool
     */
    public function isUserModifier($user_id)
    {
        if ($user_id == $this->modified_by) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate Modified Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getModifiedWidget($form)
    {
        $modified = $this->isNewRecord ? "0000-00-00 00:00:00" : ($this->modified ? $this->modified : "0000-00-00 00:00:00");

        return $form->field($this, 'modified')->widget(\kartik\widgets\DateTimePicker::classname(), [
            'disabled' => true,
            'options' => [
                'value' => $modified,
            ],
            'pluginOptions' => [
                'autoclose'      => true,
                'format'         => 'yyyy-mm-dd hh:ii:ss',
                'todayHighlight' => true,
            ]
        ]);
    }

    /**
     * Generate ModifiedBy Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getModifiedByWidget($form)
    {
        $modified_by = $this->isNewRecord ? NULL : [$this->modified_by => $this->modifiedBy->username];

        return $form->field($this, 'modified_by')->widget(Select2::classname(), [
            'data' => $modified_by,
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-user"></i>'
                ]
            ],
        ]);
    }

    /**
     * Generate GridView for ModifiedBy
     *
     * @return string
     */
    public function getModifiedGridView()
    {
        $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->modified_by]));
        $modifiedBy = isset($this->modifiedBy->username) ? $this->modifiedBy->username : "";

        if($this->modified_by) {
            return Html::a($modifiedBy,$url);
        } else {
            return \Yii::t('traits', 'Nobody');
        }
    }

    /**
     * Generate DetailView for Modified
     *
     * @return array
     */
    public function getModifiedDetailView()
    {
        return ['attribute' => 'modified'];
    }

    /**
     * Generate DetailView for ModifiedBy
     *
     * @return array
     */
    public function getModifiedByDetailView()
    {
        return [
            'attribute' => 'modified_by',
            'format' => 'html',
            'value' => $this->modified_by ? Html::a($this->modifiedBy->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->modifiedBy]))) : \Yii::t('traits', 'Nobody'),
            'type' => DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

}
