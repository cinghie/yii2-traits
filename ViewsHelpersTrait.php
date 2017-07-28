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
     * @return string
     */
    public function getUpdateButton()
    {
        return $this->getStandardButton('fa fa-pencil text-yellow', Yii::t('traits','Update'), '#', ['class' => 'btn btn-mini btn-update']);
    }

    /**
     * Return javascript for action update button
     *
     * @param string $w
     * @return string
     */
    public function getUpdateButtonJavascript($w)
    {
        return '$("a.btn-update").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t("traits", "Select at least one item").'");
            } else if(selectedId.length>1){
                alert("'.Yii::t("traits", "Select only 1 item").'");
            } else {
                var url = "'.Url::to(['update']).'?id="+selectedId[0];
                window.location.href= url;
            }
        });';
    }

    /**
     * Return action delete button
     *
     * @return string
     */
    public function getDeleteButton()
    {
        return $this->getStandardButton('fa fa-trash text-red', Yii::t('traits','Delete'), '#', ['class' => 'btn btn-mini btn-delete']);
    }

    /**
     * Return javascript for action delete button
     *
     * @param string $w
     * @return string
     */
    public function getDeleteButtonJavascript($w)
    {
        return '$("a.btn-delete").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("'.Yii::t("traits", "Select at least one item").'");
            } else {
                var choose = confirm("'.Yii::t("traits", "Do you want delete selected items?").'");

                if (choose == true) {
                    $.ajax({
                        type: \'POST\',
                        url : "'.Url::to(['deletemultiple']).'?id="+selectedId,
                        data : {ids: selectedId},
                        success : function() {
                            $.pjax.reload({container:"'.$w.'"});
                        }
                    });
                }
            }
        });';
    }

    /**
     * Return action preview button
     *
     * @return string
     */
    public function getPreviewButton()
    {
        return $this->getStandardButton('fa fa-eye text-blue', Yii::t('traits','Preview'), '#', ['class' => 'btn btn-mini btn-preview']);
    }

    /**
     * Return javascript for action preview button
     *
     * @param string $w
     * @return string
     */
    public function getPreviewButtonJavascript($w)
    {
        return '$("a.btn-preview").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("'.Yii::t("traits", "Select at least one item").'");
            } else if(selectedId.length>1){
                alert("'.Yii::t("traits", "Select only 1 item").'");
            } else {
                var url = "'.Url::to(['view']).'?id="+selectedId[0];
                window.open(url,"_blank");
            }
        });';
    }

    /**
     * Return action active button
     *
     * @return string
     */
    public function getActiveButton()
    {
        return $this->getStandardButton('fa fa-check-circle text-green', Yii::t('traits','Active'), '#', ['class' => 'btn btn-mini btn-active']);
    }

    /**
     * Return javascript for action active button
     *
     * @param string $w
     * @return string
     */
    public function getActiveButtonJavascript($w)
    {
        return '$("a.btn-active").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t("traits", "Select at least one item").'");
            } else {
                $.ajax({
                    type: \'POST\',
                    url : "'.Url::to(['activemultiple']).'?id="+selectedId,
                    data : {ids: selectedId},
                    success : function() {
                        $.pjax.reload({container:"'.$w.'"});
                    }
                });
            }
        });';
    }

    /**
     * Return action deactive button
     *
     * @return string
     */
    public function getDeactiveButton()
    {
        return $this->getStandardButton('fa fa-stop-circle text-red', Yii::t('traits','Deactive'), '#', ['class' => 'btn btn-mini btn-deactive']);
    }

    /**
     * Return javascript for action deactive button
     *
     * @param string $w
     * @return string
     */
    public function getDeactiveButtonJavascript($w)
    {
        return '$("a.btn-deactive").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t("traits", "Select at least one item").'");
            } else {
                $.ajax({
                    type: \'POST\',
                    url : "'.Url::to(['deactivemultiple']).'?id="+selectedId,
                    data : {ids: selectedId},
                    success : function() {
                        $.pjax.reload({container:"'.$w.'"});
                    }
                });
            }
        });';
    }

    /**
     * Return action reset button
     *
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
     * @return string
     */
    public function getExitButton()
    {
        return $this->getStandardButton('fa fa-sign-out text-blue', Yii::t('traits','Exit'), Url::to('index'));
    }

    /**
     * Return standard button
     *
     * @param string $icon
     * @param string $title
     * @param string $url
     * @param array $class
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
     * Return Javascript for Button Actions
     *
     * @param array $actions
     * @return string
     */
    public function getStandardButtonJavascript($actions)
    {
        $javascript = "$(document).ready(function() {";

        foreach ($actions as $action)
        {

        }

        $javascript .= "});";

        return '
            $(document).ready(function()
            {
                $("a.btn-update").click(function() {
                    var selectedId = $("#w1").yiiGridView("getSelectedRows");
        
                    if(selectedId.length == 0) {
                        alert("'.Yii::t("traits", "Select at least one item").'");
                    } else if(selectedId.length>1){
                        alert("'.Yii::t("traits", "Select only 1 item").'");
                    } else {
                        var url = "'.Url::to(['/articles/categories/update']).'?id="+selectedId[0];
                        window.location.href= url;
                    }
                });
                $("a.btn-delete").click(function() {
                    var selectedId = $("#w1").yiiGridView("getSelectedRows");
        
                    if(selectedId.length == 0) {
                        alert("'.Yii::t("traits", "Select at least one item").'");
                    } else {
                        var choose = confirm("'.Yii::t("traits", "Do you want delete selected items?").'");
        
                        if (choose == true) {
                            $.ajax({
                                type: \'POST\',
                                url : "'.Url::to(['/articles/categories/deletemultiple']).'?id="+selectedId,
                                data : {ids: selectedId},
                                success : function() {
                                    $.pjax.reload({container:"#w1"});
                                }
                            });
                        }
                    }
                });
                $("a.btn-preview").click(function() {
                    var selectedId = $("#w1").yiiGridView("getSelectedRows");
        
                    if(selectedId.length == 0) {
                        alert("'.Yii::t("traits", "Select at least one item").'");
                    } else if(selectedId.length>1){
                        alert("'.Yii::t("traits", "Select only 1 item").'");
                    } else {
                        var url = "'.Url::to(['/articles/categories/view']).'?id="+selectedId[0];
                        window.open(url,"_blank");
                    }
                });
            });
        ';
    }

    /**
     * Generate DetailView for Entry Informations
     *
     * @return string
     * @throws \Exception
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