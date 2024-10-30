<?php
/*
 * Plugin Name: CRUDE Menus
 * Plugin URI:	https://www.robertgillmer.com
 * Description: This plugin allows an admin to create navigation menus by simply pasting a list of comma-separated values into a text box.
 * Author:		Robert Gillmer
 * Author URI:	http://www.robertgillmer.com
 * Version:		1.0.0
 * Text Domain: crude-menus
 */

// If this file is called directly, then die
if ( ! defined( 'WPINC' ) ) {
	die();
}

// General functions
include_once( __DIR__ . '/inc/general.php' );

// Register the backend page
include_once( __DIR__ . '/inc/options-page.php' );

// Code for creating the menu via what's AJAXed to admin-ajax.php
include_once( __DIR__ . '/inc/create-menu.php' );