<?php

namespace cinghie\traits;

use Yii;
use cinghie\traits\ui\AuditUi;
use dektrium\user\models\User;
use yii\db\ActiveRecord;

/**
 * Trait CreatedTrait
 *
 * Model rules, relations and authorization checks remain here; presentation
 * is delegated to ui/AuditUi.
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
        return static::rules();
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
        return static::attributeLabels();
    }

    public function getCreatedBy()
    {
        /** @var $this ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'created_by'])->from(User::tableName() . ' AS createdBy');
    }

    public function getCreatedWidget($form)
    {
        $created = $this->isNewRecord ? date('Y-m-d H:i:s') : $this->created;
        return AuditUi::dateTimeWidget($this, $form, 'created', $created);
    }

    public function getCreatedDetailView()
    {
        return ['attribute' => 'created'];
    }

    public function getCreatedByWidget($form)
    {
        if ($this->isNewRecord) {
            $data = $this->getCurrentUserSelect2();
        } elseif ($this->created_by && $this->createdBy) {
            $data = [$this->created_by => $this->createdBy->username];
        } else {
            $data = [];
        }

        return AuditUi::userWidget($this, $form, 'created_by', $data);
    }

    public function getCreatedByGridView()
    {
        return AuditUi::userGridValue($this->created_by, $this->createdBy);
    }

    public function getCreatedByDetailView()
    {
        return AuditUi::userDetailView('created_by', $this->created_by, $this->createdBy);
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
