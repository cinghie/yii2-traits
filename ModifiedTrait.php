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
        return $this->hasOne(User::className(), ['id' => 'modified_by'])->from(User::tableName() . ' AS user');
    }

    /**
     * check if current user is the modified_by
     * @return bool
     */
    public function isCurrentUserModifier()
    {
        if ( \Yii::$app->user->identity->id == $this->modified_by ) {
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
        if ( $user_id == $this->modified_by ) {
            return true;
        } else {
            return false;
        }
    }

}
