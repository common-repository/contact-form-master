<?php
class YcfAjax {

	public function __construct() {

		$this->init();
	}

	public function init() {

		add_action('wp_ajax_delete_contact_form', array($this, 'ycfDeleteContactForm'));
		add_action('wp_ajax_shape-form-element', array($this, 'YcfShapeElementsList'));
		add_action('wp_ajax_change-element-data', array($this, 'ycfChangeElementData'));
		add_action('wp_ajax_remove_element_from_list', array($this, 'YcfElementRemoveFromList'));
		add_action('wp_ajax_element_option_data', array($this, 'elementOptionData'));
		add_action('wp_ajax_delete_sub_option', array($this, 'deleteOptionData'));
		add_action('wp_ajax_add_sub_option-option', array($this, 'addSubOptionOption'));
		add_action('wp_ajax_mailchimp-fields', array($this, 'mailchimpFields'));
		add_action('wp_ajax_mailchimp_fields_refresh', array($this, 'refreshFields'));
	}

	public function ycfDeleteContactForm() {

		$postData = $_POST;
		$formType = '';
		if(!isset($postData)) {
			return false;
		}

		$formId = (int)$postData['formId'];

		if($formId == 0) {
			return false;
		}
		if(!empty($postData['formType'])) {
			$formType = $postData['formType'];
		}

		$formDataObj = YcfForm::createFormTypeObj($formType);
		if(empty($formDataObj)) {
			die();
		}
		$formDataObj->deleteFormById($formId);
		return 0;
	}

	public function ycfChangeElementData() {

		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');

		$elementData = $_POST['editElementData'];
		$formId = sanitize_text_field($elementData['formCurrentId']);
		$changedElementId = sanitize_text_field($elementData['changedElementId']);
		$changedValue = sanitize_text_field($elementData['changedElementValue']);
		$changedKey = sanitize_text_field($elementData['changedElementKey']);

		if($formId == 0) {
			$formListData = get_option('YcfFormDraft');
		}
		else {
			$formListData = YcfForm::getFormListById($formId);
		}

		if(is_array($formListData) && !empty($formListData)) {
			foreach($formListData as $key => $currentListFieldData) {
				if($currentListFieldData['id'] == $changedElementId) {
					$formListData[$key][$changedKey] = $changedValue;
				}
			}
		}

		update_option('YcfFormDraft', $formListData);
	}

	public function YcfElementRemoveFromList() {

		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');

		$elementData = $_POST['removeElementData'];
		$elementId = $elementData['id'];
		$draftElements = get_option('YcfFormDraft');

		foreach ($draftElements as $key => $draftElement) {
			if($elementId == $draftElement['id']) {
				unset($draftElements[$key]);
			}
		}

		update_option('YcfFormDraft', $draftElements);
		echo '1';
		die();
	}

	public function addElementsToList($formElement, $contactFormId) {

		if($contactFormId == 0) {
			$formListData = get_option('YcfFormDraft');
		}
		else {
			$formListData = YcfForm::getFormListById($contactFormId);
		}

		$formSize = sizeof($formListData);

		array_splice($formListData, $formSize, 0, array($formElement));

		update_option('YcfFormDraft', $formListData);
	}

	public function YcfShapeElementsList() {

		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');
		$dataArray = get_option('YcfFormElements');
		$formElement = $_POST['formElements'];
		$contactFormId = (int)$_POST['contactFormId'];
		$formType = $_POST['formType'];

		$formDataObj = YcfForm::createFormTypeObj($formType);
		$currentElement = $formDataObj->getFormFieldData($formElement);

		//$currentElement = $formDataObj->getAddedFieldOptions($_POST)
		if($_POST['modification'] == 'add-element') {
			$this->addElementsToList($currentElement, $contactFormId);
		}

		$formElementId = $formElement['id'];

		if(!get_option('YcfFormElements')) {
			$dataArray = array();
		}

		$args['oderId'] = $formElement['orderNumber'];

		$element = YcfFunctions::createAdminViewHtml($currentElement, $args);
		echo $element;
		die();
	}

