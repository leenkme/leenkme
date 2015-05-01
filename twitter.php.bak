<?php

if ( ! class_exists( 'leenkme_Twitter' ) ) {
	
	// Define class
	class leenkme_Twitter {
	
		// Constructor
		function leenkme_Twitter() {
			//Not Currently Needed
		}
		
		/*--------------------------------------------------------------------
			Administrative Functions
		  --------------------------------------------------------------------*/
		
		// Option loader function
		function get_user_settings( $user_id ) {
			
			// Default values for the options
			$defaults = array(
								 'tweetFormat' 			=> '%TITLE% %URL%',
								 'tweetCats '			=> array( '0' ),
								 'clude'	 			=> 'in',
								 'message_preference'	=> 'author'
							);
							
			// Get values from the WP options table in the database, re-assign if found
			$user_settings = get_user_option( 'leenkme_twitter', $user_id );
			
			return wp_parse_args( $user_settings, $defaults );
			
		}
		
		// Print the admin page for the plugin
		function print_twitter_settings_page() {
			
			global $dl_pluginleenkme;
			
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Get the user options
			$user_settings = $this->get_user_settings( $user_id );
			$twitter_settings = get_option( 'leenkme_twitter' );
			
			if ( isset( $_REQUEST['update_twitter_settings'] ) ) {		
				
				if ( !empty( $_REQUEST['leenkme_tweetformat'] ) )
					$user_settings['tweetFormat'] = $_REQUEST['leenkme_tweetformat'];
				else
					$user_settings['tweetFormat'] = '';
				
				if ( !empty( $_REQUEST['clude'] ) && !empty( $_REQUEST['tweetCats'] ) ) {
					
					$user_settings['clude'] = $_REQUEST['clude'];
					$user_settings['tweetCats'] = $_REQUEST['tweetCats'];
					
				} else {
					
					$user_settings['clude'] = 'in';
					$user_settings['tweetCats'] = array( '0' );
					
				}
				
				if ( !empty( $_REQUEST['message_preference'] ) )
					$user_settings['message_preference'] = $_REQUEST['message_preference'];
				else
					$user_settings['message_preference'] = '';
				
				update_user_option( $user_id, 'leenkme_twitter', $user_settings );
				
				// update settings notification ?>
				<div class="updated"><p><strong><?php _e( 'Settings Updated.', 'leenkme' );?></strong></p></div>
				<?php
			}
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
				<form id="leenkme" method="post" action="">
					<h2 style='margin-bottom: 10px;' ><img src='<?php echo $dl_pluginleenkme->base_url; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> Twitter <?php _e( 'Settings', 'leenkme' ); ?> (<a href="http://leenk.me/2010/09/04/how-to-use-the-leenk-me-twitter-plugin-for-wordpress/" target="_blank"><?php _e( 'help', 'leenkme' ); ?></a>)</h2>
                    <div id="post-types" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span><?php _e( 'Message Settings', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                            <p><?php _e( 'Tweet Format:', 'leenkme' ); ?> <input name="leenkme_tweetformat" type="text" maxlength="140" style="width: 75%;" value="<?php _e( htmlspecialchars( stripcslashes( $user_settings['tweetFormat'] ) ), 'leenkme') ?>" /></p>
                            
                            <p style="font-size: 11px;;">Format Options:</p>
                            <ul style="font-size: 11px; margin-left: 50px;">
                                <li>%TITLE% - <?php _e( 'Displays Title of your post in your Twitter feed.*', 'leenkme' ); ?></li>
                                <li>%URL% - <?php _e( 'Displays TinyURL of your post in your Twitter feed.*', 'leenkme' ); ?></li>
                                <li>%CATS% - <?php _e( 'Displays the categories of your post in your Twitter feed as a hashtag.*', 'leenkme' ); ?></li>
                                <li>%TAGS% - <?php _e( 'Displays ags your post in your Twitter feed as a hashtag.*', 'leenkme' ); ?></li>
                            </ul>
                            <p>
                            	<?php _e( 'Message Preference:', 'leenkme' ); ?>
                                <select id="message_preference" name="message_preference">
                                    <option value="author" <?php selected( 'author', $user_settings['message_preference'] ) ?>><?php _e( "Author", 'leenkme' ); ?></option>
                                    <option value="mine" <?php selected( 'mine', $user_settings['message_preference'] ) ?>><?php _e( "Mine", 'leenkme' ); ?></option>
                                    <option value="manual" <?php selected( 'manual', $user_settings['message_preference'] ) ?>><?php _e( "Manual", 'leenkme' ); ?></option>
                                </select>
                            </p>
                            <p style="font-size: 11px;">Format Preference Options:</p>
                            <ul style="font-size: 11px; margin-left: 50px;">
                                <li><?php _e( "Author - Most efficient, uses the post author's Message Settings.", 'leenkme' ); ?></li>
                                <li><?php _e( 'Mine - Most inefficient, uses your Message Settings regardless of what the post author does.', 'leenkme' ); ?></li>
                                <li><?php _e( 'Manual - Slightly inefficient, uses your Message Settigns unless the post author manually changes the message in the post.', 'leenkme' ); ?></li>
                            </ul>
							<p style="font-size: 11px; margin-top: 25px;"><?php _e( '*NOTE: Twitter only allows a maximum of 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it\'s either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis). If you use the %CATS% or %TAGS% variable, categories are given priority, it will display every category that will fit within the tweet length limitation. After adding the categories leenk.me moves onto tags and will add every tag that will fit within the tweet length limitation. leenk.me will also strip out any non-word character from the Twitter "hashtag" a single word.', 'leenkme' ); ?></p>
					
                    
							<p>
								<input type="button" class="button" name="verify_twitter_connect" id="tweet" value="<?php _e( 'Send a Test Tweet', 'leenkme' ) ?>" />
								<?php wp_nonce_field( 'tweet', 'tweet_wpnonce' ); ?>
                                
                                <input class="button-primary" type="submit" name="update_twitter_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                            </p>
                        
                        </div>
                    </div>
				   
					<div id="post-types" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span><?php _e( 'Publish Settings', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        <p><?php _e( 'Tweet Categories:', 'leenkme' ); ?></p>
                        
                        <div class="tweet-cats" style="margin-left: 50px;">
                            <p>
							<input type='radio' name='clude' id='include_cat' value='in' <?php checked( 'in', $user_settings['clude'] ); ?> /><label for='include_cat'><?php _e( 'Include', 'leenkme' ); ?></label> &nbsp; &nbsp; <input type='radio' name='clude' id='exclude_cat' value='ex' <?php checked( 'ex', $user_settings['clude'] ); ?> /><label for='exclude_cat'><?php _e( 'Exclude', 'leenkme' ); ?></label>
                            </p>
                            <p>
                            <select id='categories' name='tweetCats[]' multiple="multiple" size="5" style="height: 70px; width: 150px;">
                                <option value="0" <?php selected( in_array( "0", (array)$user_settings['tweetCats'] ) ); ?>>All Categories</option>
                            <?php 
                            $categories = get_categories( array( 'hide_empty' => 0, 'orderby' => 'name' ) );
                            foreach ( (array)$categories as $category ) {
                                ?>
                                
                                <option value="<?php echo $category->term_id; ?>" <?php selected( in_array( $category->term_id, (array)$user_settings['tweetCats'] ) ); ?>><?php echo $category->name; ?></option>
            
            
                                <?php
                            }
                            ?>
                            </select></p>
                            
                            <p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'To "deselect" hold the SHIFT key on your keyboard while you click the category.', 'leenkme' ); ?></p>
                        </div>
                        
                        <p>
                            <input class="button-primary" type="submit" name="update_twitter_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>
                        
                        </div>
                    </div>
				</form>
            </div>
            </div>
            </div>
			</div>
			<?php
		}
		
		function leenkme_twitter_meta_tags( $new_status, $old_status, $post ) {
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;
				
			if ( isset( $_REQUEST['_inline_edit'] ) || isset( $_REQUEST['doing_wp_cron'] ) )
				return;
	
			if ( !empty( $_REQUEST['lm_tweet_type'] ) ) {
				$manual = true;
				update_post_meta( $post->ID, '_lm_tweet_type', true );
			} else {
				$manual = false;
				delete_post_meta( $post->ID, '_lm_tweet_type' );
			}
			
			if ( $manual ) {
					
				if ( !empty( $_REQUEST['leenkme_tweet'] ) )
					update_post_meta( $post->ID, '_leenkme_tweet', $_REQUEST['leenkme_tweet'] );
				else
					delete_post_meta( $post->ID, '_leenkme_tweet' );
				
			}
			
			if ( !empty( $_REQUEST['twitter_exclude'] ) )
				update_post_meta( $post->ID, '_twitter_exclude', true );
			else
				delete_post_meta( $post->ID, '_twitter_exclude' );
		}
		
		function leenkme_twitter_meta_box() {
			
			global $post;
			
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Get the user options
			$user_settings = $this->get_user_settings( $user_id );
			
			if ( $tweet = get_post_meta( $post->ID, 'leenkme_tweet', true ) ) {
				
				delete_post_meta( $post->ID, 'leenkme_tweet', true );
				update_post_meta( $post->ID, '_leenkme_tweet', $tweet );
				
				
			}
			
			$tweet = get_post_meta( $post->ID, '_leenkme_tweet', true );
			$format_type = get_post_meta( $post->ID, '_lm_tweet_type', true );
			$exclude = get_post_meta( $post->ID, '_twitter_exclude', true ); ?>
		
			<input value="twitter_edit" type="hidden" name="twitter_edit" />
			<table style="margin-left: auto; margin-right: auto;">
            
				<tr><td colspan="2" scope="row"><p style='font-size: 11px;'>
				<?php 
				_e( 'Format:', 'leenkme' );
				echo " ";
				?>
                    
                <span id="lm_tweet_format" class="tw_manual_format manual_format" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>"><?php _e( 'Manual', 'leenkme' ); ?></span> <a id="set_to_default_tweet" href="#" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>">Reset</a>
                <span id="lm_tweet_format" class="tw_default_format default_format" style="display:<?php if ( $format_type ) echo "none"; else echo "inline"; ?>"><?php _e( 'Default', 'leenkme' ); ?></span>
                <input type="hidden" name="lm_tweet_type" value="<?php echo (int)$format_type; ?>" />
                <input type="hidden" name="lm_tweet_format" value="<?php echo $user_settings['tweetFormat']; ?>" />
                    
                </p></td></tr>
                
                <?php 
				 if ( 0 == $format_type )
					 $tweet = $user_settings['tweetFormat'];
				 
				$expanded_tweet = get_leenkme_expanded_tweet( $post->ID, $tweet, $post->post_title ); 
				
				?>
                
				<tr><td colspan="2">
                <textarea id="leenkme_tweet" name="leenkme_tweet" cols="65" rows="1" maxlength="140"><?php echo $expanded_tweet; ?></textarea>
                </td></tr>
                
				<tr><td scope="row" style="text-align:left; padding-top: 5px; padding-bottom:5px; padding-right:10px; line-height: 15px; font-size: 11px;"><?php _e( 'Exclude from Twitter:', 'leenkme' ) ?> <input type="checkbox" name="twitter_exclude" <?php checked( $exclude || "on" == $exclude ); ?> />
				</td>
                
				<td style="text-align: right;">
                <?php 
				$tweet_len = 140 - strlen( $expanded_tweet );
				
				if ( 10 > $tweet_len )
					$tweet_len_class = 'lm_tweet_count_superwarn';
				else if ( 20 > $tweet_len )
					$tweet_len_class = 'lm_tweet_count_warn';
				else
					$tweet_len_class = 'lm_tweet_count';
				
				 ?>
                <span id="lm_tweet_count" class='<?php echo $tweet_len_class; ?>'><?php echo $tweet_len; ?></span>
				<?php // Only show ReTweet button if the post is "published"
				if ( "publish" === $post->post_status ) { ?>
				<input style="float: right;" type="button" class="button" name="lm_retweet_button" id="lm_retweet_button" value="<?php _e( 'ReTweet', 'leenkme' ) ?>" />
				<?php wp_nonce_field( 'tweet', 'tweet_wpnonce' ); ?>
				</td>
				<?php } ?>
                </tr>
                
			</table>
		
		<?php

		}

	}

}

if ( class_exists( 'leenkme_Twitter' ) ) {
	$dl_pluginleenkmeTwitter = new leenkme_Twitter();
}

function get_leenkme_expanded_tweet( $post_id, $tweet = false, $title, $cats = false, $tags = false ) {
	
	if ( !empty( $tweet ) ) {
		
		$maxLen = 140;
	
		if ( has_filter( 'get_shortlink', 'leenkme_get_shortlink_handler', 1, 4 ) ) {
				
			if ( !( $url = get_post_meta( $post_id, '_leenkme_shortened_url', true ) ) )
				$url = leenkme_url_shortener( $post_id );
					
			if ( preg_match( '/%URL%/i', $tweet ) ) {
				
				$urlLen = strlen( $url );
				$tweetLen = strlen( utf8_decode( $tweet ) );
				$totalLen = $urlLen + $tweetLen - 5; // subtract 5 for "%URL%".
				
				if ( $totalLen <= $maxLen )
					$tweet = str_ireplace( "%URL%", $url, $tweet );
				else
					$tweet = str_ireplace( "%URL%", "", $tweet ); // Too Long (need to get rid of URL).
				
			}
			
		}
					
		if ( preg_match( '/%TITLE%/i', $tweet ) ) {
			
			$title = stripcslashes( $title );
						
			$titleLen = strlen( utf8_decode( $title ) ); 
			$tweetLen = strlen( utf8_decode( $tweet ) );
			$diffLen = $maxLen - $tweetLen;
			$totalLen = $titleLen + $tweetLen - 7;	// subtract 7 for "%TITLE%".
			
			if ( $titleLen > $diffLen )
				$title = leenkme_trim_words( $title, $diffLen );
		
			$tweet = str_ireplace( "%TITLE%", $title, $tweet );
			
		}
		
		if ( preg_match( '/%CATS%/i', $tweet ) ) {
			
			$cat_array = array();
			$post_categories = array();
			
			if ( false === $cats ) 
				$post_categories = wp_get_post_categories( $post_id );
			else if ( !empty( $cats ) )
				$post_categories = explode( ',', $cats );
			
			foreach( $post_categories as $c ) {
				
				$cat = get_category( $c );
				$cat_array[] = "#" . preg_replace( '/[^\p{L}\p{N}]/u', '', $cat->name );
				
			}
			$cat_str = trim( join( ' ', $cat_array ) );
		
			$tweetLen = strlen( utf8_decode( $tweet ) );
			$catLen = strlen( utf8_decode( $cat_str ) );
			$totalLen = $catLen + $tweetLen - 6;	// subtract 5 for "%CATS%".
			
			if ( $totalLen > $maxLen ) {
				
				$split_cat_str = preg_split( '/\s/', $cat_str );
				
				while ( $totalLen > $maxLen ) {
					
					array_pop( $split_cat_str );
					
					$cat_str = join( ' ', (array)$split_cat_str );
					$catLen = strlen( utf8_decode( $cat_str ) );
					$totalLen = $catLen + $tweetLen - 6;	// subtract 5 for "%CATS%".
	
				}
				
			}
			
			$tweet = str_ireplace( '%CATS%', $cat_str, $tweet );
			
		}
		
		if ( preg_match( '/%TAGS%/i', $tweet ) ) {
			
			$tag_array = array();
			
			if ( false === $tags ) {
				
				$post_tags = wp_get_post_tags( $post_id );
			
				$tag_str = "";
				foreach( (array)$post_tags as $t ) {
					
					$tag = get_tag( $t );
					$tag_array[] = "#" . preg_replace( '/[^\p{L}\p{N}]/u', '', $tag->name );
					
				}
				
			} else if ( !empty( $tags ) ) {
				
				$post_tags = explode( ',', $tags );
			
				$tag_str = "";
				foreach($post_tags as $t){
					
					$tag_array[] = "#" . preg_replace( '/\W/', '', $t );
					
				}
				
			}
			
			$tag_str = trim( join( ' ', $tag_array ) );
		
			$tweetLen = strlen( utf8_decode( $tweet ) );
			$tagLen = strlen( utf8_decode( $tag_str ) );
			$totalLen = $tagLen + $tweetLen - 6;	// subtract 5 for "%CATS%".
			
			if ( $totalLen > $maxLen ) {
				
				$split_tag_str = preg_split( '/\s/', $tag_str );
				
				while ( $totalLen > $maxLen ) {
					
					array_pop( $split_tag_str );
					
					$tag_str = join( " ", (array)$split_tag_str );
					$tagLen = strlen( utf8_decode( $tag_str ) );
					$totalLen = $tagLen + $tweetLen - 6;	// subtract 5 for "%CATS%".
					
				}
				
			}
			
			$tweet = str_ireplace( '%TAGS%', $tag_str, $tweet );
			
		}
		
	} else {
		
		$tweet = get_post_meta( $post_id, '_leenkme_tweet', true );
		
	}
	
	$tweet = apply_filters( 'leenkme_custom_replacement_args', $tweet, $post_id );
	
	return trim( html_entity_decode( $tweet, ENT_COMPAT, get_bloginfo('charset') ) );
	
}

function leenkme_ajax_tweet() {
	
	check_ajax_referer( 'tweet' );
	global $current_user;
	get_currentuserinfo();
	$user_id = $current_user->ID;
	
	global $dl_pluginleenkme;
	$user_settings = $dl_pluginleenkme->get_user_settings( $user_id );
	
	if ( $api_key = $user_settings['leenkme_API'] ) {
		
		$tweet = sprintf( __( 'Testing the @leenk_me Twitter Plugin for #WordPress %s - %d', 'leenkme' ), 'http://leenk.me/', rand(10,99) );
	
		$connect_arr[$api_key]['twitter_status'] = $tweet;
		
		$result = leenkme_ajax_connect( $connect_arr );
		
		if ( !empty( $result[$api_key] ) ) {			
		
			if ( is_wp_error( $result[$api_key] ) ) {
				
				die( $result[$api_key]->get_error_message() );	
				
			} else if ( !empty( $result[$api_key]['response']['code'] ) ) {
				
				die( $result[$api_key]['body'] );
				
			} else {
				
				die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );
			}
			
		} else {
			
			die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );
		
		}
		
	} else {
		
		die( __( 'ERROR: You have no entered your leenk.me API key. Please check your leenk.me settings.', 'leenkme' ) );
		
	}
	
}

