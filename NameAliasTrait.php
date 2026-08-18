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
    /** Validation rules contributed by this trait. */
    public function getNameAliasRules()
    {
        return [
            [['alias'], 'unique'],
            [['alias','name'], 'string', 'max' => 255],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getNameAliasAttributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'name' => Yii::t('traits', 'Name'),
        ];
    }

    /** Generate an alias from a name. */
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

    /** Fill the alias when it is missing from submitted data. */
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

		if (!array_key_exists('alias', $post) || (string) $post['alias'] === '') {
			$this->alias = $slugify->slugify($post[$field] ?? '');
		}
	}

    /** Normalize a string into a basic alias. */
	public function purgeAlias($string)
	{
		$string = str_replace(array('-','_'), ' ', $string);
		$string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);
		return strtolower(trim($string));
	}

    /** Render the name field. */
    public function getNameWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'name',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-pencil fas fa-pencil-alt"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    /** Render the alias field. */
    public function getAliasWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'alias', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-bookmark"></i>'
                ]
            ]
        ] )->textInput(['maxlength' => true]);
    }
}
