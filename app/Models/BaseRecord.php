<?php
namespace DM_ZCRM\Models;

use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\RecordOperations;
use DM_ZCRM\SDK\Initialize;

class BaseRecord {
	public static $sdkInitialized = false;
	public static $recordInstance;

	public function __construct() {
		if (!static::$sdkInitialized) {
			$this->initializeSdk();
		}
	}

	protected function initializeSdk() {
		Initialize::initialize();
		static::$sdkInitialized = true;
	}

	public static function get($recordId = '') {
		static::$recordInstance = new static();
		static::$recordInstance->getRawRecord($recordId);

		static::$recordInstance->extractGeneralData();

		echo '<pre>';
		print_r( static::$recordInstance );
		echo '</pre>';
	}

	public function getRawRecord($recordId = '') {
		$recordOperations = new RecordOperations();
		$paramInstance = new ParameterMap();
		$headerInstance = new HeaderMap();

		$response = $recordOperations->getRecord($recordId, $this->module, $paramInstance, $headerInstance);

		if (empty($response)) {
			$this->raw_record = [];
			return [];
		};

		$responseHandler = $response->getObject();
		$records = $responseHandler->getData();

		if (empty($records[0])) {
			$this->raw_record = [];
			return [];
		};
		
		$this->raw_record = $records[0];
		return $records[0];
	}

	public function extractGeneralData() {
		
	}
}