function leenkme_ajax_retweet() {

	check_ajax_referer( 'leenkme' );
	
	if ( !empty( $_REQUEST['id'] ) ) {
		
		if ( get_post_meta( $_REQUEST['id'], '_twitter_exclude', true ) ) {
		
			die( __( 'You have excluded this post from publishing to your Twitter account. If you would like to publish it, edit the post and remove the exclude check box in the post settings.', 'leenkme' ) );
		
		} else if ( !empty( $_REQUEST['tweet'] ) ) {
			
			$results = leenkme_ajax_connect( leenkme_publish_to_twitter( array(), array( 'ID' => $_REQUEST['id'], 'post_author' => $_REQUEST['post_author'] ), $_REQUEST['tweet'], true ) );
			
			if ( !empty( $results ) ) {	
			
				$out = array();	
				
				foreach( $results as $result ) {	
		
					if ( is_wp_error( $result ) ) {
		
						$out[] = "<p>" . $result->get_error_message() . "</p>";
		
					} else if ( !empty( $result['response']['code'] ) ) {
		
						$out[] = "<p>" . $result['body'] . "</p>";
		
					} else {
		
						$out[] = "<p>" . __( 'Error received! Please check your <a href="admin.php?page=leenkme_twitter">Twitter settings</a> and try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
		
					}
		
				}
				
				die( join( (array)$out ) );
				
			} else {
				
				die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );

			}
			
		} else {
			
				die( __( 'ERROR: Unable to determine tweet. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );
				
		}
		
	} else {
		
		die( __( 'ERROR: Unable to determine Post ID.', 'leenkme' ) );
	
	}	
	
}

// Add function to publish to twitter
function leenkme_publish_to_twitter( $connect_arr = array(), $post, $tweet = false, $debug = false ) {
	
	global $wpdb, $dl_pluginleenkme, $dl_pluginleenkmeTwitter;
	
	$exclude_twitter = get_post_meta( $post['ID'], '_twitter_exclude', true );
	
	if ( !$exclude_twitter ) {
		
		$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
		
		if ( in_array( get_post_type( $post['ID'] ), $leenkme_settings['post_types'] ) ) {
			
			$options = get_option( 'leenkme_twitter' );
			
			$leenkme_users = leenkme_get_users();
			
			foreach ( $leenkme_users as $leenkme_user ) {
				
				$user_settings = $dl_pluginleenkme->get_user_settings( $leenkme_user->ID );
				
				if ( empty( $user_settings['leenkme_API'] ) )
					continue; 	//Skip user if they do not have an API key set
				
				$api_key = $user_settings['leenkme_API'];
				
				$options = $dl_pluginleenkmeTwitter->get_user_settings( $leenkme_user->ID );
				
				if ( !empty( $options ) ) {	
					
					if ( ( !empty( $options['tweetCats'] ) && !empty( $options['clude'] ) )
							&& !( 'in' == $options['clude'] && in_array( '0', $options['tweetCats'] ) ) ) {
						
						if ( 'ex' == $options['clude'] && in_array( '0', (array)$options['tweetCats'] ) ) {
							
							if ( $debug ) echo '<p>' . __( 'You have your <a href="admin.php?page=leenkme_twitter">Leenk.me Twitter settings</a> set to Exclude All Categories.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
						
						$match = false;
						
						$post_categories = wp_get_post_categories( $post['ID'] );
						
						foreach ( $post_categories as $cat ) {
						
							if ( in_array( (int)$cat, $options['tweetCats'] ) ) {
							
								$match = true;
								
							}
							
						}
						
						if ( ( 'ex' == $options['clude'] && $match ) ) {
							
							if ( $debug ) echo '<p>' . __( '<p>Post in an excluded category, check your <a href="admin.php?page=leenkme_twitter">Leenk.me Twitter settings</a> or remove the post from the excluded category.', 'leenkme' ) . '</p>';
							
							continue;
							
						} else if ( ( 'in' == $options['clude'] && !$match ) ) {
							
							if ( $debug ) echo '<p>' . __( 'Post not found in an included category, check your <a href="admin.php?page=leenkme_twitter">Leenk.me Twitter settings</a> or add the post into the included category.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
					}
					
					if ( $leenkme_user->ID != $post['post_author'] && ( 'mine' == $options['message_preference'] 
						|| ( 'manual' == $options['message_preference'] && !get_post_meta( $post['ID'], '_lm_tweet_type', true ) ) ) )
						$prefer_user = true;
					else
						$prefer_user = false;
												
					if ( $prefer_user ) {
						
						$tweet = stripslashes( html_entity_decode( get_leenkme_expanded_tweet( $post['ID'], $options['tweetFormat'], get_the_title( $post['ID'] ) ), ENT_COMPAT, get_bloginfo('charset') ) );
												
					} else {
						
						$manual = get_post_meta( $post['ID'], '_lm_tweet_type', true );
												
						if ( $manual )
							$tweet = get_post_meta( $post['ID'], '_leenkme_tweet', true );
													
						if ( empty( $tweet ) )
							$tweet = get_leenkme_expanded_tweet( $post['ID'], $options['tweetFormat'], get_the_title( $post['ID'] ) );												
						$tweet = stripslashes( html_entity_decode( $tweet, ENT_COMPAT, get_bloginfo('charset') ) );
					}
										
					if ( !has_filter( 'get_shortlink', 'leenkme_get_shortlink_handler', 1, 4 ) ) {
		
						if ( !( $url = get_post_meta( $post['ID'], '_leenkme_shortened_url', true ) ) )
							$url = leenkme_url_shortener( $post['ID'] );
							
						echo $tweet;
						
						if ( preg_match( '/%URL%/i', $tweet ) ) {
							
							$urlLen = strlen( $url );
							$tweetLen = strlen( utf8_decode( $tweet ) );
							$totalLen = $urlLen + $tweetLen - 5; // subtract 5 for "%URL%".
							
							if ( 140 >= $totalLen )
								$tweet = str_ireplace( "%URL%", $url, $tweet );
							else
								$tweet = str_ireplace( "%URL%", "", $tweet ); // Too Long (need to get rid of URL).
							
						}
							
						echo $tweet;
						
					}
					
					$connect_arr[$api_key]['twitter_status'] = $tweet;
					
				}
				
			}
			
		}
		
	}
	
	return $connect_arr;
	
}

// Actions and filters	
if ( !empty( $dl_pluginleenkmeTwitter ) ) {
	
	//This use to be 'save_post' but the save_post action happens AFTER the 'transition_post_status' action
	//which means the leenkme_connect function was running before the leenk.me meta data was being saved!
	add_action( 'transition_post_status', array( $dl_pluginleenkmeTwitter, 'leenkme_twitter_meta_tags' ), 10, 3 );
	
	// Whenever you publish a post, post to twitter
	add_filter( 'leenkme_connect', 'leenkme_publish_to_twitter', 10, 2 );
	
	add_action( 'wp_ajax_tweet', 'leenkme_ajax_tweet' );
	add_action( 'wp_ajax_retweet', 'leenkme_ajax_retweet' );
	
}