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
use kartik\widgets\Select2;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Trait UserTrait
 *
 * @property int $user_id
 * @property User user
 */
trait UserTrait
{
    /** Validation rules contributed by this trait. */
    public function getUserRules()
    {
        return [
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getUserAttributeLabels()
    {
        return [
            'user_id' => Yii::t('traits', 'User ID'),
        ];
    }

    /** Return the related user query. */
    public function getUser()
    {
        /** @var $this ActiveRecord */
        return $this->hasOne(User::class, ['id' => 'user_id'])->from(User::tableName() . ' AS user');
    }

    /** Render the user selector. */
	public function getUserWidget($form,$disabled = false)
    {
        if($disabled)
        {
            $value = !$this->user_id ? [0 => Yii::t('traits', 'Nobody')] : [$this->user_id => $this->user->username];

	        /** @var Model $this */
	        return $form->field($this, 'user_id')->widget(Select2::class, [
		        'disabled' => true,
		        'data' => $value,
		        'addon' => [
			        'prepend' => [
				        'content'=>'<i class="fa fa-user"></i>'
			        ]
		        ],
	        ]);
        }

	    /** @var $this Model | UserHelpersTrait */
	    return $form->field($this, 'user_id')->widget(Select2::class, [
		    'data' => $this->getUsersSelect2(),
		    'addon' => [
			    'prepend' => [
				    'content'=>'<i class="fa fa-user"></i>'
			    ]
		    ],
	    ]);
    }

    /** Render the user value for grid views. */
    public function getUserGridView()
    {
        if (isset($this->user->id)) {
            $url = urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->user_id]));
            return Html::a($this->user->username,$url);
        }

	    return '<span class="fa fa-ban text-danger"></span>';
    }

    /** Return the user configuration for DetailView. */
    public function getUserDetailView()
    {
        return [
            'attribute' => 'user_id',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->user_id ? Html::a($this->user->username,urldecode(Url::toRoute(['/user/admin/update', 'id' => $this->user_id]))) : Yii::t('traits', 'Nobody'),
            'valueColOptions'=> [
                'style'=>'width:30%'
            ]
        ];
    }
}
