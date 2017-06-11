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
use yii\helpers\Url;

/**
 * Trait UserTrait
 *
 * @package cinghie\traits
 * @property int $user_id
 * @property User user
 */
trait UserTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => \Yii::t('traits', 'User Id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->from(User::tableName() . ' AS user');
    }

    /**
     * Generate User Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getUserWidget($form)
    {
        $value = $this->isNewRecord && !$this->user_id ? \Yii::t('traits', 'Nobody') : $this->user->username;

        return $form->field($this, 'user_id')->textInput([
            'disabled' => true,
            'value' => $value
        ]);
    }

    /**
     * Generate GridView for User
     *
     * @return string
     */
    public function getUserGridView()
    {
        if (isset($this->user->id)) {
            $url = urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->user_id]));
            return Html::a($this->user->username,$url);
        } else {
            return '<span class="fa fa-ban text-danger"></span>';
        }
    }

    /**
     * Generate DetailView for User
     *
     * @return array
     */
    public function getUserDetailView()
    {
        return [
            'attribute' => 'user_id',
            'format' => 'html',
            'value' => $this->user_id ? Html::a($this->user->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->user_id]))) : \Yii::t('traits', 'Nobody'),
            'type' => DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

}
