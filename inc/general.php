<?php

// Register our JS

function cm_register_scripts() {
	wp_register_script( 'cm_ajax', plugin_dir_url( __FILE__ ) . '/js/cm-ajax.js', array( 'jquery' ), '', true );
	wp_register_script( 'cm_general', plugin_dir_url( __FILE__ ) . '/js/cm-general.js', array( 'jquery' ), '', true );
}

add_action( 'admin_enqueue_scripts', 'cm_register_scripts' );