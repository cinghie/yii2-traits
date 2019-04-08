<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.2.2
 */

namespace cinghie\traits;

use Yii;
use Faker\Factory;
use Faker\Generator;

/**
 * Trait FakerTraits
 *
 * @see https://github.com/fzaninotto/Faker
 */
trait FakerTraits
{
	/**
	 * Get Faker Class
	 *
	 * @param string $locale
	 *
	 * @return Generator
	 * @see https://github.com/fzaninotto/Faker#basic-usage
	 */
	public static function getInstance($locale = '')
	{
		$locale = $locale ?: Yii::$app->sourceLanguage;

		return Factory::create($locale);
	}

	/**
	 * Get Credit Card array
	 *
	 * @return array
	 * @see https://github.com/fzaninotto/Faker#fakerproviderpayment
	 */
	public static function getCreditCard()
	{
		$faker = self::getInstance();
		$creditCard = $faker->creditCardDetails;

		$creditCardName = explode(' ',$creditCard['name']);
		$creditCard['firstname'] = $creditCardName[0];
		$creditCard['lastname'] = $creditCardName[1];

		$creditCard['cvv2'] = (string)$faker->randomNumber(3,true);

		$creditCardDate = explode('/',$creditCard['expirationDate']);
		$creditCard['month'] = $creditCardDate[0];
		$creditCard['year'] = $creditCardDate[1];

		$creditCard['type'] = mb_strtolower($creditCard['type']);

		return $creditCard;
	}
}
