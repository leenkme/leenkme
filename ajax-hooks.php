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
	
function leenkme_remove_social_network_account_ajax() {
	check_ajax_referer( 'remove_account', 'remove_account_wpnonce' );
	if ( empty( $_POST['api_key'] ) ) {
		die( __( 'Missing or invalid API Key. Please try again.', 'leenkme' ) );
	}
	if ( empty( $_POST['api_action'] ) ) {
		die( __( 'Missing or invalid API action. Please try again.', 'leenkme' ) );
	}
	if ( empty( $_POST['network'] ) ) {
		die( __( 'Missing or invalid network. Please try again.', 'leenkme' ) );
	}
	if ( empty( $_POST['account_id'] ) ) {
		die( __( 'Missing or invalid account ID. Please try again.', 'leenkme' ) );
	}
	if ( empty( $_POST['user_id'] ) ) {
		die( __( 'Missing or invalid user ID. Please try again.', 'leenkme' ) );
	}
	$args = array(
		'api-key'    => $_POST['api_key'],
		'action'     => $_POST['api_action'],
		'network'    => $_POST['network'],
		'account-id' => $_POST['account_id'],
	);
	$response = leenkme_api_remote_post( $args );
    delete_user_meta( $_POST['user_id'], 'leenkme_' . $_POST['network'] . '_account_cache' );
    delete_user_meta( $_POST['user_id'], 'leenkme_' . $_POST['network'] . '_cache_expiry' );
	die( json_encode( $response ) );
}
add_action( 'wp_ajax_leenkme_remove_social_network_account', 'leenkme_remove_social_network_account_ajax' );