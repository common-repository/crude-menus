<?php

function crude_menus_create_menu() {
	if( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'cm_create_menu_nonce') ) {
		$text = __( 'Your request has timed out.  Please refresh this page and try again.', 'crude-menus' );
		$response = crude_menus_create_admin_notice( 'expired_nonce', 'notice-success', $text );
		print wp_json_encode( $response ); die();
	}

	$full_menu_array = array();
	$menu_title = isset( $_POST[ 'title' ] ) ? sanitize_text_field( $_POST[ 'title' ] ) : '';
	$menu_data = isset( $_POST[ 'menuData' ] ) ? sanitize_textarea_field( $_POST[ 'menuData' ] ): '';

	if( ! $menu_title || ! $menu_data ) {
		$text = __( 'Please ensure that you have filled out both fields.', 'crude-menus' );
		$response = crude_menus_create_admin_notice( 'missing_data', 'notice-error', $text );
		print wp_json_encode( $response ); die();
	}

	/*
	 * The menu title might have escaped characters, so remove backticks
	 * This is really just one backtick, but it's escaped. Très ironique, no?
	 */

	$menu_title = str_replace( '\\', '', $menu_title ); 

	$menu_exists = wp_get_nav_menu_object( $menu_title );

	if( $menu_exists ) {
		$text = __( 'A menu named "' . $menu_title . '" already exists.  Please change it below and try again.', 'crude-menus' );
		$response = crude_menus_create_admin_notice( 'menu_exists', 'notice-error', $text );
		print wp_json_encode( $response ); die();
	}

	// This will create an array of rows
	$menu_array = explode( PHP_EOL, $menu_data );

	/*
	 * These rows might have properties like CSS class or link target
	 * as a comma-separated list.  Explode each row into a separate array
	 * of properties for that new link.
	 */

	foreach( $menu_array as $row ) {
		// Replace ', ' with ',' to avoid spaces in the elements
		$row = str_replace( ', ', ',', $row );

		// Make an array of the row, splitting at commas
		$row_array = explode( ',', $row );

		// Add the array to our master array
		$full_menu_array[] = $row_array;
	}
	/*
	 * Tabs dictate hierarchy.  If there's one element which is indented
	 * too far - i.e., a direct grandchild of a parent with no intervening
	 * child - that's just not natural, man.  Life...uh...finds a way,
	 * but not on my beat.
	 */

	$previous_link_tab_count = 0;
	foreach( $full_menu_array as $menu_item ) {
		$link = $menu_item[ 0 ];
		$this_link_tab_count = substr_count( $link, "\t" );

		if( ( $this_link_tab_count - $previous_link_tab_count ) > 1 ) {
			$text = __( 'Please check your indentation to make sure one or more  of your submenus aren\'t indented too far.', 'crude-menus'  );
			$response = crude_menus_create_admin_notice( 'incorrect_indentation', 'notice-error', $text );
			print wp_json_encode( $response ); die();
		}

		$previous_link_tab_count = $this_link_tab_count;
	}

	/*
	 * If we've gotten to this point, everything checks out.  There's a menu
	 * name (which doesn't already exist), there's menu content, and every
	 * child has a parent.  So, let's create the menu, then assign the links to it.
	 */

	$new_menu_id = wp_create_nav_menu( $menu_title );

	/*
	 * Similar to above, we're going to let the number of tabs dictate the
	 * hierarchy.  This is when things get weird.
	 *
	 * Consider this map:
	 *
	 * Parent (zero tabs)
	 * Parent (zero tabs)
	 *		Child (one tab)
	 *		Child (one tab)
	 *			Grandchild (two tabs)
 	 *			Grandchild (two tabs)
	 *		Child (one tab)
	 * Parent (zero tabs)
	 *
	 * Comparing the number of tabs for this row vs. the number of tabs in the
	 * previous row:
	 * $this_link_tab_count - $previous_link_tab_count > 1 
	 * 		Broken hierarchy, and has already been kicked back with an error message.
	 * $this_link_tab_count - $previous_link_tab_count = 1 
	 *		This is a child of the previous link.  Use its ID as this link's parent
	 * $this_link_tab_count - $previous_link_tab_count = 0
	 *		This is a sibling of the previous link.  Use that link's parent's ID as
	 *		this link's parent.
	 * $this_link_tab_count - $previous_link_tab_count = -1
	 *		This is an "uncle" of the previous link.  Use that link's parent's *parent's*
	 *		id as this link's parent.
	 * In general, the formula is "from the previous ID, go back one generation more
	 * than the difference between the two to get this link's parent."
	 *
	 * I did say this is when things get weird.
	 */

	$previous_link_tab_count = 0;
	$previous_link_id = 0;

	foreach( $full_menu_array as $menu_item ) {
		$link = $menu_item[ 0 ];
		$link_details = crude_menus_find_link_details( $link );

		$css_class = isset( $menu_item[ 1 ] ) ? $menu_item[ 1 ] : '';
		$link_target = isset( $menu_item[ 2 ] ) ? '_blank' : '';
		// Allow the user to override the titles
		$link_title = isset( $menu_item[ 3 ] ) ? $menu_item[ 3 ] : $link_details[ 'title' ];

		$this_link_tab_count = strspn( $link, "\t" );
		$difference_in_tabs =  $this_link_tab_count - $previous_link_tab_count;
		$parent_id = crude_menus_determine_top_parent( $difference_in_tabs, $previous_link_id );

		$this_links_id = wp_update_nav_menu_item( $new_menu_id, 0, array(
			'menu-item-type'		=> $link_details[ 'type' ],
			'menu-item-url'			=> $link_details[ 'url' ],
			'menu-item-title'		=> $link_title,
			'menu-item-target'		=> $link_target,
			'menu-item-classes'		=> $css_class,
			'menu-item-status'		=> 'publish',
			'menu-item-parent-id'	=> $parent_id,
			'menu-item-object'		=> $link_details[ 'object' ],
			'menu-item-object-id'	=> $link_details[ 'object_id' ],
		) );

		$previous_link_tab_count = $this_link_tab_count;
		$previous_link_id = $this_links_id;
	}


	// Send a success message back through the AJAX tubes
	$text = __( 'The "' . $menu_title . '" menu has been created!', 'crude-menus' );
	$response = crude_menus_create_admin_notice( 'menu_created', 'notice-success', $text );
	print wp_json_encode( $response ); die();
}

