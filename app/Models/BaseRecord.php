<?php
namespace DM_ZCRM\Models;

use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\Record;
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

		static::$recordInstance->record = static::$recordInstance->extractRawRecord(static::$recordInstance->raw_record);

		// static::$recordInstance->extractGeneralData();
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

	public function extractCreatedTimestamp() {
		$this->record['id'] = $this->raw_record->getId(); // Already exists.
		$this->record['created'] = $this->raw_record->getCreatedTime()->format('Y-m-d H:i:s');;
	}

	public function extractRawRecord($rawRecord) {
		$rawArray = $rawRecord->getKeyValues();

		$processedArray = $rawArray;

		foreach ($processedArray as $key => $value) {
			if ($this->hasDollar($key)) {
				unset($processedArray[$key]);
				continue;
			}
			// Ignore Module Speciffic fields
			if (in_array($key, $this->specifficFields)) {
				$processedArray[$key] == $value;
				continue;
			}
			$processedArray[$key] = $this->extractProcessedField($value);
		}
		return $processedArray;
	}

	// public function extractGeneralData() {
	// 	foreach ($this->record as $key => $fieldValue) {
	// 		if ($this->hasDollar($key)) {
	// 			unset($this->record[$key]);
	// 			continue;
	// 		}

	// 		$this->record[$key] = $this->extractProcessedField($fieldValue);
	// 	}
	// }

	private function extractProcessedField($fieldValue) {
		if ($fieldValue instanceof Record) {
			return $this->extractRawRecord($fieldValue);
		}

		if ($fieldValue instanceof Choice) {
			return $fieldValue->getValue();
		}

		if ($fieldValue instanceof DateTime) {
			return $fieldValue->format('Y-m-d H:i:s');
		}

		if (is_array($fieldValue)) {
			$dataArray = $fieldValue;
			foreach ($dataArray as $keyData => $value) {
				if ($this->hasDollar($keyData)) {
					unset($this->record[$keyData]);
					continue;
				}
				$dataArray[$keyData] =  $this->extractProcessedField($value);
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
