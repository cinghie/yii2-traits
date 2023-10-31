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

use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use yii\base\InvalidConfigException;

/**
 * Trait ParentTrait
 *
 * @see https://demos.krajee.com/mpdf
 */
trait PDFTrait
{
    /**
     * Render PDF
     *
     * @param string $destination
     * @param string $title
     * @param string $content
     * @return string
     *
     * @throws CrossReferenceException
     * @throws InvalidConfigException
     * @throws MpdfException
     * @throws PdfParserException
     * @throws PdfTypeException
     */
    public function renderPDF($destination = 'browser', $title = 'Title', $content = 'Hello World!', $filename = '')
    {
        if($destination === 'download') {
            $destination = Pdf::DEST_DOWNLOAD;
        } elseif($destination === 'file') {
            $destination = Pdf::DEST_FILE;
        } else {
            $destination = Pdf::DEST_BROWSER;
        }

        if(!$filename) {
            $filename = $title.'.pdf';
        }

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => $destination,
            'filename' => $filename,
            // default Font
            'defaultFont' => '',
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '',
            // set mPDF properties on the fly
            'options' => [
                'title' => $title
            ],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' => $title,
            ],
        ]);

        return $pdf->render();
    }
}
