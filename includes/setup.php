<?php
/*
	setup
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
 * Setup registration of activation hooks, actions, filters and shortcodes.
 */

// activation.
register_activation_hook( PLUGIN_FILE, __NAMESPACE__ . '\\activate_plugin' );

// add actions.
add_action( 'init', __NAMESPACE__ . '\\create_custom_post_type' );
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'admin_post_' . PLUGIN_UNDERSCORE . '_save_options', __NAMESPACE__ . '\\save_options' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\add_meta_boxes' );
add_action( 'save_post', __NAMESPACE__ . '\\save_settings_metabox', 1, 2 );

// Note: this plugin has no network-wide settings, so no network_admin_menu /
// network_admin_edit_* hooks are registered here (see PRD §6, bug #1 — the
// previous version registered hooks pointing at functions that were never
// defined, which fataled when visiting Network Admin on a multisite install).

// front-end: only enqueue the front-end stylesheet on requests where the
// shortcode is actually present in the queried posts' content.
add_filter( 'the_posts', __NAMESPACE__ . '\\maybe_flag_shortcode_present', 10, 2 );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_front_end_styles' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_front_end_styles' );

// admin: register/enqueue admin styles and scripts on this plugin's own pages.
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );
$plugin_slug_for_um = plugin_basename( trim( PLUGIN_FILE ) );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_url', __NAMESPACE__ . '\\custom_image_url' );

// add shortcodes.
add_shortcode( 'call-out-box', __NAMESPACE__ . '\\render_shortcode' );
add_shortcode( 'cob', __NAMESPACE__ . '\\render_shortcode' );
