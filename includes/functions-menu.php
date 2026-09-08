<?php
/*
	menu functions
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
 * Add action link on plugins page.
 */
function add_plugin_action_link( $links, $file ) {

	$this_plugin = PLUGIN_SLUG . '/' . PLUGIN_SLUG . '.php';

	if ( $file == $this_plugin ) {
		// Text domain corrected to 'azrcrv-cob' ('call-out-boxes'); the
		// previous version of this string used the text domain of a
		// different azurecurve plugin ('get-github-file') by mistake, so it
		// was never actually translated by this plugin's own .po file.
		$settings_link = '<a href="' . esc_url_raw( admin_url( 'admin.php?page=' . PLUGIN_HYPHEN ) ) . '"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-cob' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 */
function create_admin_menu() {

	// add settings to the call-out-box templates submenu.
	add_submenu_page(
		'edit.php?post_type=call-out-box',
		esc_html__( 'Call-out Boxes Settings', 'azrcrv-cob' ),
		esc_html__( 'Settings', 'azrcrv-cob' ),
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_options'
	);

	// add settings to the azurecurve menu.
	add_submenu_page(
		'azrcrv-plugin-menu',
		esc_html__( 'Call-out Boxes Settings', 'azrcrv-cob' ),
		esc_html__( 'Call-out Boxes', 'azrcrv-cob' ),
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_options'
	);
}
