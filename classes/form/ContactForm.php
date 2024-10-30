<?php
namespace ycf;
use \YcfForm;
use \YcfBuilder;

require_once dirname(__FILE__).'/YcfForm.php';
class ContactForm extends YcfForm {

	public function __construct() {

	}

	public function defaultFormObjectData() {

		$defaults = array(
			'firstName',
			'email',
			'textarea',
			'submit'
		);

		foreach($defaults as $key) {
			$formData[] = $this->getFormDefaultConfigByKey($key);
		}

		return $formData;
	}

	public function getFormFieldData($formElement) {

		$type = $formElement['type'];
		$defaultConfig = $this->getFormDefaultConfig();

		return $defaultConfig[$type];
	}

	public function getFormDefaultConfig() {

		$typesData = array();
		$randomId = $this->getRandomNumber();
		$typesData['firstName'] = array(
			'id' => $randomId,
			'type' => 'text',
			'name' => 'ycf-'.$randomId,
			'label' => 'Name',
			'orderNumber' => 0,
			'value' => '',
			'options' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			)
		);

		$randomId = $this->getRandomNumber();
		$typesData['text'] = array(
			'id' => $randomId,
			'type' => 'text',
			'name' => 'ycf-'.$randomId,
			'label' => 'Text',
			'orderNumber' => 0,
			'value' => '',
			'options' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			)
		);

		$randomId = $this->getRandomNumber();
		$typesData['number'] = array(
			'id' => $randomId,
			'type' => 'number',
			'name' => 'ycf-'.$randomId,
			'label' => 'Number',
			'orderNumber' => 0,
			'value' => '',
			'options' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			)
		);

		$randomId = $this->getRandomNumber();
		$typesData['email'] = array(
			'id' => $randomId,
			'type' => 'email',
			'name' => 'ycf-'.$randomId,
			'label' => 'Email',
			'orderNumber' => 0,
			'value' => '',
			'options' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			)
		);

		$randomId = $this->getRandomNumber();
		$typesData['textarea'] = array(
			'id' => $randomId,
			'type' => 'textarea',
			'name' => 'ycf-'.$randomId,
			'label' => 'Message',
			'orderNumber' => 0,
			'value' => '',
			'options' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			)
		);

		$randomId = $this->getRandomNumber();
		$formOptionValueData = $this->formOptionValueData();
		$options['fieldsOptions'] = json_encode(array_values($formOptionValueData));
		$options['fieldsOrder'] = json_encode(array_keys($formOptionValueData));
		$typesData['select'] = array(
			'id' => $randomId,
			'type' => 'select',
			'name' => 'ycf-'.$randomId,
			'label' => 'select',
			'orderNumber' => 0,
			'value' => '',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			),
			'options' => json_encode($options)
		);
		$typesData['submit'] = array(
			'id' => 'ycf-submit-wrapper',
			'type' => 'submit',
			'name' => 'ycf-submit',
			'label' => 'Submit',
			'orderNumber' => 0,
			'value' => 'Submit',
			'settings' => array(
//				'required' => ''
			),
			'attrs' => array(),
			'disableConfig' => array(
				'name' => true
			),
			'options' => json_encode($options)
		);

		return $typesData;
	}

	public function render() {

		$formId = $this->getFormId();
		$formData = YcfForm::getFormFieldsDataById($formId);
		$form = '';

		if(isset($formData)) {
			$this->includeData();
			$this->includeCss();
			$options = $formData['fields_data'];
			$options = json_decode($options, true);

			$formBuilderObj = new YcfBuilder();
			$formBuilderObj->setFormId($formId);
			$formBuilderObj->setFormElementsData($options);
			$contactForm = '<form id="ycf-contact-form" data-id="'.$formId.'" class="ycf-contact-form ycf-form-'.$formId.'" action="admin-post.php" method="post">';
			$contactForm .= $formBuilderObj->getFormFields();
			$contactForm .= '</form>';
		}

		return $contactForm;
	}

	private function includeData() {

		$formId = $this->getFormId();
		$formData = YcfForm::getSavedData($formId);
		$ycfFormData = array(
			'ycfWpAjaxUrl' => admin_url('admin-ajax.php'),
			'ycfValidateEmail' => 'Please enter a valid email.',
			'ycfRequiredField' => 'This field is required.',
			'sendToEmail' => $formData['contact-form-send-to-email'],
			'sendFromEmail' => $formData['contact-form-send-from-email'],
			'contactFormSendEmailSubject' => $formData['contact-form-send-email-subject'],
			'ajaxNonce' => wp_create_nonce('ycfFormAjaxNoce'),
			'formId' => $formId,
			'ycfMessage' => $formData['ycf-message']
		);

		wp_enqueue_script('ycfFormJs');
		wp_localize_script('ycfFormJs', 'YcfFormData'.$formId, $ycfFormData);
		wp_enqueue_script('ycfValidate');

		echo "<script type=\"text/javascript\">
			jQuery(document).ready(function () {
				var formObj = new YcfForm();
				formObj.init($formId);
			});
		</script>";
	}

	private function includeCss() {

		$formId = $this->getFormId();
		wp_register_style('theme1css', YCF_CSS_URL.'/form/theme1.css', array(), YCF_VERSION);
		wp_register_style('ycfFormStyle', YCF_CSS_URL.'/form/ycfFormStyle.css', array(), YCF_VERSION);
		wp_enqueue_style('theme1css');
		wp_enqueue_style('ycfFormStyle');
		$contactFormWidth = $this->getOptionValue('contact-form-width');
		$contactFormWidthMeasure = $this->getOptionValue('contact-form-width-measure');
		echo "<style type=\"text/css\">
			.ycf-form-$formId {
				width: $contactFormWidth$contactFormWidthMeasure;
			}
		</style>";
	}
}