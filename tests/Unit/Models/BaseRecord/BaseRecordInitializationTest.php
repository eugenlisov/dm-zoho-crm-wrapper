<?php

namespace Tests\Unit\Models;

use Tests\CustomTestCase;
use Tests\TestDoubles\Spies\BaseRecord\BaseRecordInitializationSpy;

class BaseRecordInitializationTest extends CustomTestCase
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
