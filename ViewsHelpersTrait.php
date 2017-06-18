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
        return '<div class="pull-right text-center" style="margin-right: 25px;">'.
            Html::a('<i class="fa fa-times-circle text-red"></i>', Url::to(['']), ['class' => 'btn btn-mini btn-cancel']).'
            <div>'.Yii::t('traits','Cancel').'</div></div>';
    }

    /**
     * Return action exit button
     *
     * @param array $url
     * @return string
     */
    public function getExitButton($url)
    {
        return '<div class="pull-right text-center" style="margin-right: 25px;">'.
            Html::a('<i class="fa fa-sign-out text-blue"></i>', $url ,['class' => 'btn btn-mini btn-exit']).'
            <div>'.Yii::t('traits','Exit').'</div></div>';
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