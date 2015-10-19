<?php

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
