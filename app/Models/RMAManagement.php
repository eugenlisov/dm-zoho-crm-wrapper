<?php

namespace DM_ZCRM\Models;

class RMAManagement extends BaseRecord {

	public $module = 'Sales_dashboard';
	
	protected static function extract_module_specific_details( $record = null, $return = [], $args = []) {

		if (!empty($return['RMA_Contact'])) {
			$return['contact_id'] = $return['RMA_Contact']->getEntityId();
			$return['contact_name'] = $return['RMA_Contact']->getLookupLabel();	
		}
		

		$return['vendor_id'] = $return['Return_Vendor']->getEntityId();
		$return['created_by'] = $record->getCreatedBy()->getId();
		
		if (!empty($return['Original_Quote'])) {
			$return['quote_id'] = $return['Original_Quote']->getEntityId();
		}
        return $return;
    }

}
