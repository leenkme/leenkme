<?php

// From PHP_Compat-1.6.0a2 Compat/Function/str_ireplace.php for PHP4 Compatibility
if ( !function_exists( 'str_ireplace' ) ) {

    function str_ireplace( $search, $replace, $subject ) {
		// Sanity check
		if ( is_string( $search ) && is_array( $replace ) ) {
			user_error( 'Array to string conversion', E_USER_NOTICE );
			$replace = (string)$replace;
		}
	
		// If search isn't an array, make it one
		$search = (array)$search;
		$length_search = count( $search );
	
		// build the replace array
		$replace = is_array( $replace )
		? array_pad( $replace, $length_search, '' )
		: array_pad( array(), $length_search, $replace );
	
		// If subject is not an array, make it one
		$was_string = false;
		if ( is_string( $subject ) ) {
			$was_string = true;
			$subject = array( $subject );
		}
	
		// Prepare the search array
		foreach ( $search as $search_key => $search_value ) {
			$search[$search_key] = '/' . preg_quote( $search_value, '/' ) . '/i';
		}
		
		// Prepare the replace array (escape backreferences)
		$replace = str_replace( array( '\\', '$' ), array( '\\\\', '\$' ), $replace );
	
		$result = preg_replace( $search, $replace, $subject );
		return $was_string ? $result[0] : $result;
	}

}

// disabled() since 3.0, needed to maintain 2.8, and 2.9 backwards compatability
if ( !function_exists( 'disabled' ) ) {

	/**
	 * Outputs the html disabled attribute.
	 *
	 * Compares the first two arguments and if identical marks as disabled
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $disabled One of the values to compare
	 * @param mixed $current (true) The other value to compare if not just true
	 * @param bool $echo Whether to echo or just return the string
	 * @return string html attribute or empty string
	 */
	function disabled( $disabled, $current = true, $echo = true ) {
		return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
	}

}

// user_can() since 3.1, needed to maintain 2.8, 2.9, and 3.0 backwards compatability
if ( !function_exists( 'user_can' ) ) {

	/**
	 * Whether a particular user has capability or role.
	 *
	 * @since 3.1.0
	 *
	 * @param int|object $user User ID or object.
	 * @param string $capability Capability or role name.
	 * @return bool
	 */
	function user_can( $user, $capability ) {
		if ( ! is_object( $user ) )
			$user = new WP_User( $user );
	
		if ( ! $user || ! $user->ID )
			return false;
	
		$args = array_slice( func_get_args(), 2 );
		$args = array_merge( array( $capability ), $args );
	
		return call_user_func_array( array( &$user, 'has_cap' ), $args );
	}

}

// user_can() since 3.0, needed to maintain 2.8 and 2.9 backwards compatability
if ( !function_exists( 'clean_user_cache' ) ) {

	/**
	 * Clean all user caches
	 *
	 * @since 3.0.0
	 *
	 * @param int $id User ID
	 */
	function clean_user_cache($id) {
		$user = new WP_User($id);
	
		wp_cache_delete($id, 'users');
		wp_cache_delete($user->user_login, 'userlogins');
		wp_cache_delete($user->user_email, 'useremail');
		wp_cache_delete($user->user_nicename, 'userslugs');
		wp_cache_delete('blogs_of_user-' . $id, 'users');
	}

}

if ( !function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Properly strip all HTML tags including script and style
	 *
	 * @since 2.9.0
	 *
	 * @param string $string String containing HTML tags
	 * @param bool $remove_breaks optional Whether to remove left over line breaks and white space chars
	 * @return string The processed string.
	 */
	function wp_strip_all_tags($string, $remove_breaks = false) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags($string);
	
		if ( $remove_breaks )
			$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
	
		return trim($string);
	}
}

if ( !function_exists( 'leenkme_trim_characters' ) ) {

	/**
	 * Clean all user caches
	 *
	 * @since 1.3.10
	 *
	 * @param int $id User ID
	 */
	function leenkme_trim_words( $string, $maxChar ) {
		
		$num_words = 55;
		$more = "...";
	
		$string = strip_shortcodes( $string );
		$string = wp_strip_all_tags( $string );
		$words_array = preg_split( "/[\n\r\t ]+/", $string, $num_words + 1, PREG_SPLIT_NO_EMPTY );
		$length = strlen( utf8_decode( $string ) );
		
		while ( $length > $maxChar ) {
		
			array_pop( $words_array );
			$string = implode( ' ', $words_array );
			$string = $string . $more;
			
			$length = strlen( utf8_decode( $string ) );
			
		}
		
		return $string;
		
	}

}

