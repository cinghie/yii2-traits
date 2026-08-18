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
 * Trait ModifiedTrait
 *
 * @property string $modified
 * @property int $modified_by
 * @property User $modifiedBy
 */
trait ModifiedTrait
{
    public static function rules()
    {
        return [
            [['modified'], 'safe'],
            [['modified_by'], 'integer'],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modified_by' => 'id']],
        ];
    }

    public function getModifiedRules()
    {
        return [
            [['modified'], 'safe'],
            [['modified_by'], 'integer'],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['modified_by' => 'id']],
        ];
    }

    public static function attributeLabels()
    {
        return [
            'modified' => Yii::t('traits', 'Modified'),
            'modified_by' => Yii::t('traits', 'Modified By'),
        ];
    }

    public function getModifiedAttributeLabels()
    {
        return [
            'modified' => Yii::t('traits', 'Modified'),
            'modified_by' => Yii::t('traits', 'Modified By'),
        ];
    }

    public function getModifiedBy()
    {
        /** @var $this ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'modified_by'])->from(User::tableName() . ' AS modifiedBy');
    }

    public function getModifiedWidget($form)
    {
        $modified = $this->isNewRecord ? null : $this->modified;

        /** @var $this Model */
        return $form->field($this, 'modified')->widget(DateTimePicker::class, [
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

    public function getModifiedDetailView()
    {
        return ['attribute' => 'modified'];
    }

    public function getModifiedByWidget($form)
    {
        $modifiedBy = $this->modifiedBy;
        $data = $this->modified_by && $modifiedBy ? [$this->modified_by => $modifiedBy->username] : [];

        /** @var $this Model */
        return $form->field($this, 'modified_by')->widget(Select2::class, [
            'data' => $data,
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-user"></i>'
                ]
            ],
        ]);
    }

    public function getModifiedByGridView()
    {
        $modifiedBy = $this->modifiedBy;
        if ($this->modified_by && $modifiedBy) {
            $url = urldecode(Url::toRoute(['/user/profile/show', 'id' => $this->modified_by]));
            return Html::a($modifiedBy->username, $url);
        }

        return Yii::t('traits', 'Nobody');
    }

    public function getModifiedByDetailView()
    {
        $modifiedBy = $this->modifiedBy;

        return [
            'attribute' => 'modified_by',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->modified_by && $modifiedBy
                ? Html::a($modifiedBy->username, urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->modified_by])))
                : Yii::t('traits', 'Nobody'),
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }

    public function isCurrentUserModifier()
    {
        /** @var User|null $currentUser */
        $currentUser = Yii::$app->user->identity;

        return $currentUser !== null && $currentUser->id === $this->modified_by;
    }

    public function isUserModifier($user_id)
    {
        return $user_id === $this->modified_by;
    }
}
