<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-traits
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package yii2-traits
 * @version 1.1.1
 */

namespace cinghie\traits;

use Yii;

/**
 * Trait AddressTrait
 *
 * @property string $latitude
 * @property string $longitude
 * @property string $street
 * @property string $number
 * @property string $postal_code
 * @property string $city
 * @property string $state
 * @property string $country
 */
trait AddressTrait
{

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['latitude', 'longitude'], 'number'],
			[['street'], 'string', 'max' => 255],
			[['number'], 'string', 'max' => 12],
			[['postal_code'], 'string', 'max' => 30],
			[['city', 'state', 'country'], 'string', 'max' => 50],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'latitude' => Yii::t('traits', 'Latitude'),
			'longitude' => Yii::t('traits', 'Longitude'),
			'street' => Yii::t('traits', 'Street'),
			'number' => Yii::t('traits', 'Number'),
			'postal_code' => Yii::t('traits', 'Postal Code'),
			'city' => Yii::t('traits', 'City'),
			'state' => Yii::t('traits', 'State'),
			'country' => Yii::t('traits', 'Country'),
		];
	}

}
