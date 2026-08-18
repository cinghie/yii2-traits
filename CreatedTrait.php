<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.3
 */

namespace cinghie\traits;

use Exception;
use Yii;
use dektrium\user\models\User;
use kartik\detail\DetailView;
use kartik\form\ActiveField;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
    public static function rules()
    {
        return [
            [['created'], 'safe'],
            [['created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function getCreatedRules()
    {
        return [
            [['created'], 'safe'],
            [['created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public static function attributeLabels()
    {
        return [
            'created' => Yii::t('traits', 'Created'),
            'created_by' => Yii::t('traits', 'Created By'),
        ];
    }

    public function getCreatedAttributeLabels()
    {
        return [
            'created' => Yii::t('traits', 'Created'),
            'created_by' => Yii::t('traits', 'Created By'),
        ];
    }

    public function getCreatedBy()
    {
        /** @var $this ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'created_by'])->from(User::tableName() . ' AS createdBy');
    }

    public function getCreatedWidget($form)
    {
        $created = $this->isNewRecord ? date('Y-m-d H:i:s') : $this->created;

        /** @var $this Model */
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

    public function getCreatedDetailView()
    {
        return ['attribute' => 'created'];
    }

    public function getCreatedByWidget($form)
    {
        if ($this->isNewRecord) {
            $createdBy = $this->getCurrentUserSelect2();
        } elseif ($this->created_by && $this->createdBy) {
            $createdBy = [$this->created_by => $this->createdBy->username];
        } else {
            $createdBy = [];
        }

        /** @var $this Model */
        return $form->field($this, 'created_by')->widget(Select2::class, [
            'data' => $createdBy,
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-user"></i>'
                ]
            ]
        ]);
    }

    public function getCreatedByGridView()
    {
        $createdBy = $this->createdBy;
        if ($this->created_by && $createdBy) {
            $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->created_by]));
            return Html::a($createdBy->username, $url);
        }

        return Yii::t('traits', 'Nobody');
    }

    public function getCreatedByDetailView()
    {
        $createdBy = $this->createdBy;

        return [
            'attribute' => 'created_by',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->created_by && $createdBy
                ? Html::a($createdBy->username, urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->created_by])))
                : Yii::t('traits', 'Nobody'),
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

    public function isCurrentUserCreator()
    {
        /** @var User|null $currentUser */
        $currentUser = Yii::$app->user->identity;

        return $currentUser !== null && $currentUser->id === $this->created_by;
    }

    public function isUserCreator($user_id)
    {
        return $user_id === $this->created_by;
    }
}
