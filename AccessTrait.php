<?php

namespace cinghie\traits;

use dektrium\rbac\models\Assignment;
use dektrium\rbac\models\AuthItem;

/**
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
            'access' => \Yii::t('traits', 'Access'),
        ];
    }

}
