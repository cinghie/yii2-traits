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

    /**
     * Generate Access Form Widget
     *
     * @return \kartik\widgets\Select2 widget
     */
    public function getAccessWidget($form,$model)
    {
        return $form->field($model, 'access')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $model->getRolesSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-log-in"></i>'
                ]
            ],
        ]);
    }

}
