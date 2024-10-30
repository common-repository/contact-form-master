<?php
/**
 * Plugin Name: Contact Form Master
 * Description: Contact form master created by Edmon Parker.
 * Version: 1.0.7
 * Author: Edmon
 * Author URI: 
 * License: GPLv2
 */
 
require_once(dirname(__FILE__).'/config.php');
require_once(YCF_CLASSES.'ContactFormInit.php');

$contactObj = new ContactFormInit();