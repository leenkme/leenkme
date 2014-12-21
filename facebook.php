<?php

if ( ! class_exists( 'leenkme_Facebook' ) ) {
	
	// Define class
	class leenkme_Facebook {
	
		// Constructor
		function leenkme_Facebook() {
			//Not Currently Needed
		}
		
		/*--------------------------------------------------------------------
			Administrative Functions
		  --------------------------------------------------------------------*/
		
		// Option loader function
		function get_user_settings( $user_id ) {
			
			// Default values for the options
			$defaults = array(
								 'facebook_profile' 		=> true,
								 'facebook_page' 			=> false,
								 'facebook_group' 			=> false,
								 'facebook_message'			=> '%TITLE%',
								 'facebook_linkname'		=> '%WPSITENAME%',
								 'facebook_caption' 		=> '%WPTAGLINE%',
								 'facebook_description' 	=> '%EXCERPT%',
								 'default_image' 			=> '',
								 'force_facebook_image'		=> false,
								 'publish_cats' 			=> array( '0' ),
								 'clude'	 				=> 'in',
								 'message_preference'	 	=> 'author'
							);
							
			// Get values from the WP options table in the database, re-assign if found
			$user_settings = get_user_option( 'leenkme_facebook', $user_id );
			
			return wp_parse_args( $user_settings, $defaults );
		}
		
		// Print the admin page for the plugin
		function print_facebook_settings_page() {
			global $dl_pluginleenkme, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Get the user options
			$user_settings = $this->get_user_settings( $user_id );
			$facebook_settings = get_option( 'leenkme_facebook' );
			
			if ( isset( $_REQUEST['update_facebook_settings'] ) ) {
				
				if ( !empty( $_REQUEST['facebook_profile'] ) )
					$user_settings['facebook_profile'] = true;
				else
					$user_settings['facebook_profile'] = false;
				
				if ( !empty( $_REQUEST['facebook_page'] ) )
					$user_settings['facebook_page'] = true;
				else
					$user_settings['facebook_page'] = false;
				
				if ( !empty( $_REQUEST['facebook_group'] ) )
					$user_settings['facebook_group'] = true;
				else
					$user_settings['facebook_group'] = false;
				
				if ( !empty( $_REQUEST['facebook_message'] ) )
					$user_settings['facebook_message'] = $_REQUEST['facebook_message'];
				else
					$user_settings['facebook_message'] = '';
	
				if ( !empty( $_REQUEST['facebook_linkname'] ) )
					$user_settings['facebook_linkname'] = $_REQUEST['facebook_linkname'];
				else
					$user_settings['facebook_linkname'] = '';
				
				if ( !empty( $_REQUEST['facebook_caption'] ) )
					$user_settings['facebook_caption'] = $_REQUEST['facebook_caption'];
				else
					$user_settings['facebook_caption'] = '';
				
				if ( !empty( $_REQUEST['facebook_description'] ) )
					$user_settings['facebook_description'] = $_REQUEST['facebook_description'];
				else
					$user_settings['facebook_description'] = '';
				
				if ( !empty( $_REQUEST['default_image'] ) )
					$user_settings['default_image'] = $_REQUEST['default_image'];
				else
					$user_settings['default_image'] = '';
				
				if ( !empty( $_REQUEST['force_facebook_image'] ) )
					$user_settings['force_facebook_image'] = true;
				else
					$user_settings['force_facebook_image'] = false;
	
				if ( !empty( $_REQUEST['clude'] ) && !empty( $_REQUEST['publish_cats'] ) ) {
					
					$user_settings['clude'] = $_REQUEST['clude'];
					$user_settings['publish_cats'] = $_REQUEST['publish_cats'];
					
				} else {
					
					$user_settings['clude'] = 'in';
					$user_settings['publish_cats'] = array( '0' );
					
				}
				
				if ( !empty( $_REQUEST['message_preference'] ) )
					$user_settings['message_preference'] = $_REQUEST['message_preference'];
				else
					$user_settings['message_preference'] = '';
				
				update_user_option( $user_id, 'leenkme_facebook', $user_settings );
				
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
					<h2 style='margin-bottom: 10px;' ><img src='<?php echo $dl_pluginleenkme->base_url; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> Facebook <?php _e( 'Settings', 'leenkme' ); ?> (<a href="http://leenk.me/2010/09/04/how-to-use-the-leenk-me-facebook-plugin-for-wordpress/" target="_blank"><?php _e( 'help', 'leenkme' ); ?></a>)</h2>
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Social Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
						
							<p><?php _e( 'Publish to Personal Profile?', 'leenkme' ); ?> <input type="checkbox" id="facebook_profile" name="facebook_profile" <?php checked( $user_settings['facebook_profile'] ); ?> /></p>
							<p><?php _e( 'Publish to Fan Page?', 'leenkme' ); ?> <input type="checkbox" id="facebook_page" name="facebook_page" <?php checked( $user_settings['facebook_page'] ); ?> /></p>
							<p><?php _e( 'Publish to Group?', 'leenkme' ); ?> <input type="checkbox" id="facebook_group" name="facebook_group" <?php checked( $user_settings['facebook_group'] ); ?> /></p>
						
							<p>
								<input type="button" class="button" name="verify_facebook_connect" id="fb_publish" value="<?php _e( 'Publish a Test Message', 'leenkme' ) ?>" />
								<?php wp_nonce_field( 'fb_publish', 'fb_publish_wpnonce' ); ?>
                            
								<input class="button-primary" type="submit" name="update_facebook_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
							</p>
							
						</div>
					
					</div>
					
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Message Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
                        	<table id="facebook_settings_table">
                            <tr>
                            	<td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Default Message:', 'leenkme' ); ?></td>
                                <td><textarea name="facebook_message" style="width: 500px;" maxlength="400"><?php echo $user_settings['facebook_message']; ?></textarea></td>
                            </tr>
                            <tr>
                            	<td><?php _e( 'Default Link Name:', 'leenkme' ); ?></td>
                                <td><input name="facebook_linkname" type="text" style="width: 500px;" value="<?php echo $user_settings['facebook_linkname']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                            	<td><?php _e( 'Default Caption:', 'leenkme' ); ?></td>
                                <td><input name="facebook_caption" type="text" style="width: 500px;" value="<?php echo $user_settings['facebook_caption']; ?>" maxlength="100"/></td>
                            </tr>
                            <tr>
                            	<td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Default Description:', 'leenkme' ); ?></td>
                                <td><textarea name="facebook_description" style="width: 500px;" maxlength="300"><?php echo $user_settings['facebook_description']; ?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="facebook-format" style="margin-left: 50px;">
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
                                    <input type="checkbox" id="force_facebook_image" name="force_facebook_image" <?php checked( $user_settings['force_facebook_image'] ); ?> /> <?php _e( 'Always Use', 'leenkme' ); ?>
                                </td>
                            </tr> 
                            <tr>
                                <td colspan="2">                   
                                    <div class="facebook-image-warning" style="margin-left: 50px;">
	                                    <p class="description"><?php _e( 'Facebook recommends images that are at least 1200 x 630 pixels for the best display on high resolution devices. Images that are 600 x 315 pixels or larger will post with larger images on Facebook. Images that are smaller than 600 x 315 px will post with smaller images on Facebook.', 'leenkme' ); ?></p>
                                        <p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'NOTE: Do not use an image URL hosted by Facebook. Facebook will reject your message.', 'leenkme' ); ?></p>
                                    </div>
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
								<input class="button-primary" type="submit" name="update_facebook_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
							</p>
							
						</div>
					</div>
					
					<div id="post-types" class="postbox">
					
						<div class="handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle"><span><?php _e( 'Publish Settings', 'leenkme' ); ?></span></h3>
						
						<div class="inside">
							<p><?php _e( 'Publish Categories:', 'leenkme' ); ?></p>
						
							<div class="fb-cats" style="margin-left: 50px;">
								<p>
								<input type='radio' name='clude' id='include_cat' value='in' <?php checked( 'in', $user_settings['clude'] ); ?> /><label for='include_cat'><?php _e( 'Include', 'leenkme' ); ?></label> &nbsp; &nbsp; <input type='radio' name='clude' id='exclude_cat' value='ex' <?php checked( 'ex', $user_settings['clude'] ); ?> /><label for='exclude_cat'><?php _e( 'Exclude', 'leenkme' ); ?></label>
                                </p>
								<p>
								<select id='categories' name='publish_cats[]' multiple="multiple" size="5" style="height: 70px; width: 150px;">
								<option value="0" <?php selected( in_array( "0", (array)$user_settings['publish_cats'] ) ); ?>>All Categories</option>
								<?php 
								$categories = get_categories( array( 'hide_empty' => 0, 'orderby' => 'name' ) );
								foreach ( (array)$categories as $category ) {
									?>
									
									<option value="<?php echo $category->term_id; ?>" <?php selected( in_array( $category->term_id, (array)$user_settings['publish_cats'] ) ); ?>><?php echo $category->name; ?></option>
				
				
									<?php
								}
								?>
								</select></p>
								<p style="font-size: 11px; margin-bottom: 0px;"><?php _e( 'To "deselect" hold the SHIFT key on your keyboard while you click the category.', 'leenkme' ); ?></p>
							</div>
							
							<p>
								<input class="button-primary" type="submit" name="update_facebook_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
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
		
		function leenkme_facebook_meta_tags( $new_status, $old_status, $post ) {
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;
				
			if ( isset( $_REQUEST['_inline_edit'] ) || isset( $_REQUEST['doing_wp_cron'] ) )
				return;
				
			if ( !empty( $_REQUEST['lm_facebook_type'] ) ) {
				$manual = true;
				update_post_meta( $post->ID, '_lm_facebook_type', true );
			} else {
				$manual = false;
				delete_post_meta( $post->ID, '_lm_facebook_type' );
			}
			
			if ( $manual ) {
				
				if ( !empty( $_REQUEST['facebook_message'] ) )
					update_post_meta( $post->ID, '_facebook_message', $_REQUEST['facebook_message'] );
				else
					delete_post_meta( $post->ID, '_facebook_message' );
				
				if ( !empty( $_REQUEST['facebook_linkname'] ) )
					update_post_meta( $post->ID, '_facebook_linkname', $_REQUEST['facebook_linkname'] );
				else
					delete_post_meta( $post->ID, '_facebook_linkname' );
				
				if ( !empty( $_REQUEST['facebook_caption'] ) )
					update_post_meta( $post->ID, '_facebook_caption', $_REQUEST['facebook_caption'] );
				else
					delete_post_meta( $post->ID, '_facebook_caption' );
				
				if ( !empty( $_REQUEST['facebook_description'] ) )
					update_post_meta( $post->ID, '_facebook_description', $_REQUEST['facebook_description'] );
				else
					delete_post_meta( $post->ID, '_facebook_description' );
		
				if ( !empty( $_REQUEST['facebook_image'] ) )
					update_post_meta( $post->ID, '_facebook_image', $_REQUEST['facebook_image'] );
				else
					delete_post_meta( $post->ID, '_facebook_image' );
				
			}
	
			if ( !empty( $_REQUEST['facebook_exclude_profile'] ) )
				update_post_meta( $post->ID, '_facebook_exclude_profile', $_REQUEST['facebook_exclude_profile'] );
			else
				delete_post_meta( $post->ID, '_facebook_exclude_profile' );
	
			if ( !empty( $_REQUEST['facebook_exclude_page'] ) )
				update_post_meta( $post->ID, '_facebook_exclude_page', $_REQUEST['facebook_exclude_page'] );
			else
				delete_post_meta( $post->ID, '_facebook_exclude_page' );
	
			if ( !empty( $_REQUEST['facebook_exclude_group'] ) )
				update_post_meta( $post->ID, '_facebook_exclude_group', $_REQUEST['facebook_exclude_group'] );
			else
				delete_post_meta( $post->ID, '_facebook_exclude_group' );
			
		}
		
		function leenkme_facebook_meta_box() {
			
			global $post, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			if ( $exclude_profile = get_post_meta( $post->ID, 'facebook_exclude_profile', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_exclude_profile', true );
				update_post_meta( $post->ID, '_facebook_exclude_profile', $exclude_profile );
				
				
			}
			$exclude_profile = get_post_meta( $post->ID, '_facebook_exclude_profile', true ); 
			
			
			if ( $exclude_page = get_post_meta( $post->ID, 'facebook_exclude_page', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_exclude_page', true );
				update_post_meta( $post->ID, '_facebook_exclude_page', $exclude_page );
				
				
			}
			$exclude_page = get_post_meta( $post->ID, '_facebook_exclude_page', true ); 
			
			if ( $exclude_group = get_post_meta( $post->ID, 'facebook_exclude_group', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_exclude_group', true );
				update_post_meta( $post->ID, '_facebook_exclude_group', $exclude_group );
				
				
			}
			$exclude_group = get_post_meta( $post->ID, '_facebook_exclude_group', true ); 
			
			if ( $facebook_array['message'] = get_post_meta( $post->ID, 'facebook_message', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_message', true );
				update_post_meta( $post->ID, '_facebook_message', $facebook_array['message'] );
				
				
			}
			$facebook_array['message'] = get_post_meta( $post->ID, '_facebook_message', true);
			
			if ( $facebook_array['linkname'] = get_post_meta( $post->ID, 'facebook_linkname', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_linkname', true );
				update_post_meta( $post->ID, '_facebook_linkname', $facebook_array['linkname'] );
				
				
			}
			$facebook_array['linkname'] = get_post_meta( $post->ID, '_facebook_linkname', true);
			
			if ( $facebook_array['caption'] = get_post_meta( $post->ID, 'facebook_caption', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_caption', true );
				update_post_meta( $post->ID, '_facebook_caption', $facebook_array['caption'] );
				
				
			}
			$facebook_array['caption'] = get_post_meta( $post->ID, '_facebook_caption', true);
			
			if ( $facebook_array['description'] = get_post_meta( $post->ID, 'facebook_description', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_description', true );
				update_post_meta( $post->ID, '_facebook_description', $facebook_array['description'] );
				
				
			}
			$facebook_array['description'] = get_post_meta( $post->ID, '_facebook_description', true);
			
			if ( $facebook_array['picture'] = get_post_meta( $post->ID, 'facebook_image', true ) ) {
				
				delete_post_meta( $post->ID, 'facebook_image', true );
				update_post_meta( $post->ID, '_facebook_image', $facebook_array['picture'] );
				
				
			}
			$facebook_array['picture'] = get_post_meta( $post->ID, '_facebook_image', true );
			
			$format_type = htmlspecialchars( stripcslashes( get_post_meta( $post->ID, '_lm_facebook_type', true ) ) );
			
			$user_settings = $this->get_user_settings( $user_id );
			$facebook_settings = get_option( 'leenkme_facebook' ); ?>
    
    		<div id="fb_format_options">
				<?php 
                _e( 'Format:', 'leenkme' );
                echo " ";
                ?>
                    
                <span id="lm_facebook_format" class="fb_manual_format manual_format" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>"><?php _e( 'Manual', 'leenkme' ); ?></span> <a id="set_to_default_fb_post" href="#" style="display:<?php if ( $format_type ) echo "inline"; else echo "none"; ?>">Reset</a>
                <span id="lm_facebook_format" class="fb_default_format default_format" style="display:<?php if ( $format_type ) echo "none"; else echo "inline"; ?>"><?php _e( 'Default', 'leenkme' ); ?></span>
                <input type="hidden" name="lm_facebook_type" value="<?php echo $format_type; ?>" />
                <input type="hidden" name="facebook_message_format" value="<?php echo $user_settings['facebook_message']; ?>" />
                <input type="hidden" name="facebook_linkname_format" value="<?php echo $user_settings['facebook_linkname']; ?>" />
                <input type="hidden" name="facebook_caption_format" value="<?php echo $user_settings['facebook_caption']; ?>" />
                <input type="hidden" name="facebook_description_format" value="<?php echo $user_settings['facebook_description']; ?>" />
                <input type="hidden" name="facebook_image" value="<?php echo $facebook_array['picture'] ?>" />
            </div>
            
            <div id="lm_facebook_box">
            
            	<?php 
				if ( 0 == $format_type ) {
				
					 $facebook_array['message'] 		= $user_settings['facebook_message'];
					 $facebook_array['linkname'] 		= $user_settings['facebook_linkname'];
					 $facebook_array['caption'] 		= $user_settings['facebook_caption'];
					 $facebook_array['description']		= $user_settings['facebook_description'];
				
				}
				
				$facebook_content = get_leenkme_expanded_fb_post( $post->ID, $facebook_array ); ?>
            
                <textarea id="lm_fb_message" name="facebook_message" maxlength="400"><?php echo $facebook_content['message']; ?></textarea>
            
                <div id="lm_fb_attachment_meta_area">
                
                	<div id="lm_fb_image">
                		<img id='lm_fb_image_src' src='<?php echo $facebook_content['picture']; ?>' />
                    </div>
            
                    <div id="lm_fb_content_area">
                        <input id="lm_fb_linkname" value="<?php echo $facebook_content['linkname']; ?>" type="text" name="facebook_linkname" maxlength="100" />
                        <input id="lm_fb_caption" value="<?php echo $facebook_content['caption']; ?>" type="text" name="facebook_caption" maxlength="100"/>
                        <textarea id="lm_fb_description" name="facebook_description" maxlength="300"><?php echo $facebook_content['description']; ?></textarea>
                    </div>
                
                </div>
                
            </div>
            
            <div id="lm_facebook_options">
            
            	<div id="lm_fb_exlusions">
					<?php if ( $user_settings['facebook_profile'] ) { ?>
                    <?php _e( 'Exclude from Profile:', 'leenkme' ) ?>
                    <input type="checkbox" name="facebook_exclude_profile" <?php checked( $exclude_profile || "on" == $exclude_profile ); ?> />
                    <br />
                    <?php } ?>
                    <?php if ( $user_settings['facebook_page'] ) { ?>
                    <?php _e( 'Exclude from Page:', 'leenkme' ) ?>
                    <input type="checkbox" name="facebook_exclude_page" <?php checked( $exclude_page || "on" == $exclude_page ); ?> />
                    <br />
                    <?php } ?>
                    <?php if ( $user_settings['facebook_group'] ) { ?>
                    <?php _e( 'Exclude from Group:', 'leenkme' ) ?>
                    <input type="checkbox" name="facebook_exclude_group" <?php checked( $exclude_group || "on" == $exclude_group ); ?> />
                    <?php } ?>
                </div>
                
                <div id="lm_fb_republish">
					<?php // Only show RePublish button if the post is "published"
                    if ( 'publish' === $post->post_status ) { ?>
                    <input style="float: right;" type="button" class="button" name="republish_facebook" id="lm_republish_button" value="<?php _e( 'RePublish', 'leenkme' ) ?>" />
                    <?php } ?>
                </div>
                
            </div>
            <?php 

		}

	}

}

if ( class_exists( 'leenkme_Facebook' ) ) {
	
	$dl_pluginleenkmeFacebook = new leenkme_Facebook();
	
}

function get_leenkme_expanded_fb_post( $post_id, $facebook_array, $post_title = false, $excerpt = false, $user_id = false ) {
	
	if ( !empty( $facebook_array ) ) {

		global $current_user, $dl_pluginleenkmeFacebook;
		
		if ( !$user_id ) {
				
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
		}

		$maxMessageLen = 400;
		$maxLinkNameLen = 100;
		$maxCaptionLen = 100;
		$maxDescLen = 300;

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
		
		$facebook_array['message'] 		= leenkme_trim_words( leenkme_replacements_args( $facebook_array['message'], $post_title, $post_id, $excerpt ), $maxMessageLen );
		$facebook_array['linkname'] 	= leenkme_trim_words( leenkme_replacements_args( $facebook_array['linkname'], $post_title, $post_id, $excerpt ), $maxLinkNameLen );
		$facebook_array['caption'] 		= leenkme_trim_words( leenkme_replacements_args( $facebook_array['caption'], $post_title, $post_id, $excerpt ), $maxCaptionLen );
		$facebook_array['description'] 	= leenkme_trim_words( leenkme_replacements_args( $facebook_array['description'], $post_title, $post_id, $excerpt ), $maxDescLen );
		
		$user_settings = $dl_pluginleenkmeFacebook->get_user_settings( $user_id );
		
		$facebook_array['picture'] 		= leenkme_get_picture( $user_settings, $post_id, 'facebook' );
	
	}
	
	return $facebook_array;
	
}

function leenkme_ajax_republish() {
	
	check_ajax_referer( 'leenkme' );
	
	if ( !empty( $_REQUEST['id'] ) && !empty( $_REQUEST['facebook_array'] ) ) {
		
		if ( get_post_meta( $_REQUEST['id'], '_facebook_exclude_profile', true ) 
				&& get_post_meta( $_REQUEST['id'], '_facebook_exclude_page', true )
				&& get_post_meta( $_REQUEST['id'], '_facebook_exclude_group', true ) ) {
					
			die( __( 'You have excluded this post from publishing to your Facebook profile, Fan Page, and Group. If you would like to publish it, edit the post and remove the appropriate exclude check boxes.' ) );
			
		} else {
			
			$results = leenkme_ajax_connect( leenkme_publish_to_facebook( array(), array( 'ID' => $_REQUEST['id'], 'post_author' => $_REQUEST['post_author'] ), $_REQUEST['facebook_array'], true ) );
	
			if ( !empty( $results ) ) {		
				
				foreach( $results as $result ) {	
		
					if ( is_wp_error( $result ) ) {
		
						$out[] = "<p>" . $result->get_error_message() . "</p>";
		
					} else if ( !empty( $result['response']['code'] ) ) {
				
						$response = json_decode( $result['body'] );
						$out[] = $response[1];
		
					} else {
		
						$out[] = "<p>" . __( 'Error received! Please check your <a href="admin.php?page=leenkme_facebook">Facebook settings</a> and try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.' ) . "</p>";
		
					}
		
				}
				
				die( join( (array)$out ) );
				
			} else {
				
				die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.' ) );
	
			}
			
		}
		
	} else {
		
		die( __( 'ERROR: Unable to determine Post ID.' ) );
		
	}

}

function leenkme_ajax_fb() {
	
	check_ajax_referer( 'fb_publish' );
	
	global $current_user;
	get_currentuserinfo();
	$user_id = $current_user->ID;
	
	global $dl_pluginleenkme;
	$user_settings = $dl_pluginleenkme->get_user_settings( $user_id );
	
	if ( $api_key = $user_settings['leenkme_API'] ) {
		
		$message = __( 'Testing the leenk.me Facebook Plugin for WordPress', 'leenkme' );
		$url = 'http://leenk.me/';
		$picture = 'http://leenk.me/leenkme.png';
		$description = __( 'leenk.me is a webapp that allows you to publish to popular social networking sites whenever you publish a new post from your WordPress website.', 'leenkme' );
		
		$connect_arr[$api_key]['facebook_message'] = $message;
		$connect_arr[$api_key]['facebook_link'] = $url;
		$connect_arr[$api_key]['facebook_picture'] = $picture;
		$connect_arr[$api_key]['facebook_description'] = $description;
						
		if ( !empty( $_REQUEST['facebook_profile'] ) 
				&& ( 'true' === $_REQUEST['facebook_profile'] || 'checked' === $_REQUEST['facebook_profile'] ) )
			$connect_arr[$api_key]['facebook_profile'] = true;
		
		if ( !empty( $_REQUEST['facebook_page'] ) 
				&& ( 'true' === $_REQUEST['facebook_page'] || 'checked' === $_REQUEST['facebook_page'] ) )
			$connect_arr[$api_key]['facebook_page'] = true;
		
		if ( !empty( $_REQUEST['facebook_group'] ) 
				&& ( 'true' === $_REQUEST['facebook_group'] || 'checked' === $_REQUEST['facebook_group'] ) )
			$connect_arr[$api_key]['facebook_group'] = true;
		
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
									
// Add function to pubslih to facebook
function leenkme_publish_to_facebook( $connect_arr = array(), $post, $facebook_array = array(), $debug = false ) {
	
	global $dl_pluginleenkme, $dl_pluginleenkmeFacebook;
	
	if ( get_post_meta( $post['ID'], '_facebook_exclude_profile', true ) )
		$exclude_profile = true;
	else
		$exclude_profile = false;
	
	if ( get_post_meta( $post['ID'], '_facebook_exclude_page', true ) )
		$exclude_page = true;
	else
		$exclude_page = false;
	
	if ( get_post_meta( $post['ID'], '_facebook_exclude_group', true ) )
		$exclude_group = true;
	else
		$exclude_group = false;
	
	if ( !( $exclude_profile && $exclude_page && $exclude_group ) ) {
		
		$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
		
		if ( in_array( get_post_type( $post['ID'] ), $leenkme_settings['post_types'] ) ) {
			
			$options = get_option( 'leenkme_facebook' );
			
			$leenkme_users = leenkme_get_users();
			
			// Facebook currently addes ref=nf on the end of every URL, this break TinyURL and YOURLS,
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
				
				$options = $dl_pluginleenkmeFacebook->get_user_settings( $leenkme_user->ID );
				if ( !empty( $options ) ) {
					
					if ( !empty( $options['publish_cats'] ) && !empty( $options['clude'] )
							&& !( 'in' == $options['clude'] && in_array( '0', $options['publish_cats'] ) ) ) {
						
						if ( 'ex' == $options['clude'] && in_array( '0', $options['publish_cats'] ) ) {
							
							if ( $debug ) echo '<p>' . __( 'You have your <a href="admin.php?page=leenkme_facebook">leenk.me Facebook settings</a> set to Exclude All Categories.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
						
						$match = false;
						
						$post_categories = wp_get_post_categories( $post['ID'] );
						
						foreach ( $post_categories as $cat ) {
						
							if ( in_array( (int)$cat, $options['publish_cats'] ) ) {
							
								$match = true;
								
							}
							
						}
						
						if ( ( 'ex' == $options['clude'] && $match ) ) {
							
							if ( $debug ) echo '<p>' . __( 'Post in an excluded category, check your <a href="admin.php?page=leenkme_facebook">Leenk.me Facebook settings</a> or remove the post from the excluded category.', 'leenkme' ) . '</p>';
							
							continue;
							
						} else if ( ( 'in' == $options['clude'] && !$match ) ) {
							
							if ( $debug ) echo '<p>' . __( 'Post not found in an included category, check your <a href="admin.php?page=leenkme_facebook">Leenk.me Facebook settings</a> or add the post into the included category.', 'leenkme' ) . '</p>';
							
							continue;
							
						}
						
					}
						
					if ( !$options['facebook_profile'] && !$options['facebook_page']  && !$options['facebook_group'])
						continue;	//Skip this user if they don't have Profile or Page checked in plugins Facebook Settings
						
					// Added facebook profile to connection array if enabled
					if ( $options['facebook_profile'] && !$exclude_profile )
						$connect_arr[$api_key]['facebook_profile'] = true;
	
					// Added facebook page to connection array if enabled
					if ( $options['facebook_page'] && !$exclude_page )
						$connect_arr[$api_key]['facebook_page'] = true;
	
					// Added facebook page to connection array if enabled
					if ( $options['facebook_group'] && !$exclude_group )
						$connect_arr[$api_key]['facebook_group'] = true;
					
					if ( $leenkme_user->ID != $post['post_author'] && ( 'mine' == $options['message_preference'] 
						|| ( 'manual' == $options['message_preference'] && !get_post_meta( $post['ID'], '_lm_facebook_type', true ) ) ) )
						$prefer_user = true;
					else
						$prefer_user = false;
						
					if ( $prefer_user ) {
						
						$facebook_array['message'] = $options['facebook_message'];
						$facebook_array['linkname'] = $options['facebook_linkname'];
						$facebook_array['caption'] = $options['facebook_caption'];
						$facebook_array['description'] = $options['facebook_description'];
					
						$facebook_array = get_leenkme_expanded_fb_post( $post['ID'], $facebook_array, false, false, $leenkme_user->ID );
						
						foreach( $facebook_array as $key => $value ) {
							$facebook_array[$key] = preg_replace_callback( '/(&#[0-9]+;)/', 'leenkme_utf8_html_entities', $value );
						}

						if ( !empty( $facebook_array['picture'] ) )
							$connect_arr[$api_key]['facebook_picture'] = $facebook_array['picture'];
						
						$connect_arr[$api_key]['facebook_message'] 		= stripslashes( html_entity_decode( $facebook_array['message'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_link'] 		= $url;
						$connect_arr[$api_key]['facebook_name'] 		= stripslashes( html_entity_decode( $facebook_array['linkname'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_caption']		= stripslashes( html_entity_decode( $facebook_array['caption'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_description'] 	= stripslashes( html_entity_decode( $facebook_array['description'], ENT_COMPAT, get_bloginfo('charset') ) );
						
					} else {
						
						$manual = get_post_meta( $post['ID'], '_lm_facebook_type', true );
						
						if ( $manual ) {
							
							$facebook_array['message']     = get_post_meta( $post['ID'], '_facebook_message', true );
							$facebook_array['linkname']    = get_post_meta( $post['ID'], '_facebook_linkname', true );
							$facebook_array['caption']     = get_post_meta( $post['ID'], '_facebook_caption', true );
							$facebook_array['description'] = get_post_meta( $post['ID'], '_facebook_description', true );
							
						} else {
							
							if ( empty( $facebook_array['message'] ) )
								$facebook_array['message'] = $options['facebook_message'];
								
							if ( empty( $facebook_array['linkname'] ) )
								$facebook_array['linkname'] = $options['facebook_linkname'];
						
							if ( empty( $facebook_array['caption'] ) )
								$facebook_array['caption'] = $options['facebook_caption'];
							
							if ( empty( $facebook_array['description'] ) )
								$facebook_array['description'] = $options['facebook_description'];
								
							$facebook_array = get_leenkme_expanded_fb_post( $post['ID'], $facebook_array, false, false, $leenkme_user->ID );
							
						}
						
						foreach( $facebook_array as $key => $value ) {
							$facebook_array[$key] = preg_replace_callback( '/(&#[0-9]+;)/', 'leenkme_utf8_html_entities', $value );
						}
																					
						if ( !empty( $facebook_array['picture'] ) )
							$connect_arr[$api_key]['facebook_picture'] = $facebook_array['picture'];
						else
							$connect_arr[$api_key]['facebook_picture'] = leenkme_get_picture( $user_settings, $post['ID'], 'facebook' );
						
						$connect_arr[$api_key]['facebook_message'] 		= stripslashes( html_entity_decode( $facebook_array['message'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_link'] 		= $url;
						$connect_arr[$api_key]['facebook_name'] 		= stripslashes( html_entity_decode( $facebook_array['linkname'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_caption']		= stripslashes( html_entity_decode( $facebook_array['caption'], ENT_COMPAT, get_bloginfo('charset') ) );
						$connect_arr[$api_key]['facebook_description'] 	= stripslashes( html_entity_decode( $facebook_array['description'], ENT_COMPAT, get_bloginfo('charset') ) );
					
					}
					
				}
				
			}
			
		}
			
	}
		
	return $connect_arr;
	
}

// Actions and filters	
if ( !empty( $dl_pluginleenkmeFacebook ) ) {
	
	//This use to be 'save_post' but the save_post action happens AFTER the 'transition_post_status' action
	//which means the leenkme_connect function was running before the leenk.me meta data was being saved!
	add_action( 'transition_post_status', array( $dl_pluginleenkmeFacebook, 'leenkme_facebook_meta_tags' ), 10, 3 );

	// Whenever you publish a post, post to facebook
	add_filter( 'leenkme_connect', 'leenkme_publish_to_facebook', 20, 2 );
	
	add_action( 'wp_ajax_fb_publish', 'leenkme_ajax_fb' );
	add_action( 'wp_ajax_republish', 'leenkme_ajax_republish' );
	
}