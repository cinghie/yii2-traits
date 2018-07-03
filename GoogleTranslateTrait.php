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
	 * @param string $text
	 * @param string $lang
	 *
	 * @return string
	 */
	public function getGoogleCloudTranslation($apiKey,$text,$lang)
	{
		// Instantiates a client
		$translate = new TranslateClient([
			'Key' => $apiKey,
			'targetLanguage' => $lang
		]);

		# Translates some text into Russian
		$translation = $translate->translate($text);

		return $translation['text'];
	}

}
