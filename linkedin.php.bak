<?php	

if ( ! class_exists( 'leenkme_LinkedIn' ) ) {
		
	// Define class
	class leenkme_LinkedIn {
	
		// Constructor
		function leenkme_LinkedIn() {
			//Not Currently Needed
		}
		
		/*--------------------------------------------------------------------
			Administrative Functions
		  --------------------------------------------------------------------*/
		
		// Option loader function
		function get_user_settings( $user_id ) {
			
			// Default values for the options
			$defaults = array(
								'linkedin_profile'		=> true,
								'linkedin_group'		=> false,
								'linkedin_company'		=> false,
								'linkedin_comment'		=> '%TITLE%',
								'linkedin_title'		=> '%WPSITENAME%',
								'linkedin_description'	=> '%EXCERPT%',
								'default_image' 		=> '',
								'force_linkedin_image' 	=> false,
								'share_cats'			=> array( '0' ),
								'clude'					=> 'in',
								'message_preference'		=> 'author'
							);
							
			// Get values from the WP options table in the database, re-assign if found
			$user_settings = get_user_option( 'leenkme_linkedin', $user_id );
			
			return wp_parse_args( $user_settings, $defaults );
			
		}
		
		// Print the admin page for the plugin
		function print_linkedin_settings_page() {
			global $dl_pluginleenkme, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Get the user options
			$user_settings = $this->get_user_settings( $user_id );
			$linkedin_settings = get_option( 'leenkme_linkedin' );
			
			if ( isset( $_REQUEST['update_linkedin_settings'] ) ) {
				
				if ( !empty( $_REQUEST['linkedin_profile'] ) )
					$user_settings['linkedin_profile'] = true;
				else
					$user_settings['linkedin_profile'] = false;
				
				if ( !empty( $_REQUEST['linkedin_group'] ) )
					$user_settings['linkedin_group'] = true;
				else
					$user_settings['linkedin_group'] = false;
				
				if ( !empty( $_REQUEST['linkedin_company'] ) )
					$user_settings['linkedin_company'] = true;
				else
					$user_settings['linkedin_company'] = false;
				
				if ( !empty( $_REQUEST['linkedin_comment'] ) )
					$user_settings['linkedin_comment'] = $_REQUEST['linkedin_comment'];
				else
					$user_settings['linkedin_comment'] = '';
	
				if ( !empty( $_REQUEST['linkedin_title'] ) )
					$user_settings['linkedin_title'] = $_REQUEST['linkedin_title'];
				else
					$user_settings['linkedin_title'] = '';
	
				if ( !empty( $_REQUEST['linkedin_description'] ) )
					$user_settings['linkedin_description'] = $_REQUEST['linkedin_description'];
				else
					$user_settings['linkedin_description'] = '';
				
				if ( !empty( $_REQUEST['default_image'] ) )
					$user_settings['default_image'] = $_REQUEST['default_image'];
				else
					$user_settings['default_image'] = '';
				
				if ( !empty( $_REQUEST['force_linkedin_image'] ) )
					$user_settings['force_linkedin_image'] = true;
				else
					$user_settings['force_linkedin_image'] = false;
	
				if ( !empty( $_REQUEST['clude'] ) && !empty( $_REQUEST['share_cats'] ) ) {
					
					$user_settings['clude'] = $_REQUEST['clude'];
					$user_settings['share_cats'] = $_REQUEST['share_cats'];
					
				} else {
					
					$user_settings['clude'] = 'in';
					$user_settings['share_cats'] = array( '0' );
					
				}
				
				if ( !empty( $_REQUEST['message_preference'] ) )
					$user_settings['message_preference'] = $_REQUEST['message_preference'];
				else
					$user_settings['message_preference'] = '';
				
				update_user_option( $user_id, 'leenkme_linkedin', $user_settings );
				
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
                
					<h2 style='margin-bottom: 10px;' ><img src='<?php echo $dl_pluginleenkme->base_url; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> LinkedIn <?php _e( 'Settings', 'leenkme' ); ?> (<a href="http://leenk.me/2010/12/01/how-to-use-the-leenk-me-linkedin-plugin-for-wordpress/" target="_blank"><?php _e( 'help', 'leenkme' ); ?></a>)</h2>
                    
                    <div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Social Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
						
							<p><?php _e( 'Share to Personal Profile?', 'leenkme' ); ?> <input type="checkbox" id="linkedin_profile" name="linkedin_profile" <?php checked( $user_settings['linkedin_profile'] ); ?> /></p>
							<p><?php _e( 'Share to Group?', 'leenkme' ); ?> <input type="checkbox" id="linkedin_group" name="linkedin_group" <?php checked( $user_settings['linkedin_group'] ); ?> /></p>
							<p><?php _e( 'Share to Company?', 'leenkme' ); ?> <input type="checkbox" id="linkedin_company" name="linkedin_company" <?php checked( $user_settings['linkedin_company'] ); ?> /></p>
                        
                            <p>
                                <input type="button" class="button" name="verify_linkedin_connect" id="li_share" value="<?php _e( 'Share a Test Message', 'leenkme' ) ?>" />
                                <?php wp_nonce_field( 'li_share', 'li_share_wpnonce' ); ?>
                            
                                <input class="button-primary" type="submit" name="update_linkedin_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                            </p>
							
						</div>
					
					</div>
                    
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Message Settings' ); ?></span></h3>
						
						<div class="inside">
                        	<table id="linkedin_settings_table">
                            <tr>
                            	<td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Default Comment:', 'leenkme' ); ?></td>
                                <td><textarea name="linkedin_comment" style="width: 500px;" maxlength="700"><?php echo $user_settings['linkedin_comment']; ?></textarea></td>
                            </tr>
                            <tr>
                            	<td><?php _e( 'Default Link Name:', 'leenkme' ); ?></td>
                                <td><input name="linkedin_title" type="text" style="width: 500px;" value="<?php echo $user_settings['linkedin_title']; ?>" maxlength="200"/></td>
                            </tr>
                            <tr>
                            	<td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Default Description:', 'leenkme' ); ?></td>
                                <td><textarea name="linkedin_description" style="width: 500px;" maxlength="256"><?php echo $user_settings['linkedin_description']; ?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="linkedin-format" style="margin-left: 50px;">
                                    <p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'Format Options:', 'leenkme' ); ?></p>
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
                                    <input name="default_image" type="text" style="width: 500px;" value="<?php _e( $user_settings['default_image'], 'leenkme' ) ?>" />
                                    <input type="checkbox" id="force_linkedin_image" name="force_linkedin_image" <?php checked( $user_settings['force_linkedin_image'] ); ?> /> <?php _e( 'Always use', 'leenkme' ); ?>
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
                            
                                <input class="button-primary" type="submit" name="update_linkedin_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                            </p>
                    
                        </div>
                    
                    </div>
                       
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Publish Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
						<p><?php _e( 'Share Categories:', 'leenkme' ); ?></p>
					
						<div class="share-cats" style="margin-left: 50px;">
						<p>
						<input type='radio' name='clude' id='include_cat' value='in' <?php checked( 'in', $user_settings['clude'] ); ?> /><label for='include_cat'><?php _e( 'Include', 'leenkme' ); ?></label> &nbsp; &nbsp; <input type='radio' name='clude' id='exclude_cat' value='ex' <?php checked( 'ex', $user_settings['clude'] ); ?> /><label for='exclude_cat'><?php _e( 'Exclude', 'leenkme' ); ?></label> </p>
						<p>
						<select id='categories' name='share_cats[]' multiple="multiple" size="5" style="height: 70px; width: 150px;">
							<option value="0" <?php selected( in_array( "0", (array)$user_settings['share_cats'] ) ); ?>><?php _e( 'All Categories', 'leenkme' ); ?></option>
						<?php 
						$categories = get_categories( array( 'hide_empty' => 0, 'orderby' => 'name' ) );
						
						foreach ( (array)$categories as $category ) {
							?>
							
							<option value="<?php echo $category->term_id; ?>" <?php selected( in_array( $category->term_id, (array)$user_settings['share_cats'] ) ); ?>><?php echo $category->name; ?></option>
		
		
							<?php
						}
						?>
                        
						</select></p>
						<p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'To "deselect" hold the SHIFT key on your keyboard while you click the category.', 'leenkme' ); ?></p>
						
						</div>
                        
                        <p>
                            <input class="button-primary" type="submit" name="update_linkedin_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
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
		
		function leenkme_linkedin_meta_tags( $new_status, $old_status, $post ) {
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;
				
			if ( isset( $_REQUEST['_inline_edit'] ) || isset( $_REQUEST['doing_wp_cron'] ) )
				return;
	
			if ( !empty( $_REQUEST['lm_linkedin_type'] ) ) {
				$manual = true;
				update_post_meta( $post->ID, '_lm_linkedin_type', true );
			} else {
				$manual = false;
				delete_post_meta( $post->ID, '_lm_linkedin_type' );
			}
			
			if ( $manual ) {
				
				if ( !empty( $_REQUEST['linkedin_comment'] ) )
					update_post_meta( $post->ID, '_linkedin_comment', $_REQUEST['linkedin_comment'] );
				else
					delete_post_meta( $post->ID, '_linkedin_comment' );
				
				if ( !empty( $_REQUEST['linkedin_title'] ) )
					update_post_meta( $post->ID, '_linkedin_title', $_REQUEST['linkedin_title'] );
				else
					delete_post_meta( $post->ID, '_linkedin_title' );
				
				if ( !empty( $_REQUEST['linkedin_description'] ) )
					update_post_meta( $post->ID, '_linkedin_description', $_REQUEST['linkedin_description'] );
				else
					delete_post_meta( $post->ID, '_linkedin_description' );
		
				if ( !empty( $_REQUEST['linkedin_image'] ) )
					update_post_meta( $post->ID, '_linkedin_image', $_REQUEST['linkedin_image'] );
				else
					delete_post_meta( $post->ID, '_linkedin_image' );
				
			}
				
	
			if ( !empty( $_REQUEST['linkedin_exclude'] ) )
				update_post_meta( $post->ID, '_linkedin_exclude', $_REQUEST['linkedin_exclude'] );
			else
				delete_post_meta( $post->ID, '_linkedin_exclude' );
	
			if ( !empty( $_REQUEST['linkedin_exclude_group'] ) )
				update_post_meta( $post->ID, '_linkedin_exclude_group', $_REQUEST['linkedin_exclude_group'] );
			else
				delete_post_meta( $post->ID, '_linkedin_exclude_group' );

			if ( !empty( $_REQUEST['linkedin_exclude_company'] ) )
				update_post_meta( $post->ID, '_linkedin_exclude_company', $_REQUEST['linkedin_exclude_company'] );
			else
				delete_post_meta( $post->ID, '_linkedin_exclude_company' );
				
		}
		
		function leenkme_linkedin_meta_box()  {
			
			global $post, $current_user;
		
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			if ( $linkedin_exclude = get_post_meta( $post->ID, 'linkedin_exclude', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_exclude', true );
				update_post_meta( $post->ID, '_linkedin_exclude', $linkedin_exclude );
				
				
			}
			$linkedin_exclude = get_post_meta( $post->ID, '_linkedin_exclude', true ); 
			
			if ( $linkedin_exclude_group = get_post_meta( $post->ID, '_linkedin_exclude_group', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_exclude_group', true );
				update_post_meta( $post->ID, '_linkedin_exclude_group', $linkedin_exclude_group );
				
				
			}
			$linkedin_exclude_group = get_post_meta( $post->ID, '_linkedin_exclude_group', true ); 

			if ( $linkedin_exclude_company = get_post_meta( $post->ID, '_linkedin_exclude_company', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_exclude_company', true );
				update_post_meta( $post->ID, '_linkedin_exclude_company', $linkedin_exclude_company );
				
				
			}
			$linkedin_exclude_company = get_post_meta( $post->ID, '_linkedin_exclude_company', true ); 
			
			if ( $linkedin_array['comment'] = get_post_meta( $post->ID, 'linkedin_comment', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_comment', true );
				update_post_meta( $post->ID, '_linkedin_comment', $linkedin_array['comment'] );
				
				
			}
			$linkedin_array['comment'] = get_post_meta( $post->ID, '_linkedin_comment', true);
			
			if ( $linkedin_array['linktitle'] = get_post_meta( $post->ID, 'linkedin_title', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_title', true );
				update_post_meta( $post->ID, '_linkedin_title', $linkedin_array['linktitle'] );
				
				
			}
			$linkedin_array['linktitle'] = get_post_meta( $post->ID, '_linkedin_title', true);
			
			if ( $linkedin_array['description'] = get_post_meta( $post->ID, 'linkedin_description', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_description', true );
				update_post_meta( $post->ID, '_linkedin_description', $linkedin_array['description'] );
				
				
			}
			$linkedin_array['description'] = get_post_meta( $post->ID, '_linkedin_description', true);
			
			if ( $linkedin_array['picture'] = get_post_meta( $post->ID, 'linkedin_image', true ) ) {
				
				delete_post_meta( $post->ID, 'linkedin_image', true );
				update_post_meta( $post->ID, '_linkedin_image', $linkedin_array['picture'] );
				
				
			}
			$linkedin_array['picture'] = get_post_meta( $post->ID, '_linkedin_image', true );
			
			$format_type = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_lm_linkedin_type', true ) ) );
			
			$user_settings = $this->get_user_settings( $user_id ); ?>
    
    		<div id="li_format_options">
				<?php 
                _e( 'Format:', 'leenkme' );
                echo " ";
                ?>
                    
                <span id="lm_linkedin_format" class="li_manual_format manual_format" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>"><?php _e( 'Manual', 'leenkme' ); ?></span> <a id="set_to_default_li_post" href="#" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>">Reset</a>
                <span id="lm_linkedin_format" class="li_default_format default_format" style="display:<?php if ( $format_type ) echo "none"; else echo "inline"; ?>"><?php _e( 'Default', 'leenkme' ); ?></span>
                <input type="hidden" name="lm_linkedin_type" value="<?php echo $format_type; ?>" />
                <input type="hidden" name="linkedin_comment_format" value="<?php echo $user_settings['linkedin_comment']; ?>" />
                <input type="hidden" name="linkedin_linktitle_format" value="<?php echo $user_settings['linkedin_title']; ?>" />
                <input type="hidden" name="linkedin_description_format" value="<?php echo $user_settings['linkedin_description']; ?>" />
                <input type="hidden" name="linkedin_image" value="<?php echo $linkedin_array['picture'] ?>" />
            </div>
            
            <div id="lm_linkedin_box">
            
            	<?php 
				if ( 0 == $format_type ) {
				
					 $linkedin_array['comment'] 		= $user_settings['linkedin_comment'];
					 $linkedin_array['linktitle'] 		= $user_settings['linkedin_title'];
					 $linkedin_array['description']		= $user_settings['linkedin_description'];
				
				}
				
				$linkedin_content = get_leenkme_expanded_li_post( $post->ID, $linkedin_array ); ?>
            
                <textarea id="lm_li_comment" name="linkedin_comment" maxlength="700"><?php echo $linkedin_content['comment']; ?></textarea>
            
                <div id="lm_li_attachment_meta_area">
                
                	<div id="lm_li_image">
                		<img id='lm_li_image_src' src='<?php echo $linkedin_content['picture']; ?>' />
                    </div>
            
                    <div id="lm_li_content_area">
                        <input id="lm_li_linktitle" value="<?php echo $linkedin_content['linktitle']; ?>" type="text" name="linkedin_title" maxlength="200" />
            	<!--
                        <p id="lm_li_caption"><?php echo $_SERVER['HTTP_HOST']; ?></p>
                -->
                        <textarea id="lm_li_description" name="linkedin_description" maxlength="256"><?php echo $linkedin_content['description']; ?></textarea>
                    </div>
                
                </div>
                
            </div>
            
            <div id="lm_linkedin_options">
            
            	<div id="lm_li_exlusions">
					<?php if ( $user_settings['linkedin_profile'] ) { ?>
                    <?php _e( 'Exclude from Profile:', 'leenkme' ) ?>
                    <input type="checkbox" name="linkedin_exclude" <?php checked( $linkedin_exclude || "on" == $linkedin_exclude ); ?> />
                    <?php } ?>
                    <br />
					<?php if ( $user_settings['linkedin_group'] ) { ?>
                    <?php _e( 'Exclude from Group:', 'leenkme' ) ?>
                    <input type="checkbox" name="linkedin_exclude_group" <?php checked( $linkedin_exclude_group || "on" == $linkedin_exclude_group ); ?> />
                    <?php } ?>
                    <br />
					<?php if ( $user_settings['linkedin_company'] ) { ?>
                    <?php _e( 'Exclude from Group:', 'leenkme' ) ?>
                    <input type="checkbox" name="linkedin_exclude_company" <?php checked( $linkedin_exclude_company || "on" == $linkedin_exclude_company ); ?> />
                    <?php } ?>
                </div>
                
                <div id="lm_li_reshare">
					<?php // Only show RePublish button if the post is "published"
                    if ( 'publish' === $post->post_status ) { ?>
                    <input style="float: right;" type="button" class="button" name="reshare_linkedin" id="lm_reshare_button" value="<?php _e( 'ReShare', 'leenkme' ) ?>" />
                    <?php } ?>
                </div>
                
            </div>
		
			<input value="linkedin_edit" type="hidden" name="linkedin_edit" />
			<?php 
			
		}
		
	}

}

if ( class_exists( 'leenkme_LinkedIn' ) ) {
	$dl_pluginleenkmeLinkedIn = new leenkme_LinkedIn();
}

function get_leenkme_expanded_li_post( $post_id, $linkedin_array, $post_title = false, $excerpt = false, $user_id = false ) {
	
	if ( !empty( $linkedin_array ) ) {

		global $current_user, $dl_pluginleenkmeLinkedIn;
		
		if ( !$user_id ) {
			
			get_currentuserinfo();
			$user_id = $current_user->ID;

		}
		
		$maxCommentLen = 700;
		$maxLinkNameLen = 200;
		$maxDescLen = 256;

		if ( false === $post_title )
			$post_title = get_the_title( $post_id );
	
		if ( false === $excerpt || empty( $excerpt ) ) {
			
			$post = get_post( $post_id );
		
			if ( !empty( $post->post_excerpt ) ) {
				
				//use the post_excerpt if available for the facebook description
				$excerpt = $post->post_excerpt; 
				
			} else {
				
				//otherwise we'll pare down the description
				$excerpt = $post->post_content; 
				
			}
			
		}
		
		$linkedin_array['comment'] 		= leenkme_trim_words( leenkme_replacements_args( $linkedin_array['comment'], $post_title, $post_id, $excerpt ), $maxCommentLen );
		$linkedin_array['linktitle'] 	= leenkme_trim_words( leenkme_replacements_args( $linkedin_array['linktitle'], $post_title, $post_id, $excerpt ), $maxLinkNameLen );
		$linkedin_array['description'] 	= leenkme_trim_words( leenkme_replacements_args( $linkedin_array['description'], $post_title, $post_id, $excerpt ), $maxDescLen );
		
		$user_settings = $dl_pluginleenkmeLinkedIn->get_user_settings( $user_id );
			
		$linkedin_array['picture'] = leenkme_get_picture( $user_settings, $post_id, 'linkedin' );
	
	}
	
	return $linkedin_array;

}

function leenkme_ajax_reshare() {

	check_ajax_referer( 'leenkme' );
	
	if ( !empty( $_REQUEST['id'] ) ) {

		if ( get_post_meta( $_REQUEST['id'], '_linkedin_exclude', true )
				&& get_post_meta( $_REQUEST['id'], '_linkedin_exclude_group', true )
				&& get_post_meta( $_REQUEST['id'], '_linkedin_exclude_company', true ) ) {

			die( __( 'You have excluded this post from sharing to your LinkedIn profile. If you would like to share it, edit the post and remove the appropriate exclude check box.', 'leenkme' ) );

		} else {
			
			$results = leenkme_ajax_connect( leenkme_publish_to_linkedin( array(), array( 'ID' => $_REQUEST['id'], 'post_author' => $_REQUEST['post_author'] ), $_REQUEST['linkedin_array'], true ) );
	
			if ( !empty( $results ) ) {		
				
				foreach( $results as $result ) {	
		
					if ( is_wp_error( $result ) ) {
		
						$out[] = "<p>" . $result->get_error_message() . "</p>";
		
					} else if ( !empty( $result['response']['code'] ) ) {
		
						$response = json_decode( $result['body'] );
						$out[] = $response[1];
		
					} else {
		
						$out[] = "<p>" . __( 'Error received! Please check your <a href="admin.php?page=leenkme_linkedin">LinkedIn settings</a> and try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
		
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

function leenkme_ajax_li() {

	check_ajax_referer( 'li_share' );

	global $current_user;
	get_currentuserinfo();
	$user_id = $current_user->ID;
	
	global $dl_pluginleenkme;
	$user_settings = $dl_pluginleenkme->get_user_settings( $user_id );

	if ( $api_key = $user_settings['leenkme_API'] ) {

		$comment = __( "Testing leenk.me's LinkedIn Plugin for WordPress", 'leenkme' );
		$title = __( 'leenk.me test', 'leenkme' );
		$url = 'http://leenk.me/';
		$picture = 'http://leenk.me/leenkme.png';
		$description = __( 'leenk.me is a webapp that allows you to publish to popular social networking sites whenever you publish a new post from your WordPress website.', 'leenkme' );
		$code = 'anyone';
		
		$connect_arr[$api_key]['li_comment'] = $comment;
		$connect_arr[$api_key]['li_title'] = $title;
		$connect_arr[$api_key]['li_url'] = $url;
		$connect_arr[$api_key]['li_image'] = $picture;
		$connect_arr[$api_key]['li_desc'] = $description;
		$connect_arr[$api_key]['li_code'] = $code;
						
		if ( !empty( $_REQUEST['linkedin_profile'] ) 
				&& ( 'true' === $_REQUEST['linkedin_profile'] || 'checked' === $_REQUEST['linkedin_profile'] ) )
			$connect_arr[$api_key]['linkedin_profile'] = true;
		
		if ( !empty( $_REQUEST['linkedin_group'] ) 
				&& ( 'true' === $_REQUEST['linkedin_group'] || 'checked' === $_REQUEST['linkedin_group'] ) )
			$connect_arr[$api_key]['linkedin_group'] = true;
		
		if ( !empty( $_REQUEST['linkedin_company'] ) 
				&& ( 'true' === $_REQUEST['linkedin_company'] || 'checked' === $_REQUEST['linkedin_company'] ) )
			$connect_arr[$api_key]['linkedin_company'] = true;
		
		$result = leenkme_ajax_connect($connect_arr);
		
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
									
// Add function to share on LinkedIn
function leenkme_publish_to_linkedin( $connect_arr = array(), $post, $linkedin_array = array(), $debug = false  ) {
	
	// https://developer.linkedin.com/documents/share-api
	global $dl_pluginleenkme, $dl_pluginleenkmeLinkedIn;
	
	if ( get_post_meta( $post['ID'], '_linkedin_exclude', true ) )
		$linkedin_exclude = true;
	else
		$linkedin_exclude = false;
	
	if ( get_post_meta( $post['ID'], '_linkedin_exclude_group', true ) )
		$linkedin_exclude_group = true;
	else
		$linkedin_exclude_group = false;

	if ( get_post_meta( $post['ID'], '_linkedin_exclude_company', true ) )
		$linkedin_exclude_company = true;
	else
		$linkedin_exclude_company = false;
	
	if ( !( $linkedin_exclude && $linkedin_exclude_group && $linkedin_exclude_company ) ) {
		
		$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
		
		if ( in_array( get_post_type( $post['ID'] ), $leenkme_settings['post_types'] ) ) {
			
			$options = get_option( 'leenkme_linkedin' );
			
			$leenkme_users = leenkme_get_users();
			
			// LinkedIn break TinyURL and YOURLS,
			// So we have to use the default non-permalink URL to be safe.
			switch( $leenkme_settings['url_shortener'] ) {
			
				case 'tinyurl' :
				case 'yourls' :
					$url = home_url( '?p=' . $post['ID'] );
					break;
			
				default:
					if ( !( $url = get_post_meta( $post['ID'], '_leenkme_shortened_url', true ) ) )
						$url = leenkme_url_shortener( $post['ID'] );
					break;
					
			}
			
			foreach ( $leenkme_users as $leenkme_user ) {
				
				$user_settings = $dl_pluginleenkme->get_user_settings( $leenkme_user->ID );
				
				if ( empty( $user_settings['leenkme_API'] ) )
					continue;	//Skip user if they do not have an API key set
				
				$api_key = $user_settings['leenkme_API'];
				
				$options = $dl_pluginleenkmeLinkedIn->get_user_settings( $leenkme_user->ID );
				if ( !empty( $options ) ) {
					
					if ( !empty( $options['share_cats'] ) && !empty( $options['clude'] )
							&& !( 'in' == $options['clude'] && in_array( '0', $options['share_cats'] ) ) ) {
						
						if ( 'ex' == $options['clude'] && in_array( '0', $options['share_cats'] ) ) {

							if ( $debug ) echo '<p>' . __( 'You have your <a href="admin.php?page=leenkme_linkedin">leenk.me LinkedIn settings</a> set to Exclude All Categories.', 'leenkme' ) . '</p>';
							
							continue;

						}
						
						$match = false;
						
						$post_categories = wp_get_post_categories( $post['ID'] );
						
						foreach ( $post_categories as $cat ) {
						
							if ( in_array( (int)$cat, $options['share_cats'] ) ) {
							
								$match = true;
								
							}
							
						}
						
						if ( ( 'ex' == $options['clude'] && $match ) ) {

							if ( $debug ) echo '<p>' . __( 'Post in an excluded category, check your <a href="admin.php?page=leenkme_linkedin">Leenk.me LinkedIn settings</a> or remove the post from the excluded category.', 'leenkme' ) . '</p>';
							
							continue;

						} else if ( ( 'in' == $options['clude'] && !$match ) ) {
							
							if ( $debug ) echo '<p>' . __( 'Post not found in an included category, check your <a href="admin.php?page=leenkme_linkedin">Leenk.me LinkedIn settings</a> or add the post into the included category.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
					}
						
					if ( !$options['linkedin_profile'] && !$options['linkedin_group'] && !$options['linkedin_company'] )
						continue;	//Skip this user if they don't have Profile or Page checked in plugins Facebook Settings
	
					// Added LinkedIn profile to connection array if enabled
					if ( $options['linkedin_profile'] && !$exclude_profile )
						$connect_arr[$api_key]['linkedin_profile'] = true;
	
					// Added LinkedIn group to connection array if enabled
					if ( $options['linkedin_group'] && !$linkedin_exclude_group )
						$connect_arr[$api_key]['linkedin_group'] = true;

					// Added LinkedIn company to connection array if enabled
					if ( $options['linkedin_company'] && !$linkedin_exclude_company )
						$connect_arr[$api_key]['linkedin_company'] = true;
					
					if ( $leenkme_user->ID != $post['post_author'] && ( 'mine' == $options['message_preference'] 
						|| ( 'manual' == $options['message_preference']  && !get_post_meta( $post['ID'], '_lm_linkedin_type', true ) ) ) )
						$prefer_user = true;
					else
						$prefer_user = false;
						
					if ( $prefer_user ) {
						
						$prefer_linkedin_array['comment'] = $options['linkedin_comment'];
						$prefer_linkedin_array['linktitle'] = $options['linkedin_title'];
						$prefer_linkedin_array['description'] = $options['linkedin_description'];
					
						$prefer_linkedin_array = get_leenkme_expanded_li_post( $post['ID'], $prefer_linkedin_array, false, false, $leenkme_user->ID );

						foreach( $prefer_linkedin_array as $key => $value ) {
							$prefer_linkedin_array[$key] = preg_replace_callback( '/(&#[0-9]+;)/', 'leenkme_utf8_html_entities', $value );
						}
													
						if ( !empty( $prefer_linkedin_array['picture'] ) )
							$connect_arr[$api_key]['li_image'] = $prefer_linkedin_array['picture'];
						
						$connect_arr[$api_key]['li_comment'] 	= stripslashes( html_entity_decode( $prefer_linkedin_array['comment'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_url']		= $url;
						$connect_arr[$api_key]['li_title']		= stripslashes( html_entity_decode( $prefer_linkedin_array['linktitle'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_desc'] 		= stripslashes( html_entity_decode( $prefer_linkedin_array['description'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_code'] 		= 'anyone';
						
					} else {
						
						$manual = get_post_meta( $post['ID'], '_lm_linkedin_type', true );
						
						if ( $manual ) {
							
							$linkedin_array['comment']     = get_post_meta( $post['ID'], '_linkedin_comment', true );
							$linkedin_array['linktitle']   = get_post_meta( $post['ID'], '_linkedin_title', true );
							$linkedin_array['description'] = get_post_meta( $post['ID'], '_linkedin_description', true );
							
						} else {
												
							if ( empty( $linkedin_array['comment'] ) )
								$linkedin_array['comment'] = $options['linkedin_comment'];
							
							if ( empty( $linkedin_array['linktitle'] ) )
								$linkedin_array['linktitle'] = $options['linkedin_title'];
							
							if ( empty( $linkedin_array['description'] ) )
								$linkedin_array['description'] = $options['linkedin_description'];
						
							$linkedin_array = get_leenkme_expanded_li_post( $post['ID'], $linkedin_array, false, false, $leenkme_user->ID );
													
						}
						
						foreach( $linkedin_array as $key => $value ) {
							$linkedin_array[$key] = preg_replace_callback( '/(&#[0-9]+;)/', 'leenkme_utf8_html_entities', $value );
						}

						if ( !empty( $linkedin_array['picture'] ) )
							$connect_arr[$api_key]['li_image'] = $linkedin_array['picture'];
						else
							$connect_arr[$api_key]['li_image'] = leenkme_get_picture( $user_settings, $post['ID'], 'linkedin' );
							
						$connect_arr[$api_key]['li_comment'] 	= stripslashes( html_entity_decode( $linkedin_array['comment'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_url']		= $url;
						$connect_arr[$api_key]['li_title']		= stripslashes( html_entity_decode( $linkedin_array['linktitle'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_desc'] 		= stripslashes( html_entity_decode( $linkedin_array['description'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['li_code'] 		= 'anyone';
					
					}
					
				}
				
			}
			
		}
		
	}
		
	return $connect_arr;
	
}

// Actions and filters	
if ( !empty( $dl_pluginleenkmeLinkedIn ) ) {
	
	//This use to be 'save_post' but the save_post action happens AFTER the 'transition_post_status' action
	//which means the leenkme_connect function was running before the leenk.me meta data was being saved!
	add_action( 'transition_post_status', array( $dl_pluginleenkmeLinkedIn, 'leenkme_linkedin_meta_tags' ), 10, 3 );

	// Whenever you publish a post, post to LinkedIn
	add_filter( 'leenkme_connect', 'leenkme_publish_to_linkedin', 20, 2);
	
	add_action( 'wp_ajax_li_share', 'leenkme_ajax_li' );
	add_action( 'wp_ajax_reshare', 'leenkme_ajax_reshare' );
	
}
