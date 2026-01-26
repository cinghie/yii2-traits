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
use Cocur\Slugify\Slugify;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Trait NameAliasTrait
 *
 * @property string $alias
 * @property string $name
 */
trait NameAliasTrait
{
    /**
     * @inheritdoc
     * 
     * Note: In PHP 8.1+, calling this method statically (e.g., NameAliasTrait::rules())
     * may generate a deprecation warning. It's recommended to use getNameAliasRules() instance method instead.
     */
    public static function rules()
    {
        return [
            [['alias'], 'unique'],
            [['alias','name'], 'string', 'max' => 255],
        ];
    }

    /**
     * Instance method to get rules without deprecation warning
     * 
     * @return array
     */
    public function getNameAliasRules()
    {
        return [
            [['alias'], 'unique'],
            [['alias','name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     * 
     * Note: In PHP 8.1+, calling this method statically (e.g., NameAliasTrait::attributeLabels())
     * will generate a deprecation warning. It's recommended to call this as an instance method
     * in your model's attributeLabels() method instead.
     * 
     * @return array
     */
    public static function attributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'name' => Yii::t('traits', 'Name'),
        ];
    }

    /**
     * Instance method to get attribute labels without deprecation warning
     * Use this method in your model's attributeLabels() instead of calling the static method
     * 
     * @return array
     */
    public function getNameAliasAttributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'name' => Yii::t('traits', 'Name'),
        ];
    }

	/**
	 * Generate alias from name
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function generateAlias($name)
	{
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?? [
			'separator' => '-',
			'lowercase' => true,
			'trim' => true,
			'rulesets' => [
			    'default'
		    ]
		];

		$slugify = new Slugify($slugifyOptions);

		return $slugify->slugify($name);
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
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?? [
            'separator' => '-',
            'lowercase' => true,
            'trim' => true,
            'rulesets' => [
                'default'
            ]
        ];

		$slugify = new Slugify($slugifyOptions);

		if($post['alias'] === '') {
			$this->alias = $slugify->slugify($post[$field]);
		}
	}

	/**
	 * Purge alias by string
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
		return strtolower( trim( $string ) );
	}

	/**
	 * Generate Name Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
    public function getNameWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'name',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-pencil"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

	/**
	 * Generate alias Form Widget
	 *
	 * @param ActiveForm $form
	 *
	 * @return ActiveField
	 * @throws InvalidConfigException
	 */
    public function getAliasWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'alias', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-bookmark"></i>'
                ]
            ]
        ] )->textInput(['maxlength' => true]);
    }
}
