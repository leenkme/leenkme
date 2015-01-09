<?php
/*
Plugin Name: leenk.me
Plugin URI: http://leenk.me/
Description: Automatically publish to your Twitter, Facebook Profile/Fan Page/Group, and LinkedIn whenever you publish a new post on your WordPress website with the leenk.me social network connector. You need a <a href="http://leenk.me/">leenk.me API key</a> to use this plugin.
Author: Lew Ayotte @ leenk.me
Version: 2.2.7
Author URI: http://leenk.me/about/
Tags: publish, automatic, facebook, twitter, linkedin, friendfeed, fan page, groups, publicize, open graph, social media, social media tools
*/

define( 'LEENKME_VERSION' , '2.2.7' );

if ( ! class_exists( 'leenkme' ) ) {
	
	class leenkme {
		
		// Class members
		var $adminpages = array( 'leenkme', 'leenkme_twitter', 'leenkme_facebook', 'leenkme_linkedin', 'leenkme_friendfeed' );
		
		function leenkme() {
			
			global $wp_version;
			
			$this->wp_version = $wp_version;
			$this->base_url = plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
			$this->api_url	= 'https://leenk.me/api/1.2/';
			$this->timeout	= '5000';		// in miliseconds
			
			add_image_size( 'leenkme_thumbnail', 150, 150 );
			add_image_size( 'leenkme_facebook_image', 1200, 630 );
		
			add_action( 'init', array( &$this, 'upgrade' ) );
			add_action( 'admin_enqueue_scripts', 				array( &$this, 'leenkme_admin_enqueue_scripts' ) );
			add_action( 'wp_ajax_show_lm_shortener_options', 	array( &$this, 'show_lm_shortener_options' ) );
			add_filter( 'get_shortlink', 'leenkme_get_shortlink_handler', 1, 4 );
			
			$leenkme_settings = $this->get_leenkme_settings();
			
			if ( $leenkme_settings['use_og_meta_tags'] )
				add_action( 'wp_head', array( &$this, 'output_leenkme_og_meta_tags' ) );
	
		}
		
		function get_leenkme_settings() {
			
			$defaults = array( 	
								'twitter' 					=> false,
								'facebook' 					=> false,
								'linkedin' 					=> false,
								'friendfeed'				=> false,
								'post_types'				=> array( 'post' ),
								'url_shortener'				=> 'tinyurl',
								'use_og_meta_tags'			=> false,
								'og_type'					=> 'website',
								'og_sitename'				=> '%WPSITENAME%',
								'og_description'			=> '%WPTAGLINE%',
								'og_image'					=> '',
								'use_single_og_meta_tags'	=> false,
								'og_single_title'			=> '%TITLE%',
								'og_single_sitename'		=> '%WPSITENAME%',
								'og_single_description'		=> '%EXCERPT%',
								'og_single_image'			=> '',
								'force_og_image'			=> false
							);
		
			$leenkme_settings = get_option( 'leenkme' );
			
			return wp_parse_args( $leenkme_settings, $defaults );
			
		}
	
		function get_user_settings( $user_id ) {
			
			$defaults = array( 'leenkme_API' => '' );
			
			$user_settings = get_user_option( 'leenkme', $user_id );
			
			return wp_parse_args( $user_settings, $defaults );
			
		}
		
		function leenkme_admin_enqueue_scripts( $hook_suffix ) {
			
			$leenkme_general_pages = array( 
											'post.php', 
											'edit.php',
											'post-new.php'
										);
			
			$leenkme_settings_pages = array( 
											'toplevel_page_leenkme', 
											'leenk-me_page_leenkme_twitter', 
											'leenk-me_page_leenkme_facebook', 
											'leenk-me_page_leenkme_friendfeed', 
											'leenk-me_page_leenkme_linkedin' 
										);
										
			if ( in_array( $hook_suffix, array_merge( $leenkme_general_pages, $leenkme_settings_pages ) ) ) {
			
				wp_enqueue_script( 'leenkme_js', $this->base_url . 'js/leenkme.js', array( 'jquery' ), LEENKME_VERSION );
			
			}
			
			if ( in_array( $hook_suffix, $leenkme_settings_pages ) ) {	
				
				wp_enqueue_style( 'global' );
				wp_enqueue_style( 'dashboard' );
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_style( 'wp-admin' );
				wp_enqueue_style( 'leenkme_plugin_css', $this->base_url . 'css/leenkme.css', array(), LEENKME_VERSION );
				
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'dashboard' );
				wp_enqueue_script( 'thickbox' );
			
			}
			
			$leenkme_post_pages 		= array( 
											'post.php', 
											'post-new.php'
										);
			
			if ( in_array( $hook_suffix, $leenkme_post_pages ) ) {
				
				wp_enqueue_style( 'leenkme_post_css', $this->base_url . 'css/post.css', array(), LEENKME_VERSION );
				
				wp_enqueue_script( 'leenkme_post_js', $this->base_url . 'js/post.js', array( 'jquery' ), LEENKME_VERSION );
			
				if ( $this->plugin_enabled( 'twitter' ) )
					wp_enqueue_script( 'leenkme_twitter_post_js', $this->base_url . 'js/post-twitter.js', array( 'leenkme_post_js' ), LEENKME_VERSION );
				
				if ( $this->plugin_enabled( 'facebook' ) )
					wp_enqueue_script( 'leenkme_facebook_post_js', $this->base_url . 'js/post-facebook.js', array( 'leenkme_post_js' ), LEENKME_VERSION );
				
				if ( $this->plugin_enabled( 'linkedin' ) )
					wp_enqueue_script( 'leenkme_linkedin_post_js', $this->base_url . 'js/post-linkedin.js', array( 'leenkme_post_js' ), LEENKME_VERSION );
				
				if ( $this->plugin_enabled( 'friendfeed' ) )
					wp_enqueue_script( 'leenkme_friendfeed_post_js', $this->base_url . 'js/post-friendfeed.js', array( 'leenkme_post_js' ), LEENKME_VERSION );
					
			}
			
		}
		
		function leenkme_settings_page() {
			
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			$leenkme_settings = $this->get_leenkme_settings();
			
			if ( isset( $_REQUEST['update_leenkme_settings'] ) ) {
					
				$user_settings = $this->get_user_settings( $user_id );
				
				if ( !empty( $_REQUEST['leenkme_API'] ) )
					$user_settings['leenkme_API'] = $_REQUEST['leenkme_API'];
				else
					unset( $user_settings['leenkme_API'] );
					
				update_user_option( $user_id, 'leenkme', $user_settings );
				
				if ( current_user_can( 'leenkme_manage_all_settings' ) ) { //we're dealing with the main Admin options
				
					if ( !empty( $_REQUEST['twitter'] ) )
						$leenkme_settings['twitter'] = true;
					else
						$leenkme_settings['twitter'] = false;
					
					if ( !empty( $_REQUEST['facebook'] ) )
						$leenkme_settings['facebook'] = true;
					else
						$leenkme_settings['facebook'] = false;
					
					if ( !empty( $_REQUEST['linkedin'] ) )
						$leenkme_settings['linkedin'] = true;
					else
						$leenkme_settings['linkedin'] = false;
					
					if ( !empty( $_REQUEST['friendfeed'] ) )
						$leenkme_settings['friendfeed'] = true;
					else
						$leenkme_settings['friendfeed'] = false;
					
					if ( !empty( $_REQUEST['post_types'] ) )
						$leenkme_settings['post_types'] = $_REQUEST['post_types'];
					else
						$leenkme_settings['post_types'] = array( 'post' );
					
					if ( !empty( $_REQUEST['url_shortener'] ) )
						$leenkme_settings['url_shortener'] = $_REQUEST['url_shortener'];
					else
						$leenkme_settings['url_shortener'] = '';
					
					if ( !empty( $_REQUEST['bitly_username'] ) )
						$leenkme_settings['bitly_username'] = $_REQUEST['bitly_username'];
					else
						$leenkme_settings['bitly_username'] = '';
					
					if ( !empty( $_REQUEST['bitly_apikey'] ) )
						$leenkme_settings['bitly_apikey'] = $_REQUEST['bitly_apikey'];
					else
						$leenkme_settings['bitly_apikey'] = '';
					
					if ( !empty( $_REQUEST['yourls_auth_type'] ) )
						$leenkme_settings['yourls_auth_type'] = $_REQUEST['yourls_auth_type'];
					else
						$leenkme_settings['yourls_auth_type'] = '';
					
					if ( !empty( $_REQUEST['yourls_api_url'] ) )
						$leenkme_settings['yourls_api_url'] = $_REQUEST['yourls_api_url'];
					else
						$leenkme_settings['yourls_api_url'] = '';
					
					if ( !empty( $_REQUEST['yourls_username'] ) )
						$leenkme_settings['yourls_username'] = $_REQUEST['yourls_username'];
					else
						$leenkme_settings['yourls_username'] = '';
					
					if ( !empty( $_REQUEST['yourls_password'] ) )
						$leenkme_settings['yourls_password'] = $_REQUEST['yourls_password'];
					else
						$leenkme_settings['yourls_password'] = '';
					
					if ( !empty( $_REQUEST['yourls_signature'] ) )
						$leenkme_settings['yourls_signature'] = $_REQUEST['yourls_signature'];
					else
						$leenkme_settings['yourls_signature'] = '';
					
					if ( !empty( $_REQUEST['use_og_meta_tags'] ) )
						$leenkme_settings['use_og_meta_tags'] = true;
					else
						$leenkme_settings['use_og_meta_tags'] = false;
					
					if ( !empty( $_REQUEST['og_type'] ) )
						$leenkme_settings['og_type'] = $_REQUEST['og_type'];
					else
						$leenkme_settings['og_type'] = '';
					
					if ( !empty( $_REQUEST['og_sitename'] ) )
						$leenkme_settings['og_sitename'] = $_REQUEST['og_sitename'];
					else
						$leenkme_settings['og_sitename'] = '';
					
					if ( !empty( $_REQUEST['og_description'] ) )
						$leenkme_settings['og_description'] = $_REQUEST['og_description'];
					else
						$leenkme_settings['og_description'] = '';
					
					if ( !empty( $_REQUEST['og_image'] ) )
						$leenkme_settings['og_image'] = $_REQUEST['og_image'];
					else
						$leenkme_settings['og_image'] = '';
					
					if ( !empty( $_REQUEST['use_single_og_meta_tags'] ) )
						$leenkme_settings['use_single_og_meta_tags'] = true;
					else
						$leenkme_settings['use_single_og_meta_tags'] = false;
					
					if ( !empty( $_REQUEST['og_single_title'] ) )
						$leenkme_settings['og_single_title'] = $_REQUEST['og_single_title'];
					else
						$leenkme_settings['og_single_title'] = '';
					
					if ( !empty( $_REQUEST['og_single_sitename'] ) )
						$leenkme_settings['og_single_sitename'] = $_REQUEST['og_single_sitename'];
					else
						$leenkme_settings['og_single_sitename'] = '';
					
					if ( !empty( $_REQUEST['og_single_description'] ) )
						$leenkme_settings['og_single_description'] = $_REQUEST['og_single_description'];
					else
						$leenkme_settings['og_single_description'] = '';
					
					if ( !empty( $_REQUEST['og_single_image'] ) )
						$leenkme_settings['og_single_image'] = $_REQUEST['og_single_image'];
					else
						$leenkme_settings['og_single_image'] = '';
					
					if ( !empty( $_REQUEST['force_og_image'] ) )
						$leenkme_settings['force_og_image'] = true;
					else
						$leenkme_settings['force_og_image'] = false;
					
					update_option( 'leenkme', $leenkme_settings );
					
					// It's not pretty, but the easiest way to get the menu to refresh after save...
					?>
						<script type="text/javascript">
						<!--
						window.location = "<?php add_query_arg( array( 'page' => 'leenkme', 'settings_saved' => 1 ) ); ?>"
						//-->
						</script>
					<?php
					
				}
				
			}
			
			if ( isset( $_REQUEST['update_leenkme_settings'] ) || isset( $_GET['settings_saved'] ) ) {
				
				// update settings notification ?>
				<div class="updated"><p><strong><?php _e( "leenk.me Settings Updated.", "leenkme" );?></strong></p></div>
				<?php
				
			}
		
			$user_settings = $this->get_user_settings( $user_id );
			
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-droppable">
            
                <form id="leenkme" method="post" action="">
                    <h2 style='margin-bottom: 10px;' ><img src='<?php echo $this->base_url; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> <?php _e( 'leenk.me General Settings', 'leenkme' ); ?></h2>
                    
                    <div id="api-key" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'leenk.me API Key', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        <p>
                        <?php _e( 'leenk.me API Key', 'leenkme' ); ?>: <input type="text" id="api" class="regular-text" name="leenkme_API" value="<?php echo htmlspecialchars( stripcslashes( $user_settings['leenkme_API'] ) ); ?>" />
                        <input type="button" class="button" name="verify_leenkme_api" id="verify" value="<?php _e( 'Verify leenk.me API', 'leenkme' ) ?>" />
                        <?php wp_nonce_field( 'verify', 'leenkme_verify_wpnonce' ); ?>
                        </p>
                        
                        <?php if ( empty( $user_settings['leenkme_API'] ) ) { ?>
                        
                            <p>
                            <a href="<?php echo apply_filters( 'leenkme_url', 'http://leenk.me/' ); ?>"><?php _e( 'Click here to subscribe to leenk.me and generate an API key', 'leenkme' ); ?></a>
                            </p>
                        
                        <?php } ?>
                        
                        <?php
						if ( current_user_can( 'leenkme_manage_all_settings' ) ) { 
										
							$leenkme_users = leenkme_get_users();
							$other_users = array();
							
							if ( 1 < count( $leenkme_users ) ) {
							
								foreach( $leenkme_users as $user ) {
												
									$leenkme_user_settings = $this->get_user_settings( $user->ID );
									
									if ( $user_id === $user->ID )
										continue; //Skip the current user.
									
									if ( empty( $leenkme_user_settings['leenkme_API'] ) )
										continue;	//Skip user if they do not have an API key set
										
									$other_users[] = '<li>' . $user->user_login . ' - ' . $leenkme_user_settings['leenkme_API'] . '</li>';
													
								}
								
							}
							
							if ( 1 <= count( $other_users ) ) {
								
								echo '<h4>' . __( 'Other leenk.me users on this site', 'leenkme' ) . '</h4>';
								
								echo '<ul>' . join( $other_users ) . '</ul>';
								
							}
						
						}
						
						?>
                            
                        <?php wp_nonce_field( 'leenkme_general_options', 'leenkme_general_options_nonce' ); ?>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_leenkme_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>
                        
                        </div>
                        
                    </div>
                    
                    <?php if ( current_user_can( 'leenkme_manage_all_settings' ) ) { ?>
                  
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Administrator Options', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="leenkme_leenkme_social_network_modules">
                        	<tr>
                                <th rowspan="1"><?php _e( 'Enable Your Social Network Modules', 'leenkme' ); ?></th>
                                <td class="leenkme_plugin_name">Twitter: </td>
                                <td class="leenkme_plugin_button"><input type="checkbox" name="twitter" <?php checked( $leenkme_settings['twitter'] ); ?> /></td>
                                <td class="leenkme_plugin_settings"> <?php if ( $leenkme_settings['twitter'] ) { ?><a href="admin.php?page=leenkme_twitter">Twitter Settings</a><?php } ?></td>
                            </tr>
                            <tr>
                                <td rowspan="4" id="leenkme_social_network_description"><?php  _e( 'Choose which social network modules you want to enable for this site.', 'leenkme' ); ?></td>
                                <td class="leenkme_plugin_name">Facebook: </td>
                                <td class="leenkme_plugin_button"><input type="checkbox" name="facebook" <?php checked( $leenkme_settings['facebook'] ); ?> /></td>
                                <td class="leenkme_plugin_settings"> <?php if ( $leenkme_settings['facebook'] ) { ?><a href="admin.php?page=leenkme_facebook">Facebook Settings</a><?php } ?></td>
                            </tr>
                            <tr>
                                <td class="leenkme_plugin_name">LinkedIn: </td>
                                <td class="leenkme_plugin_button"><input type="checkbox" name="linkedin" <?php checked( $leenkme_settings['linkedin'] ); ?> /></td>
                                <td class="leenkme_plugin_settings"> <?php if ( $leenkme_settings['linkedin'] ) { ?><a href="admin.php?page=leenkme_linkedin">LinkedIn Settings</a><?php } ?></td>
                            </tr>
                            <tr>
                                <td id="leenkme_plugin_name">FriendFeed: </td>
                                <td id="leenkme_plugin_button"><input type="checkbox" name="friendfeed" <?php checked( $leenkme_settings['friendfeed'] ); ?> /></td>
                                <td id="leenkme_plugin_settings"> <?php if ( $leenkme_settings['friendfeed'] ) { ?><a href="admin.php?page=leenkme_friendfeed">Friendfeed Settings</a><?php } ?></td>
                            </tr>
                        </table>
                        
                        <table id="leenkme_leenkme_post_type_to_publish">
                        
                        <tr>
                            <th rowspan="1"><?php _e( 'Select Your Post Types', 'leenkme' ); ?></th>
                            <td class="leenkme_post_type_name"><?php _e( 'Post:', 'leenkme' ); ?></td>
                            <td class="leenkme_module_checkbox">
                                <input type="checkbox" value="post" name="post_types[]" <?php checked( in_array( 'post', $leenkme_settings['post_types'] ) ); ?> />
                            </td>
                        </tr>
                        <?php if ( version_compare( $this->wp_version, '2.9', '>' ) ) {
                            
                            $hidden_post_types = array( 'post', 'attachment', 'revision', 'nav_menu_item' );
                            $post_types = get_post_types( array(), 'objects' );
							$post_types_num = count( $post_types );
							$first = true;
							
							echo '<tr>';
							echo '	<td rowspan="' . ( $post_types_num - 4 ) . '">' . __( 'Choose which post types you want leenk.me to automatically publish to your social networks.', 'leenkme' ) . '</td>';
							 
                            foreach ( $post_types as $post_type ) {
                                
                                if ( in_array( $post_type->name, $hidden_post_types ) ) 
                                    continue;
									
								if ( !$first )
									echo "<tr>";
									
								$first = false;
                                ?>
                                
                                <td class="leenkme_post_type_name"><?php echo ucfirst( $post_type->name ); ?>: </td>
                                <td class="post_type_checkbox"><input type="checkbox" value="<?php echo $post_type->name; ?>" name="post_types[]" <?php checked( in_array( $post_type->name, $leenkme_settings['post_types'] ) ); ?> /></td></tr>
                                
                                <?php } ?>
                        </table>
                        
                        <?php } else { ?>
                        
                        </table>
                        <p><?php _e( 'To take advantage of publishing to Pages and Custom Post Types, please upgrade to the latest version of WordPress.', 'leenkme' ); ?></p>
                        
                        <?php } ?>
                        
                        <table id="leenkme_leenkme_url_shortener">
                        
                        <tr>
                        	<th rowspan="1"><?php _e( 'Select Your Default URL Shortner', 'leenkme' ); ?></th>
                            <td class="leenkme_url_shortener">
                            	<select id="leenkme_url_shortener_select" name="url_shortener"> 
                                	<option value="bitly" <?php selected( 'bitly', $leenkme_settings['url_shortener'] ); ?>>bit.ly</option>
                                    <option value="yourls" <?php selected( 'yourls', $leenkme_settings['url_shortener'] ); ?>>YOURLS</option>
                                    <option value="isgd" <?php selected( 'isgd', $leenkme_settings['url_shortener'] ); ?>>is.gd</option>
                                    <option value="wpme" <?php selected( 'wpme', $leenkme_settings['url_shortener'] ); ?>>wp.me</option>
                                    <option value="tinyurl" <?php selected( 'tinyurl', $leenkme_settings['url_shortener'] ); ?>>TinyURL</option>
                                    <option value="tflp" <?php selected( 'tflp', $leenkme_settings['url_shortener'] ); ?>>Twitter Friendly Links Plugin</option>
                                    <option value="wppostid" <?php selected( 'wppostid', $leenkme_settings['url_shortener'] ); ?>>WordPress Post ID</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td></td>
                            <td class='url_shortener_options'>
                            	<?php
									switch( $leenkme_settings['url_shortener'] ) {
										
										case 'bitly' :
											leenkme_show_bitly_options();
											break;
										
										case 'yourls' :
											leenkme_show_yourls_options();
											break;
										
										case 'wpme' :
											leenkme_show_wpme_options();
											break;
										
										case 'tflp' :
											leenkme_show_tflp_options();
											break;
										
									}
								?>
                            </td>
                        </tr>
                        
                        </table>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_leenkme_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>

                        </div>
                        
                    </div>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Open Graph Meta Tag Options', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="leenkme_site_og_meta_tags">
                        	<tr>
                                <th><?php _e( 'Enable OG Meta Tags on Home/Front Page', 'leenkme' ); ?></th>
                                <td><input type="checkbox" name="use_og_meta_tags" <?php checked( $leenkme_settings['use_og_meta_tags'] ); ?> /></td>
                            </tr>
                        
                            <tr>
                                <td><?php _e( 'Select Site Type', 'leenkme' ); ?></td>
                                <td class="leenkme_url_shortener">
                                    <select id="og_type" name="og_type"> 
                                        <option value="website" <?php selected( 'website', $leenkme_settings['og_type'] ); ?>>Website</option>
                                        <option value="blog" <?php selected( 'blog', $leenkme_settings['og_type'] ); ?>>Blog</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td><?php _e( 'Site Name:', 'leenkme' ); ?></td>
                                <td><input name="og_sitename" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_sitename']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Site Description:', 'leenkme' ); ?></td>
                                <td><textarea name="og_description" style="width: 500px;" maxlength="300"><?php echo $leenkme_settings['og_description']; ?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="facebook-format" style="margin-left: 50px;">
                                        <p style="font-size: 11px; margin-bottom: 0px;">Format Options:</p>
                                        <ul style="font-size: 11px;">
                                            <li>%WPSITENAME% - <?php _e( 'Displays the WordPress site name (found in Settings -> General).', 'leenkme' ); ?></li>
                                            <li>%WPTAGLINE% - <?php _e( 'Displays the WordPress TagLine (found in Settings -> General).', 'leenkme' ); ?></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Site Image URL:', 'leenkme' ); ?></td>
                                <td>
                                    <input name="og_image" type="text" style="width: 500px;" value="<?php _e(  $leenkme_settings['og_image'], 'leenkme' ) ?>" />
                                </td>
                            </tr>
                        </table>
                        
                        <table id="leenkme_single_og_meta_tags">
                        	<tr>
                                <th><?php _e( 'Enable OG Meta Tags on Posts', 'leenkme' ); ?></th>
                                <td><input type="checkbox" name="use_single_og_meta_tags" <?php checked( $leenkme_settings['use_single_og_meta_tags'] ); ?> /></td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Post Title:', 'leenkme' ); ?></td>
                                <td><input name="og_single_title" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_single_title']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Post Site Name:', 'leenkme' ); ?></td>
                                <td><input name="og_single_sitename" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_single_sitename']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <td style='vertical-align: top; padding-top: 5px;'><?php _e( 'Post Description:', 'leenkme' ); ?></td>
                                <td><textarea name="og_single_description" style="width: 500px;" maxlength="300"><?php echo $leenkme_settings['og_single_description']; ?></textarea></td>
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
                                <td><?php _e( 'Default Post Image URL:', 'leenkme' ); ?></td>
                                <td>
                                    <input name="og_single_image" type="text" style="width: 500px;" value="<?php _e(  $leenkme_settings['og_single_image'], 'leenkme' ) ?>" />
                                    <input type="checkbox" id="force_og_image" name="force_og_image" <?php checked( $user_settings['force_og_image'] ); ?> /> <?php _e( 'Always Use', 'leenkme' ); ?>
                                </td>
                            </tr>
                        </table>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_leenkme_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>

                        </div>
                        
                    </div>
                    
                    <?php } ?>
                    
                </form>
                
            </div>
            </div>
            </div>
			</div>
			<?php
			
		}
		
		function leenkme_add_wpnonce() {
			
			wp_nonce_field( 'leenkme', 'leenkme_wpnonce' );
			
		}
		
		function plugin_enabled( $plugin ) {
			
			$leenkme_settings = $this->get_leenkme_settings();
			return $leenkme_settings[$plugin];
			
		}
		
		function upgrade() {
			
			$leenkme_settings = $this->get_leenkme_settings();
			
			if ( !empty( $leenkme_settings['version'] ) )
				$old_version = $leenkme_settings['version'];
			else
				$old_version = 0;
			
			if ( version_compare( $old_version, '1.2.3', '<' ) )
				$this->upgrade_to_1_2_3();
			
			if ( version_compare( $old_version, '1.3.0', '<' ) )
				$this->upgrade_to_1_3_0();
			
			if ( isset( $_REQUEST['hide_admin_notice'] ) )
				update_option( 'show_leenkme_notice', LEENKME_VERSION );
			
			if ( version_compare( get_option( 'show_leenkme_notice' ), '1.4.0', '<' ) ) {
				
				//add_action( 'admin_notices', array( $this, 'leenkme_notices' ) );
				
			} else {
				
				update_option( 'show_leenkme_notice', LEENKME_VERSION );
			
			}
			
			$leenkme_settings['version'] = LEENKME_VERSION;
			update_option( 'leenkme', $leenkme_settings );
			
		}
		
		function upgrade_to_1_2_3() {
			
			$role = get_role('administrator');
			if ($role !== NULL)
				$role->add_cap('leenkme_manage_all_settings');
				$role->add_cap('leenkme_edit_user_settings');
	
			$role = get_role('editor');
			if ($role !== NULL)
				$role->add_cap('leenkme_edit_user_settings');
	
			$role = get_role('author');
			if ($role !== NULL)
				$role->add_cap('leenkme_edit_user_settings');
	
			$role = get_role('contributor');
			if ($role !== NULL)
				$role->add_cap('leenkme_edit_user_settings');
				
		}
		
		function upgrade_to_1_3_0() {
			
			global $wpdb;
			
			$user_ids = $wpdb->get_col( 'SELECT ID FROM '. $wpdb->users );
			
			foreach ( (array)$user_ids as $user_id ) {
				
				if ( !user_can( $user_id, 'leenkme_edit_user_settings' ) ) {
					
					clean_user_cache( $user_id );
					continue;
					
				}
				
				$tw_user_settings = get_user_option( 'leenkme_twitter', $user_id );
				if ( !empty( $tw_user_settings ) && !empty( $tw_user_settings['tweetcats'] ) ) {
				
					$new_tweetcats = $this->convert_old_categories( $tw_user_settings['tweetcats'] );
					if ( !empty( $new_tweetcats ) ) {
						
						$tw_user_settings['clude'] = array_shift( $new_tweetcats );
						$tw_user_settings['tweetcats'] = $new_tweetcats;
						update_user_option( $user_id, 'leenkme_twitter', $tw_user_settings );
						
					} 
				
				}
				
				$fb_user_settings = get_user_option( 'leenkme_facebook', $user_id );
				if ( !empty( $fb_user_settings ) && !empty( $fb_user_settings['publish_cats'] ) ) {
				
					$new_publish_cats = $this->convert_old_categories( $fb_user_settings['publish_cats'] );
					
					if ( !empty( $new_publish_cats ) ) {
						
						$fb_user_settings['clude'] = array_shift( $new_publish_cats );
						$fb_user_settings['publish_cats'] = $new_publish_cats;
						update_user_option( $user_id, 'leenkme_facebook', $fb_user_settings );
						
					} 
				
				}
				
				$li_user_settings = get_user_option( 'leenkme_linkedin', $user_id );
				if ( !empty( $li_user_settings ) && !empty( $li_user_settings['share_cats'] ) ) {
				
					$new_share_cats = $this->convert_old_categories( $li_user_settings['share_cats'] );
					
					if ( !empty( $new_share_cats ) ) {
						
						$li_user_settings['clude'] = array_shift( $new_share_cats );
						$li_user_settings['share_cats'] = $new_share_cats;
						update_user_option( $user_id, 'leenkme_linkedin', $li_user_settings );
						
					}
					
				}
				
				clean_user_cache( $user_id );
				
			}
			
		}
		
		function leenkme_notices() {
		
			echo "<div class='update-nag'>" . sprintf( __( "<p>Due to some changes Facebook has made to their system, you will need to update your leenk.me account settings.<br />Please log into <a href='http://leenk.me/' target='_blank'>http://leenk.me/</a> and follow the instructions for updating your settings.</p><p><a href='%s'>hide this notice</a></p>" ), add_query_arg( 'hide_admin_notice', 'yes' ) ) . "</div>";
		
		}
		
		function convert_old_categories( $categories ) {
	
			$cats = split( ",", $categories );
			
			foreach ( (array)$cats as $cat ) {
				
				if ( preg_match( '/^-\d+/', $cat ) ) {
					
					$exclude[] = (int)preg_replace( '/^-/', '', $cat );
					
				} else if ( preg_match( '/\d+/', $cat ) ) {
					
					$include[] = (int)$cat;
					
				}
				
			}
			
			if ( !empty( $include ) ) {
			
				array_unshift( $include, 'in' );
				return $include;
				
			} else if ( !empty( $exclude ) ) {
			
				array_unshift( $exclude, 'ex' );
				return $exclude;
				
			} else {
	
				return array( 'in', '0' ); // Default to include all categories
				
			}
			
		}
		
		function leenkme_add_meta_tag_options() {
			
			global $dl_pluginleenkme;
			
			$leenkme_settings = $dl_pluginleenkme->get_leenkme_settings();
			foreach ( $leenkme_settings['post_types'] as $post_type ) {
				
				add_meta_box( 
					'leenkme',
					__( 'leenk.me', 'leenkme' ),
					array( $this, 'leenkme_meta_box' ),
					$post_type 
				);
				
			}
			
		}
		
		function leenkme_meta_box() {
			
			global $dl_pluginleenkme, $post, $current_user;
			
			get_currentuserinfo();
			$user_id = $current_user->ID;
	
			echo '<div id="leenkme_meta_box">';
			
			echo '<a href class="leenkme_refresh_button button button-primary button-large right">' . __( 'Refresh Preview', 'leenkme' ) . '</a>';
			
				echo '<ul class="leenkme_tabs">';
				
				if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) ) {
					
					echo '<li><a href="#leenkme_twitter_meta_content"><img src="' . $this->base_url . '/images/twitter-16x16.png" alt="Twitter" /></a></li>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) ) {
					
					echo '<li><a href="#leenkme_facebook_meta_content"><img src="' . $this->base_url . '/images/facebook-16x16.png" alt="Facebook" /></a></li>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) ) {
					
					echo '<li><a href="#leenkme_linkedin_meta_content"><img src="' . $this->base_url . '/images/linkedin-16x16.png" alt="LinkedIn" /></a></li>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) ) {
					
					echo '<li><a href="#leenkme_friendfeed_meta_content"><img src="' . $this->base_url . '/images/friendfeed-16x16.png" alt="FriendFeed" /></a></li>';
					
				}
				
				echo '</ul>';
				
				echo '<div class="leenkme_tab_container">';
				
				if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) ) {
					
					echo '<div id="leenkme_twitter_meta_content" class="leenkme_tab_content">';
					
					global $dl_pluginleenkmeTwitter;
					echo $dl_pluginleenkmeTwitter->leenkme_twitter_meta_box();
					
					echo '</div>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) ) {
					
					echo '<div id="leenkme_facebook_meta_content" class="leenkme_tab_content">';
					
					global $dl_pluginleenkmeFacebook;
					echo $dl_pluginleenkmeFacebook->leenkme_facebook_meta_box();
					
					echo '</div>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) ) {
					
					echo '<div id="leenkme_linkedin_meta_content" class="leenkme_tab_content">';
					
					global $dl_pluginleenkmeLinkedIn;
					echo $dl_pluginleenkmeLinkedIn->leenkme_linkedin_meta_box();
					
					echo '</div>';
					
				}
				
				if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) ) {
					
					echo '<div id="leenkme_friendfeed_meta_content" class="leenkme_tab_content">';
					
					global $dl_pluginleenkmeFriendFeed;
					echo $dl_pluginleenkmeFriendFeed->leenkme_friendfeed_meta_box();
					
					echo '</div>';
					
				}
				
				echo '</div>';
				
				echo "<div style='clear: both;'></div>";
				
			echo '</div>';

		}
		
		/**
		 * Save the data via AJAX
		 *
		 * @TODO clean params
		 * @since 0.3
		 */
		function show_lm_shortener_options() {
			
			check_ajax_referer( 'leenkme_general_options' );
			
			if ( !empty( $_REQUEST['selected'] ) ) {
				
				switch( $_REQUEST['selected'] ) {
					
					case 'bitly' :
						die( leenkme_show_bitly_options() );
						break;
					
					case 'yourls' :
						die( leenkme_show_yourls_options() );
						break;
					
					case 'wpme' :
						die( leenkme_show_wpme_options() );
						break;
					
					case 'tflp' :
						die( leenkme_show_tflp_options() );
						break;
						
					default :
						die();
						break;
						
				}	
				
			} else {
				
				die();	
				
			}
			
		}
		
		
		/**
		 * Output Open Graph Meta Tags - http://ogp.me/
		 *
		 * @since 2.0.0
		 */
		function output_leenkme_og_meta_tags() {
			
			global $post;
			
			$leenkme_settings = $this->get_leenkme_settings();
			
			if ( is_single() && $leenkme_settings['use_single_og_meta_tags'] ) {
				
				$post_title = get_the_title();
		
				if ( !empty( $post->post_excerpt ) ) {
					
					//use the post_excerpt if available for the facebook description
					$excerpt = $post->post_excerpt; 
					
				} else {
					
					//otherwise we'll pare down the description
					$excerpt = $post->post_content; 
					
				}
				
				$og_array['og_title'] 		= leenkme_replacements_args( $leenkme_settings['og_single_title'], $post_title, $post->ID, $excerpt );
				$og_array['og_sitename'] 	= leenkme_replacements_args( $leenkme_settings['og_single_sitename'], $post_title, $post->ID, $excerpt );
				$og_array['og_description'] = leenkme_trim_words( leenkme_replacements_args( $leenkme_settings['og_single_description'], $post_title, $post->ID, $excerpt ), 300 );
				
				$og_array['og_image'] 		= leenkme_get_picture( $leenkme_settings, $post->ID, 'og' );
				
				if ( empty( $og_array['og_image'] ) && !empty( $leenkme_settings['og_image'] ) )
					$og_array['og_image'] 	= $leenkme_settings['og_image'];
			
				?>
                
                <meta property="og:url"			content="<?php echo get_permalink(); ?>">
                <meta property="og:type"		content="article"> 
                <meta property="og:title"		content="<?php echo htmlentities( $og_array['og_title'] ); ?>">
                <meta property="og:site_name"	content="<?php echo htmlentities( $og_array['og_sitename'] ); ?>"/>
                <meta property="og:description"	content="<?php echo htmlentities( $og_array['og_description'] ); ?>">
                
                <?php if ( !empty( $og_array['og_image'] ) ) ?>
                <meta property="og:image"		content="<?php echo $og_array['og_image']; ?>">
                
                <?php
				
			} else if ( ( is_home() || is_front_page() ) && $leenkme_settings['use_og_meta_tags'] ) {
				
				?>
                
                <meta property="og:url"			content="<?php echo site_url(); ?>">
                <meta property="og:type"		content="<?php echo $leenkme_settings['og_type']; ?>"> 
                <meta property="og:title"		content="<?php echo htmlentities( leenkme_replacements_args( $leenkme_settings['og_sitename'] ) ); ?>">
                <meta property="og:description"	content="<?php echo htmlentities( leenkme_trim_words( leenkme_replacements_args( $leenkme_settings['og_description'] ), 300 ) ); ?>">
                
                <?php if ( !empty( $leenkme_settings['og_image'] ) ) ?>
                <meta property="og:image"		content="<?php echo $leenkme_settings['og_image']; ?>">
                
                <?php
				
			}
			
			
		}
	
	}

}

