<?php

namespace Tests\TestDoubles\Spies\BaseRecord;

use DM_ZCRM\Models\BaseRecord;

class BaseRecordSpy extends BaseRecord {

	public static $spy = [];

	protected function initializeSdk() {
		static::$spy[] = 'initialized sdk';
		static::$sdkInitialized = true;
	}
	
}

