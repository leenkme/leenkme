<?php
/**
 * Registers leenk.me Facebook class for setting up leenk.me
 *
 * @package leenk.me
 * @since 3.0.0
 */

if ( !class_exists( 'LeenkMe_Facebook' ) ) {
	
	/**
	 * This class registers the main leenkme functionality
	 *
	 * @since 3.0.0
	 */	
	class LeenkMe_Facebook {
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 3.0.0
		 * @uses add_action() Calls 'admin_init' hook on $this->upgrade
		 * @uses add_action() Calls 'admin_enqueue_scripts' hook on $this->admin_wp_enqueue_scripts
		 * @uses add_action() Calls 'admin_print_styles' hook on $this->admin_wp_print_styles
		 * @uses add_action() Calls 'admin_menu' hook on $this->admin_menu
		 * @uses add_action() Calls 'wp_ajax_verify' hook on $this->api_ajax_verify
		 * @uses add_action() Calls 'transition_post_status' hook on $this->transition_post_status
		 */
		function __construct() {
			
			$settings = $this->get_settings();
								
			add_action( 'leenkme_admin_menu', array( $this, 'leenkme_admin_menu' ) );
			
		}
				
		/**
		 * Initialize leenk.me Admin Menu
		 *
		 * @since 3.0.0
		 * @uses add_menu_page() Creates leenk.me menu
		 * @uses add_submenu_page() Creates Settings submenu to leenk.me menu
		 * @uses add_submenu_page() Creates Help submenu to leenk.me menu
		 * @uses do_action() To call 'leenkme_admin_menu' for future addons
		 */
		function leenkme_admin_menu() {
			
			add_submenu_page( 'leenkme', __( 'Facebook', 'leenkme' ), __( 'Facebook', 'leenkme' ), apply_filters( 'leenkme_menu_access_capability', 'edit_posts' ), 'leenkme-facebook', array( $this, 'settings_page' ) );
			
		}
		
		/**
		 * Get leenkme options set in options table
		 *
		 * @since 3.0.0
		 * @uses apply_filters() To call 'leenkme_default_settings' for future addons
		 * @uses wp_parse_args function to merge default with stored options
		 *
		 * return array leenk.me settings
		 */
		function get_settings() {
			$defaults = array( 
				 'tweetFormat' 			=> '%TITLE% %URL%',
				 'tweetCats '			=> array( '0' ),
				 'clude'	 			=> 'in',
				 'message_preference'	=> 'author'
			);
			$defaults = apply_filters( 'leenkme_facebook_default_settings', $defaults );
		
			$settings = get_option( 'leenkme_facebook' );
			
			return wp_parse_args( $settings, $defaults );
		}
		
		function update_settings( $settings ) {
			update_option( 'leenkme_facebook', $settings );
		}
		
		function addon_enabled( $addon ) {
			$settings = $this->get_settings();
			return $settings[$addon];
		}
		
		/**
		 * Output leenk.me's settings page and saves new settings on form submit
		 *
		 * @since 3.0.0
		 * @uses do_action() To call 'leenkme_settings_page' for future addons
		 */
		function settings_page() {
			
			// Get the user options
			$settings = $this->get_settings();
			
		}
		
	}
	
}