// Instantiate the class
if ( class_exists( 'leenkme' ) ) {
	
	require_once( 'includes/functions.php' );
	require_once( 'includes/url-shortener.php' );
	
	$dl_pluginleenkme = new leenkme();
	
	if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
	
		if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) )
			require_once( 'twitter.php' );
	
		if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) )
			require_once( 'facebook.php' );
	
		if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) )
			require_once( 'linkedin.php' );
	
		if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) )
			require_once( 'friendfeed.php' );
	
	}
}

// Initialize the admin panel if the plugin has been activated
function leenkme_ap() {
	
	global $dl_pluginleenkme;
	
	if ( empty( $dl_pluginleenkme ) )
		return;
	
	add_menu_page( __( 'leenk.me Settings', 'leenkme' ), __( 'leenk.me', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme', array( &$dl_pluginleenkme, 'leenkme_settings_page' ), $dl_pluginleenkme->base_url . '/images/leenkme-logo-16x16.png' );
	
	if (substr($dl_pluginleenkme->wp_version, 0, 3) >= '2.9')
		add_submenu_page( 'leenkme', __( 'leenk.me Settings', 'leenkme' ), __( 'leenk.me Settings', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme', array( &$dl_pluginleenkme, 'leenkme_settings_page' ) );
	
	if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) ) {
		
		global $dl_pluginleenkmeTwitter;
		add_submenu_page( 'leenkme', __( 'Twitter Settings', 'leenkme' ), __( 'Twitter', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme_twitter', array( &$dl_pluginleenkmeTwitter, 'print_twitter_settings_page' ) );
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) ) {
		
		global $dl_pluginleenkmeFacebook;
		add_submenu_page( 'leenkme', __( 'Facebook Settings', 'leenkme' ), __( 'Facebook', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme_facebook', array( &$dl_pluginleenkmeFacebook, 'print_facebook_settings_page' ) );
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) ) {
		
		global $dl_pluginleenkmeLinkedIn;
		add_submenu_page( 'leenkme', __( 'LinkedIn Settings', 'leenkme' ), __( 'LinkedIn', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme_linkedin', array( &$dl_pluginleenkmeLinkedIn, 'print_linkedin_settings_page' ) );
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) ) {
		
		global $dl_pluginleenkmeFriendFeed;
		add_submenu_page( 'leenkme', __( 'FriendFeed Settings', 'leenkme' ), __( 'FriendFeed', 'leenkme' ), 'leenkme_edit_user_settings', 'leenkme_friendfeed', array( &$dl_pluginleenkmeFriendFeed, 'print_friendfeed_settings_page' ) );
		
	}
}

function leenkme_ajax_verify() {

	check_ajax_referer( 'verify' );
	
	if ( !empty( $_REQUEST['leenkme_API'] ) ) {

		$api_key = $_REQUEST['leenkme_API'];
		$connect_arr[$api_key]['verify'] = true;
		
		$results = leenkme_ajax_connect( $connect_arr );
	
		if ( !empty( $results ) ) {		
			
			foreach( $results as $result ) {	
	
				if ( is_wp_error( $result ) ) {
	
					$out[$api_key] = "<p>" . $result->get_error_message() . "</p>";
	
				} else if ( !empty( $result['response']['code'] ) ) {
			
					$response = json_decode( $result['body'] );
					$out[$api_key] = $response[1];
	
				} else {
	
					$out[$api_key] = "<p>" . __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
	
				}
	
			}
			
			die( join( (array)$out ) );
		
		} else {

			die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.', 'leenkme' ) );

		}

	} else {

		die( __( 'Please fill in your API key.', 'leenkme' ) );

	}

}

function leenkme_ajax_leenkme_row_action() {
	
	global $dl_pluginleenkme;

	if ( empty( $_REQUEST['id'] ) )
		die( __( 'Unable to determine Post ID.', 'leenkme' ) );

	if ( empty( $_REQUEST['colspan'] ) )
		die( __( 'Unable to determine column size.', 'leenkme' ) );
		
	$out = '<td colspan="' . $_REQUEST['colspan'] . '">';
	
	$out .= '<h4>' . __( 'Choose the Social Networks that you want to ReLeenk and click the ReLeenk button.', 'leenkme' ) . '</h4>';
	
	if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) ) {
		
		$out .= '<label><input type="checkbox" class="lm_releenk_networks_' . $_REQUEST['id'] . '" name="lm_releenk[]" value="twitter" /> Twitter</label><br />';
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) ) {
		
		$out .= '<label><input type="checkbox" class="lm_releenk_networks_' . $_REQUEST['id'] . '" name="lm_releenk[]" value="facebook" /> Facebook</label><br />';
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) ) {
		
		$out .= '<label><input type="checkbox" class="lm_releenk_networks_' . $_REQUEST['id'] . '" name="lm_releenk[]" value="linkedin" /> LinkedIn</label><br />';
		
	}
	
	if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) ) {
		
		$out .= '<label><input type="checkbox" class="lm_releenk_networks_' . $_REQUEST['id'] . '" name="lm_releenk[]" value="friendfeed" /> Friendfeed</label><br />';
		
	}
	
	$out .= '<p class="submit inline-leenkme">';
	$out .= '<a class="button-secondary cancel alignleft inline-leenkme-cancel" title="Cancel" post_id="' . $_REQUEST['id'] .'" href="#inline-releenk">' . __( 'Cancel', 'leenkme' ) . '</a>';
	$out .= '<a style="margin-left: 10px;" class="button-primary save alignleft inline-leenkme-releenk" title="ReLeenk" post_id="' . $_REQUEST['id'] .'"  post_author="' . $_REQUEST['post_author'] . '" href="#inline-releenk">' . __( 'ReLeenk', 'leenkme' ) . '</a>';
	$out .= '</p>';
	
	$out .= '</td>';
		
	die( $out );
	
}

