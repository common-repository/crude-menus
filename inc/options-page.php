<?php

// Add a submenu as part of the Appearance menu

function cm_add_crude_menus_page() { 
	add_theme_page(
		'CRUDE Menus', 					// Page title
		'CRUDE Menus', 					// Menu title
		'manage_options',				// Cap needed
		'crude-menus',					// Menu slug
		'cm_create_menu_form_output'	// Callback function
	);
}

add_action( 'admin_menu', 'cm_add_crude_menus_page' );

/*
 * Since this plugin isn't writing to the wp_options table, I'm electing to
 * roll my own form, rather than using the Settings API.  Validation will
 * happen in create-menu.php, which handles the POST submission.
 */

function cm_create_menu_form_output() {
	wp_enqueue_script( 'cm_ajax' );
	wp_localize_script( 'cm_ajax', 'cm_menu_settings', array(
		'nonce'		=> wp_create_nonce( 'cm_create_menu_nonce' ),
	) );
	wp_enqueue_script( 'cm_general' );

	$form_action_url = esc_url( admin_url( 'admin-post.php' ) );

	// Easier to set the text here so as not to clutter the HTML
	$textareaplacehodler = 'Link, CSS Class, Link Target, Label' . PHP_EOL . 'Link, CSS Class, Link Target, Label...';
	?>

	<h1>CRUDE Menus - Create Menu</h1>
	<p>You can create a new menu by simply pasting a list of links and page titles into the box below.</p>
	<p>Your list should have only one menu item per row.  These rows can be comma-separated values, with the following options:</p>
	<ol>
		<li>Link (required)</li>
		<li>Extra CSS Class(es) (separated by spaces)</li>
		<li>Should this menu item open in a new tab? If left blank, it will open in the same tab; any other value will set it to open in a new tab.</li>
		<li>Title (if different from the page title)</li>
	</ol>
	<form id="cm-create-form" method="post" action="<?php echo $form_action_url; ?>">
		<input type="hidden" name="action" value="cm_create_menu_form" />
		<label for="cm-create-menu-title">Menu Title</label><br />
		<input type="text" class="cm-text-input" id="cm-create-menu-title" name="cm_create_menu_title" value="" /><br /><br />
		<label for="cm-create-menu-content"">Menu Content</label><br />
		<textarea placeholder="<?php echo $textareaplacehodler; ?>" cols="80" rows="15" type="text" class="cm-textarea" id="cm-create-menu-content" name="cm_create_menu_content" /></textarea><br /><br />
		<input type="submit" value="Create Menu" />
	</form>
<?php }