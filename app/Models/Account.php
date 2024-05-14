<?php

namespace DM_ZCRM\Models;

class Account extends ZohoCRMModules {

    static $module = 'Accounts';

	protected static function extract_module_specific_details( $record = '', $return = '', $args = []) {
		$rawData = $record->getData();
		$return['dm_price_list_id'] = ($rawData['Price_List']) 
										? $rawData['Price_List']->getEntityId() 
										: null;

		return $return;
    }

}
