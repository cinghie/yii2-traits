<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.1.1
 */

namespace cinghie\traits;

use Yii;
use dektrium\user\models\User;
use kartik\widgets\DateTimePicker;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

/**
 * Trait ModifiedTrait
 *
 * @property string $modified
 * @property integer $modified_by
 * @property User $modifiedBy
 */
trait ModifiedTrait
{

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['modified'], 'safe'],
            [['modified_by'], 'integer'],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['modified_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'modified' => Yii::t('traits', 'Modified'),
            'modified_by' => Yii::t('traits', 'Modified By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        /** @var $this \yii\db\ActiveRecord */
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'modified_by'])->from(Yii::$app->user->identityClass::tableName() . ' AS modifiedBy');
    }

    /**
     * Generate Modified Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     *
     * @return \kartik\form\ActiveField
     */
    public function getModifiedWidget($form)
    {
        $modified = $this->isNewRecord ? date('Y-m-d H:i:s') : ($this->modified ? $this->modified : '0000-00-00 00:00:00');

	    return $form->field($this, 'modified')->widget(DateTimePicker::className(), [
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
     * Generate DetailView for Modified
     *
     * @return array
     */
    public function getModifiedDetailView()
    {
        return ['attribute' => 'modified'];
    }

    /**
     * Generate ModifiedBy Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     *
     * @return \kartik\form\ActiveField
     */
    public function getModifiedByWidget($form)
    {
        $modified_by = !$this->modified_by ? NULL : [$this->modified_by => $this->modifiedBy->username];

        return $form->field($this, 'modified_by')->widget(Select2::className(), [
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
     * @throws \yii\base\InvalidParamException
     */
    public function getModifiedByGridView()
    {
        $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->modified_by]));
        $modifiedBy = isset($this->modifiedBy->username) ? $this->modifiedBy->username : '';

        if($this->modified_by) {
            return Html::a($modifiedBy,$url);
        }

	    return Yii::t('traits', 'Nobody');
    }

    /**
     * Generate DetailView for ModifiedBy
     *
     * @return array
     * @throws \yii\base\InvalidParamException
     */
    public function getModifiedByDetailView()
    {
        return [
            'attribute' => 'modified_by',
            'format' => 'html',
            'value' => $this->modified_by ? Html::a($this->modifiedBy->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->modifiedBy]))) : Yii::t('traits', 'Nobody'),
            'type' => DetailView::INPUT_SWITCH,
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

    /**
     * Check if current user is the modified_by
     *
     * @return bool
     */
    public function isCurrentUserModifier()
    {
        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

	    return $currentUser->id === $this->modified_by;
    }

    /**
     * Check if user_id params is the modified_by
     *
     * @param User $user_id
     *
     * @return bool
     */
    public function isUserModifier($user_id)
    {
	    return $user_id === $this->modified_by;
    }

}
