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
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use kartik\widgets\Select2;
use yii\base\Model;

/**
 * Trait StateTrait
 *
 * @property int $state
 */
trait StateTrait
{
    /** Return the event name emitted after a state change. */
    protected function stateChangeEventName(): string
    {
        return 'cinghie.traits.afterStateChange';
    }

    /** Validation rules contributed by this trait. */
    public function getStateRules()
    {
        return [
            [['state'], 'integer']
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getStateAttributeLabels()
    {
        return [
            'state' => Yii::t('traits', 'State'),
        ];
    }

    /** Activate the current record. */
    public function active()
    {
        $ok = (bool)$this->updateAttributes([
            'state' => 1
        ]);
        if ($ok) {
            $this->triggerStateChangeEvent();
        }

        return $ok;
    }

    /** Deactivate the current record. */
    public function deactive()
    {
        $ok = (bool)$this->updateAttributes([
            'state' => 0
        ]);
        if ($ok) {
            $this->triggerStateChangeEvent();
        }

        return $ok;
    }

    /** Trigger the state-change event when the host supports events. */
    protected function triggerStateChangeEvent(): void
    {
        if (!$this instanceof \yii\base\Component) {
            return;
        }
        $this->trigger($this->stateChangeEventName());
    }

    /** Render the state selector. */
    public function getStateWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'state')->widget(Select2::class, [
            'data' => static::getStateSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-check"></i>'
                ]
            ],
        ]);
    }

    /** Render the state value for grid views. */
    public function getStateGridView($removeLink = false)
    {
        if($this->state)
        {
        	if($removeLink) {
        		return '<span class="fa fa-check text-success"></span>';
	        }

            return Html::a(
                '<span class="fa fa-check text-success"></span>',
                ['changestate', 'id' => $this->id],
                ['data-method' => 'post']
            );
        }

	    if($removeLink) {
		    return '<span class="fa fa-times text-danger"></span>';
	    }

	    return Html::a(
		    '<span class="fa fa-times text-danger"></span>',
		    ['changestate', 'id' => $this->id],
		    ['data-method' => 'post']
	    );
    }

    /** Return the state configuration for DetailView. */
    public function getStateDetailView()
    {
        return [
            'attribute' => 'state',
            'format' => 'html',
            'type' => DetailView::INPUT_SWITCH,
            'value' => $this->state ? '<span class="label label-success">'. Yii::t('traits', 'Actived').'</span>' : '<span class="label label-danger">'. Yii::t('traits', 'Deactivated').'</span>',
            'valueColOptions'=> [
	            'style' => 'width:30%'
            ],
            'widgetOptions' => [
                'pluginOptions' => [
                    'onText' => 'Yes',
                    'offText' => 'No',
                ]
            ]
        ];
    }

    /** Return state options for selectors. */
    public static function getStateSelect2()
    {
        return [
            '1' => Yii::t('traits', 'Actived'),
            '0' => Yii::t('traits', 'Inactived')
        ];
    }
}
