<?php

// Example followed from http://planetozh.com/blog/2009/08/how-to-make-http-requests-with-wordpress/
function leenkme_url_shortener( $post_id ) { 

	global $dl_pluginleenkme;
	
	$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
	
	$url = get_permalink( $post_id );
	
	switch ( $leenkme_settings['url_shortener'] ) {
		
		case 'bitly' :
			$short_url = leenkme_get_bitly_url( $url );
			break;
		
		case 'yourls' :
			$short_url = leenkme_get_yourls_url( $url );
			break;
		
		case 'isgd' :
			$short_url = leenkme_get_isgd_url( $url );
			break;
		
		case 'wpme' :
			$short_url = leenkme_get_wpme_url( $post_id );
			break;
		
		case 'tinyurl' :
			$short_url = leenkme_get_tinyurl_url( $url );
			break;
	
		case 'tflp' :
			$short_url = leenkme_get_tflp_url( $url );
			break;
		
		case 'wppostid' :
		default :
			$short_url = home_url( '?p=' . $post_id );;
			break;
		
	}

	update_post_meta( $post_id, '_leenkme_shortened_url', $short_url );
	return $short_url;

}

function leenkme_get_shortened_url( $http_query, $url ) {
										
	$result = wp_remote_request( apply_filters( 'leenkme_url_shortener', $http_query, $url ) );
	
	if ( is_wp_error( $result ) ) { //if we get an error just us the normal permalink URL
	
		return $url;
	
	} else if ( !empty( $result ) && !empty( $result['body'] ) ) {
	
		return $result['body']; 
	
	} else {
	
		return apply_filters( 'leenkme_url_shortener_result', $result );	
		
	}
	
}

function leenkme_get_bitly_url( $url ) {

	global $dl_pluginleenkme;
	
	$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
				
	$bitly_api = "http://api.bitly.com/v3/shorten"; 
	
	if ( !empty( $leenkme_settings['bitly_username'] ) && !empty( $leenkme_settings['bitly_apikey'] ) ) {
		
		$bitly_args = array(  
								'login' => $leenkme_settings['bitly_username'],  
								'apiKey' => $leenkme_settings['bitly_apikey'],  
								'longURL' => $url,  
								'format' => 'txt',  
								'domain' => 'bit.ly' // either bit.ly or j.mp  
							);  
							
	} else {
	
    	return $url;  
		
	}
  
    $bitly_query = $bitly_api . '?' .  http_build_query( $bitly_args ); 
	
	return leenkme_get_shortened_url( $bitly_query, $url );
	
}

function leenkme_get_yourls_url( $url ) {

	global $dl_pluginleenkme;
	
	$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
	
	if ( !empty( $leenkme_settings['yourls_api_url'] ) 
			&& ( ( !empty( $leenkme_settings['yourls_username'] ) && !empty( $leenkme_settings['yourls_password'] ) ) 
					|| !empty( $leenkme_settings['yourls_signature'] ) ) ) {
	
		$yourls_api = $leenkme_settings['yourls_api_url']; 
	  
	  	if ( 1 == $leenkme_settings['yourls_auth_type'] ) {
	  
			$yourls_args = array(  
									'signature' =>  $leenkme_settings['yourls_signature'],
									'url' => $url,  
									'format' => 'simple',  
									'action' => 'shorturl' // either bit.ly or j.mp  
								);  
							
		} else {
	  
			$yourls_args = array(  
									'username' => $leenkme_settings['yourls_username'],  
									'password' => $leenkme_settings['yourls_password'],
									'url' => $url,  
									'format' => 'simple',  
									'action' => 'shorturl' // either bit.ly or j.mp  
								);  
			
		}
	  
		$yourls_query = $yourls_api . '?' .  http_build_query( $yourls_args );
		
		return leenkme_get_shortened_url( $yourls_query, $url );
	
	} else {
		
		return $url;
			
	}
	
}

function leenkme_get_isgd_url( $url ) {
				
	$isgd_api = "http://is.gd/create.php"; 
  
    $isgd_args = array(  
                            'url' => $url,  
               				'format' => 'simple' 
                        );  
  
    $isgd_query = $isgd_api . '?' .  http_build_query( $isgd_args ); 
	
	return leenkme_get_shortened_url( $isgd_query, $url );
	
}

function leenkme_get_wpme_url( $post_id ) {
	
	//check to see if Twitter Friendly Links plugin is activated			
	if ( function_exists( 'wpme_get_shortlink' ) ) {
		
		return wpme_get_shortlink( $post_id ); // if yes, we want to use that for our URL shortening service.
		
	} else {
		
		return $url;
	
	}
	
}

function leenkme_get_tinyurl_url( $url ) {
	
	$tinyurl_api = "http://tinyurl.com/api-create.php"; 
  
    $tinyurl_args = array(  
                            'url' => $url
                        );  
  
    $tinyurl_query = $tinyurl_api . '?' .  http_build_query( $tinyurl_args );
	
	return leenkme_get_shortened_url( $tinyurl_query, $url );
	
}

function leenkme_get_tflp_url( $url ) {
	
	//check to see if Twitter Friendly Links plugin is activated			
	if ( function_exists( 'permalink_to_twitter_link' ) ) {
		
		return permalink_to_twitter_link( $url ); // if yes, we want to use that for our URL shortening service.
		
	} else {
		
		return $url;
	
	}
	
}

