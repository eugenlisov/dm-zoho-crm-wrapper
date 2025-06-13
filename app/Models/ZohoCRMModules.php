<?php

namespace DM_ZCRM\Models;

use Exception;
use zcrmsdk\crm\crud\ZCRMInventoryLineItem;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\oauth\ZohoOAuth;
use zcrmsdk\crm\setup\users\ZCRMUser;

use \ZohoCore\Models\ZohoCRM\User;

// NOTE: This is copied from the RMA Plugin and adapted
// Uses the PHP SDK library provided by Zoho

class ZohoCRMModules extends ZohoCRM
{ // Not an abstract because we instantiate it at one point.

	// static $module; // NOTE: Plural. Ex: Accounts / Contacts / Potentials / Quotes / Leads/ Must be defined in child, before the __construct runs.
	static $module_instance;


	public function __construct()
	{

		if (empty(static::$module)) return false;
		parent::__construct();

		static::$module_instance = ZCRMModule::getInstance(static::$module);
	}


	/**
	 * Likely the most direct method to retrieve a particular record from the CRM by its ID.
	 * Ex:
	 * Quote::get($quote_id);
	 * Product::get($product_id);
	 * Account::get($account_id);
	 *  $args -> here we can specify to load the quote_owner.
	 *  Props:
	 *  - load_quote_owner Boolean.
	 *
	 * @param  string $quote_id [description]
	 * @return [type]           [description]
	 */
	public static function get($record_id = '', $args = [])
	{

		static::new();

		try {
			$apiResponse = static::$module_instance->getRecord($record_id);
		} catch (\Exception $e) {
			// return [];
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return [];
		}

		// NOTE: we do getData() twice because the first one returns a ZCRMRecord Object
		$record = $apiResponse->getData();

		$return = static::extract_record_details($record, $args);

		return $return;
	}

	public static function delete($recordId)
	{
		static::new();

		try {
			$apiResponse = static::$module_instance->getRecord($recordId);
			$response = $apiResponse->getData()->delete();
		} catch (\Exception $e) {
			// return [];
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return [];
		}

		$json = $response->getResponseJSON();
		if ($json['data'][0])
			return $json['data'][0];

		return $json;
	}


	public static function getFields()
	{ // Used the the LayoutFieldsExtractor in Lead

		static::new();

		try {
			// $apiResponse = static::$module_instance -> getFields();
			$apiResponse = static::$module_instance->getAllFields();
		} catch (\Exception $e) {
			return [];
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return [];
		}

		$fields = $apiResponse->getData();

		$processed_fields = [];

		foreach ($fields as $key => $field) {

			$options = $field->getPickListFieldValues();
			if ($options) {
				foreach ($options as $key => $value) {
					$options[$key] = $value->getActualValue();
				}
			}

			if (static::isIndustriesLookupField($field)) {
				$options = Industries::getIndustriesIdValueList();
			}

			$extracted = [
				'section'      => 'No Section Data',
				'label'        => $field->getApiName(),
				'type'         => $field->getDataType(),
				'required'     => '',
				'readOnly'     => '',
				'maxLength'    => '',
				'options'      => $options,
				'customField'  => '',
				'hidden'       => '',
				'customClass'  => '',
				'customId'     => '',
				'visibleLabel' => $field->getFieldLabel(),
			];

			$processed_fields[] = $extracted;
		}

		return $processed_fields;
	}


	/**
	 * Extracts only the fields we actually need, not all.
	 * NB: There are a few extra methods used in the Quote model.
	 */
	protected static function extract_record_details($record = '', $args = [])
	{

		$return = $record->getData();

		// Two of the fields we need on all Record types
		$return['id']      = $record->getEntityId();
		$return['created'] = $record->getCreatedTime();
		$return['modified_at'] = $record->getModifiedTime();



		$return = static::extract_module_specific_details($record, $return, $args);

		return $return;
	}


	protected static function extract_module_specific_details($record = '', $return = '', $args = [])
	{
		// Define in child.
		return $return;
	}


