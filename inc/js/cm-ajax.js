(function($) {
	var nonce = cm_menu_settings.nonce,
		form = $( '#cm-create-form' );

	$( form ).on( 'submit', function( e ) {
		e.preventDefault();
		var titleEl = $( '#cm-create-menu-title' ),
			titleVal = titleEl.val(),
			menuDataEl = $( '#cm-create-menu-content' );
			menuDataVal = menuDataEl.val();

		jQuery.ajax({
			url: ajaxurl,  // Defined global for back-end access
			type: 'post',
			data: {
				action: 'crude_menus_create_menu',
				nonce: nonce,
				title: titleVal,
				menuData: menuDataVal
			},
			success: function( data ) {
				var response = JSON.parse( data );
				if( $( '.cm-notice' ).length ) { // If there's already a notice...
					$( '.cm-notice' ).remove();  // Remove it
				}
				$( '#wpbody-content h1' ).after( response.markup );
				if( response.key == 'menu_created' ) {
					form[ 0 ].reset();
				}
			}
		} );
	} );
})( jQuery );