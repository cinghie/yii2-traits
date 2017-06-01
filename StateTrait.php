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
 * @property int $state
 */
trait StateTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'state' => \Yii::t('traits', 'State'),
        ];
    }

    /**
     * Active the item setting state = 1
     *
     * @return bool
     */
    public function active()
    {
        return (bool)$this->updateAttributes([
            'state' => 1
        ]);
    }

    /**
     * Inactive the item setting state = 0
     *
     * @return bool
     */
    public function inactive()
    {
        return (bool)$this->updateAttributes([
            'state' => 0
        ]);
    }

}
