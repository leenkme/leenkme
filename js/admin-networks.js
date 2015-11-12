var $leenkme_admin_networks_jquery = jQuery.noConflict();

$leenkme_admin_networks_jquery( document ).ready( function($) {
    $( '.wp-admin' ).on( 'click', '#add_new_account', function( event ) {
        event.preventDefault();
		var data = {
			action:     'leenkme_get_social_network_accounts',
			api_key:    $( 'input#leenkme-api-key' ).val(),
			api_action: 'add-account',
			network:    $( 'input#network' ).val(),
			_wpnonce:   $('input#leenkme_add_account_wpnonce').val()
		};
		$.post( ajaxurl, data, function( response ) {
			var obj = $.parseJSON( response );
			console.log( obj );
			if ( obj.success ) {
				window.location = obj.results; //redirect to oAuth URL
			} else {
				alert( obj.message );
			}
		});
	});
    $( '.wp-admin' ).on( 'click', '.remove', function( event ) {
        event.preventDefault();
		var data = {
			action:     'leenkme_remove_social_network_account',
			api_key:    $( 'input#leenkme-api-key' ).val(),
			api_action: 'remove-account',
			network:    $( 'input#network' ).val(),
			account_id: $( this ).data( 'account-id' ),
			_wpnonce:   $('input#leenkme_remove_account_wpnonce').val(),
		};
		$.post( ajaxurl, data, function( response ) {
			var obj = $.parseJSON( response );
			console.log( obj );
			if ( obj.success ) {
				$( 'li#account-id-' . data['account_id'] ).remove();
			} else {
				alert( obj.message );
			}
		});
	});
});