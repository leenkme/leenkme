var $lm_post_linkedin_jquery = jQuery.noConflict();

$lm_post_linkedin_jquery(document).ready(function($) {
	
	$( '.post-php, .post-new-php' ).on( 'mousedown', 'textarea#lm_li_comment, input#lm_li_linktitle, textarea#lm_li_description', function() {
		
		$( 'input[name=lm_linkedin_type]' ).val( 1 );
		$( 'span.li_default_format' ).hide();
		$( 'span.li_manual_format' ).show();
		$( 'a#set_to_default_li_post' ).show();
		
	});
	
	$( '.post-php, .post-new-php' ).on( 'click', 'a#set_to_default_li_post', function( e ) {
	
		e.preventDefault();
		
		$( 'input[name=lm_linkedin_type]' ).val( 0 );
		$( 'span.li_default_format' ).show();
		$( 'span.li_manual_format' ).hide();
		$( 'a#set_to_default_li_post' ).hide();
		
	});
		
	$('.post-php, .post-new-php').on( 'click', 'input#lm_reshare_button', function() {
		
		linkedin_array = {
			'comment':			$( 'textarea#lm_li_comment' ).val(),
			'linkedin_profile':	$('input#linkedin_profile').attr('checked'),
			'linkedin_group':	$('input#linkedin_group').attr('checked'),
			'linkedin_company':	$('input#linkedin_company').attr('checked'),
			'linktitle':		$( 'input#lm_li_linktitle' ).val(),
			'description':		$( 'textarea#lm_li_description' ).val(),
			'picture':			$( 'input[name=linkedin_image]' ).val()
		};
		
		var data = {
			'action': 			'reshare',
			'id':  				$( 'input#post_ID' ).val(),
			'linkedin_array':  	linkedin_array,
			'_wpnonce': 		$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
	});
	
});
