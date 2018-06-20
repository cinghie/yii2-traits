<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.0
 */

namespace cinghie\traits;

use Yii;
use dektrium\user\models\User;
use kartik\detail\DetailView;
use kartik\form\ActiveField;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * Trait CreatedTrait
 *
 * @property string $created
 * @property int $created_by
 * @property User $createdBy
 */
trait CreatedTrait
{

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['created'], 'safe'],
            [['created_by'], 'int'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'created' => Yii::t('traits', 'Created'),
            'created_by' => Yii::t('traits', 'Created By'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        /** @var $this \yii\db\ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'created_by'])->from(User::tableName() . ' AS createdBy');
    }

    /**
     * Generate Created Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
     */
    public function getCreatedWidget($form)
    {
        $created = $this->isNewRecord ? date('Y-m-d H:i:s') : $this->created;

	    /** @var $this \yii\base\Model */
	    return $form->field($this, 'created')->widget(DateTimePicker::class, [
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
     * Generate DetailView for Created
     *
     * @return array
     */
    public function getCreatedDetailView()
    {
        return ['attribute' => 'created'];
    }

    /**
     * Generate CreatedBy Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
     */
    public function getCreatedByWidget($form)
    {
        $created_by = $this->isNewRecord ? $this->getCurrentUserSelect2() : [$this->created_by => $this->createdBy->username];

	    /** @var $this \yii\base\Model */
        return $form->field($this, 'created_by')->widget(Select2::class, [
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
     * @throws InvalidParamException
     */
    public function getCreatedByGridView()
    {
        $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->created_by]));
        $createdBy = isset($this->createdBy->username) ? $this->createdBy->username : '';

        if($this->created_by) {
            return Html::a($createdBy,$url);
        }

	    return Yii::t('traits', 'Nobody');
    }

    /**
     * Generate DetailView for CreatedBy
     *
     * @return array
     * @throws InvalidParamException
     */
    public function getCreatedByDetailView()
    {
        return [
            'attribute' => 'created_by',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->created_by ? Html::a($this->createdBy->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->createdBy]))) : Yii::t('traits', 'Nobody'),
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

    /**
     * Check if current user is the created_by
     *
     * @return bool
     */
    public function isCurrentUserCreator()
    {
        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

	    return $currentUser->id === $this->created_by;
    }

    /**
     * Check if user_id params is the created_by
     *
     * @param User $user_id
     *
     * @return bool
     */
    public function isUserCreator($user_id)
    {
	    return $user_id === $this->created_by;
    }

}
