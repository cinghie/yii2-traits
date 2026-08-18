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

use cinghie\traits\services\RuntimeConfig;
use Google\Cloud\Translate\TranslateClient;
use RuntimeException;
use Throwable;
use Yii;

/**
 * Trait GoogleTranslateTrait
 */
trait GoogleTranslateTrait
{
    /**
     * Get Translation from Google Cloud Translate.
     *
     * @param string $apiKey
     * @param string $lang
     * @param string $text
     *
     * @return string
     */
    public function getGoogleCloudTranslation($apiKey = '', $lang = '', $text = '')
    {
        if (!class_exists(TranslateClient::class)) {
            throw new RuntimeException(Yii::t(
                'traits',
                'Google Cloud Translate is not installed. Install a version compatible with your PHP runtime.'
            ));
        }

        if (!$apiKey) {
            $apiKey = (string)RuntimeConfig::get($this, 'googleTranslateApiKey', '', 'googleTranslateApiKey');
        }

        $lang = str_replace(['ch', 'pr'], ['zh', 'pt'], $lang);

        $translate = new TranslateClient([
            'key' => $apiKey,
        ]);

        if (!$text) {
            return '';
        }

        try {
            $translation = $translate->translate($text, [
                'target' => $lang,
            ]);

            return isset($translation['text']) ? (string)$translation['text'] : '';
        } catch (Throwable $e) {
            $message = $this->formatGoogleTranslateError($e);

            if (Yii::$app !== null && method_exists(Yii::$app, 'has') && Yii::$app->has('session')) {
                Yii::$app->session->setFlash('error', $message);
            }

            return '';
        }
    }

    /**
     * Convert Google JSON errors and ordinary transport/runtime exceptions into
     * a safe human-readable message without throwing a second exception.
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
