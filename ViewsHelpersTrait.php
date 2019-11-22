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

use Yii;
use Exception;
use kartik\detail\DetailView;
use kartik\helpers\Html;
use yii\base\InvalidParamException;
use yii\helpers\Url;

/**
 * Trait ViewsHelper
 */
trait ViewsHelpersTrait
{
	/**
	 * Return action create button
	 *
	 * @param array $url
	 *
	 * @return string
	 */
    public function getCreateButton(array $url = ['create'])
    {
        return $this->getStandardButton('fa fa-plus-circle text-green', Yii::t('traits','Create'), $url);
    }

    /**
     * Return action update button
     *
     * @param int $id
     *
     * @return string
     */
    public function getUpdateButton($id = 0)
    {
        if($id) {
            return $this->getStandardButton('fa fa-pencil-alt text-yellow', Yii::t('traits','Update'), ['update', 'id' => $id], ['class' => 'btn btn-mini btn-update']);
        }

	    return $this->getStandardButton('fa fa-pencil-alt text-yellow', Yii::t('traits','Update'), '#', ['class' => 'btn btn-mini btn-update']);
    }

    /**
     * Return javascript for action update button
     *
     * @param string $w
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getUpdateButtonJavascript($w)
    {
        return '$("a.btn-update").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t('traits', 'Select at least one item').'");
            } else if(selectedId.length>1){
                alert("'.Yii::t('traits', 'Select only 1 item').'");
            } else {
                var url = "'.Url::to(['update']).'?id="+selectedId[0];
                window.location.href= url;
            }
        });';
    }

    /**
     * Return action delete button
     *
     * @param int $id
     *
     * @return string
     */
    public function getDeleteButton($id = 0)
    {
        if($id) {
            return $this->getStandardButton('fa fa-trash text-red', Yii::t('traits','Delete'), ['delete', 'id' => $id], [
                'class' => 'btn btn-mini btn-delete',
                'data' => [
                    'confirm' => Yii::t('traits', 'Do you want delete selected items?'),
                    'method' => 'post',
                ],
            ]);
        }

	    return $this->getStandardButton('fa fa-trash text-red', Yii::t('traits','Delete'), '#', ['class' => 'btn btn-mini btn-delete']);
    }

    /**
     * Return javascript for action delete button
     *
     * @param string $w
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getDeleteButtonJavascript($w)
    {
        return '$("a.btn-delete").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("'.Yii::t('traits', 'Select at least one item').'");
            } else {
                var choose = confirm("'.Yii::t('traits', 'Do you want delete selected items?').'");

                if (choose == true) {
                    $.ajax({
                        type: \'POST\',
                        url : "'.Url::to(['deletemultiple']).'?id="+selectedId,
                        data : {ids: selectedId},
                        success : function() {
                            var url = "'.Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id]).'";
                            $.pjax.reload({url: url, container: "'.$w.'-container", push: false, replace: false, timeout: 8000});
                        }
                    });
                }
            }
        });';
    }

	/**
	 * Return action preview button
	 *
	 * @param array $url
	 *
	 * @return string
	 */
    public function getPreviewButton(array $url = [ '#' ])
    {
        return $this->getStandardButton('fa fa-eye text-blue', Yii::t('traits','Preview'), $url, ['class' => 'btn btn-mini btn-preview']);
    }

    /**
     * Return javascript for action preview button
     *
     * @param string $w
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getPreviewButtonJavascript($w)
    {
        return '$("a.btn-preview").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("'.Yii::t('traits', 'Select at least one item').'");
            } else if(selectedId.length>1){
                alert("'.Yii::t('traits', 'Select only 1 item').'");
            } else {
                var url = "'.Url::to(['view']).'?id="+selectedId[0];
                window.open(url,"_blank");
            }
        });';
    }

    /**
     * Return action active button
     *
     * @param int $id
     *
     * @return string
     */
    public function getActiveButton($id = 0)
    {
        if($id) {
            return $this->getStandardButton('fa fa-check-circle text-green', Yii::t('traits','Active'), ['changestate', 'id' => $id], [
                'class' => 'btn btn-mini btn-active',
                'data' => [
                    'method' => 'post',
                ],
            ]);
        }

	    return $this->getStandardButton('fa fa-check-circle text-green', Yii::t('traits','Active'), '#', ['class' => 'btn btn-mini btn-active']);
    }

    /**
     * Return javascript for action active button
     *
     * @param string $w
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getActiveButtonJavascript($w)
    {
        return '$("a.btn-active").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t('traits', 'Select at least one item').'");
            } else {
                $.ajax({
                    type: \'POST\',
                    url : "'.Url::to(['activemultiple']).'?id="+selectedId,
                    data : {ids: selectedId},
                    success : function() {
                        var url = "'.Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id]).'";
                        $.pjax.reload({url: url, container: "'.$w.'-container", push: false, replace: false, timeout: 8000});
                    }
                });
            }
        });';
    }

    /**
     * Return action deactive button
     *
     * @param int $id
     *
     * @return string
     */
    public function getDeactiveButton($id = 0)
    {
        if($id) {
            return $this->getStandardButton('fa fa-stop-circle text-red', Yii::t('traits','Deactive'), ['changestate', 'id' => $id], [
                'class' => 'btn btn-mini btn-deactive',
                'data' => [
                    'method' => 'post',
                ],
            ]);
        }

	    return $this->getStandardButton('fa fa-stop-circle text-red', Yii::t('traits','Deactive'), '#', ['class' => 'btn btn-mini btn-deactive']);
    }

