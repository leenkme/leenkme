var $lm_post_jquery = jQuery.noConflict();

$lm_post_jquery(document).ready(function($) {
	
	$( '.leenkme_refresh_button' ).on( 'click', function( event ) {
		event.preventDefault();
		
		excerpt = $( 'textarea#excerpt' ).val();
		
		if ( '' == excerpt ) {
			
			if ( tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) {
			
				excerpt = tinyMCE.get('content').getContent();
				
			} else {
			
				excerpt = $( '#post #content' ).val();
		
			}
			
		}
		
		tweet_format = $( 'textarea#leenkme_tweet' ).val();
		cats = new Array;
		
		if ( 0 == ( $( 'input[name=lm_tweet_type]' ).val() ) ) {
			
			tweet_format = $( 'input[name=lm_tweet_format]' ).val();
		
			$( 'input[name="post_category[]"]' ).each( function() {
				
				if ( true == $( this ).attr( 'checked' ) || 'checked' == $( this ).attr( 'checked' ) ) {
					
					var str = $( this ).val();
					cats[ cats.length ] = str;
					
				}
				
			});
		
		}
		
		facebook_array = new Array;
		
		if ( 0 == ( $( 'input[name=lm_facebook_type]' ).val() ) ) {
			
			facebook_array = {
				'message':			$( 'input[name=facebook_message_format]' ).val(),
				'linkname':			$( 'input[name=facebook_linkname_format]' ).val(),
				'caption':			$( 'input[name=facebook_caption_format]' ).val(),
				'description':		$( 'input[name=facebook_description_format]' ).val()
			};
			
		}
		
		linkedin_array = new Array;
		
		if ( 0 == ( $( 'input[name=lm_linkedin_type]' ).val() ) ) {
			
			linkedin_array = {
				'comment':			$( 'input[name=linkedin_comment_format]' ).val(),
				'linktitle':		$( 'input[name=linkedin_linktitle_format]' ).val(),
				'description':		$( 'input[name=linkedin_description_format]' ).val()
			};
			
		}
		
		friendfeed_array = new Array;
		
		if ( 0 == ( $( 'input[name=lm_friendfeed_type]' ).val() ) ) {
			
			friendfeed_array = {
				'body':				$( 'input[name=friendfeed_body_format]' ).val(),
			};
		
		}
		
		data = {
			'action': 			'get_leenkme_expanded_post',
			'post_id': 			$( 'input#post_ID' ).val(),
			'tweet': 			tweet_format,
			'facebook_array': 	facebook_array,
			'linkedin_array': 	linkedin_array,
			'friendfeed_array': friendfeed_array,
			'title': 			$( 'input#title' ).val(),
			'cats': 			cats.join( ',' ),
			'tags': 			$( '.the-tags' ).val(),
			'excerpt':			excerpt,
			'_wpnonce': 		$('input#expanded_post_wpnonce').val()
		};
		
		$lm_post_jquery.post( ajaxurl, data, function( response ) {
			
			data = $lm_post_jquery.parseJSON( response );
			
			if ( 0 != data['twitter'].length  ) {
		
				if ( 0 == ( $( 'input[name=lm_tweet_type]' ).val() ) && 0 != data['twitter'].length ) {
				
					$( 'textarea#leenkme_tweet' ).val( data['twitter'] );
					$( 'span#lm_tweet_count' ).text( lm_tweet_len( data['twitter'] ) );
						
				}
			
			}
			
			if ( 0 != data['facebook'].length ) {
			
				if ( 0 == ( $( 'input[name=lm_facebook_type]' ).val() ) ) {
					
					$( 'textarea#lm_fb_message' ).val( data['facebook']['message'] );
					$( 'input#lm_fb_linkname' ).val( data['facebook']['linkname'] );
					$( 'input#lm_fb_caption' ).val( data['facebook']['caption'] );
					$( 'textarea#lm_fb_description' ).val( data['facebook']['description'] );
					$( 'img#lm_fb_image_src' ).attr( 'src', data['facebook']['picture'] );
					$( 'input[name=facebook_image]' ).val( data['facebook']['picture'] );
					
				} else {
					
					$( 'img#lm_fb_image_src' ).attr( 'src', data['facebook']['picture'] );
					$( 'input[name=facebook_image]' ).val( data['facebook']['picture'] );
					
				}
			
			}
			
			if ( 0 != data['linkedin'].length ) {
			
				if ( 0 == ( $( 'input[name=lm_linkedin_type]' ).val() ) ) {
					
					$( 'textarea#lm_li_comment' ).val( data['linkedin']['comment'] );
					$( 'input#lm_li_linktitle' ).val( data['linkedin']['linktitle'] );
					$( 'textarea#lm_li_description' ).val( data['linkedin']['description'] );
					$( 'img#lm_li_image_src' ).attr( 'src', data['linkedin']['picture'] );
					$( 'input[name=linkedin_image]' ).val( data['linkedin']['picture'] );
					
				} else {
					
					$( 'img#lm_li_image_src' ).attr( 'src', data['linkedin']['picture'] );
					$( 'input[name=linkedin_image]' ).val( data['linkedin']['picture'] );
					
				}
			
			}
			
			if ( 0 != data['friendfeed'].length ) {
			
				if ( 0 == ( $( 'input[name=lm_friendfeed_type]' ).val() ) ) {
					
					$( 'textarea#lm_ff_body' ).val( data['friendfeed']['body'] );
					$( 'img#lm_ff_image_src' ).attr( 'src', data['friendfeed']['picture'] );
					$( 'input[name=friendfeed_image]' ).val( data['friendfeed']['picture'] );
					
				} else {
					
					$( 'img#lm_ff_image_src' ).attr( 'src', data['friendfeed']['picture'] );
					$( 'input[name=friendfeed_image]' ).val( data['friendfeed']['picture'] );
					
				}
			
			}
			
		});
		
	});

	//When page loads...
	$( '.leenkme_tab_content' ).hide(); //Hide all content
	$( 'ul.leenkme_tabs li:first' ).addClass('active').show(); //Activate first tab
	$( '.leenkme_tab_content:first' ).show(); //Show first tab content

	//On Click Event
	$( 'ul.leenkme_tabs li' ).click(function() {

		$( 'ul.leenkme_tabs li' ).removeClass('active'); //Remove any 'active' class
		$( this ).addClass('active'); //Add 'active' class to selected tab
		$( '.leenkme_tab_content' ).hide(); //Hide all tab content

		var activeTab = $( this ).find( 'a' ).attr( 'href' ); //Find the href attribute value to identify the active tab + content
		$( activeTab ).fadeIn(); //Fade in the active ID content
		return false;
		
	});

});
	
function lm_tweet_len( response ) {
	
	tweet_len = 140 - response.length;

	if ( 10 > tweet_len ) {
		
		jQuery( 'span#lm_tweet_count' ).removeClass();
		jQuery( 'span#lm_tweet_count' ).addClass( 'lm_tweet_count_superwarn' );
		
	} else if ( 20 > tweet_len ) {
		
		jQuery( 'span#lm_tweet_count' ).removeClass();
		jQuery( 'span#lm_tweet_count' ).addClass( 'lm_tweet_count_warn' );
		
	} else {
		
		jQuery( 'span#lm_tweet_count' ).removeClass();
		jQuery( 'span#lm_tweet_count' ).addClass( 'lm_tweet_count' );
		
	}
	return tweet_len;	
	
}