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

use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextRequest;
use Yii;
use cinghie\traits\services\RuntimeConfig;
use RuntimeException;
use Throwable;

/**
 * Trait GoogleTranslateTrait
 */
trait GoogleTranslateTrait
{
    /**
     * Get a translation using Google Cloud Translation V3.
     *
     * The first parameter is retained for backwards compatibility. Current
     * Google Cloud Translation releases authenticate through Application
     * Default Credentials; configure `googleTranslateProjectId` in yii2Traits.
     *
     * @param string $apiKey Legacy API-key parameter, retained for compatibility.
     * @param string $lang Target language code.
     * @param string $text Text to translate.
     * @return string
     */
    public function getGoogleCloudTranslation($apiKey = '', $lang = '', $text = '')
    {
        if (!class_exists(TranslationServiceClient::class) || !class_exists(TranslateTextRequest::class)) {
            throw new RuntimeException(Yii::t(
                'traits',
                'Google Cloud Translate runtime dependency is not available.'
            ));
        }

        if ($text === '') {
            return '';
        }

        $projectId = (string)RuntimeConfig::get(
            $this,
            'googleTranslateProjectId',
            '',
            'googleTranslateProjectId'
        );

        if ($projectId === '') {
            throw new RuntimeException(Yii::t(
                'traits',
                'Google Cloud Translate requires googleTranslateProjectId and Application Default Credentials.'
            ));
        }

        $lang = str_replace(['ch', 'pr'], ['zh', 'pt'], $lang);

        try {
            $translate = new TranslationServiceClient();
            $request = (new TranslateTextRequest())
                ->setParent(TranslationServiceClient::locationName($projectId, 'global'))
                ->setTargetLanguageCode($lang)
                ->setContents([$text]);

            $response = $translate->translateText($request);
            $translations = $response->getTranslations();

            if (method_exists($translate, 'close')) {
                $translate->close();
            }

            return isset($translations[0])
                ? (string)$translations[0]->getTranslatedText()
                : '';
        } catch (Throwable $e) {
            $message = $this->formatGoogleTranslateError($e);

            if (Yii::$app !== null && method_exists(Yii::$app, 'has') && Yii::$app->has('session')) {
                Yii::$app->session->setFlash('error', $message);
            }

            return '';
        }
    }

    /**
     * Convert Google and transport errors into a safe readable message.
     *
     * @param Throwable $e
     * @return string
     */
    protected function formatGoogleTranslateError(Throwable $e)
    {
        $decoded = json_decode($e->getMessage());
        if (is_object($decoded) && isset($decoded->error) && is_object($decoded->error)) {
            $status = isset($decoded->error->status) ? $decoded->error->status : 'UNKNOWN';
            $code = isset($decoded->error->code) ? $decoded->error->code : $e->getCode();
            $message = isset($decoded->error->message) ? $decoded->error->message : $e->getMessage();

            return $status . ' - Error ' . $code . ': ' . $message;
        }

        return $e->getMessage() !== ''
            ? $e->getMessage()
            : Yii::t('traits', 'Google Cloud Translate request failed.');
    }
}
