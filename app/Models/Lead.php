<?php

namespace DM_ZCRM\Models;

use DM_ZCRM\Helpers\LayoutFieldsExtractor;

class Lead extends ZohoCRMModules
{
	static $module = 'Leads';

	/**
	 * Loads the Standard Layout from the CRM and extracts the Fields off it, while making sure to also populate the Section Name
	 * 
	 * $layouts = get_option('dm_test_layouts');
	 * 	if (count($layouts) < 1) return [];
	 * 	$standardLayout = $layouts[0];
	 */
	public static function getFieldsFromStandardLayout()
	{
		$standardLayout = static::getStandardLayout();

		$fieldsExtractor = new LayoutFieldsExtractor($standardLayout);
		return $fieldsExtractor->getFields();
	}

	// This assumes there is only one layout and it's the Standard one. 
	// There are now dedicated API calls to load the lyout directly, so this will do for now.
	private static function getStandardLayout()
	{
		static::new();

		try {
			$apiResponse = static::$module_instance->getAllLayouts();
		} catch (\Exception $e) {
			// return [];
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return [];
		}

		$layouts = $apiResponse->getData();
		if (count($layouts) < 1) return [];

		return $layouts[0];
	}
}
