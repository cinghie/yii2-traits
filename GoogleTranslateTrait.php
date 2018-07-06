<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.0
 */

namespace cinghie\traits;

use Yii;
use Google\Cloud\Translate\TranslateClient;

/**
 * Trait GoogleTranslateTrait
 */
trait GoogleTranslateTrait
{

	/**
	 * Get Translation from Google Cloud Translate
	 *
	 * @param string $apiKey
	 * @param string $lang
	 * @param string $text
	 *
	 * @return string
	 */
	public function getGoogleCloudTranslation($apiKey = '',$lang,$text)
	{
		if(!$apiKey) {
			$apiKey = Yii::$app->controller->module->googleTranslateApiKey;
		}

		$translate = new TranslateClient([
			'key' => $apiKey
		]);

		if($text)
		{
			try {

				$translation = $translate->translate($text,[
					'target' => $lang
				]);

				return $translation['text'];

			} catch (\Exception $e) {

				$error = json_decode($e->getMessage())->error;
				$message = $error->status.' - Error '.$error->code.': '.$error->message;

				Yii::$app->session->setFlash('error', $message);

				return $error;
			}
		}

		return '';
	}

}
