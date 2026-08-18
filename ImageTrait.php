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
use kartik\widgets\FileInput;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Trait ImageTrait
 *
 * @property string $image
 * @property string $image_caption
 * @property string $image_credits
 */
trait ImageTrait
{
    /** Validation rules contributed by this trait. */
    public function getImageRules()
    {
        $getimageallowed = self::getImagesAllowed();

        return [
            [['image_caption', 'image_credits'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => $getimageallowed],
            [['image'], 'safe'],
        ];
    }

    /** Attribute labels contributed by this trait. */
    public function getImageAttributeLabels()
    {
        return [
            'image' => Yii::t('traits', 'Image'),
            'image_caption' => Yii::t('traits', 'Image Caption'),
            'image_credits' => Yii::t('traits', 'Image Credits'),
        ];
    }

    /** Render the image upload widget. */
    public function getImageWidget()
    {
        /** @var $this Model */
        $image = '<label class="control-label" for="items-photo_name">' .Yii::t('traits','Image'). '</label>';
        $image .= FileInput::widget([
            'model' => $this,
            'attribute' => 'image',
            'name' => 'image',
            'options'=>[
                'accept' => $this->getImagesAccept()
            ],
            'pluginOptions' => [
                'allowedFileExtensions' => self::getImagesAllowed(),
                'previewFileType' => 'image',
                'showPreview' => true,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'initialPreview' => $this->image ? $this->getImageUrl() : false,
                'initialPreviewAsData' => (bool) $this->image,
                'initialPreviewConfig' => $this->isNewRecord ? [] : [ ['url' => Url::to(['deleteimage', 'id' => $this->id])] ],
                'overwriteInitial' => (bool) $this->image
            ]
        ]);

        return $image;
    }

    /** Render the image caption field. */
    public function getImageCaptionWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'image_caption', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-picture-o fas fa-image"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true])->textarea(['rows' => 6]);
    }

    /** Render the image credits field. */
    public function getImageCreditsWidget($form)
    {
        /** @var $this Model */
        return $form->field($this, 'image_credits', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="fa fa-barcode"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    /** Render the image value for grid views. */
	public function getImageGridView()
	{
		if ($this->image) {
			return Html::img($this->getImageThumbUrl('small'), ['width' => '36px']);
		}

		return '<span class="fa fa-ban text-danger"></span>';
	}

    /** Return the configured PHP upload size limit. */
    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    /** Return allowed image extensions. */
    public static function getImagesAllowed()
    {
        return Yii::$app->controller->module->imageType;
    }

    /** Return allowed image MIME accept values. */
    public function getImagesAccept()
    {
        $imageAccept = [];
        $imagesAllowed = self::getImagesAllowed();

        foreach ($imagesAllowed as $imageAllowed) {
            $imageAccept[] = 'image/'.$imageAllowed;
        }

        return $imageAccept;
    }
}
