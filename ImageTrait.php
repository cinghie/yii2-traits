<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.0.1
 */

namespace cinghie\traits;

use Yii;
use kartik\widgets\FileInput;

/**
 * Trait ImageTrait
 *
 * @property string $image
 * @property string $image_caption
 * @property string $image_credits
 */
trait ImageTrait
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $getimageallowed = ImageTrait::getImagesAllowed();
        return [
            [['image_caption', 'image_credits'], 'string', 'max' => 255],
            [['image'], 'file', 'extensions' => $getimageallowed,],
            [['image'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image' => Yii::t('traits', 'Image'),
            'image_caption' => Yii::t('traits', 'Image Caption'),
            'image_credits' => Yii::t('traits', 'Image Credits'),
        ];
    }

    /**
     * Generate Image Form Widget
     *
     * @return string
     * @throws \Exception
     */
    public function getImageWidget()
    {
        /** @var $this \yii\base\Model */
        $image = '<label class="control-label" for="items-photo_name">' .Yii::t('traits','Image'). '</label>';
        $image .= FileInput::widget([
            'model' => $this,
            'attribute' => 'image',
            'name' => 'image',
            'options'=>[
                'accept' => $this->getImagesAccept()
            ],
            'pluginOptions' => [
                'allowedFileExtensions' => $this->getImagesAllowed(),
                'previewFileType' => 'image',
                'showPreview' => true,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => true,
                'initialPreview' => $this->image ? $this->getImageUrl() : false,
                'initialPreviewAsData' => $this->image ? true : false,
                'overwriteInitial' => $this->image ? true : false
            ]
        ]);

        return $image;
    }

    /**
     * Generate Image Caption Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getImageCaptionWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'image_caption', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon glyphicon-picture"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true])->textarea(['rows' => 6]);
    }

    /**
     * Generate Image Credits Form Widget
     *
     * @param \kartik\widgets\ActiveForm $form
     * @return \kartik\form\ActiveField
     */
    public function getImageCreditsWidget($form)
    {
        /** @var $this \yii\base\Model */
        return $form->field($this, 'image_credits', [
            'addon' => [
                'prepend' => [
                    'content'=>'<i class="glyphicon-barcode"></i>'
                ]
            ]
        ])->textInput(['maxlength' => true]);
    }

    /**
     * Get Upload Max Size
     *
     * @return string
     */
    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    /**
     * Get Allowed images
     *
     * @return array
     */
    public function getImagesAllowed()
    {
        return Yii::$app->controller->module->imageType;
    }

    /**
     * Get Allowed images in Accept Format
     *
     * @return array
     */
    public function getImagesAccept()
    {
        $imageAccept = [];
        $imagesAllowed = $this->getImagesAllowed();

        foreach ($imagesAllowed as $imageAllowed)
        {
            $imageAccept[] = 'image/'.$imageAllowed;
        }

        return $imageAccept;
    }

}
