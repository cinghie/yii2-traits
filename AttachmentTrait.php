<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.0
 */

namespace cinghie\traits;

use Yii;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\base\InvalidParamException;
use yii\helpers\Url;

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
    public static function rules()
    {
        return [
            [['size'], 'int'],
            [['extension'], 'string', 'max' => 12],
            [['alias', 'filename', 'mimetype', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function attributeLabels()
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
	 * Generate File Ipunt Form Widget
	 *
	 * @param ActiveForm $form
	 * @param array $attachType
	 *
	 * @return ActiveField
	 */
    public function getFileWidget($form,$attachType)
    {
        if($this->filename) {
	        /** @var \yii\base\Model $this */
	        return $form->field($this, 'filename')->widget(FileInput::class, [
	            'options'=>[
		            'multiple'=> true
	            ],
                'pluginOptions' => [
                    'allowedFileExtensions' => $attachType,
                    'initialPreview' => $this->getFileUrl(),
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [
	                    ['caption' => $this->filename, 'size' => $this->size]
                    ],
                    'overwriteInitial' => true,
                    'previewFileType' => 'any',
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => false,
                    'showUpload' => false
                ],
            ]);
        }

	    /** @var \yii\base\Model $this */
	    return $form->field($this, 'filename')->widget(FileInput::class, [
		    'options'=>[
			    'multiple' => true
		    ],
		    'pluginOptions' => [
			    'allowedFileExtensions' => $attachType,
			    'browseLabel' => Yii::t('traits', 'Upload'),
			    'previewFileType' => 'any',
			    'showRemove' => false,
			    'showUpload' => false,
		    ],
	    ]);

    }

	/**
	 * Generate Files Ipunt Form Widget
	 *
	 * @param array $attachType
	 * @param string $attachURL
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getFilesWidget($attachType,$attachURL)
	{
		$attachments = $this->getAttachs();
		$html = '';

		$i = 0;
		$initialPreview = array();
		$initialPreviewConfig = array();

		if(count($attachments))
		{
			foreach($attachments as $attach) {
				$initialPreviewConfig[$i]['caption'] = $attach['title'];
				$initialPreviewConfig[$i]['size'] = $attach['size'];
				$initialPreviewConfig[$i]['url'] = Url::to(['attachments/deleteonfly', 'id' => $attach['id']]);
				$initialPreview[] = $attachURL.$attach['filename'];
				$i++;
			}

			$html .= '<label class=\"control-label\" for=\"items-photo_name\">'.Yii::t('traits','Upload').'</label>';
			$html .= FileInput::widget([
				'model' => $this,
				'attribute' => 'attachments[]',
				'name' => 'attachments[]',
				'options'=>[
					'multiple'=>true,
				],
				'pluginOptions' => [
					'allowedFileExtensions' => $attachType,
					'previewFileType' => 'any',
					'showPreview' => true,
					'showCaption' => true,
					'showRemove' => false,
					'showUpload' => false,
					'initialPreview' => $initialPreview,
					'initialPreviewAsData' => true,
					'initialPreviewConfig' => $initialPreviewConfig,
					'overwriteInitial' => false
				]
			]);

		} else {

			$html .= '<label class=\"control-label\" for=\"items-photo_name\">'.Yii::t('traits','Upload').'</label>';
			$html .= FileInput::widget([
				'model' => $this,
				'attribute' => 'attachments[]',
				'name' => 'attachments[]',
				'options'=>[
					'multiple'=>true,
				],
				'pluginOptions' => [
					'allowedFileExtensions' => $attachType,
					'previewFileType' => 'any',
					'showPreview' => true,
					'showCaption' => true,
					'showRemove' => false,
					'showUpload' => false
				]
			]);
		}

		return $html;
	}

    /**
     * Generate Extension Form Widget
     *
     * @param ActiveForm $form
     *
     * @return ActiveField
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
     * @param ActiveForm $form
     *
     * @return ActiveField
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
     * @param ActiveForm $form
     *
     * @return ActiveField
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
     * Return URL to file attached
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getFileUrl()
    {
    	$fileUrl = Yii::$app->controller->module->attachURL ? Yii::getAlias(Yii::$app->controller->module->attachURL).$this->filename : '';

    	return $fileUrl;
    }

    /**
     * Delete file attached
     *
     * @return boolean
     * @throws InvalidParamException
     */
    public function deleteFile()
    {
        $file = Yii::getAlias(Yii::$app->controller->module->attachPath).$this->filename;

        // check if image exists on server
        if ( empty($this->filename) || !file_exists($file) ) {
            return false;
        }

        // check if uploaded file can be deleted on server
        if (unlink($file)) {
            return true;
        }

        return false;
    }

    /**
     * Generate Attachment type from mimetype
     *
     * @return string[]
     */
    public function getAttachmentType()
    {
        return explode('/',$this->mimetype);
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

            if (array_key_exists($unit, $units) === true) {
                return sprintf('%d %s', $bytes / (1024 * $unit), $units[$unit]);
            }
        }

        return $bytes;
    }

    /**
     * Generate a MD5 filename by original filename
     *
     * @param string $filename
     * @param string $extension
     *
     * @return string
     */
    public function generateMd5FileName($filename, $extension)
    {
        return md5( uniqid($filename, FALSE) ) . '.' . $extension;
    }

    /**
     * Get Attachmente Type Image by Type
     *
     * @return string
     */
    public function getAttachmentTypeIcon()
    {
        $applications = [
            'csv' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'pdf' => '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            'plain' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'text' => '<i class="fa fa-file-text-o" aria-hidden="true"></i>',
            'vnd.ms-excel' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
        ];

        $texts = [
            'csv' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'pdf' => '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            'plain' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'text' => '<i class="fa fa-file-text-o" aria-hidden="true"></i>',
        ];

        $types = [
            'audio' => '<i class="fa fa-file-audio-o" aria-hidden="true"></i>',
            'archive' => '<i class="fa fa-file-archive-o" aria-hidden="true"></i>',
            'image' => '<i class="fa fa-file-image-o" aria-hidden="true"></i>',
            'video' => '<i class="fa fa-file-video-o" aria-hidden="true"></i>',
        ];

        $mimetype = $this->getAttachmentType();

        foreach($types as $type => $icon)
        {
            if (isset($mimetype[0]) && $mimetype[0] === $type) {
                return $icon;
            }
        }

        foreach($applications as $application => $icon)
        {
            if (isset($mimetype[1]) && $mimetype[1] === $application) {
                return $icon;
            }
        }

        foreach($texts as $text => $icon)
        {
            if (isset($mimetype[1]) && $mimetype[1] === $text) {
                return $icon;
            }
        }

        return '<i class="fa fa-file-o" aria-hidden="true"></i>';
    }

}
