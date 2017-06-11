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

use kartik\detail\DetailView;

/**
 * Trait ViewsHelper
 *
 * @package cinghie\traits
 */
trait ViewsHelper
{

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