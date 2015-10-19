<?php
	
function leenkme_verify_api_key_ajax() {
	check_ajax_referer( 'verify', 'leenkme_verify_api_key_wpnonce' );
	global $leenkme;
	if ( !empty( $_POST['api_key'] ) ) {
		$response = $leenkme->verify_api_key( $_POST['api_key'] );
		if ( !empty( $response['success'] ) ) {
			die( $response['message'] );
		}
	}
	die( __( 'Missing or invalid API Key. Please try again.', 'leenkme' ) );
}
add_action( 'wp_ajax_leenkme_verify_api_key', 'leenkme_verify_api_key_ajax' );