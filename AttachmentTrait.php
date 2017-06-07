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

/**
 * @property string $extension
 * @property string $filename
 * @property string $mimetype
 */
trait AttachmentTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['extension'], 'string', 'max' => 12],
            [['filename','mimetype'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'extension' => \Yii::t('traits', 'Extension'),
            'filename' => \Yii::t('traits', 'Filename'),
            'mimetype' => \Yii::t('traits', 'MimeType'),
        ];
    }

    /**
     * Generate a MD5 filename by original filename
     *
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public function generateMd5FileName($filename, $extension)
    {
        return md5( uniqid($filename, FALSE) ) . '.' . $extension;
    }

}
