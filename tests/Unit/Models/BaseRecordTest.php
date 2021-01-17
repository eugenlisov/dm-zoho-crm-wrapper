<?php

namespace Tests\Unit\Models;

use com\zoho\crm\api\record\InventoryLineItems;
use com\zoho\crm\api\record\LineItemProduct;
use DM_ZCRM\Models\BaseRecord;
use DM_ZCRM\Models\Quote;
use Tests\CustomTestCase;
use Tests\TestDoubles\Spies\BaseRecordInitializationSpy;

class BaseRecordTest extends CustomTestCase
{

	public function testItInitializesTheSdk() {
		BaseRecordInitializationSpy::$sdkInitialized = false;
		BaseRecordInitializationSpy::$spy = [];

		new BaseRecordInitializationSpy;

		$this->assertTrue(BaseRecordInitializationSpy::$sdkInitialized);
	}

	public function testItOnlyInitializesTheSdkOnlyOnce() {
		BaseRecordInitializationSpy::$sdkInitialized = false;
		BaseRecordInitializationSpy::$spy = [];

		new BaseRecordInitializationSpy;
		new BaseRecordInitializationSpy;
		new BaseRecordInitializationSpy;
		new BaseRecordInitializationSpy;
		$expectedSpy = ['initialized sdk'];

		$this->assertTrue(BaseRecordInitializationSpy::$sdkInitialized);
		$this->assertEquals($expectedSpy, BaseRecordInitializationSpy::$spy);
		
	}
	
}
