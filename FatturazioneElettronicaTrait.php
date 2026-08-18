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

use Yii;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Trait FatturazioneElettronicaTrait
 *
 * @property string $pec
 * @property string $sdi
 */
trait FatturazioneElettronicaTrait
{
	/** Validation rules contributed by this trait. */
	public function getFatturazioneElettronicaRules()
	{
		return [
			[['sdi'], 'string', 'max' => 7],
			[['pec'], 'string', 'max' => 100],
		];
	}

	/** Attribute labels contributed by this trait. */
	public function getFatturazioneElettronicaAttributeLabels()
	{
		return [
			'pec' => Yii::t('traits', 'PEC'),
			'sdi' => Yii::t('traits', 'SDI'),
		];
	}

	/**
	 * Get PEC Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getPecWidget($form)
	{
		/** @var $this Model */
		return $form->field($this, 'pec', [
			'addon' => [
				'prepend' => [
					'content'=>'<i class="fa fa-envelope-open-text"></i>'
				]
			]
		])->textInput(['maxlength' => true]);
	}

	/**
	 * Get SDI Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
	public function getSdiWidget($form)
	{
		/** @var $this Model */
		return $form->field($this, 'sdi', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-file-invoice-dollar"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
	}
}
