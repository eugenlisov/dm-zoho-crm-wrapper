<?php

namespace Tests\Unit\Models\BaseRecord;

use Tests\CustomTestCase;
use Tests\TestDoubles\Models\BaseRecord\BaseRecordTestDouble;

class BaseRecordTest extends CustomTestCase
{

	public function testGivenChoiceValues_ItExtractsGeneralData() {
		$baseRecord = new BaseRecordTestDouble;
		
		$baseRecord->extractKeyValues();
		$baseRecord->extractGeneralData();

		// Test Choinces are extracted
		$this->assertEquals('CAD', $baseRecord->record['Currency']);

		// Test Text field are extracted
		$this->assertEquals('Custom Shipping Code', $baseRecord->record['Shipping_Code']);
		$this->assertEquals('Custom City', $baseRecord->record['Billing_City']);

		// Test DateTime field are extracted
		$this->assertTrue(is_string($baseRecord->record['Some_Date']));
	}
}
