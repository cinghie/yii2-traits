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
 * @property User $modifiedBy
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
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => [ 'created_by' => 'id' ] ]
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
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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

}
