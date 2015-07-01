<?php
/*
Plugin Name: Advanced Cloner
Plugin URI:  http://wordpress.org/extend/plugins/health-check/
Description: Checks the health of your WordPress install
Version:     0.1-alpha
Author:      The Health Check Team
Author URI:  http://wordpress.org/extend/plugins/health-check/
Text Domain: health-check
Domain Path: /lang
 */
define('AC_FILE', __FILE__);
define('AC_DIR', __DIR__);
require_once AC_DIR . '/inc/framework/load.php';

function advanced_clonser_init() {
	require_once AC_DIR. '/vendor/autoload.php';
	new \AdvancedCloner\AdvancedCloner();
}

scb_init( 'advanced_clonser_init' );