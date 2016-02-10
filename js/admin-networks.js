var $leenkme_admin_networks_jquery = jQuery.noConflict();

$leenkme_admin_networks_jquery( document ).ready( function($) {
	/*
    $( '.wp-admin' ).on( 'click', '#add_new_account', function( event ) {
        event.preventDefault();
		var data = {
			action:     'leenkme_add_social_network_account',
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
	/**/
    $( '.wp-admin' ).on( 'click', '.remove', function( event ) {
        event.preventDefault();
        if ( window.confirm( 'Are you sure you want to remove ' + $( this ).data( 'account-name' ) + '?' ) ) {
			var data = {
				action:     'leenkme_remove_social_network_account',
				api_key:    $( 'input#leenkme-api-key' ).val(),
				api_action: 'remove-account',
				network:    $( 'input#network' ).val(),
				account_id: $( this ).data( 'account-id' ),
				user_id:    $( 'input#user-id' ).val(),
				_wpnonce:   $( 'input#remove_account_wpnonce' ).val(),
			};
			console.log( data );
			$.post( ajaxurl, data, function( response ) {
				var obj = $.parseJSON( response );
				console.log( obj );
				if ( obj.success ) {
					$( 'li#account-id-' + data['account_id'] ).remove();
				} else {
					alert( obj.message );
				}
			});
		}
	});
    $( '.wp-admin' ).on( 'click', '.account', function( event ) {
        event.preventDefault();
        var account_id = $( this ).data( 'account-id' );
        if ( $( '.select-class', this ).hasClass( 'selected' ) ) {
	        $( '#selected-' + account_id, this ).val( '0' );
        } else {
	        $( '#selected-' + account_id, this ).val( '1' );
        }
        $( '.select-class', this ).toggleClass( 'selected' );
        /*
		var data = {
			action:     'leenkme_set_social_network_account',
			api_action: 'set-account',
			network:    $( 'input#network' ).val(),
			account_id: $( this ).data( 'account-id' ),
			_wpnonce:   $('input#remove_account_wpnonce').val(),
		};
		$.post( ajaxurl, data, function( response ) {
			var obj = $.parseJSON( response );
			console.log( obj );
			if ( obj.success ) {
				$( 'li#account-id-' + data['account_id'] ).remove();
			} else {
				alert( obj.message );
			}
		});
		/**/
	});
});