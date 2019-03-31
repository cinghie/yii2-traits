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

use Faker\Factory;

/**
 * Trait FakerTraits
 */
trait FakerTraits
{
	/** @var Factory */
	protected $faker;

	/**
	 * FakerTraits constructor.
	 */
	private function __construct() {
		$this->faker = Factory::create();
	}
}