function leenkme_ajax_releenk() {
	
	if ( empty( $_REQUEST['id'] ) )
		die( __( 'Unable to determine Post ID.', 'leenkme' ) );
	
	if ( empty( $_REQUEST['networks'] ) )
		die( __( 'No Social Networks selected.', 'leenkme' ) );
		
	$connect_array = array();
	$post_array = array( 'ID' => $_REQUEST['id'], 'post_author' => $_REQUEST['post_author'] );
				
	if ( in_array( 'twitter', $_REQUEST['networks'] ) ) {
		
		$connect_array = leenkme_publish_to_twitter( $connect_array, $post_array, false, true );
		
	}
		
	if ( in_array( 'facebook', $_REQUEST['networks'] ) ) {
	
		$connect_array = leenkme_publish_to_facebook( $connect_array, $post_array, false, true );
		
	}
		
	if ( in_array( 'linkedin', $_REQUEST['networks'] ) ) {
	
		$connect_array = leenkme_publish_to_linkedin( $connect_array, $post_array, false, true );
		
	}
		
	if ( in_array( 'friendfeed', $_REQUEST['networks'] ) ) {
	
		$connect_array = leenkme_publish_to_friendfeed( $connect_array, $post_array, false, true );
		
	}
	
	$results = leenkme_ajax_connect( $connect_array );
	
	if ( !empty( $results ) ) {		
				
		foreach( $results as $api_key => $result ) {	

			if ( is_wp_error( $result ) ) {

				$out[] = "<p>" . $result->get_error_message() . "</p>";

			} else if ( !empty( $result['response']['code'] ) ) {
		
				$response = json_decode( $result['body'] );
				$out[] = $response[1];

			} else {

				$out[] = "<p>" . __( 'Error received! If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.' ) . "</p>";

			}

		}
		
		die( join( (array)$out ) );
		
	} else {
		
		die( __( 'ERROR: Unknown error, please try again. If this continues to fail, contact <a href="http://leenk.me/contact/" target="_blank">leenk.me support</a>.' ) );

	}
	
}

