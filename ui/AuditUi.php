<?php

namespace cinghie\traits\ui;

use Yii;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\Url;

/**
 * Presentation helpers shared by CreatedTrait and ModifiedTrait.
 */
final class AuditUi
{
    public static function dateTimeWidget($model, $form, $attribute, $value)
    {
        return $form->field($model, $attribute)->widget(DateTimePicker::class, [
            'options' => ['value' => $value],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayHighlight' => true,
            ],
        ]);
    }

    public static function userWidget($model, $form, $attribute, array $data)
    {
        return $form->field($model, $attribute)->widget(Select2::class, [
            'data' => $data,
            'addon' => [
                'prepend' => ['content' => '<i class="fa fa-user"></i>'],
            ],
        ]);
    }

    public static function userGridValue($userId, $user)
    {
        if ($userId && $user) {
            $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $userId]));
            return Html::a($user->username, $url);
        }

        return Yii::t('traits', 'Nobody');
    }

    public static function userDetailView($attribute, $userId, $user)
    {
        return [
            'attribute' => $attribute,
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $userId && $user
                ? Html::a($user->username, urldecode(Url::toRoute(['/user/admin/update', 'id' => $userId])))
                : Yii::t('traits', 'Nobody'),
            'valueColOptions' => [
                'style' => 'width:30%',
            ],
        ];
    }
}
