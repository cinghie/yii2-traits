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

use Yii;

/**
 * Trait AttachmentTrait
 *
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
            [['size'], 'integer'],
            [['extension'], 'string', 'max' => 12],
            [['alias', 'filename', 'mimetype', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'alias' => Yii::t('traits', 'Alias'),
            'extension' => Yii::t('traits', 'Extension'),
            'filename' => Yii::t('traits', 'Filename'),
            'mimetype' => Yii::t('traits', 'MimeType'),
            'size' => Yii::t('traits', 'Size'),
            'title' => Yii::t('traits', 'Title'),
        ];
    }

    /**
     * Generate Extension Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getExtensionWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'extension',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-file-o"></i>'
                ]
            ],
        ])->textInput(['disabled' => true]);
    }

    /**
     * Generate MimeType Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getMimeTypeWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'mimetype',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-file"></i>'
                ]
            ],
        ])->textInput(['disabled' => true]);
    }

    /**
     * Generate Size Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getSizeWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'size',[
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-balance-scale"></i>'
                ]
            ],
        ])->textInput(['disabled' => true, 'value' => $this->formatSize()]);
    }

    /**
     * Generate Attachment type from mimetype
     *
     * return string
     */
    public function getAttachmentType()
    {
        return explode("/",$this->mimetype);
    }

    /**
     * Get Attachmente Type Image by Type
     *
     * return string
     */
    public function getAttachmentTypeIcon()
    {
        $applications = [
            'pdf' => '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
        ];

        $types = [
            'audio' => '<i class="fa fa-file-audio-o" aria-hidden="true"></i>',
            'image' => '<i class="fa fa-file-image-o" aria-hidden="true"></i>',
            'text' => '<i class="fa fa-file-text-o" aria-hidden="true"></i>',
            'video' => '<i class="fa fa-file-video-o" aria-hidden="true"></i>',
        ];

        $mimetype = $this->getAttachmentType();

        foreach($types as $type => $icon)
        {
            if (isset($mimetype[$type])) {

                return $icon;

            }
        }

        foreach($applications as $application => $icon)
        {
            if (isset($mimetype[1][$application])) {

                return $icon;

            }
        }

        return '<i class="fa fa-file-o" aria-hidden="true"></i>';
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

    /**
     * Format size in readable size
     *
     * @return string
     */
    public function formatSize()
    {
        $bytes = sprintf('%u', $this->size);

        if ($bytes > 0)
        {
            $unit = (int)log($bytes, 1024);
            $units = array('B', 'KB', 'MB', 'GB');

            if (array_key_exists($unit, $units) === true)
            {
                return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
            }
        }

        return $bytes;
    }

}
