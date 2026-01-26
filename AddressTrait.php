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

use Exception;
use RuntimeException;
use Yii;

/**
 * Trait AddressTrait
 *
 * @property string $name
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
	public static function rules()
	{
		return [
			[['latitude', 'longitude'], 'number'],
			[['name','street'], 'string', 'max' => 255],
			[['number'], 'string', 'max' => 12],
			[['postal_code'], 'string', 'max' => 30],
			[['city', 'state', 'country'], 'string', 'max' => 50],
		];
	}

	/**
	 * @inheritdoc
	 * 
	 * Note: In PHP 8.1+, calling this method statically will generate a deprecation warning.
	 * It's recommended to use getAddressAttributeLabels() instance method instead.
	 * 
	 * @return array
	 */
	public static function attributeLabels()
	{
		return [
			'latitude' => Yii::t('traits', 'Latitude'),
			'longitude' => Yii::t('traits', 'Longitude'),
			'name' => Yii::t('traits', 'Name'),
			'street' => Yii::t('traits', 'Street'),
			'number' => Yii::t('traits', 'Number'),
			'postal_code' => Yii::t('traits', 'Postal Code'),
			'city' => Yii::t('traits', 'City'),
			'state' => Yii::t('traits', 'State'),
			'country' => Yii::t('traits', 'Country'),
		];
	}

	/**
	 * Instance method to get attribute labels without deprecation warning
	 * 
	 * @return array
	 */
	public function getAddressAttributeLabels()
	{
		return static::attributeLabels();
	}

	/**
	 * Get latitude and longitude from Google Maps API
	 *
	 * @param string $address
	 * @param string $key
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getLatLng($address, $key = null)
	{
		$settings = Yii::$app->settings;

		if($settings->get('googleMapsAPIKey', 'API')) {
			$apiKEY = $settings->get('googleMapsAPIKey', 'API');
		} elseif ($key) {
			$apiKEY = $key;
		} else {
			throw new RuntimeException(Yii::t('traits','Google Maps API KEY Missing'));
		}

		$latLng = array();
		$address = str_replace( array( ' ', '++' ), '+', $address );
		$geocode = file_get_contents( 'https://maps.google.com/maps/api/geocode/json?address=' . $address . '&sensor=false&key=' . $apiKEY);
		$output = json_decode( $geocode, false );

		if ($output->results) {
			$lat = $output->results[0]->geometry->location->lat;
			$lng = $output->results[0]->geometry->location->lng;
			$latLng['latitude']  = $lat;
			$latLng['longitude'] = $lng;
		}

		return $latLng;
	}
}
