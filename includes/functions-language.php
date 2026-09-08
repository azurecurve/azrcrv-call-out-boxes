<?php
/*
	language functions
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\CallOutBoxes;

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Load language files.
 */
function load_languages() {
	$plugin_rel_path = basename( dirname( PLUGIN_FILE ) ) . '/assets/languages';
	load_plugin_textdomain( 'azrcrv-cob', false, $plugin_rel_path );
}
