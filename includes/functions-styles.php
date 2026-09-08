<?php
/*
	style functions
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
 * Register admin styles.
 */
function register_admin_styles() {
	wp_register_style( PLUGIN_HYPHEN . '-admin-styles', esc_url_raw( plugins_url( '../assets/css/admin.css', __FILE__ ) ), array(), '2.0.0' );
	wp_register_style( 'azrcrv-admin-standard-styles', esc_url_raw( plugins_url( '../assets/css/admin-standard.css', __FILE__ ) ), array(), '22.3.2' );
	wp_register_style( 'azrcrv-pluginmenu-admin-styles', esc_url_raw( plugins_url( '../assets/css/admin-pluginmenu.css', __FILE__ ) ), array(), '22.3.2' );
}

/**
 * Enqueue admin styles.
 */
function enqueue_admin_styles() {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == PLUGIN_HYPHEN || $_GET['page'] == 'azrcrv-plugin-menu' ) ) {
		wp_enqueue_style( PLUGIN_HYPHEN . '-admin-styles' );
		wp_enqueue_style( 'azrcrv-admin-standard-styles' );
		wp_enqueue_style( 'azrcrv-pluginmenu-admin-styles' );
	}
}

/**
 * Runtime flag: whether any post in the currently-queried set contains the
 * call-out-box shortcode, and therefore whether the front-end stylesheet is
 * needed on this request. Set on 'the_posts' (the earliest point the actual
 * post content is available), consumed on 'wp_enqueue_scripts'.
 *
 * Retained as the detection strategy (rather than switching to a
 * is_singular()/get_post_type() candidacy check like azrcrv-related-posts
 * uses) because this plugin is shortcode-based: a call-out box can appear on
 * any post type, and only actually needs styling when the shortcode is
 * genuinely present in the content being displayed.
 *
 * @var bool
 */
$GLOBALS['azrcrv_cob_shortcode_present'] = false;

/**
 * Scan queried posts for the call-out-box shortcode and flag it for
 * enqueue_front_end_styles() if found.
 *
 * @param array          $posts Posts returned by the query.
 * @param \WP_Query|null $query The query object (unused; accepted so this can
 *                              be hooked to 'the_posts' with 2 args).
 *
 * @return array Unmodified $posts.
 */
function maybe_flag_shortcode_present( $posts, $query = null ) {

	if ( empty( $posts ) ) {
		return $posts;
	}

	$shortcodes = array( 'call-out-box', 'cob' );

	foreach ( $posts as $post ) {
		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $post->post_content, $shortcode ) ) {
				$GLOBALS['azrcrv_cob_shortcode_present'] = true;
				return $posts;
			}
		}
	}

	return $posts;
}

/**
 * Register the front-end stylesheet. Registration is cheap and always safe;
 * actual loading is gated in enqueue_front_end_styles().
 */
function register_front_end_styles() {
	wp_register_style( PLUGIN_HYPHEN . '-styles', esc_url_raw( plugins_url( '../assets/css/call-out-boxes.css', __FILE__ ) ), array(), '1.0.0' );
}

/**
 * Enqueue the front-end stylesheet, but only when the shortcode was found in
 * the queried content by maybe_flag_shortcode_present().
 */
function enqueue_front_end_styles() {

	if ( empty( $GLOBALS['azrcrv_cob_shortcode_present'] ) ) {
		return;
	}

	wp_enqueue_style( PLUGIN_HYPHEN . '-styles' );
}
