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
 * Trait TitleAliasTrait
 *
 * @property string $alias
 * @property string $title
 */
trait TitleAliasTrait
{
    public function getTitleAliasRules()
    {
        return [
            [['alias'], 'unique'],
            [['alias','title'], 'string', 'max' => 255],
        ];
    }

    public function getTitleAliasAttributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'title' => Yii::t('traits', 'Title'),
        ];
    }

	public function generateAlias($title)
	{
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?? [
            'separator' => '-',
            'lowercase' => true,
            'trim'      => true,
            'rulesets'  => [
                'default'
            ]
        ];

		$slugify = new Slugify($slugifyOptions);
		return $slugify->slugify($title);
	}

	public function setAlias($post,$field)
	{
		$slugifyOptions = Yii::$app->controller->module->slugifyOptions ?? [
            'separator' => '-',
            'lowercase' => true,
            'trim'      => true,
            'rulesets'  => [
                'default'
            ]
        ];

		$slugify = new Slugify($slugifyOptions);

		if (!array_key_exists('alias', $post) || (string) $post['alias'] === '') {
			$this->alias = $slugify->slugify($post[$field] ?? '');
		}
	}

	public function purgeAlias($string)
	{
		$string = str_replace(array('-','_'), ' ', $string);
		$string = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $string);
		return strtolower(trim($string));
	}

    public function getTitleWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'title', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-pencil fas fa-pencil-alt"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

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
