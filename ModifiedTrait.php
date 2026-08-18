<?php

namespace cinghie\traits;

use Yii;
use cinghie\traits\ui\AuditUi;
use dektrium\user\models\User;
use yii\db\ActiveRecord;

/**
 * Trait ModifiedTrait
 *
 * Model rules, relations and authorization checks remain here; presentation
 * is delegated to ui/AuditUi.
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
        return static::rules();
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
        return static::attributeLabels();
    }

    public function getModifiedBy()
    {
        /** @var $this ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'modified_by'])->from(User::tableName() . ' AS modifiedBy');
    }

    public function getModifiedWidget($form)
    {
        $modified = $this->isNewRecord ? null : $this->modified;
        return AuditUi::dateTimeWidget($this, $form, 'modified', $modified);
    }

    public function getModifiedDetailView()
    {
        return ['attribute' => 'modified'];
    }

    public function getModifiedByWidget($form)
    {
        $modifiedBy = $this->modifiedBy;
        $data = $this->modified_by && $modifiedBy ? [$this->modified_by => $modifiedBy->username] : [];
        return AuditUi::userWidget($this, $form, 'modified_by', $data);
    }

    public function getModifiedByGridView()
    {
        return AuditUi::userGridValue($this->modified_by, $this->modifiedBy);
    }

    public function getModifiedByDetailView()
    {
        return AuditUi::userDetailView('modified_by', $this->modified_by, $this->modifiedBy);
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
