<?php
/**
 * Registers leenk.me class for setting up leenk.me
 *
 * @package leenk.me
 * @since 3.0.0
 */

if ( !class_exists( 'LeenkMe' ) ) {
	
	/**
	 * This class registers the main leenkme functionality
	 *
	 * @since 3.0.0
	 */	
	class LeenkMe {
		
		private $addon_map = array( 
			'twitter' => 'LeenkMe_Twitter',
			'facebook' => 'LeenkMe_Facebook',
			'linkedin' => 'LeenkMe_LinkedIn',
		);
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 3.0.0
		 * @uses add_action() Calls 'admin_init' hook on $this->upgrade
		 * @uses add_action() Calls 'admin_enqueue_scripts' hook on $this->admin_wp_enqueue_scripts
		 * @uses add_action() Calls 'admin_print_styles' hook on $this->admin_wp_print_styles
		 * @uses add_action() Calls 'admin_menu' hook on $this->admin_menu
		 * @uses add_action() Calls 'wp_ajax_verify' hook on $this->api_ajax_verify
		 * @uses add_action() Calls 'transition_post_status' hook on $this->transition_post_status
		 */
		function __construct() {
			
			$settings = $this->get_leenkme_settings();
			
			add_action( 'init', array( $this, 'addon_init' ) );
			add_action( 'admin_init', array( $this, 'upgrade' ) );
			add_action( 'admin_init', array( $this, 'process_requests' ), 15 );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_wp_enqueue_scripts' ), 999 );
			add_action( 'admin_print_styles', array( $this, 'admin_wp_print_styles' ), 999 );
					
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			
			add_action( 'wp_ajax_verify', array( $this, 'api_ajax_verify' ) );
			add_action( 'wp_ajax_add_social_network_account', array( $this, 'add_social_network_account' ) );
			
			add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 100, 3 );
			
			add_image_size( 'leenkme_thumbnail', 150, 150 );
			add_image_size( 'leenkme_facebook_image', 1200, 630 );		
			
			if ( !empty( $settings['use_og_meta_tags'] ) ) {
				add_action( 'wp_head', array( $this, 'output_og_meta_tags' ) );
			}
			
		}
		
		function process_requests() {
			if ( current_user_can( 'edit_posts' ) ) {
				if ( !empty( $_REQUEST['add_new_leenkme_connection'] ) ) {
					if ( wp_verify_nonce( $_REQUEST['add_account_wpnonce'], 'add_account' ) ) {
						$args = array(
							'action' 	=> 'verify',
							'api-key' 	=> $_REQUEST['leenkme-api-key'],	
						);
						$verified = $this->api_request( $args );
						if ( $verified ) {
							wp_redirect( 'https://leenk.me/' );
						}
						exit;
					} else {
						wp_die( 'unable to verify nonce' );
					}
				}
			}
		}
		
		function api_request( $args ) {
			$data = array(
				'method' 		=> 'POST',
				'timeout' 		=> 45,
				'redirection' 	=> 5,
				'httpversion' 	=> '1.0',
				'blocking' 		=> true,
				'headers' 		=> array(),
				'body' 			=> json_encode( $args ),
				'cookies' 		=> array()
			);
			$response = wp_remote_post( LEENKME_API_URL, $data );
			wp_print_r( $response );
		}
		
		function addon_init() {
			$settings = $this->get_leenkme_settings();
			foreach ( $this->addon_map as $addon => $class ) {
				if ( !empty( $settings[$addon] ) ) {
					require_once( 'class.' . $addon . '.php' );
					new $class;
				}
			}
		}
		
		/**
		 * Initialize leenk.me Admin Menu
		 *
		 * @since 3.0.0
		 * @uses add_menu_page() Creates leenk.me menu
		 * @uses add_submenu_page() Creates Settings submenu to leenk.me menu
		 * @uses add_submenu_page() Creates Help submenu to leenk.me menu
		 * @uses do_action() To call 'leenkme_admin_menu' for future addons
		 */
		function admin_menu() {
			
			add_menu_page( __( 'leenk.me', 'leenkme' ), __( 'leenk.me', 'leenkme' ), apply_filters( 'leenkme_menu_access_capability', 'edit_posts' ), 'leenkme', array( $this, 'settings_page' ), LEENKME_PLUGIN_URL . '/images/leenkme-logo-16x16.png' );
			
			do_action( 'leenkme_admin_menu' );
		
			add_submenu_page( 'leenkme', __( 'Open Graph', 'leenkme' ), __( 'Open Graph', 'leenkme' ), apply_filters( 'leenkme_menu_access_capability', 'edit_posts' ), 'open-graph-leenkme', array( $this, 'open_graph_settings_page' ) );
			add_submenu_page( 'leenkme', __( 'Help', 'leenkme' ), __( 'Help', 'leenkme' ), apply_filters( 'leenkme_menu_access_capability', 'edit_posts' ), 'help-leenkme', array( $this, 'help_page' ) );
			
		}
		
		/**
		 * Prints backend leenk.me styles
		 *
		 * @since 3.0.0
		 * @uses $hook_suffix to determine which page we are looking at, so we only load the CSS on the proper page(s)
		 * @uses wp_enqueue_style to enqueue the necessary leenk.me style sheets
		 */
		function admin_wp_print_styles() {
		
			global $hook_suffix;
			
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				
			if ( !empty( $_REQUEST['post_type'] ) ) {
				
				$post_type = $_REQUEST['post_type'];
				
			} else {
				
				if ( !empty( $_REQUEST['post'] ) )
					$post_id = (int) $_REQUEST['post'];
				elseif ( !empty( $_REQUEST['post_ID'] ) )
					$post_id = (int) $_REQUEST['post_ID'];
				else
					$post_id = 0;
				
				if ( $post_id )
					$post = get_post( $post_id );
				
				if ( !empty( $post ) )
					$post_type = $post->post_type;
				
			}
			
		}
		
		/**
		 * Enqueues backend leenkme scripts
		 *
		 * @since 3.0.0
		 * @uses wp_enqueue_script to enqueue the necessary leenk.me javascripts
		 * 
		 * @param $hook_suffix passed through by filter used to determine which page we are looking at
		 *        so we only load the CSS on the proper page(s)
		 */
		function admin_wp_enqueue_scripts( $hook_suffix ) {
		
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			
			if ( !empty( $_REQUEST['post_type'] ) ) {
				
				$post_type = $_REQUEST['post_type'];
				
			} else {
				
				if ( !empty( $_REQUEST['post'] ) )
					$post_id = (int) $_REQUEST['post'];
				elseif ( !empty( $_REQUEST['post_ID'] ) )
					$post_id = (int) $_REQUEST['post_ID'];
				else
					$post_id = 0;
				
				if ( $post_id )
					$post = get_post( $post_id );
				
				if ( !empty( $post ) && !empty( $post ) )
					$post_type = $post->post_type;
				
			}
						
		}
		
		/**
		 * Get leenkme options set in options table
		 *
		 * @since 3.0.0
		 * @uses apply_filters() To call 'leenkme_default_settings' for future addons
		 * @uses wp_parse_args function to merge default with stored options
		 *
		 * return array leenk.me settings
		 */
		function get_leenkme_settings() {
			$defaults = array( 
				'twitter' 					=> false,
				'facebook' 					=> false,
				'linkedin' 					=> false,
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
			$defaults = apply_filters( 'leenkme_default_settings', $defaults );
		
			$settings = get_option( 'leenkme' );
			
			return wp_parse_args( $settings, $defaults );
		}
		
		/**
		 * Updates leenkme options set in options table
		 *
		 * @since 3.0.0
		 *
		 * @param $settings Array of settings
		 */
		function update_leenkme_settings( $settings ) {
			update_option( 'leenkme', $settings );
		}
		
		/**
		 * Get leenkme user options set in user meta table
		 *
		 * @since 3.0.0
		 * @uses apply_filters() To call 'leenkme_default_user_settings' for future addons
		 * @uses wp_parse_args function to merge default with stored options
		 *
		 * return array leenk.me user settings
		 */
		function get_user_settings( $user_id ) {
			$defaults = array( 
				'leenkme_API' => '' 
			);
			$defaults = apply_filters( 'leenkme_default_user_settings', $defaults );

			$settings = get_user_option( 'leenkme', $user_id );
			
			return wp_parse_args( $settings, $defaults );
		}
		
		/**
		 * Updates leenkme options set in options table
		 *
		 * @since 3.0.0
		 *
		 * @param $settings Array of settings
		 */
		function update_user_settings( $user_id, $settings ) {
			update_user_option( $user_id, 'leenkme', $settings );
		}
		
		/**
		 * Output leenk.me's settings page and saves new settings on form submit
		 *
		 * @since 3.0.0
		 * @uses do_action() To call 'leenkme_settings_page' for future addons
		 */
		function settings_page() {
			$user_override = false;
			$user_id = get_current_user_id();
			
			if ( current_user_can( 'manage_options' ) ) {
				if ( !empty( $_GET['user-id'] ) && is_numeric( $_GET['user-id'] ) ) {
					$user_id = $_GET['user-id'];
					$user_override = true;
				}
			}
			
			$leenkme_settings = $this->get_leenkme_settings();
			$user_settings = $this->get_user_settings( $user_id );
			
			if ( !empty( $_REQUEST['update_leenkme_settings'] ) ) {
				
				if ( !empty( $_REQUEST['leenkme_API'] ) ) {
					$user_settings['leenkme_API'] = $_REQUEST['leenkme_API'];
				} else {
					$user_settings['leenkme_API'] = '';
				}
				
				$this->update_user_settings( $user_id, $user_settings );
				
				if ( current_user_can( 'manage_options' ) ) {
				
					if ( !empty( $_REQUEST['twitter'] ) ) {
						$leenkme_settings['twitter'] = true;
					} else {
						$leenkme_settings['twitter'] = false;
					}
					if ( !empty( $_REQUEST['facebook'] ) ) {
						$leenkme_settings['facebook'] = true;
					} else {
						$leenkme_settings['facebook'] = false;
					}
					if ( !empty( $_REQUEST['linkedin'] ) ) {
						$leenkme_settings['linkedin'] = true;
					} else {
						$leenkme_settings['linkedin'] = false;
					}
										
					if ( !empty( $_REQUEST['post_types'] ) ) {
						$leenkme_settings['post_types'] = $_REQUEST['post_types'];
					} else {
						$leenkme_settings['post_types'] = array( 'post' );
					}
					
					if ( !empty( $_REQUEST['url_shortener'] ) ) {
						$leenkme_settings['url_shortener'] = $_REQUEST['url_shortener'];
					} else {
						$leenkme_settings['url_shortener'] = '';
					}
					
					if ( !empty( $_REQUEST['bitly_username'] ) ) {
						$leenkme_settings['bitly_username'] = $_REQUEST['bitly_username'];
					} else {
						$leenkme_settings['bitly_username'] = '';
					}
					
					if ( !empty( $_REQUEST['bitly_apikey'] ) ) {
						$leenkme_settings['bitly_apikey'] = $_REQUEST['bitly_apikey'];
					} else {
						$leenkme_settings['bitly_apikey'] = '';
					}
					
					if ( !empty( $_REQUEST['yourls_auth_type'] ) ) {
						$leenkme_settings['yourls_auth_type'] = $_REQUEST['yourls_auth_type'];
					} else {
						$leenkme_settings['yourls_auth_type'] = '';
					}
					
					if ( !empty( $_REQUEST['yourls_api_url'] ) ) {
						$leenkme_settings['yourls_api_url'] = $_REQUEST['yourls_api_url'];
					} else {
						$leenkme_settings['yourls_api_url'] = '';
					}
					
					if ( !empty( $_REQUEST['yourls_username'] ) ) {
						$leenkme_settings['yourls_username'] = $_REQUEST['yourls_username'];
					} else {
						$leenkme_settings['yourls_username'] = '';
					}
					
					if ( !empty( $_REQUEST['yourls_password'] ) ) {
						$leenkme_settings['yourls_password'] = $_REQUEST['yourls_password'];
					} else {
						$leenkme_settings['yourls_password'] = '';
					}
					
					if ( !empty( $_REQUEST['yourls_signature'] ) ) {
						$leenkme_settings['yourls_signature'] = $_REQUEST['yourls_signature'];
					} else {
						$leenkme_settings['yourls_signature'] = '';
					}
					
					$this->update_leenkme_settings( $leenkme_settings );

					?>
					<div class="updated"><p><strong><?php _e( 'General Settings Updated.', 'leenkme' );?></strong></p></div>
					<?php
					
				}
				
			}
			
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-droppable">
            
                <form id="leenkme" method="post" action="">
                    <h2 style='margin-bottom: 10px;' >
	                    <img src='<?php echo LEENKME_PLUGIN_URL; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> <?php _e( 'General Settings', 'leenkme' ); ?>
	                    <?php
						if ( $user_override ) {
							$user = get_user_by( 'id', $user_id );
							printf( __( ' (for %s)', 'leenkme' ), $user->user_login );
						}	                    
	                    ?>
                    </h2>
                    
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
						if ( current_user_can( 'manage_options' ) ) { 
										
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
                    
                    <?php if ( current_user_can( 'manage_options' ) ) { ?>
                  
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Administrator Options', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="leenkme_leenkme_administrator_options">
                        	<tr>
                                <th style="width: 200px;">
	                                <?php _e( 'Social Networks', 'leenkme' ); ?>
	                                <p class="description"><?php _e( 'Select the social networks you want to use on this site.', 'leenk.me' ); ?></p>
	                            </th>
                                <td class="leenkme_addon_checkbox">
	                                <?php
		                            foreach ( $this->addon_map as $addon => $class ) {
										echo '<p>';
										echo '<input id="' . $addon . '" type="checkbox" value="' . $addon . '" name="' . $addon . '" ' . checked( !empty( $leenkme_settings[$addon] ), true, false ) . ' /> &nbsp; ';
										echo '<label for="' . $addon . '" >' . ucfirst( $addon ) . '</label> &nbsp; ';
										echo '<a href="admin.php?page=leenkme-' . $addon . '">' . ucfirst( $addon ) . ' Settings</a>';
										echo '</p>';
									}
	                                ?>
                                </td>
                            </tr>
	                        <tr>
	                            <th>
		                            <?php _e( 'Post Types', 'leenkme' ); ?>
	                                <p class="description"><?php _e( 'Select the post types you want to share through leenk.me.', 'leenk.me' ); ?></p>
	                            </th>
	                            <td class="leenkme_module_checkbox">
	                                <?php
		                            $hidden_post_types = apply_filters( 'leenkme_hidden_post_types', array( 'attachment', 'revision', 'nav_menu_item' ) );
		                            $post_types = get_post_types( array(), 'objects' );
		                            foreach ( $post_types as $post_type ) {
		                                if ( in_array( $post_type->name, $hidden_post_types ) ) 
		                                    continue;
										echo '<p>';
										echo '<input id="' . $post_type->name . '" type="checkbox" value="' . $post_type->name . '" name="post_types[]" ' . checked( in_array( $post_type->name, $leenkme_settings['post_types'] ), true, false ) . ' /> &nbsp; ';
										echo '<label for="' . $post_type->name . '" >' . ucfirst( $post_type->name ) . '</label>';
										echo '</p>';
									}
	                                ?>
	                            </td>
	                        </tr>
                        
	                        <tr>
	                        	<th rowspan="1"><?php _e( 'URL Shortener', 'leenkme' ); ?></th>
	                            <td class="leenkme_url_shortener">
	                            	<select id="leenkme_url_shortener_select" name="url_shortener"> 
	                                	<option value="bitly" <?php selected( 'bitly', $leenkme_settings['url_shortener'] ); ?>>bit.ly</option>
	                                    <option value="yourls" <?php selected( 'yourls', $leenkme_settings['url_shortener'] ); ?>>YOURLS</option>
	                                    <option value="wpme" <?php selected( 'wpme', $leenkme_settings['url_shortener'] ); ?>>wp.me</option>
	                                    <option value="tinyurl" <?php selected( 'tinyurl', $leenkme_settings['url_shortener'] ); ?>>TinyURL</option>
	                                    <option value="tflp" <?php selected( 'tflp', $leenkme_settings['url_shortener'] ); ?>>Twitter Friendly Links Plugin</option>
	                                    <option value="wppostid" <?php selected( 'wppostid', $leenkme_settings['url_shortener'] ); ?>>WordPress Post ID</option>
	                            	</select>
		                            <div class='url_shortener_options'>
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
		                            </div>
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

			do_action( 'leenkme_settings_page' );
		}
		
		/**
		 * Output leenk.me's open graph settings page and saves new settings on form submit
		 *
		 * @since 3.0.0
		 * @uses do_action() To call 'leenkme_open_graph_settings_page' for future addons
		 */
		function open_graph_settings_page() {
			// Get the leenk.me options
			$leenkme_settings = $this->get_leenkme_settings();
			
			if ( !empty( $_REQUEST['update_leenkme_opengraph_settings'] ) ) {
				
				if ( current_user_can( 'manage_options' ) ) {
					
					if ( !empty( $_REQUEST['use_og_meta_tags'] ) ) {
						$leenkme_settings['use_og_meta_tags'] = true;
					} else {
						$leenkme_settings['use_og_meta_tags'] = false;
					}
					
					if ( !empty( $_REQUEST['og_type'] ) ) {
						$leenkme_settings['og_type'] = $_REQUEST['og_type'];
					} else {
						$leenkme_settings['og_type'] = '';
					}
					
					if ( !empty( $_REQUEST['og_sitename'] ) ) {
						$leenkme_settings['og_sitename'] = $_REQUEST['og_sitename'];
					} else {
						$leenkme_settings['og_sitename'] = '';
					}
					
					if ( !empty( $_REQUEST['og_description'] ) ) {
						$leenkme_settings['og_description'] = $_REQUEST['og_description'];
					} else {
						$leenkme_settings['og_description'] = '';
					}
					if ( !empty( $_REQUEST['og_image'] ) ) {
						$leenkme_settings['og_image'] = $_REQUEST['og_image'];
					} else {
						$leenkme_settings['og_image'] = '';
					}
					
					if ( !empty( $_REQUEST['use_single_og_meta_tags'] ) ) {
						$leenkme_settings['use_single_og_meta_tags'] = true;
					} else {
						$leenkme_settings['use_single_og_meta_tags'] = false;
					}
					
					if ( !empty( $_REQUEST['og_single_title'] ) ) {
						$leenkme_settings['og_single_title'] = $_REQUEST['og_single_title'];
					} else {
						$leenkme_settings['og_single_title'] = '';
					}
					
					if ( !empty( $_REQUEST['og_single_sitename'] ) ) {
						$leenkme_settings['og_single_sitename'] = $_REQUEST['og_single_sitename'];
					} else {
						$leenkme_settings['og_single_sitename'] = '';
					}
					
					if ( !empty( $_REQUEST['og_single_description'] ) ) {
						$leenkme_settings['og_single_description'] = $_REQUEST['og_single_description'];
					} else {
						$leenkme_settings['og_single_description'] = '';
					}
					
					if ( !empty( $_REQUEST['og_single_image'] ) ) {
						$leenkme_settings['og_single_image'] = $_REQUEST['og_single_image'];
					} else {
						$leenkme_settings['og_single_image'] = '';
					}
					
					if ( !empty( $_REQUEST['force_og_image'] ) ) {
						$leenkme_settings['force_og_image'] = true;
					} else {
						$leenkme_settings['force_og_image'] = false;
					}
					
					$this->update_leenkme_settings( $leenkme_settings );

					?>
					<div class="updated"><p><strong><?php _e( "Open Graph Settings Updated.", "leenkme" );?></strong></p></div>
					<?php
					
				}
				
			}
			
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-droppable">
            
                <form id="leenkme" method="post" action="">
                    <h2 style='margin-bottom: 10px;' ><img src='<?php echo LEENKME_PLUGIN_URL; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> <?php _e( 'Open Graph Settings', 'leenkme' ); ?></h2>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'Open Graph Meta Tag Options', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="leenkme_site_og_meta_tags">
                        	<tr>
                                <th style="width: 200px;"><?php _e( 'Enable on Home/Front Page', 'leenkme' ); ?></th>
                                <td><input type="checkbox" name="use_og_meta_tags" <?php checked( $leenkme_settings['use_og_meta_tags'] ); ?> /></td>
                            </tr>
                        
                            <tr>
                                <th><?php _e( 'Select Site Type', 'leenkme' ); ?></th>
                                <td class="leenkme_url_shortener">
                                    <select id="og_type" name="og_type"> 
                                        <option value="website" <?php selected( 'website', $leenkme_settings['og_type'] ); ?>>Website</option>
                                        <option value="blog" <?php selected( 'blog', $leenkme_settings['og_type'] ); ?>>Blog</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php _e( 'Site Name:', 'leenkme' ); ?></th>
                                <td><input name="og_sitename" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_sitename']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Site Description:', 'leenkme' ); ?></th>
                                <td>
	                                <textarea name="og_description" style="width: 500px;" maxlength="300"><?php echo $leenkme_settings['og_description']; ?></textarea>
                                    <p class="description">
	                                    <?php _e( 'Format Options:', 'leenkme' ); ?>
	                                    <ul>
	                                        <li>%WPSITENAME% - <?php _e( 'Displays the WordPress site name (found in Settings -> General).', 'leenkme' ); ?></li>
	                                        <li>%WPTAGLINE% - <?php _e( 'Displays the WordPress TagLine (found in Settings -> General).', 'leenkme' ); ?></li>
	                                    </ul>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Site Image URL:', 'leenkme' ); ?></th>
                                <td>
                                    <input name="og_image" type="text" style="width: 500px;" value="<?php _e(  $leenkme_settings['og_image'], 'leenkme' ) ?>" />
                                </td>
                            </tr>
                        </table>
                        
                        <br />
                        <br />
                                                
                        <table id="leenkme_single_og_meta_tags">
                        	<tr>
                                <th style="width: 200px;"><?php _e( 'Enable on Posts', 'leenkme' ); ?></th>
                                <td><input type="checkbox" name="use_single_og_meta_tags" <?php checked( $leenkme_settings['use_single_og_meta_tags'] ); ?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Post Title:', 'leenkme' ); ?></th>
                                <td><input name="og_single_title" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_single_title']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Post Site Name:', 'leenkme' ); ?></th>
                                <td><input name="og_single_sitename" type="text" style="width: 500px;" value="<?php echo $leenkme_settings['og_single_sitename']; ?>"  maxlength="100"/></td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Post Description:', 'leenkme' ); ?></th>
                                <td>
	                                <textarea name="og_single_description" style="width: 500px;" maxlength="300"><?php echo $leenkme_settings['og_single_description']; ?></textarea>
									<p class="description">
	                                    <?php _e( 'Format Options:', 'leenkme' ); ?>
	                                    <ul>
                                            <li>%TITLE% - <?php _e( 'Displays the post title.', 'leenkme' ); ?></li>
                                            <li>%WPSITENAME% - <?php _e( 'Displays the WordPress site name (found in Settings -> General).', 'leenkme' ); ?></li>
                                            <li>%WPTAGLINE% - <?php _e( 'Displays the WordPress TagLine (found in Settings -> General).', 'leenkme' ); ?></li>
                                            <li>%EXCERPT% - <?php _e( 'Displays the WordPress Post Excerpt (only used with Description Field).', 'leenkme' ); ?></li>
	                                    </ul>
                                    </p>

                                </td>
                            </tr>
                            <tr>
                                <th><?php _e( 'Default Post Image URL:', 'leenkme' ); ?></th>
                                <td>
                                    <input name="og_single_image" type="text" style="width: 80%;" value="<?php _e(  $leenkme_settings['og_single_image'], 'leenkme' ) ?>" />
                                    <input type="checkbox" id="force_og_image" name="force_og_image" <?php checked( $user_settings['force_og_image'] ); ?> /> <label for="force_og_image"><?php _e( 'Always Use', 'leenkme' ); ?></label>
                                </td>
                            </tr>
                        </table>
                                                  
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_leenkme_opengraph_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
                        </p>

                        </div>
                        
                    </div>
                    
                </form>
                
            </div>
            </div>
            </div>
			</div>
			<?php
			
			do_action( 'leenkme_open_graph_settings_page' );
		}
		
		/**
		 * Output leenk.me's help page
		 *
		 * @since 3.0.0
		 * @uses do_action() To call 'leenkme_help_page' for future addons
		 */
		function help_page() {
			// Get the leenk.me options
			$settings = $this->get_leenkme_settings();
			
			do_action( 'leenkme_help_page' );	
		}
		
		/**
		 * Checks if plugin is being upgraded to newer version and runs necessary upgrade functions
		 *
		 * @since 3.0.0
		 */
		function upgrade() {
			$settings = $this->get_leenkme_settings();
			
			/* Plugin Version Changes */
			if ( !empty( $settings['version'] ) )
				$old_version = $settings['version'];
			else
				$old_version = 0;
			
			if ( version_compare( $old_version, '3.0.0', '<' ) )
				$this->upgrade_to_3_0_0();
			
			$settings['version'] = LEENKME_VERSION;
			
			$settings['db_version'] = LEENKME_DB_VERSION;
			
			$this->update_leenkme_settings( $settings );
			
		}
		
		/**
		 * Runs through upgrade routine for 3.0.0
		 *
		 * @since 3.0.0
		 */
		function upgrade_to_3_0_0() {
			
		}
				
		/**
		 * Action from 'transition_post_status' to determine if new post has been published.
		 *
		 * If post status is 'publish' it gets sent and/or added to digests (if digest campaigns exist)
		 * If post status is not 'publish' it gets removed from digests (if digest campaigns exist);
		 *
		 * @since 3.0.0
		 * @uses do_action() to call the 'leenkme_transition_post_status_trash_to_publish' hook
		 * 
		 * @param string $new_status Post transition's new status
		 * @param string $old_status Post transition's old status
		 * @param object $post WordPress post object
		 */
		function transition_post_status( $new_status, $old_status, $post ) {
		
			
		}
		
	}
	
}
