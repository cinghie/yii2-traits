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

/**
 * SequentialTrait
 */
trait SequentialTrait
{
	/**
	 * Generate Sequential Code
	 *
	 * @param int $number
	 * @param string $prefix
	 * @param string $sequence
	 *
	 * @return string
	 */
	public function generateSequentialCode($number, $prefix ='A', $sequence = '00000000') {
		return $prefix.substr($sequence,0, strlen($sequence) - strlen($number)).$number;
	}
}
