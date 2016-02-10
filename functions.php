<?php

if ( !function_exists( 'leenkme_get_settings' ) ) {
	function leenkme_get_settings() {
		global $leenkme;
		return $leenkme->get_settings();
	}
}

if ( !function_exists( 'leenkme_verify_api_key' ) ) {
	function leenkme_verify_api_key( $api_key ) {
		global $leenkme;
		return $leenkme->verify_api_key( $api_key );
	}
}

if ( !function_exists( 'leenkme_api_remote_post' ) ) {
	function leenkme_api_remote_post( $args ) {
		global $leenkme;
		return $leenkme->api_remote_post( $args );
	}
}

if ( !function_exists( 'leenkme_api_connect_url' ) ) {
	function leenkme_api_get_request_url( $action, $network ) {
		return add_query_arg( array(
			'action'        => $action,
			'network'       => $network,
			'action-nonce'  => wp_create_nonce( 'add-account' ),
			'service-nonce' => wp_create_nonce( 'add-account-' . $network ),
		), menu_page_url( 'leenkme-' . $network, false ) );
	}
}

/**
 * Registers leenk.me helper functions
 *
 * @package leenk.me
 * @since 3.0.0
 */
  
if ( !function_exists( 'leenkme_get_users' ) ) {

	function leenkme_get_users() {
		global $wpdb;

		$args = array(
			'meta_query' => array( 
				'relation' => 'AND',
				array(
					'key'     =>  $wpdb->get_blog_prefix() . 'leenkme',
					'value'   => 'leenkme_API',
					'compare' => 'LIKE'
				),
				array(
					'key'     =>  $wpdb->get_blog_prefix() . 'leenkme',
					'value'   => 0,
					'compare' => '!='
				)
			)
		);

		$users = get_users( $args );
		return $users;

	}
	
}

if ( !function_exists( 'wp_print_r' ) ) { 

	/**
	 * Helper function used for printing out debug information
	 *
	 * HT: Glenn Ansley @ iThemes.com
	 *
	 * @since 0.0.1
	 *
	 * @param int $args Arguments to pass to print_r
	 * @param bool $die
	 */
    function wp_print_r( $args, $die = false ) { 
        $echo = '<pre>' . print_r( $args, true ) . '</pre>';
		
        if ( $die ) {
	        die( $echo );
    	} else {
	    	echo $echo;
		}
    }   
	
}

if ( !function_exists( 'leenkme_utf8_html_entities' ) ) {
	
	function leenkme_utf8_html_entities( $matches ) {
		return mb_convert_encoding( $matches[1], 'UTF-8', 'HTML-ENTITIES' );
	}
	
}