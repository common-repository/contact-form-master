<?php
Class YCFContactFormAdminPost {

	public function __construct() {

		$this->actions();
	}

	public function actions() {

		add_action('admin_post_ycf_save_data', array($this, 'ycfSaveData'));
		add_action('admin_post_ycf_mailchimp_api_key', array($this, 'ycfMailchimpApiKey'));
		//add_action('admin_post_delete_readmore', array($this, 'expmDeleteData'));
	}

	public function ycfMailchimpApiKey() {

		if(isset($_POST['mailchimp-api-key']) && $_POST['mailchimp-api-key'] != '') {
			$apiKey = sanitize_text_field($_POST['mailchimp-api-key']);
			update_option("YCF_MAILCHIMP_API_KEY", $apiKey);
		}
		wp_redirect(admin_url()."admin.php?page=mailchimp");
	}

	public function ycfSanitizeDate($optionName, $textField = false) {

		if(!isset($_POST[$optionName])) {
			return '';
		}

		if($textField) {
			return $_POST[$optionName];
		}

		return sanitize_text_field($_POST[$optionName]);
	}

	public function expmDeleteData() {

		global $wpdb;
		$id = $_GET['readMoreId'];
		$wpdb->delete($wpdb->prefix.'ycf_form', array('id'=>$id), array('%d'));
		wp_redirect(admin_url()."admin.php?page=ExpMaker");
	}

	public function ycfSaveData() {
		
		global $wpdb;

		check_admin_referer('ycf_nonce_check');

		$_POST = $_POST;

		$options = array(
			'contact-form-send-to-email' => $this->ycfSanitizeDate('contact-form-send-to-email'),
			'contact-form-send-from-email' => $this->ycfSanitizeDate('contact-form-send-from-email'),
			'contact-form-send-email-subject' => $this->ycfSanitizeDate('contact-form-send-email-subject'),
			'ycf-message' => $this->ycfSanitizeDate('ycf-message', true),
			'contact-form-width' => $this->ycfSanitizeDate('contact-form-width'),
			'contact-form-width-measure' => $this->ycfSanitizeDate('contact-form-width-measure'),
			'ycf-mailchimp-list-id' => $this->ycfSanitizeDate('ycf-mailchimp-list-id'),
			'ycf-mailchimp-required-message' => $this->ycfSanitizeDate('ycf-mailchimp-required-message'),
			'ycf-mailchimp-email-message' => $this->ycfSanitizeDate('ycf-mailchimp-email-message'),
			'ycf-mailchimp-error-message' => $this->ycfSanitizeDate('ycf-mailchimp-error-message'),
			'ycf-mailchimp-success-message' => $this->ycfSanitizeDate('ycf-mailchimp-success-message'),
			'ycf-mailchimp-input-width' => $this->ycfSanitizeDate('ycf-mailchimp-input-width'),
			'ycf-mailchimp-input-height' => $this->ycfSanitizeDate('ycf-mailchimp-input-height'),
			'ycf-mailchimp-input-border-radius' => $this->ycfSanitizeDate('ycf-mailchimp-input-border-radius'),
			'ycf-mailchimp-input-border-width' => $this->ycfSanitizeDate('ycf-mailchimp-input-border-width'),
			'ycf-mailchimp-input-border-color' => $this->ycfSanitizeDate('ycf-mailchimp-input-border-color'),
			'ycf-mailchimp-input-bg-color' => $this->ycfSanitizeDate('ycf-mailchimp-input-bg-color'),
			'ycf-mailchimp-input-text-color' => $this->ycfSanitizeDate('ycf-mailchimp-input-text-color'),
			'ycf-mailchimp-submit-width' => $this->ycfSanitizeDate('ycf-mailchimp-submit-width'),
			'ycf-mailchimp-submit-height' => $this->ycfSanitizeDate('ycf-mailchimp-submit-height'),
			'ycf-mailchimp-submit-border-width' => $this->ycfSanitizeDate('ycf-mailchimp-submit-border-width'),
			'ycf-mailchimp-submit-border-radius' => $this->ycfSanitizeDate('ycf-mailchimp-submit-border-radius'),
			'ycf-mailchimp-submit-border-color' => $this->ycfSanitizeDate('ycf-mailchimp-submit-border-color'),
			'ycf-mailchimp-submit-bg-color' => $this->ycfSanitizeDate('ycf-mailchimp-submit-bg-color'),
			'ycf-mailchimp-submit-color' => $this->ycfSanitizeDate('ycf-mailchimp-submit-color'),
			'ycf-mailchimp-double-optin' => $this->ycfSanitizeDate('ycf-mailchimp-double-optin'),
		);
		$options = json_encode($options);
		$title = $this->ycfSanitizeDate('ycf-form-title');
		$id = $this->ycfSanitizeDate('ycf-form-id');
		$fieldOrder = $this->ycfSanitizeDate('contact-fields-order');
		$type = $this->ycfSanitizeDate('ycf-form-type');

		$data = array(
			'title' => $title,
			'type' => $type,
			'options' => $options
		);

		$format = array(
			'%s',
			'%s',
			'%s',
		);

		$fieldsFormat = array(
			'%d',
			'%s'
		);

		$fieldsData = YcfFunctions::changeFieldsOrdering(get_option('YcfFormDraft'), $fieldOrder);
		$formFields = json_encode($fieldsData);

		if(!$id) {
			$wpdb->insert($wpdb->prefix.'ycf_form', $data, $format);
			$contactId = $wpdb->insert_id;

			$inserToFieldsQuery = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."ycf_fields (form_id, fields_data) VALUES (%d, %s)", $contactId, $formFields);
			$res = $wpdb->query($inserToFieldsQuery);
		}
		else {
			$data['form_id'] = $id;
			$wpdb->update($wpdb->prefix.'ycf_form', $data, array('form_id'=>$id), $format, array('%d'));

			$fieldsUpdateSql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."ycf_fields SET fields_data=%s WHERE form_id=%d",$formFields, $id);
			$wpdb->query($fieldsUpdateSql);
			$contactId = $id;
		}
		$url = add_query_arg(
			array(
				'page' => 'addNewForm',
				'formId' => $contactId,
				'saved' => '1',
				'type' => $type
			), admin_url()."admin.php"
		);
		wp_redirect($url);
	}

}

$ycfContactFormObj = new YCFContactFormAdminPost();