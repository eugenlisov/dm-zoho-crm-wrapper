<?php

namespace Tests\TestDoubles\Spies;

use DM_ZCRM\Models\BaseRecord;

class BaseRecordInitializationSpy extends BaseRecord {

	public static $spy = [];

	protected function initializeSdk() {
		static::$spy[] = 'initialized sdk';
		static::$sdkInitialized = true;
	}
	
}

