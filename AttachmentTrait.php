<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.1
 */

namespace cinghie\traits;

use FFMpeg\Media\Frame;
use Yii;
use getID3;
use getid3_exception;
use FFMpeg\Coordinate;
use FFMpeg\FFMpeg;
use kartik\form\ActiveField;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\Html;
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
            [['size'], 'integer'],
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
	 * @param $attachPath
	 *
	 * @return array
	 * @throws getid3_exception
	 */
	public static function getID3Info($attachPath)
    {
	    $getID3 = new getID3;

	    return $getID3->analyze($attachPath);
    }

	/**
	 * @param $attachPath
	 *
	 * @return mixed
	 * @throws getid3_exception
	 */
	public function getVideoDuration($attachPath)
    {
    	$fileInfo = AttachmentTrait::getID3Info($attachPath);

    	if(strpos($fileInfo['mime_type'], 'video') !== false && isset($fileInfo['video'])) {
		    return $fileInfo['playtime_string'];
	    }

    	return null;
    }

	/**
	 * @param string $attachPath
	 * @param int $sec
	 *
	 * @return Frame
	 * @throws getid3_exception
	 */
	public function getVideoThumb($attachPath,$sec = 3)
	{
		$frame = null;
		$fileInfo  = AttachmentTrait::getID3Info($attachPath);
		$ffmpegOptions = [];
		$operationSystem = PHP_OS;

		if(strpos($operationSystem, 'Windows') !== false || strpos($operationSystem, 'WIN') !== false) {
			$ffmpegOptions = [
				'ffmpeg.binaries'  => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffmpeg.exe'),
				'ffprobe.binaries' => Yii::getAlias('@vendor/cinghie/yii2-traits/vendor/ffmpeg/windows/ffprobe.exe')
			];
		}

		if(strpos($operationSystem, 'Linux') !== false) {
			$ffmpegOptions = [
				'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
				'ffprobe.binaries' => '/usr/bin/ffprobe'
			];
		}

		if(strpos($fileInfo['mime_type'], 'video') !== false && isset($fileInfo['video'])) {
			$ffmpeg = FFMpeg::create($ffmpegOptions);
			$video  = $ffmpeg->open($attachPath);
			$frame  = $video->frame(Coordinate\TimeCode::fromSeconds($sec));
		}

		return $frame;
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
	 * Purge Attachment name from dangerous chars
	 *
	 * @param string $attachName
	 *
	 * @return string
	 */
	public function purgeAttachmentName($attachName)
	{
		$attachName = str_replace(["/'/",'’','"',':',';',',','.',' ','__'],'_',$attachName);

		return $attachName;
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
	 * Return Preview image or file icon
	 *
	 * @param string $class
	 * @param string $style
	 *
	 * @return string
	 */
	public function getAttachmentPreview($class = 'img-responsive',$style = '')
    {
	    if (strpos($this->mimetype, 'image') !== false) {
		    return Html::img($this->fileUrl,['class' => $class,'style' => $style]);
	    }

	    if (strpos($this->mimetype, 'video') !== false) {
		    return Html::img($this->fileUrl,['class' => $class,'style' => $style]);
	    }

	    return $this->getAttachmentTypeIcon();
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
	        'msword' => '<i class="fa fa-file-word-o" aria-hidden="true"></i>',
	        'vnd.openxmlformats-officedocument.wordprocessingml.document' => '<i class="fa fa-file-word-o" aria-hidden="true"></i>',
	        'vnd.openxmlformats-officedocument.wordprocessingml.template' => '<i class="fa fa-file-word-o" aria-hidden="true"></i>',
	        'vnd.ms-word.document.macroEnabled.12' => '<i class="fa fa-file-word-o" aria-hidden="true"></i>',
	        'vnd.ms-word.template.macroEnabled.12' => '<i class="fa fa-file-word-o" aria-hidden="true"></i>',
            'vnd.ms-excel' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.openxmlformats-officedocument.spreadsheetml.template' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.ms-excel.sheet.macroEnabled.12' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.ms-excel.template.macroEnabled.12' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.ms-excel.addin.macroEnabled.12' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.ms-excel.sheet.binary.macroEnabled.12' => '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            'vnd.ms-powerpoint' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.openxmlformats-officedocument.presentationml.presentation' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.openxmlformats-officedocument.presentationml.template' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.openxmlformats-officedocument.presentationml.slideshow' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.ms-powerpoint.addin.macroEnabled.12' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.ms-powerpoint.presentation.macroEnabled.12' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.ms-powerpoint.template.macroEnabled.12' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
            'vnd.ms-powerpoint.slideshow.macroEnabled.12' => '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>',
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
					'initialPreview' => strpos($this->mimetype, 'image') === 0 ? $this->getFileUrl() : $this->getAttachmentTypeIcon(),
					'initialPreviewAsData' => strpos($this->mimetype, 'image') === 0,
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
				$initialPreview[] = strpos($attach->mimetype, 'image') === 0 ? Html::img($attach->fileUrl,['class' => 'img-responsive']) : $attach->getAttachmentTypeIcon();
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
					'initialPreview' => $initialPreview,
					'initialPreviewAsData' => false,
					'initialPreviewConfig' => $initialPreviewConfig,
					'overwriteInitial' => false,
					'previewFileType' => 'any',
					'showPreview' => true,
					'showCaption' => true,
					'showRemove' => false,
					'showUpload' => false
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
	 * @throws InvalidConfigException
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
	 * @throws InvalidConfigException
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
	 * @throws InvalidConfigException
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

}
