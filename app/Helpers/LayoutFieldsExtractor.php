<?php
namespace DM_ZCRM\Helpers;

use DM_ZCRM\Models\Industries;

class LayoutFieldsExtractor {
	private $layout;
	private $sections;
	private $fields = [];

	public function __construct($layout) {
		$this->layout = $layout;

		$this->extractSections();
		$this->extractFields();
	}

	private function extractSections() {
		$this->sections = $this->layout->getSections();
	}

	private function extractFields() {
		foreach ($this->sections as $key => $section) {
			$this->extractFieldsFromSection($section);
		}
	}

	private function extractFieldsFromSection($section) {
		$sectionDisplayName = $section->getDisplayName();

		$fields = $section->getFields();

		foreach ($fields as $key => $field) {
			$this->extractFieldData($field, $sectionDisplayName);
		}

	}

	private function extractFieldData($field, $sectionDisplayName) {
		$options = null;
		if ($rawOptions = $this->isPickList($field)) {
			$options = $this->turnRawOptionsToSimpleList($rawOptions);
		}

		if ($this->isIndustriesLookupField($field)) {
			$options = Industries::getIndustriesIdValueList();
		}
		
		$fieldId = 'ID_' . $field->getId();
		$presentableField = [
			'id'           => $fieldId,
			'section'      => $sectionDisplayName,
			'label'        => $field -> getApiName(),
			'visibleLabel' => $field -> getFieldLabel(),
			'type'         => $field -> getDataType(),
			'required'     => $field -> isMandatory(),
			'readOnly'     => $field -> isReadOnly(),
			'maxLength'    => '',
			'customField'  => '',
			'hidden'       => '',
			'customClass'  => '',
			'customId'     => '',
			
		];
		if (!empty($options)) {
			$presentableField['options'] = $options;
		}
		$this->fields[$fieldId] = $presentableField;
	}

	public function getFields() {
		return $this->fields;
	}

	private function isPickList($field) {
		$rawOptions = $field -> getPickListFieldValues();
		if (empty($rawOptions)) return false;

		return $rawOptions;
	}

	private function turnRawOptionsToSimpleList($rawOptions) {
		$return = [];
		foreach ($rawOptions as $key => $value) {
			$return[$key] = $value -> getActualValue();
		}
		return $return;
	}

	private function isIndustriesLookupField($field) {
		if ($field -> getDataType() != 'lookup') return false;

		$lookupField = $field->getLookupField();

		return ($lookupField->getModule() == 'Training_Modules');

	}
	
}
