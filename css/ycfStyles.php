<?php
Class ycfStyles {

	public function __construct() {
		
	}

	public function registerStyles($hook) {
	
		wp_register_style('ycfPageBootstrap', YCF_CSS_URL.'bootstrap.css');
		wp_register_style('ycfAdmin', YCF_CSS_URL.'ycfAdmin.css');
	
		if($hook == 'toplevel_page_YcfMenu' || $hook == 'contact-form_page_addNewForm' || $hook == 'contact-form_page_addType'|| $hook == 'contact-form_page_mailchimp') {
			wp_enqueue_style('ycfPageBootstrap');
			wp_enqueue_style('ycfAdmin');
			wp_enqueue_style('wp-color-picker');
		}
	}

}
