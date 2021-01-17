<?php
namespace DM_ZCRM\Models;

use DM_ZCRM\SDK\Initialize;

class BaseRecord {
	public static $sdkInitialized = false;

	public function __construct() {
		if (!static::$sdkInitialized) {
			$this->initializeSdk();
		}
	}

	protected function initializeSdk() {
		Initialize::initialize();
		static::$sdkInitialized = true;
	}
}
