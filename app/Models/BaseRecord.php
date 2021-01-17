<?php
namespace DM_ZCRM\Models;

use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\util\Choice;
use DateTime;
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

		static::$recordInstance->extractKeyValues();
		static::$recordInstance->extractGeneralData();
		static::$recordInstance->extractCreatedTimestamp();

		echo '<pre>';
		print_r( static::$recordInstance->record );
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

	// Turns the Raw Record object into an array which we can further process with smaller methods.
	public function extractKeyValues() {
		$this->record = $this->raw_record->getKeyValues();
	}

	public function extractCreatedTimestamp() {
		// $this->record['id'] = $this->raw_record->getId(); // Already exists.
		$this->record['created'] = $this->raw_record->getCreatedTime()->format('Y-m-d H:i:s');;
	}

	public function extractGeneralData() {
		foreach ($this->record as $key => $fieldValue) {
			if ($this->hasDollar($key)) {
				unset($this->record[$key]);
				continue;
			}

			$this->record[$key] = $this->extractProcessedField($fieldValue);
		}
	}

	private function extractProcessedField($fieldValue) {
		if ($fieldValue instanceof Choice) {
			return $fieldValue->getValue();
		}

		if ($fieldValue instanceof DateTime) {
			return $fieldValue->format('Y-m-d H:i:s');
		}

		if (is_array($fieldValue)) {
			$dataArray = $fieldValue;
			foreach ($dataArray as $keyData => $value) {
				if ($value instanceof Choice) {
					$dataArray[$keyData] = $value->getValue();
				}
			}
			return $dataArray;
		}

		return $fieldValue;
	}

	private function hasDollar($string) {
		$firstLetter = substr($string, 0, 1);

		return $firstLetter == '$';
	}
}
