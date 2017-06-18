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
use kartik\detail\DetailView;
use kartik\helpers\Html;
use yii\helpers\Url;

/**
 * Trait ViewsHelper
 */
trait ViewsHelpersTrait
{

    /**
     * Return action create button
     *
     * @return string
     */
    public function getCreateButton()
    {
        return $this->getStandardButton('fa fa-plus-circle text-green', Yii::t('traits','Create'), Url::to(['create']));
    }

    /**
     * Return action update button
     *
     * $param int $id
     * @return string
     */
    public function getUpdateButton()
    {
        return $this->getStandardButton('fa fa-pencil text-yellow', Yii::t('traits','Update'), '#', ['class' => 'btn btn-mini btn-update']);
    }

    /**
     * Return action delete button
     *
     * $param int $id
     * @return string
     */
    public function getDeleteButton()
    {
        return $this->getStandardButton('fa fa-trash text-red', Yii::t('traits','Delete'), '#', ['class' => 'btn btn-mini btn-delete']);
    }

    /**
     * Return action preview button
     *
     * $param int $id
     * @return string
     */
    public function getPreviewButton()
    {
        return $this->getStandardButton('fa fa-eye text-blue', Yii::t('traits','Preview'), '#', ['class' => 'btn btn-mini btn-preview']);
    }

    /**
     * Return action active button
     *
     * $param int $id
     * @return string
     */
    public function getActiveButton()
    {
        return $this->getStandardButton('fa fa-check-circle text-green', Yii::t('traits','Active'), '#', ['class' => 'btn btn-mini btn-active']);
    }

    /**
     * Return action deactive button
     *
     * $param int $id
     * @return string
     */
    public function getDeactiveButton()
    {
        return $this->getStandardButton('fa fa-stop-circle text-red', Yii::t('traits','Deactive'), '#', ['class' => 'btn btn-mini btn-deactive']);
    }

    /**
     * Return action reset button
     *
     * $param int $id
     * @return string
     */
    public function getResetButton()
    {
        return $this->getStandardButton('fa fa-repeat text-aqua', Yii::t('traits','Reset'), Url::to(['index']), ['class' => 'btn btn-mini btn-reset', 'data-pjax' => 0]);
    }


    /**
     * Return action save button
     *
     * @return string
     */
    public function getSaveButton()
    {
        return '<div class="pull-right text-center" style="margin-right: 25px;">'.
            Html::submitButton('<i class="fa fa-floppy-o text-green"></i>', ['class' => 'btn btn-mini btn-save']).
            '<div>'.Yii::t('traits','Save').'</div></div>';
    }

    /**
     * Return action annull button
     *
     * @return string
     */
    public function getCancelButton()
    {
        return $this->getStandardButton('fa fa-times-circle text-red', Yii::t('traits','Cancel'), Url::to(['']));
    }

    /**
     * Return action exit button
     *
     * @param string $url
     * @return string
     */
    public function getExitButton()
    {
        return $this->getStandardButton('fa fa-sign-out text-blue', Yii::t('traits','Exit'), Url::to('index'));
    }

    /**
     * Return standard button
     *
     * @param $icon
     * @param string $title
     * @param string $url
     * @return string
     */
    public function getStandardButton($icon,$title,$url,$class = ['class' => 'btn btn-mini'])
    {
        return '<div class="pull-right text-center" style="margin-right: 25px;">'.
                    Html::a('<i class="'.$icon.'"></i>', $url , $class).'
                    <div>'.$title.'</div>
                </div>';
    }

    /**
     * Generate DetailView for Entry Informations
     *
     * @return string
     */
    public function getEntryInformationsDetailView()
    {
        return DetailView::widget([
            'model' => $this,
            'enableEditMode' => false,
            'deleteOptions' => false,
            'condensed' => true,
            'hover' => true,
            'mode' => DetailView::MODE_VIEW,
            'panel' => [
                'heading' => \Yii::t('traits', 'Entry Informations'),
                'type' => DetailView::TYPE_INFO,
            ],
            'attributes' => [
                $this->getUserDetailView(),
                $this->getStateDetailView(),
                $this->getCreatedByDetailView(),
                $this->getCreatedDetailView(),
                $this->getModifiedByDetailView(),
                $this->getModifiedDetailView(),
            ]
        ]);
    }

}