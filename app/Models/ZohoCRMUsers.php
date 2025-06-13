<?php

namespace DM_ZCRM\Models;

use zcrmsdk\crm\setup\org\ZCRMOrganization;

// NOTE: This is copied from the RMA Plugin and adapted
// Uses the PHP SDK library provided by Zoho

class ZohoCRMUsers extends ZohoCRM
{ // Not an abstract because we instantiate it at one point.

	// static $module; // NOTE: Plural. Ex: Accounts / Contacts / Potentials / Quotes / Leads/ Must be defined in child, before the __construct runs.
	// static $module_instance;

	static $organization_instance;

	/**
	 * Initializez the new Zoho v.2.0 API.
	 */
	public function __construct()
	{
		parent::__construct();

		self::$organization_instance = ZCRMOrganization::getInstance();
	}


	/**
	 * Likely the most direct method to retrieve a particular record from the CRM by its ID.
	 * Ex:
	 * Quote::get($quote_id);
	 * Product::get($product_id);
	 * Account::get($account_id);
	 *
	 * @param  string $quote_id [description]
	 * @return [type]           [description]
	 */
	public static function get($record_id = '')
	{

		static::new();

		try {
			$bulkAPIResponse = self::$organization_instance->getUser($record_id);
		} catch (\ZCRMException $e) {
			// return [];
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		$user = $bulkAPIResponse->getData();
		$user = static::extract_user_details($user);

		return $user;
	}


	/**
	 * Extracts only the fields we actually need, not all.
	 * @param  string $user [description]
	 * @return Array       [description]
	 */
	public static function extract_user_details($user = '')
	{

		$return = [
			'id'        => $user->getId(),
			'full_name' => $user->getFullName(),
			'email'     => $user->getEmail(),
			'created'   => $user->getCreatedTime(),
		];

		return $return;
	}
}