if ( !function_exists( 'leenkme_replacements_args' ) ) {

	/**
	 * Replaces variables with WordPress content
	 *
	 * @since 2.0
	 *
	 * @param int $id User ID
	 */
	function leenkme_replacements_args( $string, $post_title = '', $post_id = 0, $excerpt = '' ) {
			
		$wp_sitename = get_bloginfo( 'name' );
		$wp_tagline = get_bloginfo( 'description' );
		
		$string = strip_shortcodes( $string );
		$string = str_ireplace( '%TITLE%', $post_title, $string );
		$string = str_ireplace( '%WPSITENAME%', $wp_sitename, $string );
		$string = str_ireplace( '%WPTAGLINE%', $wp_tagline, $string );
		$string = str_ireplace( '%EXCERPT%', $excerpt, $string );
		
		$string = apply_filters( 'leenkme_custom_replacement_args', $string, $post_id );
		
		return wp_strip_all_tags( stripcslashes( html_entity_decode( $string, ENT_COMPAT, get_bloginfo('charset') ) ) );
		
	}

}

if ( !function_exists( 'leenkme_get_picture' ) ) {

	/**
	 * Gets appropriate image associated with post and social network type.
	 *
	 * @since 2.0
	 *
	 * @param int $id User ID
	 */
	function leenkme_get_picture( $settings, $post_id, $type ) {
	
		if ( !empty( $settings['force_' . $type . '_image'] ) ) {
			
			if ( 'og' == $type )
				$picture = $settings['og_single_image'];
			else
				$picture = $settings['default_image'];
		
		} else if ( !( $picture = apply_filters( $type . '_image', false, $post_id ) ) ) {
			
			if ( 'facebook' === $type ) {
				$image_type = 'leenkme_facebook_image';
			} else {
				$image_type = 'leenkme_thumbnail';
			}
			
			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post_id ) ) {
				
				$post_thumbnail_id = get_post_thumbnail_id( $post_id );
				list( $picture, $width, $height ) = wp_get_attachment_image_src( $post_thumbnail_id, $image_type );
				
			} else if ( $images = get_children( 'post_parent=' . $post_id . '&post_type=attachment&post_mime_type=image&numberposts=1' ) ) {
				
				foreach ( $images as $attachment_id => $attachment ) {
					
					list( $picture, $width, $height ) = wp_get_attachment_image_src( $attachment_id, $image_type );
					break;
					
				}
				
			} else if ( $picture = get_post_meta( $post_id, '_' . $type . '_image', true ) ) {
				
				//Already set
				
			} else if ( !empty( $settings['default_image'] ) ) {
				
				$picture = $settings['default_image'];
				
			} else if ( !empty( $settings['og_single_image'] ) ) {
				
				$picture = $settings['og_single_image'];
				
			} else {
			
				$picture = '';
				
			}
			
		}
		
		return $picture;
		
	}
	
}

if ( !function_exists( 'leenkme_rate_limit' ) ) {

	function leenkme_rate_limit() {
		
		// BEGIN RATE LIMITING
		$call_limit = 350; // API calls
		$time_limit = 60 * 60; // 1 hour (in seconds)
		$transient = 'leenkme_rate_limit';
	
		// using PHP time() epoch timestamp to handle rolling transient time.
		if ( false === ( $calls = get_transient( $transient ) ) ) {
			
			$calls[] = time();
			set_transient( $transient, $calls, $time_limit ); // New Transient for API Calls
			
		} else {
			
			$calls[] = time();
			set_transient( $transient, $calls, $time_limit );
			
			$call_count = count( $calls );
			
			// Rolling Time... If more than 350 calls are in the transient,
			// check to see if any of those calls are older than 60 minutes
			// if they are, remove them from the array...
			// stop checking once we get to a call that is within the 60 minutes period
			if ( $call_limit < $call_count ) {
				
				for ( $i = 1; $i <= $call_count; $i++ ) {
					
					$call = array_shift( $calls );
					
					if ( $call >= ( time() - $time_limit ) ) {
						
						array_unshift( $calls, $call );
						set_transient( $transient, $calls, $time_limit );
						break;
						
					}
					
				}
			
				// recheck the new call array, if we're still over the limit, send error
				if ( $call_limit <= count( $calls ) ) {
					
					return false;
					
				}
				
			}
			
		}
		// END RATE LIMITING
		
		return true;
		
	}
	
}

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
	 * @param bool $die TRUE to die else FALSE (default TRUE)
	 */
    function wp_print_r( $args, $die = true ) { 
	
        $echo = '<pre>' . print_r( $args, true ) . '</pre>';
		
        if ( $die ) die( $echo );
        	else echo $echo;
		
    }   
	
}

if ( !function_exists( 'leenkme_utf8_html_entities' ) ) {
	
	function leenkme_utf8_html_entities( $matches ) {
		return mb_convert_encoding( $matches[1], 'UTF-8', 'HTML-ENTITIES' );
	}
	
}
