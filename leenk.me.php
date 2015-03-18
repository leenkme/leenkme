<?php
/**
 * Main PHP file used to for initial calls to leenk.me classes and functions.
 *
 * @package leenk.me
 * @since 3.0.0
 */
 
/*
Plugin Name: leenk.me
Plugin URI: http://leenk.me/
Description: Free and easy email marketing, newsletters, and campaigns; built into your WordPress dashboard!
Author: layotte
Version: 3.0.0
Author URI: http://leenk.me/
Tags: email, marketing, email marketing, newsletters, email newsletters, campaigns, email campaigns, widget, form, mailing lists
Text Domain: leenkme
Domain Path: /i18n
Minifiers:
http://www.minifyjs.com/javascript-compressor/
http://www.minifycss.com/css-compressor/
*/

//Define global variables...
define( 'LEENKME_VERSION' , 	'3.0.0' );
define( 'LEENKME_DB_VERSION', 	'3.0.0' );
define( 'LEENKME_API_URL', 		'https://leenk.me/api/3.0/' );
define( 'LEENKME_PLUGIN_URL', 	plugin_dir_url( __FILE__ ) );
define( 'LEENKME_PLUGIN_PATH', 	plugin_dir_path( __FILE__ ) );
define( 'LEENKME_REL_DIR', 		dirname( plugin_basename( __FILE__ ) ) );

/**
 * Instantiate leenk.me class, require helper files
 *
 * @since 0.0.1
 */
function leenkme_plugins_loaded() {
	require_once( LEENKME_PLUGIN_PATH . '/class.php' );

	// Instantiate the leenk.me class
	if ( class_exists( 'LeenkMe' ) ) {
		global $leenkme;
		$leenkme = new LeenkMe();
		require_once( LEENKME_PLUGIN_PATH . 'functions.php' );
			
		//Internationalization
		load_plugin_textdomain( 'leenkme', false, LEENKME_REL_DIR . '/i18n/' );
	}
}
add_action( 'plugins_loaded', 'leenkme_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init