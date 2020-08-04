<?php
namespace DM_ZCRM\Models;

class Industries extends ZohoCRMModules {

	// Find this here: https://crm.zoho.com/crm/org3130230/settings/api/modules (CRM Settings---->Setup---->Developer space----->APIs)
	static $module = 'Training_Modules';
	
	public static function getIndustriesIdValueList() {

		$industries = static::list();

		$return = [];
		foreach ($industries as $key => $industry) {
			$return[] = [
				'id' => $industry['id'],
				'name' => $industry['Name'],
			];
		}

		usort($return, function ($a, $b) {
			return $a['name'] <=> $b['name'];
		});

		return $return;

	}

}
