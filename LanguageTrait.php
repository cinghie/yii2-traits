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
use kartik\widgets\Select2;
use yii\base\Model;

/**
 * Trait LanguageTrait
 *
 * @property string $language
 */
trait LanguageTrait
{
    /** Validation rules contributed by this trait. */
    public function getLanguageRules()
    {
        return [
            [['language'], 'string', 'max' => 7],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getLanguageAttributeLabels()
    {
        return [
            'language' => Yii::t('traits', 'Language'),
        ];
    }

    /** Return the two-character language code. */
    public function getLang()
    {
        return substr($this->language, 0, 2);
    }

    /** Return the full language tag. */
    public function getLangTag()
    {
        return $this->language;
    }

    /** Render the language selector. */
    public function getLanguageWidget($form)
    {
	    /** @var $this Model */
        return $form->field($this, 'language')->widget(Select2::class, [
            'data' => static::getLanguagesSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-globe"></i>'
                ]
            ],
        ]);
    }

    /** Return configured languages for Select2. */
    public static function getLanguagesSelect2()
    {
        $languages = Yii::$app->urlManager->languages;
        $array = ['all' => Yii::t('traits', 'All Female')];

        /** @var array $languages */
        foreach($languages as $language) {
            $array[$language] = strtoupper($language);
        }

        return $array;
    }

    /** Return configured languages for filters. */
	public static function getLanguagesFilterSelect2($showOnlyDefault = false)
	{
		$languages = Yii::$app->urlManager->languages;
		$languageAll = Yii::$app->controller->module->languageAll;
		$languageDefault = substr($languageAll,0,2);

		if($showOnlyDefault) {
			return ['all' => Yii::t('traits', 'All Female')];
		}

		$array = [];

		/** @var array $languages */
		foreach($languages as $language)
		{
			if($language === $languageDefault) {
				$array[$language] = strtoupper($language).' (Default)';
			} else {
				$array[$language] = strtoupper($language);
			}
		}

		return $array;
	}
}