add_action( 'wp_ajax_crude_menus_create_menu', 'crude_menus_create_menu' );

// Helper function to create admin notices

function crude_menus_create_admin_notice( $key, $class, $text ) {
	$out = array(
		'key' 		=> $key,
		'markup'	=> '',
	);

	$markup = '';
	$markup .= '<div class="cm-notice notice ' . $class . '">';
		$markup .= '<p>' . $text . '</p>';
	$markup .= '</div>';

	$out[ 'markup' ] = $markup;

	return $out;
}

// Helper function to tranverse the menu hierarchy

function crude_menus_determine_top_parent( $difference_in_tabs, $previous_post_id ) {
	if( $difference_in_tabs == 1 ) {
		return $previous_post_id;
	} else {
		/*
		 * Note: These are not post parents, these are menu item parents, which
		 * are stored in `_menu_item_menu_item_parent` in the postmeta table.
		 */
		$previous_post_ids_parent = get_post_meta( $previous_post_id, '_menu_item_menu_item_parent', true );
		$difference_in_tabs++; // Incrementing because we're counting *up* to 1.
		// Recursive functions FTW!
		return crude_menus_determine_top_parent( $difference_in_tabs, $previous_post_ids_parent );
	}
}

// Helper function to find the details for a menu item given the URL

function crude_menus_find_link_details( $link ) {
	$link_slug = basename( untrailingslashit( $link ) );
	$link_details = array();

	/*
	 * Maybe this slug is for a Page, Post, or Custom Post Type.  For
	 * get_page_by_path() to work, we need to know what post type it is,
	 * so we have to loop through all the public post types and try
	 * each one.
	 */
	$post_types = get_post_types( array( 'public' => true ) );
	foreach( $post_types as $post_type ) {
		$post_obj = get_page_by_path( $link_slug, OBJECT, $post_type );
		if( $post_obj ) {
			$link_details = array(
				'type'		=> 'post_type',
				'url'		=> '', // Unneeded for post links
				'title'		=> $post_obj->post_title,
				'object'	=> $post_obj->post_type,
				'object_id'	=> $post_obj->ID,
			);
			return $link_details;
		}
	}

	/*
	 * Welp, no matches were found for this link being for a post type.
	 * Maybe it's a Category, Post Tag, or other custom taxonomy?  Same
	 * thing applies - loop through each taxonomy and test the slug
	 * against it.
	 */
	$taxonomies = get_taxonomies( array( 'public' => true ) );
	foreach( $taxonomies as $taxonomy ) {
		$tax_obj = get_term_by( 'slug', $link_slug, $taxonomy, OBJECT );
		// var_dump( $tax_obj );
		if( $tax_obj ) {
			$link_details = array(
				'type'		=> 'taxonomy',
				'url'		=> '', // Unneeded for taxonomies
				'title'		=> $tax_obj->name,
				'object'	=> $tax_obj->taxonomy,
				'object_id'	=> $tax_obj->term_taxonomy_id,
			);
			return $link_details;
		}
	}

	/*
	 * Double welp.  If we've gotten here, it's gotta be a Custom link.
	 * The results needed are slightly different.
	 */

	$link_details = array(
		'type'		=> 'custom',
		'url'		=> $link, // Original link
		'title'		=> 'Custom Menu Link', // This can be overridden
		'object'	=> 'custom',
		'object_id'	=> '', // Unneeded for custom links
	);

	return $link_details;
}