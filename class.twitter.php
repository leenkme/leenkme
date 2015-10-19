<?php
/**
 * Registers leenk.me Twitter class for setting up leenk.me
 *
 * @package leenk.me
 * @since 3.0.0
 */

if ( !class_exists( 'LeenkMe_Twitter' ) ) {
	
	/**
	 * This class registers the main leenkme functionality
	 *
	 * @since 3.0.0
	 */	
	class LeenkMe_Twitter {
	
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
			$settings = $this->get_twitter_settings();
			add_action( 'wp', array( $this, 'process_requests' ) );
			add_action( 'leenkme_admin_menu', array( $this, 'leenkme_admin_menu' ) );
		}
		
		function process_requests() {
			
			if ( !empty( $_POST['api-callback'] ) && 'leenkme-twitter' === $_GET['api-callback'] ) {
				
				//do something
				
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
		function leenkme_admin_menu() {
			
			add_submenu_page( 'leenkme', __( 'Twitter', 'leenkme' ), __( 'Twitter', 'leenkme' ), apply_filters( 'leenkme_menu_access_capability', 'edit_posts' ), 'leenkme-twitter', array( $this, 'settings_page' ) );
			
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
		function get_twitter_settings() {
			$defaults = array( 
				'tweetFormat' 	=> '%TITLE% %URL%',
				'tweetCats ' 	=> array( '0' ),
				'clude' 		=> 'in',
			);
			$defaults = apply_filters( 'leenkme_twitter_default_settings', $defaults );
		
			$settings = get_option( 'leenkme_twitter' );
			
			return wp_parse_args( $settings, $defaults );
		}
		
		/**
		 * Updates leenkme options set in options table
		 *
		 * @since 3.0.0
		 *
		 * @param $settings Array of settings
		 */
		function update_twitter_settings( $settings ) {
			update_option( 'leenkme_twitter', $settings );
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
				'accounts' => array(), 
			);
			$defaults = apply_filters( 'leenkme_twitter_default_user_settings', $defaults );

			$settings = get_user_option( 'leenkme_twitter', $user_id );
			
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
			update_user_option( $user_id, 'leenkme_twitter', $settings );
		}
				
		/**
		 * Output leenk.me's settings page and saves new settings on form submit
		 *
		 * @since 3.0.0
		 * @uses do_action() To call 'leenkme_settings_page' for future addons
		 */
		function settings_page() {
			global $leenkme;
			$user_override = false;
			$user_id = get_current_user_id();
			
			if ( current_user_can( 'manage_options' ) ) {
				if ( !empty( $_GET['user-id'] ) && is_numeric( $_GET['user-id'] ) ) {
					$user_id = $_GET['user-id'];
					$user_override = true;
				}
			}
			
			$twitter_settings = $this->get_twitter_settings();
			$user_settings = $this->get_user_settings( $user_id );
			
			if ( !empty( $_REQUEST['update_twitter_settings'] ) ) {
				
				if ( !empty( $_REQUEST['accounts'] ) ) {
					$user_settings['accounts'] = $_REQUEST['accounts'];
				} else {
					$user_settings['accounts'] = array();
				}
				
				$this->update_user_settings( $user_id, $user_settings );
				
				if ( current_user_can( 'manage_options' ) ) {
					
					if ( !empty( $_REQUEST['leenkme_tweetformat'] ) ) {
						$twitter_settings['tweetFormat'] = $_REQUEST['leenkme_tweetformat'];
					} else {
						$twitter_settings['tweetFormat'] = '';
					}
					
					if ( !empty( $_REQUEST['clude'] ) && !empty( $_REQUEST['tweetCats'] ) ) {
						$twitter_settings['clude'] = $_REQUEST['clude'];
						$twitter_settings['tweetCats'] = $_REQUEST['tweetCats'];
					} else {
						$twitter_settings['clude'] = 'in';
						$twitter_settings['tweetCats'] = array( '0' );
					}
					
					$this->update_twitter_settings( $twitter_settings );

					?>
					<div class="updated"><p><strong><?php _e( 'Twitter Settings Updated.', 'leenkme' );?></strong></p></div>
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
	                    <img src='<?php echo LEENKME_PLUGIN_URL; ?>/images/leenkme-logo-32x32.png' style='vertical-align: top;' /> <?php _e( 'Twitter Settings', 'leenkme' ); ?>
	                    <?php
						if ( $user_override ) {
							$user = get_user_by( 'id', $user_id );
							printf( __( ' (for %s)', 'leenkme' ), $user->user_login );
						}	                    
	                    ?>
                    </h2>
                    
                    <div id="post-types" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span><?php _e( 'Connected Accounts', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
	                        <?php
		                        $user_settings['accounts'] = array(); //this should be populated by default somewhere
	                        $leenkme_user_settings = $leenkme->get_user_settings( $user_id );
	                        if ( !empty( $leenkme_user_settings['leenkme_API'] ) ) {
		                        $args = array(
									'api-key' => $leenkme_user_settings['leenkme_API'],
									'action'  => 'get-accounts',
									'network' => 'twitter',
		                        );
		                        $twitter_accounts = leenkme_api_remote_post( $args );
			                    echo '<div id="connected-accounts">';
								if ( !empty( $twitter_accounts['results'] ) ) {
				                    foreach( $twitter_accounts['results'] as $account_id => $data ) {
					                    if ( !empty( $data->selected ) || in_array( $account_id, $user_settings['accounts'] ) ) {
						                    $selected_class = 'selected';
					                    } else {
						                    $selected_class = '';
					                    }
					                    echo '<div style="float: left; margin-right: 15px;" class="account ' . $selected_class . '">';
					                    echo '<img src="' . $data->profile_image_url . '" alt="' . $data->screen_name . '" title="' . $data->screen_name . '" />';
					                    echo '<span class="remove">&times;</span>';
					                    echo '<input type="hidden" name="accounts[' . $account_id . ']" value="' . (bool)$selected_class . '" />';
										echo '</div>';
				                    }
			                    }
								echo '</div>';
	                        ?>
	                        <div style="clear: both;"></div>
							<p>
								<input id="leenkme-api-key" type="hidden" name="leenkme-api-key" value="<?php echo $leenkme_user_settings['leenkme_API']; ?>" />
								<input id="network" type="hidden" name="network" value="twitter" />
                                <input id="add_new_twitter_account" class="button-primary" type="submit" name="add_new_twitter_account" value="<?php _e( 'Add New Twitter Account', 'leenkme' ) ?>" />
								<?php wp_nonce_field( 'add_account', 'leenkme_add_account_wpnonce' ); ?>
                            </p>
                            <?php
	                        } else {
		                        echo '<p>' . __( 'You must add a leenk.me API key to your settings before adding new accounts', 'leenkme' ) . '</p>';
	                        }  
	                        ?>
                        </div>
                    </div>
                    
                    <?php if ( current_user_can( 'manage_options' ) ) { ?>
                    <div id="post-types" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span><?php _e( 'Message Settings', 'leenkme' ); ?></span></h3>
                        
                        <div class="inside">
                            <p><?php _e( 'Tweet Format:', 'leenkme' ); ?> <input name="leenkme_tweetformat" type="text" maxlength="140" style="width: 75%;" value="<?php _e( htmlspecialchars( stripcslashes( $twitter_settings['tweetFormat'] ) ), 'leenkme') ?>" /></p>
                            
                            <p style="font-size: 11px;;"><?php _e( 'Format Options:', 'leenkme' ); ?></p>
                            <ul style="font-size: 11px; margin-left: 50px;">
                                <li>%TITLE% - <?php _e( 'Displays Title of your post in your Twitter feed.', 'leenkme' ); ?></li>
                                <li>%URL% - <?php _e( 'Displays TinyURL of your post in your Twitter feed.', 'leenkme' ); ?></li>
                                <li>%CATS% - <?php _e( 'Displays the categories of your post in your Twitter feed as a hashtag.', 'leenkme' ); ?></li>
                                <li>%TAGS% - <?php _e( 'Displays tags your post in your Twitter feed as a hashtag.', 'leenkme' ); ?></li>
                            </ul>
							<p class="description"><?php _e( 'Twitter only allows a maximum of 140 characters per tweet. If your format is too long to accommodate %TITLE% and/or %URL% then this plugin will cut off your title to fit and/or remove the URL. URL is given preference (since it is either all or nothing). So if your TITLE ends up making your Tweet go over the 140 characters, it will take a substring of your title (plus some ellipsis). If you use the %CATS% or %TAGS% variable, categories are given priority, it will display every category that will fit within the tweet length limitation. After adding the categories leenk.me moves onto tags and will add every tag that will fit within the tweet length limitation. leenk.me will also strip out any non-word character from the Twitter hashtag.', 'leenkme' ); ?></p>
							<p>
								<input type="button" class="button" name="verify_twitter_connect" id="tweet" value="<?php _e( 'Send a Test Tweet', 'leenkme' ) ?>" />
								<?php wp_nonce_field( 'tweet', 'tweet_wpnonce' ); ?>
                                
                                <input class="button-primary" type="submit" name="update_twitter_settings" value="<?php _e( 'Save Settings', 'leenkme' ) ?>" />
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

			do_action( 'leenkme_twitter_settings_page' );			
		}
		
	}
	
}