	// $args = [
	//     'page' => $page,
	//     'per_page' => $batch_size,
	//     'fields' => 'Last_Name,Email'
	// ];
	public static function list($args = [])
	{

		static::new();
		// dd(static::$module_instance);

		try {
			$bulkAPIResponse = static::$module_instance->getRecords($args); // Documentation here: https://www.zoho.com/crm/developer/docs/api/get-records.html

			$records = $bulkAPIResponse->getData(); // $records - array of ZCRMRecord instances.
		} catch (Exception $e) { // TODO do a better job handling this. ZCRMException should be used.
			echo $e->getMessage();
			return [];
		}

		// Transform the array of ZCRMRecord into a simple "array of array" with the record ID as index.
		$return = [];
		foreach ($records as $key => $record) {
			$id      = $record->getEntityId();
			$created = $record->getCreatedTime();

			$return[$id] = $record->getData();

			// For products we need to extract the products in a particular format.
			if (static::$module == 'Quotes') {
				$return[$id]['products'] = static::extract_products($record);
				$return[$id] = static::calculateTotal($return[$id]);
				$return[$id]['dm_contact_id'] = static::extract_contact_id($record); // NB: Need to extract this because searching for a Books Contact doesn't work just with the CRM Account ID.
			}

			// Used in the RMAManagement
			if (!empty($return[$id]['Original_Quote'])) {
				$return[$id]['quote_id'] = $return[$id]['Original_Quote']->getEntityId();
				unset($return[$id]['Original_Quote']);
			}

			// Attach the Records ID and Created times.
			$return[$id]['id'] = $id;
			if (! empty($created)) {
				$return[$id]['created'] = $created;
			}
		}

		return $return;
	}

	/**
	 * $properties are added as key -> value
	 */
	public static function update($record_id = false, $properties = [])
	{

		static::new();

		if (empty($record_id)) return false;
		if (empty($properties)) return false;


		$record = ZCRMRecord::getInstance(static::$module, null);

		$record->setFieldValue('id', $record_id);
		foreach ($properties  as $key => $value) {
			if ($key == 'products') continue;
			$record->setFieldValue($key, $value);
		}

		$productDetails = [];

		$record->Product_Details = $productDetails;

		$record->setEntityId($record_id);

		// $recordsArray - array of ZCRMRecord instances filled with required data for creation.
		$bulkAPIResponse = static::$module_instance->updateRecords([$record]);
		$entityResponses = $bulkAPIResponse->getEntityResponses();

		foreach ($entityResponses as $entityResponse) {
			if ("success" == $entityResponse->getStatus()) {

				$createdRecordInstance = $entityResponse->getData();

				$return = $createdRecordInstance->getData();
				$return['id'] = $createdRecordInstance->getEntityId();
			} else {

				$return = [
					'status'         => $entityResponse->getStatus(),
					'code'           => $entityResponse->getCode(),
					'message'        => $entityResponse->getMessage(),
					'custom_message' => 'The Record was not generated. See if you can fix the issue and try again. Otherwise, please contact the developer with details about this issue to look into it.',
				];
			}
		}

		// Force trigger the workflow; TODO: Check that it really works
		$record->update(['workflow']);

		return $return;
	}

	// Copied from ZohoBooks plugin. It's slightly different from the update() above, but not signifficantly
	public static function updateWithoutWf($record_id = false, $properties = [])
	{

		if (empty($record_id)) return false;
		if (empty($properties)) return false;

		static::new();


		$record = ZCRMRecord::getInstance(static::$module, null);

		$record->setFieldValue('id', $record_id);
		foreach ($properties  as $key => $value) {
			$record->setFieldValue($key, $value);
		}

		// $recordsArray - array of ZCRMRecord instances filled with required data for creation.
		$trigger = [];
		$bulkAPIResponse = self::$module_instance->updateRecords([$record], $trigger);
		$entityResponses = $bulkAPIResponse->getEntityResponses();

		foreach ($entityResponses as $entityResponse) {
			if ("success" == $entityResponse->getStatus()) {

				$createdRecordInstance = $entityResponse->getData();

				$return = $createdRecordInstance->getData();
				$return['id'] = $createdRecordInstance->getEntityId();
			} else {

				$return = [
					'status'         => $entityResponse->getStatus(),
					'code'           => $entityResponse->getCode(),
					'message'        => $entityResponse->getMessage(),
					'custom_message' => 'The Quote was not generated. See if you can fix the issue and try again. Otherwise, please contact the developer with details about this issue to look into it.',
				];
			}
		}

		return $return;
	}

	// This instantiates the model so we can go ahead and create a Zoho CRM Record with it.
	public static function new($data = [])
	{
		return new static($data);
	}

	public static function create($data = [])
	{
		$instance = static::new();
		return $instance->createRecord($data);
	}


