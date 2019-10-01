var $lm_plugin_jquery = jQuery.noConflict();

$lm_plugin_jquery( document ).ready( function($) {
	
	/* Start leenk.me General Settings */
	$('.toplevel_page_leenkme').on( 'click', 'input#api', function() {
		
		$('input#api').css('background-color', 'white');
		
	});

	$('.toplevel_page_leenkme').on( 'click', 'input#verify', function() {
		
		var leenkme_API = $('input#api').val();
		var error = false;
		
		if (leenkme_API == "") {
			
			$('input#api').css('background-color', 'red');
			return false;
			
		}
	
		var data = {
			action: 	'verify',
			leenkme_API: leenkme_API,
			_wpnonce: 	$('input#leenkme_verify_wpnonce').val()
		};
		
		ajax_response( data );
		
	});
	/* End leenk.me General Settings */

	/* Start leenk.me Twitter Settings */
	$('.toplevel_page_leenkme_twitter, .leenk-me_page_leenkme_twitter').on( 'click', 'input#tweet', function() {
		
		var data = {
			action: 	'tweet',
			_wpnonce: 	$('input#tweet_wpnonce').val()
		};
		
		ajax_response( data );
		
	});
	/* End leenk.me Twitter Settings */
	
	/* Start leenk.me Facebook Settings */
	$('.toplevel_page_leenkme_facebook, .leenk-me_page_leenkme_facebook').on( 'click', 'input#fb_publish', function() {
		
		var facebook_page = $('input#facebook_page').attr('checked')
		var facebook_group = $('input#facebook_group').attr('checked')
		
		var data = {
			action:				'fb_publish',
			facebook_page:		facebook_page,
			facebook_group:		facebook_group,
			_wpnonce:			$('input#fb_publish_wpnonce').val()
		};
		
		ajax_response( data );
		
	});
	/* End leenk.me Facebook Settings */
		
	/* Start leenk.me LinkedIn Settings */
	$('.toplevel_page_leenkme_linkedin, .leenk-me_page_leenkme_linkedin').on( 'click', 'input#li_share', function() {
		
		var data = {
			action:		'li_share',
			'linkedin_profile':	$('input#linkedin_profile').attr('checked'),
			'linkedin_group':	$('input#linkedin_group').attr('checked'),
			'linkedin_company':	$('input#linkedin_company').attr('checked'),
			_wpnonce:	$('input#li_share_wpnonce').val()
		};
		
		ajax_response( data );
		
	});
	/* End leenk.me LinkedIn Settings */
		
	$('.edit-php').on( 'click', 'a.releenk_row_action', function( e ) {
		
		e.preventDefault();
		
		show_leenkme_options( $( this ).attr( 'id' ) );
		
	});
	
	$( '.edit-php' ).on( 'click', 'a.inline-leenkme-cancel', function( e ) {
		
		e.preventDefault();
		
		var post_id = $( this ).attr( 'post_id' );
		
		$( 'tr#post-' + post_id ).show();
		$( 'tr#inline-leenkme-' + post_id ).remove();
		
		
	});
	
	$( '.edit-php' ).on( 'click', 'a.inline-leenkme-releenk', function( e ) {
		
		e.preventDefault();
		
		var post_id = $( this ).attr( 'post_id' );
		var post_author = $( this ).attr( 'post_author' );
		
		networks = new Array();
		$( '.lm_releenk_networks_' + post_id ).each( function() {
			if ( true == $( this ).is(':checked') ) {
				networks[networks.length] = $( this ).val();
			}
		});
		
		var data = {
			'action': 		'releenk',
			'id':  			post_id,
			'post_author':	post_author,
			'networks':		networks,
			'_wpnonce': 	$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
		$( 'tr#post-' + post_id ).show();
		$( 'tr#inline-leenkme-' + post_id ).remove();
		
	});

	function show_leenkme_options( id ) {
		
		leenkmeRow = '<tr id="inline-leenkme-' + id + '" class="inline-leenkme"></tr>';
			
		var data = {
			'action':	'leenkme_row_action',
			'id':		id,
			'post_author':	$( 'div#inline_' + id + ' > div.post_author' ).html(),
			'colspan':	$('.widefat:first thead th:visible').length,
			'_wpnonce':	$( 'input#leenkme_wpnonce' ).val()
		};
			
		// We just need this to refresh the image being used.
		$lm_plugin_jquery.post( ajaxurl, data, function( response ) {
				
			$( 'tr#post-' + id ).hide().after( leenkmeRow );
		
			if ( $( 'tr#post-' + id ).hasClass('alternate') )
				$( 'tr#inline-leenkme-' + id ).addClass('alternate');
			
			$( 'tr#inline-leenkme-' + id ).append( response );
			
		});
		
	}

});

function ajax_response( data ) {
	
	var style = "position: fixed; " +
				"display: none; " +
				"z-index: 1000; " +
				"top: 50%; " +
				"left: 50%; " +
				"background-color: #E8E8E8; " +
				"border: 1px solid #555; " +
				"padding: 15px; " +
				"width: 500px; " +
				"min-height: 80px; " +
				"margin-left: -250px; " + 
				"margin-top: -150px;" +
				"text-align: center;" +
				"vertical-align: middle;";
	jQuery('body').append("<div id='results' style='" + style + "'></div>");
	jQuery('#results').html("<p>Sending data to leenk.me</p>" +
							"<p><img src='/wp-includes/js/thickbox/loadingAnimation.gif' /></p>");
	jQuery('#results').show();
	
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		
		jQuery('#results').html('<p>' + response + '</p>' +
								'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
		jQuery('#results_ok_button').click(remove_results);
		
	});
	
}

function remove_results() {
	
	jQuery("#results_ok_button").unbind("click");
	jQuery("#results").remove();
	
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
	
		jQuery("body","html").css({height: "auto", width: "auto"});
		jQuery("html").css("overflow","");
		
	}
	
	document.onkeydown = "";
	document.onkeyup = "";
	return false;
	
}