function leenkme_connect( $new_status, $old_status, $post ) {

	$out = array();
	
	if ( 'publish' === $new_status && 'publish' !== $old_status ) {
		
		global $dl_pluginleenkme;
		
		if ( leenkme_rate_limit() ) {
		
			$connect_arr = apply_filters( 'leenkme_connect', array(), array( 'ID' => $post->ID, 'post_author' => $post->post_author ) );

			if ( !empty( $connect_arr ) ) {
				
				foreach ( $connect_arr as $api_key => $body ) {
					
					$body['host'] = $_SERVER['SERVER_NAME'];
					$body['leenkme_API'] = $api_key;
					$headers = array( 'Authorization' => 'None' );
															
					$result = wp_remote_post( apply_filters( 'leenkme_api_url', $dl_pluginleenkme->api_url ), 
												array( 	'body' => $body, 
														'headers' => $headers,
														'sslverify' => false,
														'httpversion' => '1.1',
														'timeout' => $dl_pluginleenkme->timeout ) );
					
					if ( !empty( $result ) ) {
						
						$out[$api_key] = $result;
						
					} else {
						
						$out[$api_key]=  "<p>" . __( 'Undefined error occurred, for help please contact <a href="http://leenk.me/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
						
					}
					
				}
				
			}
		
		} else {
			
			$out[] = __( 'Error: You have exceeded your rate limit for API calls, only 350 API calls are allowed every hour.', 'leenkme' );
			
		}
		
	}
	
	return $out;

}

function leenkme_ajax_connect( $connect_arr ) {
	
	global $dl_pluginleenkme;
	
	$out = array();
	
	if ( leenkme_rate_limit() ) {
		
		if ( !empty( $connect_arr ) ) {
			
			foreach ( $connect_arr as $api_key => $body ) {
				
				$body['host'] = $_SERVER['SERVER_NAME'];
				$body['leenkme_API'] = $api_key;
				$headers = array( 'Authorization' => 'None' );
														
				$result = wp_remote_post( apply_filters( 'leenkme_api_url', $dl_pluginleenkme->api_url ), 
											array( 	'body' => $body, 
													'headers' => $headers,
													'sslverify' => false,
													'httpversion' => '1.1',
													'timeout' => $dl_pluginleenkme->timeout ) );
				
				if ( !empty( $result ) ) {
					
					$out[$api_key] = $result;
					
				} else {
					
					$out[$api_key] =  "<p>" . __( 'Undefined error occurred, for help please contact <a href="http://leenk.me/" target="_blank">leenk.me support</a>.', 'leenkme' ) . "</p>";
					
				}
				
			}
			
		} else {
		
			$out[] = __( 'Invalid leenk.me setup, for help please contact <a href="http://leenk.me/" target="_blank">leenk.me support</a>.', 'leenkme' );
		
		}
		
	} else {
		
		$out[] = __( 'Error: You have exceeded your rate limit for API calls, only 350 API calls are allowed every hour.', 'leenkme' );
		
	}
	
	return $out;
	
}

function get_leenkme_expanded_post_ajax() {
	
	global $dl_pluginleenkme;
	
	$return_array = array();
	
	if ( !empty( $_REQUEST['post_id'] ) )
		$post_id = $_REQUEST['post_id'];
	else
		die( __( 'Error: Unable to determine post ID', 'leenkme' ) );
	
	if ( !empty( $_REQUEST['title'] ) )
		$title = $_REQUEST['title'];
	
	if ( !empty( $_REQUEST['excerpt'] ) )
		$excerpt = $_REQUEST['excerpt'];
	
	if ( !empty( $_REQUEST['cats'] ) )
		$cats = $_REQUEST['cats'];
	else
		$cats = false;
	
	if ( !empty( $_REQUEST['tags'] ) )
		$tags = $_REQUEST['tags'];
	else
		$tags = false;
		
	if ( $dl_pluginleenkme->plugin_enabled( 'twitter' ) && !empty( $_REQUEST['tweet'] ) )
		$return_array['twitter'] = get_leenkme_expanded_tweet( $post_id, $_REQUEST['tweet'], $title, $cats, $tags );
	else
		$return_array['twitter'] = array();
		
	if ( $dl_pluginleenkme->plugin_enabled( 'facebook' ) && !empty( $_REQUEST['facebook_array'] ) )
		$return_array['facebook'] = get_leenkme_expanded_fb_post( $post_id, $_REQUEST['facebook_array'], $title, $excerpt );
	else
		$return_array['facebook'] = array();
		
	if ( $dl_pluginleenkme->plugin_enabled( 'linkedin' ) && !empty( $_REQUEST['linkedin_array'] ) )
		$return_array['linkedin'] = get_leenkme_expanded_li_post( $post_id, $_REQUEST['linkedin_array'], $title, $excerpt );
	else
		$return_array['linkedin'] = array();
		
	if ( $dl_pluginleenkme->plugin_enabled( 'friendfeed' ) && !empty( $_REQUEST['friendfeed_array'] ) )
		$return_array['friendfeed'] = get_leenkme_expanded_ff_post( $post_id, $_REQUEST['friendfeed_array'], $title, $excerpt );
	else
		$return_array['friendfeed'] = array();

	die( json_encode( $return_array ) );
	
}

function leenkme_help_list( $contextual_help, $screen ) {
	
	if ( 'leenkme' == $screen->parent_base ) {
		
		$contextual_help[$screen->id] = __( '<p>Need help working with the leenk.me plugin? Try these links for more information:</p>', 'leenkme' ) 
			. '<a href="http://leenk.me/2010/09/04/how-to-use-the-leenk-me-twitter-plugin-for-wordpress/" target="_blank">Twitter</a> | '
			. '<a href="http://leenk.me/2010/09/04/how-to-use-the-leenk-me-facebook-plugin-for-wordpress/" target="_blank">Facebook</a> | '
			. '<a href="http://leenk.me/2010/12/01/how-to-use-the-leenk-me-linkedin-plugin-for-wordpress/" target="_blank">LinkedIn</a> | '
			. '<a href="http://leenk.me/2011/04/08/how-to-use-the-leenk-me-friendfeed-plugin-for-wordpress/" target="_blank">FriendFeed</a>';

	}

	return $contextual_help;

}


function releenk_row_action( $actions, $post ) {
	
	global $dl_pluginleenkme;
	
	$leenkme_options = $dl_pluginleenkme->get_leenkme_settings();
	
	if ( in_array( $post->post_type, $leenkme_options['post_types'] ) ) {
		
		// Only show leenk.me button if the post is "published"
		if ( 'publish' === $post->post_status )
			$actions['leenkme'] = '<a class="releenk_row_action" id="' . $post->ID . '" title="leenk.me" href="#">leenk.me</a>';
		
	}
	

	return $actions;
	
}

// Actions and filters	
if ( !empty( $dl_pluginleenkme ) ) {

	/*--------------------------------------------------------------------
	    Actions
	  --------------------------------------------------------------------*/
	
	add_action( 'admin_init', array( $dl_pluginleenkme, 'leenkme_add_meta_tag_options' ), 1 );

	// Add the admin menu
	add_action( 'admin_menu', 'leenkme_ap');
	
	// Whenever you publish a post, connect to leenk.me
	add_action( 'transition_post_status', 'leenkme_connect', 100, 3 );
	
	add_action( 'admin_footer', array( $dl_pluginleenkme, 'leenkme_add_wpnonce' ) );
	
	add_action( 'wp_ajax_verify', 						'leenkme_ajax_verify' );
	add_action( 'wp_ajax_plugins', 						'leenkme_ajax_plugins' );
	add_action( 'wp_ajax_leenkme_row_action', 			'leenkme_ajax_leenkme_row_action' );
	add_action( 'wp_ajax_releenk', 						'leenkme_ajax_releenk' );
	add_action( 'wp_ajax_get_leenkme_expanded_post', 	'get_leenkme_expanded_post_ajax' );
	
	add_filter( 'contextual_help_list', 'leenkme_help_list', 10, 2);
	
	// edit-post.php post row update
	add_filter( 'post_row_actions', 'releenk_row_action', 10, 2 );
	add_filter( 'page_row_actions', 'releenk_row_action', 10, 2 );
	
	load_plugin_textdomain( 'leenkme', false, basename( dirname( __FILE__ ) ) . '/i18n' );
	
}