	public function addHiddenAccordionDiv($formElement) {
		$elementId = $formElement['id'];
		ob_start();
		?>
		<div class="ycf-element-options-wrapper ycf-hide-element">
			<div class="ycf-sub-option-wrapper">
				<span class="element-option-sub-label">Label</span>
				<input type="text" class="element-label"  value="<?php echo $formElement['label'];?>" data-id="<?php echo $elementId;?>">
			</div>
			<div class="ycf-sub-option-wrapper">
				<span class="element-option-sub-label">Name</span>
				<input type="text" class="element-name" value="<?php echo $formElement['name']; ?>">
			</div>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function deleteOptionData() {

		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');
		
		$formId  = sanitize_text_field($_POST['contactFormId']);
		$elementId = sanitize_text_field($_POST['elementId']);
		$elementType = sanitize_text_field($_POST['elementType']);
		$elementOrderId = sanitize_text_field($_POST['elementOrderId']);
		$elementName = sanitize_text_field($_POST['elementName']);
		$elementValue = sanitize_text_field($_POST['elementValue']);
		$modificationType = sanitize_text_field($_POST['modificationType']);

		$elementOptions = $this->getElementOptionsById($formId, $elementId);

		$fieldOptions =  json_decode($elementOptions['fieldsOptions'], true);
		$fieldsOrder =  json_decode($elementOptions['fieldsOrder'], true);
		$modifiedOptions = $fieldOptions;

		foreach($fieldOptions as $key => $field) {
			if($field['orderId'] == $elementOrderId) {
				unset($modifiedOptions[$key]);

				if(($fieldsOrderKey = array_search($elementOrderId,$fieldsOrder)) !== false) {
					unset($fieldsOrder[$fieldsOrderKey]);
				}
			}
		}

		$fieldOptions = json_encode($modifiedOptions);
		$fieldsOrder = json_encode($fieldsOrder);
		$elementOptions['fieldsOptions'] = addslashes($fieldOptions);
		$elementOptions['fieldsOrder'] = addslashes($fieldsOrder);

		$elementOptions = json_encode($elementOptions);
		$this->changeElementOptions($formId, $elementId, $elementOptions);
		echo "";
		wp_die();
	}
	public function elementOptionData() {

		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');

		$formId  = sanitize_text_field($_POST['contactFormId']);
		$elementId = sanitize_text_field($_POST['elementId']);
		$elementType = sanitize_text_field($_POST['elementType']);
		$elementOrderId = sanitize_text_field($_POST['elementOrderId']);
		$elementName = sanitize_text_field($_POST['elementName']);
		$elementValue = sanitize_text_field($_POST['elementValue']);
		$modificationType = sanitize_text_field($_POST['modificationType']);

		$elementOptions = $this->getElementOptionsById($formId, $elementId);

		$fieldOptions =  json_decode($elementOptions['fieldsOptions'], true);

		if($modificationType == 'change') {
			foreach($fieldOptions as $key => $field) {
				if($field['orderId'] == $elementOrderId) {
					$fieldOptions[$key][$elementName] = $elementValue;
				}
			}

			$fieldOptions = json_encode($fieldOptions);
			$elementOptions['fieldsOptions'] = addslashes($fieldOptions);
		}
		$elementOptions = json_encode($elementOptions);
		$this->changeElementOptions($formId, $elementId, $elementOptions);
	}

	public function addSubOptionOption()
	{
		check_ajax_referer('ycfAjaxNonce', 'ajaxNonce');
		$formId  = (int)$_POST['contactFormId'];
		$elementId = (int)$_POST['elementId'];
		$elementType = sanitize_text_field($_POST['elementType']);
		$elementOrderId = (int)$_POST['elementOrderId'];
		$newSubOptionName = sanitize_text_field($_POST['newSubOptionName']);
		$newSubOptionLabel = sanitize_text_field($_POST['newSubOptionLabel']);

		$elementOptions = $this->getElementOptionsById($formId, $elementId);
		$fieldOptions =  json_decode($elementOptions['fieldsOptions'], true);
		$fieldsOrder =  json_decode($elementOptions['fieldsOrder'], true);

		$newSubOption = array(
			'label' => 	$newSubOptionLabel,
			'value' => $newSubOptionName,
			'orderId' => $elementOrderId,
			'options' => ''
		);
		$fieldOptions[] = $newSubOption;
		$fieldsOrder[] = $elementOrderId;
		$fieldOptions = json_encode($fieldOptions);
		$fieldsOrder = json_encode($fieldsOrder);
		$elementOptions['fieldsOptions'] = addslashes($fieldOptions);
		$elementOptions['fieldsOrder'] = addslashes($fieldsOrder);
		$elementOptions = json_encode($elementOptions);
		$this->changeElementOptions($formId, $elementId, $elementOptions);

		echo YcfFunctions::subOptionsGroupOptions($elementOrderId, $elementId, $newSubOptionName, $newSubOptionLabel);
		die();
	}

	public function getElementOptionsById($formId, $elementId) {

		$formListData = get_option("YcfFormDraft");

		$optionsData = array();

		if(empty($formListData)) {
			return $optionsData;
		}

		foreach ($formListData as $key => $draftElement) {
			if($elementId == $draftElement['id']) {
				$optionData = $formListData[$key];
			}
		}

		if(empty($optionData['options'])) {
			return $optionsData;
		}

		$options = json_decode(stripslashes($optionData['options']), true);

		return $options;
	}

	public function changeElementOptions($formId, $elementId, $options) {

		$formListData = get_option('YcfFormDraft');

		foreach ($formListData as $key => $draftElement) {
			if($elementId == $draftElement['id']) {
				$formListData[$key]['options'] = $options;
			}
		}

		update_option('YcfFormDraft', $formListData);
	}
	
	private function mailchimpApiObj() {

		$apiKey = $apiKey = get_option("YCF_MAILCHIMP_API_KEY");
		$mailchimpObj = YcfMailchimpConnector::getInstance($apiKey);
		$ycfMailchimpObj = new YcfMailchimp($mailchimpObj);

		return $ycfMailchimpObj;
	}

	private function mailchimpFieldsView($listId, $ycfMailchimpObj) {

		$formBuilderObj = new YcfBuilder();

		$ycfMailchimpObj->setListId($listId);
		$adminViewData = $ycfMailchimpObj->getAdminViewData();

		/*set draft data*/
		update_option('YcfFormDraft', $adminViewData);

		$formBuilderObj->setFormElementsData($adminViewData);

		return $formBuilderObj->createFormAdminElement();
	}

	private function changedListFields($listId, $mailchimpObj) {

		$data = $mailchimpObj->getMailchimpAllFields($listId);

		return $data;
	}

	public function mailchimpFields() {

		$listId = $_POST['mailchimpListId'];
		$mailchimpObj = $this->mailchimpApiObj();
		$contentView = $this->mailchimpFieldsView($listId, $mailchimpObj);
		/*Field call  must be after fields view */
		$fields = $this->changedListFields($listId, $mailchimpObj);
		$content['fields'] = $fields;
		$content['fieldsView'] = $contentView;

		echo json_encode($content);
		die();
	}

	public function refreshFields() {

		$listId = $_POST['listId'];
		$apiKey = get_option("YCF_MAILCHIMP_API_KEY");

		$mailchimpObj = YcfMailchimpConnector::getInstance($apiKey);
		$ycfMailchimpObj = new YcfMailchimp($mailchimpObj);
		$data = $ycfMailchimpObj->getMailchimpAllFields($listId);

		echo $data;
		die();
	}
}

$ajaxObj = new YcfAjax();