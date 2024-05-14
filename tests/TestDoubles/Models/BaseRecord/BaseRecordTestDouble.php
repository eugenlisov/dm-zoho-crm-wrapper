<?php

namespace Tests\TestDoubles\Models\BaseRecord;

use com\zoho\crm\api\record\Field;
use com\zoho\crm\api\record\Record;
use com\zoho\crm\api\util\Choice;
use DateTime;
use DM_ZCRM\Models\BaseRecord;

class BaseRecordTestDouble extends BaseRecord {
	public function __construct() {
		
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

	public function rawSimpleRmaRecord() {
		$record = new Record;

		$grandchildRecord = new Record;
		$grandchildRecord->addKeyValue('name', 'CR&R Waste');
		$grandchildRecord->setId('123123123');

		$childRecord = new Record;
		$childRecord->addFieldValue(new Field('Account'), $grandchildRecord);
		$childRecord->addFieldValue(new Field('Postal_Code'), '92708');


		$record->addFieldValue(new Field('Ship_To'), [$childRecord]);

		return $record;
	}
	
	
}

