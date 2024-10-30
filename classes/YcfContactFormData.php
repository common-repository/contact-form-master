<?php
class YcfContactFormData {

	private $formId;

	public function setFormId($formId){

		$this->formId = $formId;
	}

	public function getFormId() {

		return $this->formId;
	}
	public static function params() {

	}

	public static function defaultsData() {

		$defaultsDataArray = array(
			'ycf-form-title' => '',
			'contact-form-send-to-email' => get_option('admin_email'),
			'contact-form-send-from-email' => get_option('admin_email'),
			'contact-form-send-email-subject' => 'Contact form',
			'ycf-message' => '<p>Hello!</p><p>This is your contact form data:</p><p>[form_data]</p>',
			'contact-form-width' => '100',
			'contact-form-width-measure' => '%'
		);

		return $defaultsDataArray;
	}

	public function getOptionValue($optionKey, $isBool = false) {

		$savedOptions = $this->getSavedData($this->getFormId());

		$defaultOptions = $this->defaultsData();

		if (isset($savedOptions[$optionKey])) {
			$elementValue = $savedOptions[$optionKey];
		}
		else if(!empty($savedOptions) && $isBool) {
			/*for checkbox elements when they does not exist in the saved data*/
			$elementValue = '';
		}
		else if(isset($defaultOptions[$optionKey])) {
			$elementValue =  $defaultOptions[$optionKey];
		}
		else {
			$elementValue = '';
		}

		if($isBool) {
			$elementValue = $this->boolCheck($elementValue);
		}

		return $elementValue;
	}

	public function deleteFormById($formId) {

		global $wpdb;
		$formId = (int)$formId;
		if($formId === 0) {
			return false;
		}

		$tableName = $wpdb->prefix.'ycf_form';
		$where = array(
			'form_id' => $formId
		);
		$whereFormat = array(
			'%d'
		);
		$wpdb->delete($tableName, $where, $whereFormat);
	}

	public static function getFormListById($formId) {

		global $wpdb;
		$formId = (int)$formId;
		$formData = array();

		$findByIdQuery = $wpdb->prepare("SELECT fields_data FROM ". $wpdb->prefix ."ycf_fields WHERE form_id = %d", $formId);
		$fieldsData = $wpdb->get_row($findByIdQuery, ARRAY_A);

		if(!isset($fieldsData)) {
			return $formData;
		}

		$formData = json_decode($fieldsData['fields_data'], true);
		
		return $formData;
	}

	public static function getAllData() {
		
		global $wpdb;

		$query = "SELECT * FROM ". $wpdb->prefix ."ycf_form ORDER BY form_id DESC";
		$forms = $wpdb->get_results($query, ARRAY_A);

		return $forms;
	}

	public static function getFormFieldsDataById($id) {

		global $wpdb;
		$findByIdQuery = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."ycf_fields WHERE form_id = %d", $id);
		$formData = $wpdb->get_row($findByIdQuery, ARRAY_A);

		return $formData;
	}

	public static function getContactFormDataById($id) {

		global $wpdb;
		$findByIdQuery = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."ycf_form WHERE form_id = %d", $id);
		$formData = $wpdb->get_row($findByIdQuery, ARRAY_A);

		return $formData;
	}

	public static function getSavedData($formId = null) {

		$data = array();
		if($formId === null && $formId == 0) {
			return $data;
		}

		$savedData = array();
		$data = self::getContactFormDataById($formId);
		$options = $data['options'];
		if(isset($options)) {
			$options = json_decode($options, true);
		}
		$savedData['id'] = $data['form_id'];
		$savedData['ycf-form-title'] = $data['title'];

		if(is_array($options)) {
			$savedData = array_merge($savedData, $options);
		}

		return $savedData;
	}

	public static function getFormListNameAndLabelsById($formId) {

		$labelNameData = array();
		$fieldsData = self::getFormListById($formId);

		if(empty($fieldsData)) {
			return $labelNameData;
		}

		foreach($fieldsData as $field) {
			/*submit button name must not be displayed inside the message*/
			if($field['type'] == 'submit') {
				continue;
			}

			$name = $field['name'];
			$label = $field['label'];
			$labelNameData[$label] = $name;
		}

		return json_encode($labelNameData);
	}

	public function boolCheck($var) {
		return ($var?'checked':'');
	}

	public static function formOptionValueData() {

		$data = array(
			0 => ['label' => 'One', 'value' => 'one', 'orderId' => 0, 'options'=> ''],
			1 => ['label' => 'Two', 'value' => 'two', 'orderId' => 1, 'options'=> ''],
			2 => ['label' => 'Three', 'value' => 'three', 'orderId' => 2, 'options'=> '']
		);

		return $data;
	}

	public static function getDataArrayFormDb() {

		$dbData = self::getAllData();
		$data['id'] = $dbData['id'];
		$data['type'] = $dbData['type'];
		$data['title'] = $dbData['title'];
		$data['width'] = $dbData['width'];
		$data['height'] = $dbData['height'];
		$data['duration'] = $dbData['duration'];

		return array_merge($data, $dbData);
	}

	public static function getOptionsData($id) {
		
		if(isset($id)) {
			return self::getAllData();
		}
		else {
			return self::defaultsData();
		}
	}
}