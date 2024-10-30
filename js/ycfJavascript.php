<?php
Class ycfJavascript {

	public function __construct() {

		add_action('admin_enqueue_scripts', array($this,'registerScripts'));
		add_action('wp_enqueue_scripts', array($this, 'ycfContactFormScripts'));
	}

	public function registerScripts($hook) {

		 wp_register_script('bootstrap.min', YCF_JAVASCRIPT.'bootstrap.min.js', array( 'wp-color-picker'), array('jquery-ui-sortable'));
		 wp_register_script('ycfBackend', YCF_JAVASCRIPT.'ycfBackend.js');
		 wp_register_script('mailchimpBackend', YCF_JAVASCRIPT.'mailchimpBackend.js');

		if($hook == 'contact-form_page_addNewForm' ||
			$hook == 'toplevel_page_YcfMenu' ||
			$hook == 'contact-form_page_mailchimp'
		) {
			$backLocalizeData = array(
				'ajaxNonce' => wp_create_nonce('ycfAjaxNonce')
			);
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script('bootstrap.min');
			wp_enqueue_script('ycfBackend');
			wp_enqueue_script('mailchimpBackend');
			wp_localize_script('ycfBackend', 'backLocalizeData', $backLocalizeData);
		}
	}

	public function ycfContactFormScripts() {

		wp_register_script('ycfFormJs', YCF_JAVASCRIPT.'ycfForm.js', array('jquery', 'ycfValidate'), YCF_VERSION);
		wp_register_script('ycfValidatejs', YCF_JAVASCRIPT.'ycfValidate.js', array('jquery'), YCF_VERSION);
		wp_register_script('ycfValidate', YCF_JAVASCRIPT.'ycfValidate.js', array('jquery'), YCF_VERSION);
		wp_register_script('ycfMailchimp', YCF_JAVASCRIPT.'ycfMailchimp.js', array('jquery'), YCF_VERSION);
	}
}

$jsObj = new ycfJavascript();