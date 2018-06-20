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
use Cocur\Slugify\Slugify;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;

/**
 * Trait TitleAliasTrait
 *
 * @property string $alias
 * @property string $title
 */
trait TitleAliasTrait
{

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['alias'], 'unique'],
            [['alias','title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'title' => Yii::t('traits', 'Title'),
        ];
    }

	/**
	 * Generate alias from title
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function generateAlias($title)
	{
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?: [
			'separator' => '-',
			'lowercase' => true,
			'trim' => true,
			'rulesets'  => [
				'default'
			]
		];

		$slugify = new Slugify($slugifyOptions);

		return $slugify->slugify($title);
	}

	/**
	 * Set alias from post
	 *
	 * @param [] $post
	 * @param string $field
	 *
	 * @return void
	 */
	public function setAlias($post,$field)
	{
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?: [
			'separator' => '-',
			'lowercase' => true,
			'trim' => true,
			'rulesets'  => [
				'default'
			]
		];

		$slugify = new Slugify($slugifyOptions);

		if($post['alias'] === '') {
			$this->alias = $slugify->slugify($post[$field]);
		}
	}

	/**
	 * Generate URL alias by string
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function purgeAlias($string)
	{
		// remove any '-' from the string they will be used as concatonater
		$string = str_replace(array('-','_'), ' ', $string);

		// remove any duplicate whitespace, and ensure all characters are alphanumeric
		$string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);

		// lowercase and trim
		$string = strtolower(trim($string));

		return $string;
	}

    /**
     * Generate Title Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
     */
    public function getTitleWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'title',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-pencil"></i>'
                ]
            ],
        ])->textInput(['maxlength' => true]);
    }

    /**
     * Generate Alias Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
     */
    public function getAliasWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'alias', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-bookmark"></i>'
                ]
            ]
        ] )->textInput(['maxlength' => 255]);
    }

}
