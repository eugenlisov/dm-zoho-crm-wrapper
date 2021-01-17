<?php

namespace Tests\TestDoubles\Models\BaseRecord;

use com\zoho\crm\api\record\Field;
use com\zoho\crm\api\record\Record;
use com\zoho\crm\api\util\Choice;
use DateTime;
use DM_ZCRM\Models\BaseRecord;

class BaseRecordTestDouble extends BaseRecord {
	public function __construct() {
		$this->raw_record = $this->rawSimpleRecord();
	}

	public function rawSimpleRecord() {
		$record = new Record;

		// Add Some Choice Fields
		$currency = new Choice('CAD');
		$record->addFieldValue(new Field('Currency'), $currency);

		// Add Some Text Fields
		$record->addFieldValue(new Field('Shipping_Code'), 'Custom Shipping Code');
		$record->addFieldValue(new Field('Billing_City'), 'Custom City');

		// Add a DateTime field
		$record->addFieldValue(new Field('Some_Date'), new DateTime('now'));
		

		return $record;
	}
	
	
}

