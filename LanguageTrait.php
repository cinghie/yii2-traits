<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 0.1.0
 */

namespace cinghie\traits;

/*
 * @property string $language
 */
trait LanguageTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language' => \Yii::t('traits', 'Language'),
        ];
    }

    /**
     * Return languages Select
     *
     * @return array
     */
    public function getLanguagesSelect2()
    {
        $languages = \Yii::$app->urlManager->languages;
        $languagesSelect = array('All' => \Yii::t('traits', 'All Female'));

        foreach($languages as $language) {
            $languagesSelect[$language] = ucwords($language);
        }

        return $languagesSelect;
    }

}
