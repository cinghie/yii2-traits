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
 * @property string $alias
 * @property string $name
 */
trait NameAliasTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alias' => \Yii::t('newsletters', 'Alias'),
            'name' => \Yii::t('newsletters', 'Name'),
        ];
    }

    /**
     * Generate URL alias by name
     *
     * @param string $name
     * @return string
     */
    public function generateAlias($name)
    {
        // remove any '-' from the string they will be used as concatonater
        $name = str_replace('-', ' ', $name);
        $name = str_replace('_', ' ', $name);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $name = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $name);

        // lowercase and trim
        $name = trim(strtolower($name));

        return $name;
    }

}