    /**
     * Return javascript for action deactive button
     *
     * @param string $w
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getDeactiveButtonJavascript($w)
    {
        return '$("a.btn-deactive").click(function() {
            var selectedId = $("'.$w.'").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("'.Yii::t('traits', 'Select at least one item').'");
            } else {
                $.ajax({
                    type: \'POST\',
                    url : "'.Url::to(['deactivemultiple']).'?id="+selectedId,
                    data : {ids: selectedId},
                    success : function() {
                        var url = "'.Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id]).'";
                        $.pjax.reload({url: url, container: "'.$w.'-container", push: false, replace: false, timeout: 8000});
                    }
                });
            }
        });';
    }

	/**
	 * Return action reset button
	 *
	 * @param array $url
	 *
	 * @return string
	 */
    public function getResetButton(array $url = ['index'])
    {
        return $this->getStandardButton('fa fa-redo text-aqua', Yii::t('traits','Reset'), $url, ['class' => 'btn btn-mini btn-reset', 'data-pjax' => 0]);
    }

    /**
     * Return action save button
     *
     * @return string
     */
    public function getSaveButton()
    {
        return '<div class="pull-right text-center" style="margin-right: 25px;">'.
            Html::submitButton('<i class="fa fa-save text-green"></i>', ['class' => 'btn btn-mini btn-save']).
            '<div>'.Yii::t('traits','Save').'</div></div>';
    }

	/**
	 * Return action cancel button
	 *
	 * @param string $icon
	 * @param string $title
	 * @param array $url
	 *
	 * @return string
	 */
    public function getCancelButton($icon = 'fa fa-times-circle text-red', $title = '', array $url = [ '' ])
    {
    	$title = $title ?: Yii::t('traits','Cancel');

        return $this->getStandardButton($icon, $title, $url);
    }

	/**
	 * Return action exit button
	 *
	 * @param string $icon
	 * @param string $title
	 * @param array|string|null $url
	 *
	 * @return string
	 */
    public function getExitButton($icon = 'fa fa-sign-out-alt text-blue', $title = '', array $url = [])
    {
	    $title = $title ?: Yii::t('traits','Exit');

	    if(empty($url) && !empty(Yii::$app->request->referrer)) {
		    $url = Yii::$app->request->referrer;
	    }

	    if ((empty($url) && empty(Yii::$app->request->referrer)) || (Yii::$app->request->referrer === Yii::$app->request->absoluteUrl)) {
	    	$url = ['index'];
	    }

        return $this->getStandardButton($icon, $title, $url);
    }

	/**
	 * Return action send button
	 *
	 * @param array $url
	 *
	 * @return string
	 */
    public function getSendButton(array $url = ['#'])
    {
        return $this->getStandardButton('fa fa-paper-plane text-orange', Yii::t('traits','Send'), $url, ['class' => 'btn btn-mini btn-send']);
    }

    /**
     * Return javascript for action deactive button
     *
     * @return string
     * @throws InvalidParamException
     */
    public function getSendButtonJavascript()
    {
        return 'var selectedId = '.$this->id.'
			$("a.btn-send").click(function(e) {
            e.preventDefault();
            if (confirm("'.Yii::t('traits','Are you sure you want to send this item?').'")) {
                $.ajax({
                    type: \'POST\',
                    url : "'.Url::to(['send']).'?id="+selectedId,
                    data : {id: selectedId},
                    success: function(result){
                        alert(result);
                    }
                });
            }
        });';
    }

	/**
	 * Return action send button
	 *
	 * @param array $url
	 *
	 * @return string
	 */
	public function getDownloadButton(array $url = ['download'])
	{
		return $this->getStandardButton('fa fa-download text-blue', Yii::t('traits','Download'), $url, ['class' => 'btn btn-mini btn-send']);
	}

	/**
	 * Return standard button
	 *
	 * @param string $icon
	 * @param string $title
	 * @param string | array $url
	 * @param array $aClass
	 * @param string $divClass
	 * @param string $divStyle
	 *
	 * @return string
	 */
    public function getStandardButton($icon, $title, $url, array $aClass = [ 'class' => 'btn btn-mini' ], $divClass = 'pull-right text-center', $divStyle = 'margin-right: 25px;')
    {
        return '<div class="'.$divClass.'" style="'.$divStyle.'">'.
                    Html::a('<i class="'.$icon.'"></i>', $url , $aClass).'
                    <div>'.$title.'</div>
                </div>';
    }

	/**
	 * Return Modal javascript
	 *
	 * @return string
	 */
	public function getModalJavascript()
    {
    	return '$(function(){
			// changed id to class
			$(\'.modalButton\').click(function (){
				$.get($(this).attr(\'href\'), function(data) {
					$(\'#modal\').modal(\'show\').find(\'#modalContent\').html(data)
				});
				return false;
			});
		});';
    }

    /**
     * Generate DetailView for Entry Informations
     *
     * @return string
     * @throws Exception
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
                'heading' => Yii::t('traits', 'Entry Informations'),
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
