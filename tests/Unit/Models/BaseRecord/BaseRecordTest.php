<?php

namespace Tests\Unit\Models\BaseRecord;

use Tests\CustomTestCase;
use Tests\TestDoubles\Models\BaseRecord\BaseRecordTestDouble;

class BaseRecordTest extends CustomTestCase
{

	public function testGivenChoiceValues_ItExtractsGeneralData() {
		$baseRecord = new BaseRecordTestDouble;
		$baseRecord->raw_record = $baseRecord->rawSimpleRecord();
		
		$baseRecord->record = $baseRecord->extractRawRecord($baseRecord->raw_record);

		// Test Choinces are extracted
		$this->assertEquals('CAD', $baseRecord->record['Currency']);

		// Test Text field are extracted
		$this->assertEquals('Custom Shipping Code', $baseRecord->record['Shipping_Code']);
		$this->assertEquals('Custom City', $baseRecord->record['Billing_City']);

		// Test DateTime field are extracted
		$this->assertTrue(is_string($baseRecord->record['Some_Date']));
	}

	public function testGivenArrayOfRecordObjects_ItExtractsEach() {
		$baseRecord = new BaseRecordTestDouble;
		$baseRecord->raw_record = $baseRecord->rawSimpleRmaRecord();

		// dd($baseRecord->raw_record);
		
		$baseRecord->extractKeyValues();
		$baseRecord->extractGeneralData();

		$expectedResult = [
			'Ship_To' => [
				0 => [
					'Account' => [
						'name' => 'CR&R Waste',
						'id' => "123123123",
					],
					'Postal_Code' => '92708',
				]
			]
		];

		$this->assertEquals($expectedResult, $baseRecord->extractRawRecord($baseRecord->raw_record));
		// return;
		// dd($baseRecord->extractRawRecord($baseRecord->raw_record));

		// dd($baseRecord->record);

		// // Test Choinces are extracted
		// $this->assertEquals('CAD', $baseRecord->record['Currency']);

		// // Test Text field are extracted
		// $this->assertEquals('Custom Shipping Code', $baseRecord->record['Shipping_Code']);
		// $this->assertEquals('Custom City', $baseRecord->record['Billing_City']);

		// // Test DateTime field are extracted
		// $this->assertTrue(is_string($baseRecord->record['Some_Date']));
	}

}
