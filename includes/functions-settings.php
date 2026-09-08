<?php
/*
	settings functions
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
 * Get options including defaults.
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'icons-integration' => 0,
		'icon'              => 'lightbulb',
		'heading-open'      => '<h4 class="azrcrv-cob">',
		'heading-close'     => '</h4>',
		'color'             => '#000',
		'background-color'  => '#99CBFF',
		'width'             => '75%',
		'margin'            => 'auto',
		'padding'           => '5px 10px',
		'border'            => '1px solid #007FFF',
		'border-radius'     => '15px',
		'do-shortcode'      => 0,
	);

	$options = get_option( $option_name, $defaults );

	$options = wp_parse_args( $options, $defaults );

	return $options;
}

/**
 * Plugin activation. The call-out-box template post type is registered on
 * 'init' as normal; rewrite rules are flushed once on activation so the
 * admin screens for it are available immediately rather than only after
 * permalinks are next resaved.
 */
function activate_plugin() {
	create_custom_post_type();
	flush_rewrite_rules();
}

/**
 * Display Settings page.
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-cob' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	echo '<div id="' . esc_attr( PLUGIN_HYPHEN ) . '-general" class="wrap">';

		echo '<h1>';
			echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
					<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-cob' ) . '</strong></p>
				</div>';
	}

		require_once 'tab-settings.php';
		require_once 'tab-instructions.php';
		require_once 'tab-other-plugins.php';
		require_once 'tabs-output.php';
	?>

	</div>
	<?php
}

/**
 * Save settings.
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-cob' ) );
	}
	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' ) ) {

		// Retrieve original plugin options array.
		$options = get_option_with_defaults( PLUGIN_HYPHEN );

		$options['icons-integration'] = isset( $_POST['icons-integration'] ) ? 1 : 0;

		if ( isset( $_POST['icon'] ) ) {
			$options['icon'] = sanitize_text_field( wp_unslash( $_POST['icon'] ) );
		}

		$allowed = get_allowed_tags();

		if ( isset( $_POST['heading-open'] ) ) {
			$options['heading-open'] = wp_kses( wp_unslash( $_POST['heading-open'] ), $allowed );
		}

		if ( isset( $_POST['heading-close'] ) ) {
			$options['heading-close'] = wp_kses( wp_unslash( $_POST['heading-close'] ), $allowed );
		}

		if ( isset( $_POST['width'] ) ) {
			$options['width'] = sanitize_text_field( wp_unslash( $_POST['width'] ) );
		}

		if ( isset( $_POST['margin'] ) ) {
			$options['margin'] = sanitize_text_field( wp_unslash( $_POST['margin'] ) );
		}

		if ( isset( $_POST['padding'] ) ) {
			$options['padding'] = sanitize_text_field( wp_unslash( $_POST['padding'] ) );
		}

		if ( isset( $_POST['border'] ) ) {
			$options['border'] = sanitize_text_field( wp_unslash( $_POST['border'] ) );
		}

		if ( isset( $_POST['border-radius'] ) ) {
			$options['border-radius'] = sanitize_text_field( wp_unslash( $_POST['border-radius'] ) );
		}

		if ( isset( $_POST['color'] ) ) {
			$options['color'] = sanitize_text_field( wp_unslash( $_POST['color'] ) );
		}

		if ( isset( $_POST['background-color'] ) ) {
			$options['background-color'] = sanitize_text_field( wp_unslash( $_POST['background-color'] ) );
		}

		// 'Process shortcodes within call-out box content' — previously this
		// option existed and was read by render_shortcode(), but had no
		// field on this screen and was never written here, so it could
		// never actually be turned on. See PRD §6, bug #2.
		$options['do-shortcode'] = isset( $_POST['do-shortcode'] ) ? 1 : 0;

		// Store updated options array to database.
		update_option( PLUGIN_HYPHEN, $options );

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( 'page', PLUGIN_HYPHEN . '&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

/**
 * Get allowed tags for wp_kses() when sanitizing the heading open/close tags.
 */
function get_allowed_tags() {

	$allowed_tags = wp_kses_allowed_html();

	$allowed_tags['h1']['class'] = 1;
	$allowed_tags['h1']['style'] = 1;
	$allowed_tags['h2']['class'] = 1;
	$allowed_tags['h2']['style'] = 1;
	$allowed_tags['h3']['class'] = 1;
	$allowed_tags['h3']['style'] = 1;
	$allowed_tags['h4']['class'] = 1;
	$allowed_tags['h4']['style'] = 1;
	$allowed_tags['h5']['class'] = 1;
	$allowed_tags['h5']['style'] = 1;

	return $allowed_tags;
}
