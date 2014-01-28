<?php

if ( ! class_exists( 'leenkme_FriendFeed' ) ) {

	// Define class
	class leenkme_FriendFeed {
	
		// Constructor
		function leenkme_FriendFeed() {
			//Not Currently Needed
		}
		
		/*--------------------------------------------------------------------
			Administrative Functions
		  --------------------------------------------------------------------*/
	  
		// Option loader function
		function get_user_settings( $user_id ) {
			
			// Default values for the options
			$options = array(
								 'friendfeed_myfeed'		=> true,
								 'friendfeed_group' 		=> false,
								 'friendfeed_body'			=> '%EXCERPT%',
								 'default_image'			=> '',
								 'force_friendfeed_image'	=> false,
								 'feed_cats' 				=> array( '0' ),
								 'clude' 					=> 'in',
								 'message_preference' 		=> 'author'
							);
							
			// Get values from the WP options table in the database, re-assign if found
			$user_settings = get_user_option( 'leenkme_friendfeed', $user_id );
			if ( !empty( $user_settings ) ) {
				
				foreach ( $user_settings as $key => $option ) {
					
					$options[$key] = $option;
					
				}
				
			}
			
			// Need this for initial INIT, for people who don't save the default settings...
			update_user_option( $user_id, 'leenkme_friendfeed', $user_settings );
			
			return $options;
			
		}
		
		// Print the admin page for the plugin
		function print_friendfeed_settings_page() {
			global $dl_pluginleenkme, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Get the user options
			$user_settings = $this->get_user_settings( $user_id );
			$friendfeed_settings = get_option( 'leenkme_friendfeed' );
			
			if ( isset( $_REQUEST['update_friendfeed_settings'] ) ) {
				
				if ( !empty( $_REQUEST['friendfeed_myfeed'] ) )
					$user_settings['friendfeed_myfeed'] = true;
				else
					$user_settings['friendfeed_myfeed'] = false;
				
				if ( !empty( $_REQUEST['friendfeed_group'] ) )
					$user_settings['friendfeed_group'] = true;
				else
					$user_settings['friendfeed_group'] = false;
				
				if ( !empty( $_REQUEST['friendfeed_body'] ) )
					$user_settings['friendfeed_body'] = $_REQUEST['friendfeed_body'];
				else
					$user_settings['friendfeed_body'] = '';
				
				if ( !empty( $_REQUEST['default_image'] ) )
					$user_settings['default_image'] = $_REQUEST['default_image'];
				else
					$user_settings['default_image'] = '';
				
				if ( !empty( $_REQUEST['force_friendfeed_image'] ) )
					$user_settings['force_friendfeed_image'] = true;
				else
					$user_settings['force_friendfeed_image'] = false;
	
				if ( !empty( $_REQUEST['clude'] ) && !empty( $_REQUEST['feed_cats'] ) ) {
					
					$user_settings['clude'] = $_REQUEST['clude'];
					$user_settings['feed_cats'] = $_REQUEST['feed_cats'];
					
				} else {
					
					$user_settings['clude'] = 'in';
					$user_settings['feed_cats'] = array( '0' );
					
				}
				
				if ( !empty( $_REQUEST['message_preference'] ) )
					$user_settings['message_preference'] = $_REQUEST['message_preference'];
				else
					$user_settings['message_preference'] = '';
				
				update_user_option( $user_id, 'leenkme_friendfeed', $user_settings );
				
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
					<h2 style='margin-bottom: 10px;' ><img src='<?php echo $dl_pluginleenkme->base_url; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> FriendFeed <?php _e( 'Settings', 'leenkme' ); ?> (<a href="http://leenk.me/2011/04/08/how-to-use-the-leenk-me-friendfeed-plugin-for-wordpress/" target="_blank">help</a>)</h2>
					
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Social Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
						<p><?php _e( 'Feed to MyFeed?', 'leenkme' ); ?> <input type="checkbox" id="friendfeed_myfeed" name="friendfeed_myfeed" <?php checked( $user_settings['friendfeed_myfeed'] ); ?> /></p>
						<p><?php _e( 'Feed to Group?', 'leenkme' ); ?> <input type="checkbox" id="friendfeed_group" name="friendfeed_group" <?php checked( $user_settings['friendfeed_group'] ); ?> /></p>

						<p>
                        	<input type="button" class="button" name="verify_friendfeed_connect" id="ff_publish" value="<?php _e( 'Feed a Test Message', 'leenkme' ) ?>" />
							<?php wp_nonce_field( 'ff_publish', 'ff_publish_wpnonce' ); ?>
                        
                            <input class="button-primary" type="submit" name="update_friendfeed_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>
					
						</div>
					
					</div>
					
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Message Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
                        	<table id="friendfeed_settings_table">
                            <tr>
                            	<td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Default Description:', 'leenkme' ); ?></td>
                                <td><textarea name="friendfeed_body" style="width: 500px;" maxlength="350"><?php echo $user_settings['friendfeed_body']; ?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="friendfeed-format" style="margin-left: 50px;">
                                        <p style="font-size: 11px; margin-bottom: 0px;">Format Options:</p>
                                        <ul style="font-size: 11px;">
                                            <li>%TITLE% - <?php _e( 'Displays the post title.', 'leenkme' ); ?></li>
                                            <li>%WPSITENAME% - <?php _e( 'Displays the WordPress site name (found in Settings -> General).', 'leenkme' ); ?></li>
                                            <li>%WPTAGLINE% - <?php _e( 'Displays the WordPress TagLine (found in Settings -> General).', 'leenkme' ); ?></li>
                                            <li>%EXCERPT% - <?php _e( 'Displays the WordPress Post Excerpt (only used with Description Field).', 'leenkme' ); ?></li>
                                        </ul>
                                    </div>
                            	</td>
                            </tr>
                            <tr>
                            	<td><?php _e( 'Default Image URL:', 'leenkme' ); ?></td>
                                <td>
                                    <input name="default_image" type="text" style="width: 500px;" value="<?php _e(  $user_settings['default_image'], 'leenkme' ) ?>" />
                                    <input type="checkbox" id="force_friendfeed_image" name="force_friendfeed_image" <?php checked( $user_settings['force_friendfeed_image'] ); ?> /> <?php _e( 'Always Use', 'leenkme' ); ?>
                                </td>
                            </tr> 
                            <tr>
                            	<td><?php _e( 'Message Preference:', 'leenkme' ); ?></td>
                                <td>
                                	<select id="message_preference" name="message_preference">
                                        <option value="author" <?php selected( 'author', $user_settings['message_preference'] ) ?>><?php _e( "Author", 'leenkme' ); ?></option>
                                        <option value="mine" <?php selected( 'mine', $user_settings['message_preference'] ) ?>><?php _e( "Mine", 'leenkme' ); ?></option>
                                        <option value="manual" <?php selected( 'manual', $user_settings['message_preference'] ) ?>><?php _e( "Manual", 'leenkme' ); ?></option>
                                    </select>
                                </td>
                            </tr> 
                            <tr>
                                <td colspan="2"> 
                                    <div class="format-preference" style="margin-left: 50px;">
                                        <p style="font-size: 11px; margin-bottom: 0px;">Format Preference Options:</p>
                                        <ul style="font-size: 11px;">
                                            <li><?php _e( "Author - Most efficient, uses the post author's Message Settings.", 'leenkme' ); ?></li>
                                            <li><?php _e( 'Mine - Most inefficient, uses your Message Settings regardless of what the post author does.', 'leenkme' ); ?></li>
                                            <li><?php _e( 'Manual - Slightly inefficient, uses your Message Settigns unless the post author manually changes the message in the post.', 'leenkme' ); ?></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </table>
                        
                            <p>
                                <input class="button-primary" type="submit" name="update_friendfeed_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                            </p>
                        
						</div>
					
					</div>
					
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Feed Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
						<p><?php _e( 'Feed Categories:', 'leenkme' ); ?> 
					
						<div class="feed-cats" style="margin-left: 50px;">
							<p>
							<input type='radio' name='clude' id='include_cat' value='in' <?php checked( 'in', $user_settings['clude'] ); ?> /><label for='include_cat'><?php _e( 'Include', 'leenkme' ); ?></label> &nbsp; &nbsp; <input type='radio' name='clude' id='exclude_cat' value='ex' <?php checked( 'ex', $user_settings['clude'] ); ?> /><label for='exclude_cat'><?php _e( 'Exclude', 'leenkme' ); ?></label> </p>
							<p>
							<select id='categories' name='feed_cats[]' multiple="multiple" size="5" style="height: 70px; width: 150px;">
							<option value="0" <?php selected( in_array( '0', (array)$user_settings['feed_cats'] ) ); ?>><?php _e( 'All Categories', 'leenkme' ); ?></option>
                            
							<?php 
							$categories = get_categories( array( 'hide_empty' => 0, 'orderby' => 'name' ) );
							foreach ( (array)$categories as $category ) {
								?>
								
								<option value="<?php echo $category->term_id; ?>" <?php selected( in_array( $category->term_id, (array)$user_settings['feed_cats'] ) ); ?>><?php echo $category->name; ?></option>
			
			
								<?php
							}
							?>
                            
							</select></p>
							<p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'To deselect hold the SHIFT key on your keyboard while you click the category.', 'leenkme' ); ?></p>
						</div>
                        
                        <p>
                            <input class="button-primary" type="submit" name="update_friendfeed_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
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
		
		function leenkme_friendfeed_meta_tags( $new_status, $old_status, $post ) {
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;
				
			if ( isset( $_REQUEST['_inline_edit'] ) || isset( $_REQUEST['doing_wp_cron'] ) )
				return;
	
			if ( !empty( $_REQUEST['lm_friendfeed_type'] ) ) {
				$manual = true;
				update_post_meta( $post->ID, '_lm_friendfeed_type', true );
			} else {
				$manual = false;
				delete_post_meta( $post->ID, '_lm_friendfeed_type' );
			}
			
			if ( $manual ) {
				
				if ( !empty( $_REQUEST['friendfeed_body'] ) )
					update_post_meta( $post->ID, '_friendfeed_body', $_REQUEST['friendfeed_body'] );
				else
					delete_post_meta( $post->ID, '_friendfeed_body' );
		
				if ( !empty( $_REQUEST['friendfeed_image'] ) )
					update_post_meta( $post->ID, '_friendfeed_image', $_REQUEST['friendfeed_image'] );
				else
					delete_post_meta( $post->ID, '_friendfeed_image' );
				
			}
	
			if ( !empty( $_REQUEST['friendfeed_exclude_myfeed'] ) )
				update_post_meta( $post->ID, '_friendfeed_exclude_myfeed', $_REQUEST['friendfeed_exclude_myfeed'] );
			else
				delete_post_meta( $post->ID, '_friendfeed_exclude_myfeed' );
	
			if ( !empty( $_REQUEST['friendfeed_exclude_group'] ) )
				update_post_meta( $post->ID, '_friendfeed_exclude_group', $_REQUEST['friendfeed_exclude_group'] );
			else
				delete_post_meta( $post->ID, '_friendfeed_exclude_group' );
			
		}
		
		function leenkme_friendfeed_meta_box() {
			
			global $post, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			if ( $exclude_myfeed = get_post_meta( $post->ID, 'friendfeed_exclude_myfeed', true ) ) {
				
				delete_post_meta( $post->ID, 'friendfeed_exclude_myfeed', true );
				update_post_meta( $post->ID, '_friendfeed_exclude_myfeed', $exclude_myfeed );
				
				
			}
			$exclude_myfeed = get_post_meta( $post->ID, '_friendfeed_exclude_myfeed', true ); 
			
			if ( $exclude_group = get_post_meta( $post->ID, 'friendfeed_exclude_group', true ) ) {
				
				delete_post_meta( $post->ID, 'friendfeed_exclude_group', true );
				update_post_meta( $post->ID, '_friendfeed_exclude_group', $exclude_group );
				
				
			}
			$exclude_group = get_post_meta( $post->ID, '_friendfeed_exclude_group', true );
			
			if ( $friendfeed_array['body'] = get_post_meta( $post->ID, 'friendfeed_body', true ) ) {
				
				delete_post_meta( $post->ID, 'friendfeed_body', true );
				update_post_meta( $post->ID, '_friendfeed_body', $friendfeed_array['body'] );
				
				
			}
			$friendfeed_array['body'] = get_post_meta( $post->ID, '_friendfeed_body', true );
			
			if ( $friendfeed_array['picture'] = get_post_meta( $post->ID, 'friendfeed_image', true ) ) {
				
				delete_post_meta( $post->ID, 'friendfeed_image', true );
				update_post_meta( $post->ID, '_friendfeed_image', $friendfeed_array['picture'] );
				
				
			}
			$friendfeed_array['picture'] = get_post_meta( $post->ID, '_friendfeed_image', true );
			
			$format_type = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_lm_friendfeed_type', true ) ) );
			
			$user_settings = $this->get_user_settings( $user_id );
			$friendfeed_settings = get_option( 'leenkme_friendfeed' ); ?>
    
    		<div id="ff_format_options">
				<?php 
                _e( 'Format:', 'leenkme' );
                echo " ";
                ?>
                    
                <span id="lm_friendfeed_format" class="ff_manual_format manual_format" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>"><?php _e( 'Manual', 'leenkme' ); ?></span> <a id="set_to_default_ff_post" href="#" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>">Reset</a>
                <span id="lm_friendfeed_format" class="ff_default_format default_format" style="display:<?php if ( $format_type ) echo "none"; else echo "inline"; ?>"><?php _e( 'Default', 'leenkme' ); ?></span>
                <input type="hidden" name="lm_friendfeed_type" value="<?php echo $format_type; ?>" />
                <input type="hidden" name="friendfeed_body_format" value="<?php echo $user_settings['friendfeed_body']; ?>" />
                <input type="hidden" name="friendfeed_image" value="<?php echo $friendfeed_array['picture'] ?>" />
            </div>
            
            <div id="lm_friendfeed_box">
            
            	<?php 
				if ( 0 == $format_type ) {
				
					 $friendfeed_array['body'] 		= $user_settings['friendfeed_body'];
				
				}
				
				$friendfeed_content = get_leenkme_expanded_ff_post( $post->ID, $friendfeed_array ); ?>
            
                <textarea id="lm_ff_body" name="friendfeed_body" maxlength="350"><?php echo $friendfeed_content['body']; ?></textarea>
            
                <div id="lm_ff_attachment_meta_area">
                
                	<div id="lm_ff_image">
                		<img id='lm_ff_image_src' src='<?php echo $friendfeed_content['picture']; ?>' />
                    </div>
                
                </div>
                
            </div>
            
            <div id="lm_friendfeed_options">
            
            	<div id="lm_fb_exlusions">
					<?php if ( $user_settings['friendfeed_myfeed'] ) { ?>
                    <?php _e( 'Exclude from MyFeed:', 'leenkme' ) ?>
                    <input style="margin-top: 5px;" type="checkbox" name="friendfeed_exclude_myfeed" <?php checked( $exclude_myfeed || 'on' == $exclude_myfeed ); ?> />
                    <br />
                    <?php } ?>
                    <?php if ( $user_settings['friendfeed_group'] ) { ?>
                    <?php _e( 'Exclude from Group:', 'leenkme' ) ?>
                    <input style="margin-top: 5px;" type="checkbox" name="friendfeed_exclude_group" <?php checked( $exclude_group || 'on' == $exclude_group ); ?> />
                    <?php } ?>
                </div>
                
                <div id="lm_fb_republish">
					<?php // Only show RePublish button if the post is "published"
                    if ( 'publish' === $post->post_status ) { ?>
						<input style="float: right;" type="button" class="button" name="refeed_friendfeed" id="lm_refeed_button" value="<?php _e( 'ReFeed', 'leenkme' ) ?>" />                   
                     <?php } ?>
                </div>
                
            </div>
			<?php

		}

	}
	
}

if ( class_exists( 'leenkme_FriendFeed' ) ) {
	$dl_pluginleenkmeFriendFeed = new leenkme_FriendFeed();
}

function get_leenkme_expanded_ff_post( $post_id, $friendfeed_array, $post_title = false, $excerpt = false, $user_id = false ) {
	
	if ( !empty( $friendfeed_array ) ) {
	
		global $current_user, $dl_pluginleenkmeFriendFeed;
		
		if ( !$user_id ) {
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
		}
		
		$maxBodyLen = 350;
	
		if ( false === $post_title )
			$post_title = get_the_title( $post_id );
		
		if ( false === $excerpt || empty( $excerpt ) ) {
			
			$post = get_post( $post_id );
		
			if ( !empty( $post->post_excerpt ) ) {
				
				//use the post_excerpt if available for the friendfeed description
				$excerpt = $post->post_excerpt; 
				
			} else {
				
				//otherwise we'll pare down the description
				$excerpt = $post->post_content; 
				
			}
			
		}
		
		$friendfeed_array['body'] = leenkme_trim_words( leenkme_replacements_args( $friendfeed_array['body'], $post_title, $post_id, $excerpt ), $maxBodyLen );
		
		$user_settings = $dl_pluginleenkmeFriendFeed->get_user_settings( $user_id );
			
		$friendfeed_array['picture'] = leenkme_get_picture( $user_settings, $post_id, 'friendfeed' );
			
	
	}
	
	return $friendfeed_array;
	
}

function leenkme_ajax_refeed() {

	check_ajax_referer( 'leenkme' );
	
	if ( !empty( $_REQUEST['id'] ) && !empty( $_REQUEST['friendfeed_array'] ) ) {

		if ( get_post_meta( $_REQUEST['id'], '_friendfeed_exclude_myfeed', true ) 
				&& get_post_meta( $_REQUEST['id'], '_friendfeed_exclude_group', true ) ) {

			die( __( 'You have excluded this post from feeding to your FriendFeed MyFeed and Group. If you would like to feed it, edit the post and remove the appropriate exclude check boxes.', 'leenkme' ) );

		} else {
			
			$results = leenkme_ajax_connect( leenkme_publish_to_friendfeed( array(), array( 'ID' => $_REQUEST['id'], 'post_author' => $_REQUEST['post_author'] ), $_REQUEST['friendfeed_array'], true ) );
		
			if ( !empty( $results ) ) {		
				
				foreach( $results as $result ) {	
		
					if ( is_wp_error( $result ) ) {
		
						$out[] = "<p>" . $result->get_error_message() . "</p>";
		
					} else if ( !empty( $result['response']['code'] ) ) {
		
						$response = json_decode( $result['body'] );
						$out[] = $response[1];
		
					} else {
		
						$out[] = "<p>" . __( 'Error received! Please check your <a href="admin.php?page=leenkme_friendfeed">Friendfeed settings</a> and try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
		
					}
		
				}
				
				die( join( (array)$out ) );
				
			} else {
				
				die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );
	
			}
			
		}
		
	} else {
		
		die( __( 'ERROR: Unable to determine Post ID.', 'leenkme' ) );
	
	}

}

function leenkme_ajax_ff() {

	check_ajax_referer( 'ff_publish' );
	global $current_user;
	get_currentuserinfo();
	$user_id = $current_user->ID;
	
	global $dl_pluginleenkme;
	$user_settings = $dl_pluginleenkme->get_user_settings( $user_id );

	if ( $api_key = $user_settings['leenkme_API'] ) {

		$body = __( "Testing leenk.me's FriendFeed Plugin for WordPress - A webapp that allows you to publicize your WordPress posts automatically.", 'leenkme' );
		$url = 'http://leenk.me/';
		$picture = 'http://leenk.me/leenkme.png';
		
		$connect_arr[$api_key]['friendfeed_body'] = $body;
		$connect_arr[$api_key]['friendfeed_link'] = $url;
		$connect_arr[$api_key]['friendfeed_picture'] = $picture;
						
		if ( !empty( $_REQUEST['friendfeed_myfeed'] ) 
				&& ( 'true' === $_REQUEST['friendfeed_myfeed'] || 'checked' === $_REQUEST['friendfeed_myfeed'] ) )
			$connect_arr[$api_key]['friendfeed_myfeed'] = true;
		
		if ( !empty( $_REQUEST['friendfeed_group'] ) 
				&& ( 'true' === $_REQUEST['friendfeed_group'] || 'checked' === $_REQUEST['friendfeed_group'] ) )
			$connect_arr[$api_key]['friendfeed_group'] = true;

		$result = leenkme_ajax_connect( $connect_arr );
		
		if ( !empty( $result[$api_key] ) ) {
				
			if ( is_wp_error( $result[$api_key] ) ) {
				
				die( $result[$api_key]->get_error_message() );	
				
			} else if ( !empty( $result[$api_key]['response']['code'] ) ) {
		
				$response = json_decode( $result[$api_key]['body'] );
				die( $response[1] );
				
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
									
// Add function to pubslih to friendfeed
function leenkme_publish_to_friendfeed( $connect_arr = array(), $post, $friendfeed_array = array(), $debug = false  ) {
	
	global $dl_pluginleenkme, $dl_pluginleenkmeFriendFeed;
	
	if ( get_post_meta( $post['ID'], '_friendfeed_exclude_myfeed', true ) )		
		$exclude_myfeed = true;
	else
		$exclude_myfeed = false;
	
	if ( get_post_meta( $post['ID'], '_friendfeed_exclude_group', true ) )
		$exclude_group = true;
	else
		$exclude_group = false;
	
	if ( !( $exclude_myfeed && $exclude_group ) ) {
		
		$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
		
		if ( in_array( get_post_type( $post['ID'] ), $leenkme_settings['post_types'] ) ) {
			
			$options = get_option( 'leenkme_friendfeed' );
			
			$leenkme_users = leenkme_get_users();
			
			if ( !( $url = get_post_meta( $post['ID'], '_leenkme_shortened_url', true ) ) )
				$url = leenkme_url_shortener( $post['ID'] );
			
			foreach ( $leenkme_users as $leenkme_user ) {

				$user_settings = $dl_pluginleenkme->get_user_settings( $leenkme_user->ID );

				if ( empty( $user_settings['leenkme_API'] ) )
					continue;	//Skip user if they do not have an API key set
				
				$api_key = $user_settings['leenkme_API'];
				
				$options = $dl_pluginleenkmeFriendFeed->get_user_settings( $leenkme_user->ID );
				
				if ( !empty( $options ) ) {
					
					if ( !empty( $options['feed_cats'] ) && !empty( $options['clude'] )
							&& !( 'in' == $options['clude'] && in_array( '0', $options['feed_cats'] ) ) ) {
						
						if ( 'ex' == $options['clude'] && in_array( '0', $options['feed_cats'] ) ) {
							
							if ( $debug ) echo '<p>' . ___( 'You have your <a href="admin.php?page=leenkme_friendfeed">Leenk.me FriendFeed settings</a> set to Exclude All Categories.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
						
						$match = false;
						
						$post_categories = wp_get_post_categories( $post['ID'] );
						
						foreach ( $post_categories as $cat ) {
						
							if ( in_array( (int)$cat, $options['feed_cats'] ) ) {
							
								$match = true;
								
							}
							
						}
						
						if ( ( 'ex' == $options['clude'] && $match ) ) {
							
							if ( $debug ) echo '<p>' . ___( 'Post in an excluded category, check your <a href="admin.php?page=leenkme_friendfeed">Leenk.me FriendFeed settings</a> or remove the post from the excluded category.', 'leenkme' ) . '</p>';
							
							continue;
							
						} else if ( ( 'in' == $options['clude'] && !$match ) ) {
							
							if ( $debug ) echo '<p>' . ___( 'Post not found in an included category, check your <a href="admin.php?page=leenkme_friendfeed">Leenk.me FriendFeed settings</a> or add the post into the included category.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
					}
						
					if ( !$options['friendfeed_myfeed'] && !$options['friendfeed_group'])
						continue;	//Skip this user if they don't have Profile or Page checked in plugins FriendFeed Settings
	
					// Added friendfeed profile to connection array if enabled
					if ( $options['friendfeed_myfeed'] && !$exclude_myfeed ) {
						
						$connect_arr[$api_key]['friendfeed_myfeed'] = true;
						
					}
	
					// Added friendfeed page to connection array if enabled
					if ( $options['friendfeed_group'] && !$exclude_group ) {
						
						$connect_arr[$api_key]['friendfeed_group'] = true;
						
					}
					
					if ( $leenkme_user->ID != $post['post_author'] && ( 'mine' == $options['message_preference'] 
						|| ( 'manual' == $options['message_preference']  && !get_post_meta( $post['ID'], '_lm_friendfeed_type', true ) ) ) )
						$prefer_user = true;
					else
						$prefer_user = false;
						
					if ( $prefer_user ) {
						
						$prefer_friendfeed_array['body'] = $options['friendfeed_body'];
							
						$prefer_friendfeed_array = get_leenkme_expanded_ff_post( $post['ID'], $prefer_friendfeed_array, false, false, $leenkme_user->ID );
														
						if ( !empty( $prefer_friendfeed_array['picture'] ) )
							$connect_arr[$api_key]['friendfeed_picture'] = $prefer_friendfeed_array['picture'];
							
						
						$connect_arr[$api_key]['friendfeed_body'] = stripslashes( html_entity_decode( $prefer_friendfeed_array['body'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['friendfeed_link'] = $url;
						
					} else {
						
						$manual = get_post_meta( $post['ID'], '_lm_friendfeed_type', true );
						
						if ( $manual ) {
							
							$friendfeed_array['body'] = get_post_meta( $post['ID'], '_friendfeed_body', true );
							
						} else {
															
							if ( empty( $friendfeed_array['body'] ) )
								$friendfeed_array['body'] = $options['friendfeed_body'];
								
							$friendfeed_array = get_leenkme_expanded_ff_post( $post['ID'], $friendfeed_array, false, false, $leenkme_user->ID );
																
						}
						
						if ( !empty( $friendfeed_array['picture'] ) )
							$connect_arr[$api_key]['friendfeed_picture'] = $friendfeed_array['picture'];
						else
							$connect_arr[$api_key]['friendfeed_picture'] = leenkme_get_picture( $user_settings, $post['ID'], 'friendfeed' );
						
						$connect_arr[$api_key]['friendfeed_body'] = stripslashes( html_entity_decode( $friendfeed_array['body'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['friendfeed_link'] = $url;
					
					}
					
				}
				
			}
			
		}
		
	}
		
	return $connect_arr;
	
}

// Actions and filters	
if ( !empty( $dl_pluginleenkmeFriendFeed ) ) {
	
	//This use to be 'save_post' but the save_post action happens AFTER the 'transition_post_status' action
	//which means the leenkme_connect function was running before the leenk.me meta data was being saved!
	add_action( 'transition_post_status', array( $dl_pluginleenkmeFriendFeed, 'leenkme_friendfeed_meta_tags' ), 10, 3 );

	// Whenever you publish a post, post to friendfeed
	add_filter('leenkme_connect', 'leenkme_publish_to_friendfeed', 20, 2);
	
	add_action( 'wp_ajax_ff_publish', 'leenkme_ajax_ff' );
	add_action( 'wp_ajax_refeed', 'leenkme_ajax_refeed' );
	
}