	public function createRecord($record_data)
	{

		$record = ZCRMRecord::getInstance(static::$module, null);

		foreach ($record_data as $field => $value) {
			//    if ( $field == 'products' ) continue;
			if ($field == 'sign') continue;
			$record->setFieldValue($field, $value);
		}


		// TEMP:
		//    TODO: Created addRecordSpecifficData() method
		if (static::$module == 'Quotes') {

			// Add the Estimate products to the Quote
			foreach ($record_data['products'] as $key => $product) {
				$product_line_item = static::build_product_line_item($product, $record_data['sign']);
				$record->addLineItem($product_line_item);
			}
		}


		$trigger = ['workflow'];
		$bulkAPIResponse = static::$module_instance->createRecords([$record], $trigger);

		$entityResponses = $bulkAPIResponse->getEntityResponses();

		foreach ($entityResponses as $entityResponse) {

			if ("success" == $entityResponse->getStatus()) {
				// echo "<br />Status:".$entityResponse->getStatus();
				// echo "<br />Message:".$entityResponse->getMessage();
				// echo "<br />Code:".$entityResponse->getCode();
				$createdRecordInstance = $entityResponse->getData();
				// echo "<br />EntityID:".$createdRecordInstance->getEntityId();
				// echo "<br />moduleAPIName:".$createdRecordInstance->getModuleAPIName();

				$data = $createdRecordInstance->getData();
				$data['id'] = $createdRecordInstance->getEntityId();
				$data['created'] = $createdRecordInstance->getCreatedTime();

				// Populate the Current Record Instance ID.
				$this->id = $createdRecordInstance->getEntityId();

				return $data;
			} else {

				$return = [
					'status'         => $entityResponse->getStatus(),
					'code'           => $entityResponse->getCode(),
					'message'        => $entityResponse->getMessage(),
					'custom_message' => 'The Record was not generated. See if you can fix the issue and try again. Otherwise, please contact the developer with details about this issue to look into it.',
				];

				return $return;
			}
		}


		// TEMP: For now, just silently assume this generates the record in Zoho correctly.
	}



	/**
	 * Searches for multiple criteria
	 * Example use:
	 * Account::search( ['Main_Contact_Email' => 'wisam+test2@gofleet.ca', 'Shipping_City' => 'Mississauga'] )
	 */
	public static function search($criteria = null, $operator = 'and')
	{
		if (empty($criteria)) return [];

		// If it's a string, call find() directly.
		if (is_string($criteria)) {
			return static::find($criteria);
		}

		// If it's an array, we need to turn critiria into a string.
		$criteria_array = [];
		foreach ($criteria as $field => $value) {
			$criteria_array[] = '(' . implode(":equals:", [$field, $value]) . ')';
		}
		$criteria_string = implode(" $operator ", $criteria_array);

		$results = static::find($criteria_string);

		return $results;
	}


	private static function find($criteria_string)
	{

		// dd($criteria_string);

		// Only this initializes the model and runs the construction on the parent.
		$instance = static::new();

		try {
			// Do the search
			$bulkAPIResponse = $instance::$module_instance->searchRecordsByCriteria($criteria_string);
		} catch (\Exception $e) {
			return [];
			// echo 'Caught exception: ',  $e->getMessage(), "\n";
		}


		$records = $bulkAPIResponse->getData(); // $records - array of ZCRMRecord instances.
		if (empty($records)) return [];

		// Transform the array of ZCRMRecord into a simple "array of array" with the record ID as index.
		$return = [];
		foreach ($records as $key => $record) {

			$id      = $record->getEntityId();
			$created = $record->getCreatedTime();

			$return[$key] = $record->getData();

			// For products we need to extract the products in a particular format.
			if (static::$module == 'Quotes') {
				$return[$key]['products'] = static::extract_products($record);

				$return[$key] = static::calculateTotal($return[$key]);
			}

			// Attach the Records ID and Created times.
			$return[$key]['id'] = $id;
			$return[$key]['created'] = $created;
		}

		return $return;
	}





	/**
	 * Turns a product array from the Estimate into a lineItem which is then passes into the Quote.
	 * @param  [type] $product [description]
	 * @return [type]          [description]
	 * TODO: Move to Quote
	 */
	private static function build_product_line_item($product, $sign = 1)
	{

		$productInstance = ZCRMRecord::getInstance("Products", $product['id']);

		$lineItem = ZCRMInventoryLineItem::getInstance($productInstance);
		$lineItem->setId($product['id']);
		$lineItem->setQuantity($product['quantity']);
		$lineItem->setTotal($sign * $product['total']);
		$lineItem->setListPrice($sign * $product['price']);
		$lineItem->setDescription($product['description']);


		return $lineItem;
	}

	public static function isIndustriesLookupField($field)
	{
		if ($field->getDataType() != 'lookup') return false;

		$lookupField = $field->getLookupField();

		return ($lookupField->getModule() == 'Training_Modules');
	}
}
