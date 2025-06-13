<?php

namespace DM_ZCRM\Services;

/**
 * This Service is used when searching an Account by a Zip Code. 
 */
class ZipService
{

	/**
	 * Builds an criteria string ready to be plugged into the CRM search() method. 
	 * $address accepts: 'billing' / 'shipping  / 'both'
	 */
	public static function search_criteria($string, $address = 'both')
	{

		$variations = static::variations($string);
		$variations = $variations['variations'];

		$key_groups = [
			'billing'  => ['Billing_Code'],
			'shipping' => ['Shipping_Code'],
			'both'     => ['Billing_Code', 'Shipping_Code'],
		];

		$keys = $key_groups[$address];

		$criteria_array = [];
		foreach ($keys as $temp_key => $key) {

			foreach ($variations as $temp_key2 => $variation) {
				$criteria_array[] = '(' . implode(":equals:", [$key, $variation]) . ')';
			}
		}

		$criteria_string = implode(" or ", $criteria_array); // It is ok to use 'or' because it can't be all variatios at the same time.

		return $criteria_string;
	}


	/**
	 * Based on the number of letters in the string, it identifies the country ( US 5 / CA 6 ) and build up the variations.
	 * @param  String $string What the user types on the frontend
	 * @return Array         Always in this format: https://cl.ly/338ae2c65e60
	 * Array
	 * (
	 *     [country] => CA
	 *     [variations] => Array
	 *         (
	 *             [0] => 076443
	 *             [1] => 076-443
	 *             [2] => 076 443
	 *         )
	 * )
	 */
	public static function variations($string)
	{

		// Lower case everything
		$string = strtolower($string);
		// Remove all characters except alphanumeric values
		$string = preg_replace("/[^a-zA-Z0-9]+/", "", $string);

		$char_count = strlen($string);

		if ($char_count == 6) {
			$return = static::ca_variations($string);
		} else {
			$return = static::us_variations($string);
		}

		return $return;
	}


	private static function ca_variations($string)
	{
		$return['country'] = 'CA';

		$halves = str_split($string, 3);

		$variations[] = $string;
		$variations[] = $halves[0] . '-' . $halves[1];
		$variations[] = $halves[0] . ' ' . $halves[1];

		$return['variations'] = $variations;

		return $return;
	}


	/**
	 * NOTE: This returns an array of variations, not a simple string!
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	private static function us_variations($string)
	{
		$return['country'] = 'US';
		$return['variations'] = [$string];

		return $return;
	}
}
