<?php
Class YcfPages {

	public $functionsObj;
	public $ycfFormData;
	public $formBuilderObj;

	public function __construct() {
		
	}

	public function mainPage() {

		require_once(YCF_VIEWS."ycfMainView.php");
	}

	public function addType() {

		require_once(YCF_VIEWS."addType.php");
	}

	public function addNewButtons() {

		require_once(YCF_VIEWS."addType.php");
	}

	public function mailchimpSettings() {

		require_once(YCF_MAILCHIPM_VIEWS.'mailchimpConnection.php');
	}

	public function addNewPage() {

		$formId = 0;
		$formType = 'contact';

		if(!empty($_GET['formId'])) {
			$formId = $_GET['formId'];
		}
		if(!empty($_GET['type'])) {
			$formType = $_GET['type'];
		}
		$formBuilderObj = $this->formBuilderObj;
		$formBuilderObj->setFormId($formId);
		$formDataObj = YcfForm::createFormTypeObj($formType);
		$formDataObj->setFormId($formId);
		$formDataObj->setBuilderObj($formBuilderObj);

		$formOptionsData = $formDataObj->getFormOptionsData();
		update_option('YcfFormDraft', $formOptionsData);

		$formBuilderObj->setFormElementsData($formOptionsData);

		@$formTitle = $formDataObj->getOptionValue('ycf-form-title');
		@$contactFormSendToEmail = $formDataObj->getOptionValue('contact-form-send-to-email');
		@$contactFormSendFromEmail = $formDataObj->getOptionValue('contact-form-send-from-email');
		@$contactFormSendEmailSubject = $formDataObj->getOptionValue('contact-form-send-email-subject');
		@$ycfMessage = $formDataObj->getOptionValue('ycf-message');
		@$contactFormWidth = $formDataObj->getOptionValue('contact-form-width');
		@$contactFormWidthMeasure = $formDataObj->getOptionValue('contact-form-width-measure');
		@$ycfMailchimpListId = $formDataObj->getOptionValue('ycf-mailchimp-list-id');

		require_once(YCF_VIEWS."expmAddNew.php");
	}

}