<?php
	
function leenkme_verify_api_key_ajax() {
	check_ajax_referer( 'verify', 'leenkme_verify_api_key_wpnonce' );
	if ( !empty( $_POST['api_key'] ) ) {
		$response = leenkme_verify_api_key( $_POST['api_key'] );
		die( $response['message'] );
	}
	die( __( 'Missing or invalid API Key. Please try again.', 'leenkme' ) );
}
add_action( 'wp_ajax_leenkme_verify_api_key', 'leenkme_verify_api_key_ajax' );
	
function leenkme_get_social_network_accounts_ajax() {
	check_ajax_referer( 'add_account', 'leenkme_add_account_wpnonce' );
	if ( !empty( $_POST['api_key'] ) ) {
		$args = array(
			'api-key' => $_POST['api_key'],
			'action'  => $_POST['api_action'],
			'network' => $_POST['network'],
		);
		$response = leenkme_api_remote_post( $args );
		die( json_encode( $response ) );
	}
	die( __( 'Missing or invalid API Key. Please try again.', 'leenkme' ) );
}
add_action( 'wp_ajax_leenkme_get_social_network_accounts', 'leenkme_get_social_network_accounts_ajax' );
	
function leenkme_remove_social_network_account_ajax() {
	check_ajax_referer( 'remove_account', 'leenkme_remove_account_wpnonce' );
	if ( !empty( $_POST['api_key'] ) ) {
		$args = array(
			'api-key'    => $_POST['api_key'],
			'action'     => $_POST['api_action'],
			'network'    => $_POST['network'],
			'account-id' => $_POST['account_id'],
		);
		$response = leenkme_api_remote_post( $args );
		die( json_encode( $response ) );
	}
	die( __( 'Missing or invalid API Key. Please try again.', 'leenkme' ) );
}
add_action( 'wp_ajax_leenkme_remove_social_network_account', 'leenkme_remove_social_network_account_ajax' );