function leenkme_show_bitly_options() {

	global $dl_pluginleenkme;
	
	$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
		
	if ( !empty( $leenkme_settings['bitly_username'] ) )
		$bitly_username = $leenkme_settings['bitly_username'];
	else
		$bitly_username = '';
		
	if ( !empty( $leenkme_settings['bitly_apikey'] ) )
		$bitly_apikey = $leenkme_settings['bitly_apikey'];
	else
		$bitly_apikey = '';	
	
	$output = 'bit.ly ' . __( 'Username', 'leenkme' ) . ': <input type="text" id="bitly_username" name="bitly_username" value="' . $bitly_username . '" /><br />';
	$output .= 'bit.ly ' . __( 'API Key', 'leenkme' ) . ': <input type="text" id="bitly_apikey" name="bitly_apikey" value="' . $bitly_apikey . '" />';

	echo $output;
	
}

function leenkme_show_yourls_options() {

	global $dl_pluginleenkme;
	
	$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
	
	if ( empty( $leenkme_settings['yourls_auth_type'] ) )
		$leenkme_settings['yourls_auth_type'] = 0;
		
	if ( !empty( $leenkme_settings['yourls_api_url'] ) )
		$yourls_api_url = $leenkme_settings['yourls_api_url'];
	else
		$yourls_api_url = '';
		
	if ( !empty( $leenkme_settings['yourls_username'] ) )
		$yourls_username = $leenkme_settings['yourls_username'];
	else
		$yourls_username = '';
		
	if ( !empty( $leenkme_settings['yourls_password'] ) )
		$yourls_password = $leenkme_settings['yourls_password'];
	else
		$yourls_password = '';	
		
	if ( !empty( $leenkme_settings['yourls_signature'] ) )
		$yourls_signature = $leenkme_settings['yourls_signature'];
	else
		$yourls_signature = '';	

    $output = '<input type="radio" class="yourls_auth_type" name="yourls_auth_type" value="0" ' . checked( '0', $leenkme_settings['yourls_auth_type'], false ) . ' /> ' . __( 'Username/Password', 'leenkme' ) . ' <input type="radio" class="yourls_auth_type" name="yourls_auth_type" value="1" ' . checked( '1', $leenkme_settings['yourls_auth_type'], false ) . ' />  ' . __( 'Signature Key', 'leenkme' ) . '<br />';
	
	
	$output .= 'YOURLS ' .__( 'API URL', 'leenkme' ) . ': <input type="text" id="yourls_api_url" name="yourls_api_url" value="' . $yourls_api_url . '" /><br />';
	$yourls_unpw_options = 'YOURLS ' .__( 'Username', 'leenkme' ) . ': <input type="text" id="yourls_username" name="yourls_username" value="' . $yourls_username . '" /><br />';
	$yourls_unpw_options .= 'YOURLS ' . __( 'Password', 'leenkme' ) . ': <input type="text" id="yourls_password" name="yourls_password" value="' . $yourls_password . '" />';
	$yourls_sign_options = 'YOURLS ' .__( 'Signature', 'leenkme' ) . ': <input type="text" id="yourls_signature" name="yourls_signature" value="' . $yourls_signature . '" /><br />';
	
	if ( 0 == $leenkme_settings['yourls_auth_type'] ) {
	
		$output .=  '<div id="yourls_unpw_options" style="display: block;">';
		$output .= $yourls_unpw_options;
		$output .= '</div>';
	
		$output .=  '<div id="yourls_signature_options" style="display: none;">';
		$output .= $yourls_sign_options;
		$output .= '</div>';
		
	} else {
	
		$output .=  '<div id="yourls_unpw_options" style="display: none;">';
		$output .= $yourls_unpw_options;
		$output .= '</div>';
		
		$output .=  '<div id="yourls_signature_options" style="display: block;">';
		$output .= $yourls_sign_options;
		$output .= '</div>';
	
	}

	echo $output;
	
}

function leenkme_show_wpme_options() {

	if ( !function_exists( 'wpme_get_shortlink' ) ) {
		
		$wpme = '<a href="http://wordpress.org/extend/plugins/jetpack/">Jetpack by WordPress.com</a>';

		printf( __( 'Warning: The %s plugin must be installed and activated to use this URL shortener.', 'leenkme' ), $wpme );
	
	} else {
	
		echo "";	
		
	}
	
}

function leenkme_show_tflp_options() {

	if ( !function_exists( 'permalink_to_twitter_link' ) ) {
		
		$tflp = '<a href="http://wordpress.org/extend/plugins/twitter-friendly-links/">Twitter Friendly Link</a>';
		
		printf( __( 'Warning: The %s plugin must be installed and activated to use this URL shortener.', 'leenkme' ), $tflp );
	
	} else {
	
		echo '';	
		
	}
	
}

function leenkme_get_shortlink_handler( $shortlink, $post_id, $context, $allow_slugs ) {
	
	if ( is_admin() ) {
	
		$url = leenkme_url_shortener( $post_id );
		update_post_meta( $post_id, '_leenkme_shortened_url', $url );
		return $url;
	
	} else {
	
		if ( $url = get_post_meta( $post_id, '_leenkme_shortened_url', true ) )
			return $url;
		
	}

	return $shortlink;
	
}
