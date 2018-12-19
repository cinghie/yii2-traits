<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.2
 */

namespace cinghie\traits;

use Yii;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/**
 * Trait LanguageTrait
 *
 * @property string $language
 */
trait LanguageTrait
{

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [
            [['language'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
    {
        return [
            'language' => Yii::t('traits', 'Language'),
        ];
    }

    /**
	 * Get language code (only 2 chars)
	 *
	 * @return string
	 */
    public function getLang() {
        return substr($this->language,0,2);
    }

    /**
     * Get language tag (5 chars)
     *
     * @return string
     */
    public function getLangTag() {
        return $this->language;
    }

    /**
     * Generate Language Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
     */
    public function getLanguageWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'language')->widget(Select2::class, [
            'data' => LanguageTrait::getLanguagesSelect2(),
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-globe"></i>'
                ]
            ],
        ]);
    }

    /**
     * Return an array with languages allowed
     *
     * @return array
     */
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

	/**
	 * Return an array with languages allowed
	 *
	 * @return array
	 */
	public static function getLanguagesFilterSelect2()
	{
		$languages   = Yii::$app->urlManager->languages;
		$languageAll = Yii::$app->controller->module->languageAll;
		$languageDefault = substr($languageAll,0,2);

		$array = ['all' => Yii::t('traits', 'All Female')];

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
