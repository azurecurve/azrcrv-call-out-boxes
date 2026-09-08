<?php
/*
	plugin functionality
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
 * Check whether a plugin is active, without relying on core's
 * is_plugin_active(). Core's is_plugin_active() lives in
 * wp-admin/includes/plugin.php, which is only loaded in admin requests — it
 * is NOT available on front-end requests. Since render_shortcode() below
 * runs on the front end (it's a shortcode callback rendering into public
 * post content) and needs to check whether the Icons plugin is active, a
 * local implementation is required rather than calling core's function
 * directly. Deliberately namespaced (not a bare global shim) so it's
 * unambiguous this is plugin-local, but still safe to call unqualified from
 * both admin and front-end code paths within this namespace.
 *
 * @param string $plugin Plugin basename, e.g. 'azrcrv-icons/azrcrv-icons.php'.
 *
 * @return bool
 */
function is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
}

/**
 * Create custom call-out-box template post type.
 */
function create_custom_post_type() {
	register_post_type(
		'call-out-box',
		array(
			'labels'              => array(
				'name'               => esc_html__( 'Templates', PLUGIN_TEXT_DOMAIN ),
				'singular_name'      => esc_html__( 'Template', PLUGIN_TEXT_DOMAIN ),
				'menu_name'          => esc_html__( 'Call-out Boxes', PLUGIN_TEXT_DOMAIN ),
				'name_admin_bar'     => esc_html__( 'Call-out Box Template', PLUGIN_TEXT_DOMAIN ),
				'all_items'          => esc_html__( 'All Templates', PLUGIN_TEXT_DOMAIN ),
				'add_new'            => esc_html__( 'Add New Template', PLUGIN_TEXT_DOMAIN ),
				'add_new_item'       => esc_html__( 'Add New Call-out Box Template', PLUGIN_TEXT_DOMAIN ),
				'edit'               => esc_html__( 'Edit Template', PLUGIN_TEXT_DOMAIN ),
				'edit_item'          => esc_html__( 'Edit Call-out Box Template', PLUGIN_TEXT_DOMAIN ),
				'new_item'           => esc_html__( 'New Call-out Box Template', PLUGIN_TEXT_DOMAIN ),
				'view'               => esc_html__( 'View Template', PLUGIN_TEXT_DOMAIN ),
				'view_item'          => esc_html__( 'View Call-out Box Template', PLUGIN_TEXT_DOMAIN ),
				'search_items'       => esc_html__( 'Search Call-out Box Templates', PLUGIN_TEXT_DOMAIN ),
				'not_found'          => esc_html__( 'No Call-out Box Templates found', PLUGIN_TEXT_DOMAIN ),
				'not_found_in_trash' => esc_html__( 'No Call-out Box Templates found in Trash', PLUGIN_TEXT_DOMAIN ),
				'parent'             => esc_html__( 'Parent Call-out Box', PLUGIN_TEXT_DOMAIN ),
			),
			'public'               => false,
			'exclude_from_search'  => true,
			'publicly_queryable'   => false,
			'menu_position'        => 50,
			'supports'             => array( 'title' ),
			'taxonomies'           => array(),
			'menu_icon'            => 'dashicons-testimonial',
			'has_archive'          => false,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_admin_bar'    => true,
			'show_in_nav_menus'    => false,
			'show_in_rest'         => false,
		)
	);
}

/**
 * Add meta boxes to the call-out-box template edit screen.
 */
function add_meta_boxes() {

	add_meta_box(
		PLUGIN_UNDERSCORE . '_key_meta_box',
		esc_html__( 'Key', PLUGIN_TEXT_DOMAIN ),
		__NAMESPACE__ . '\\show_key_meta_box',
		'call-out-box',
		'normal',
		'default'
	);

	add_meta_box(
		PLUGIN_UNDERSCORE . '_example_meta_box',
		esc_html__( 'Example', PLUGIN_TEXT_DOMAIN ),
		__NAMESPACE__ . '\\show_example_meta_box',
		'call-out-box',
		'normal',
		'default'
	);

	add_meta_box(
		PLUGIN_UNDERSCORE . '_settings_meta_box',
		esc_html__( 'Template Settings', PLUGIN_TEXT_DOMAIN ),
		__NAMESPACE__ . '\\show_settings_meta_box',
		'call-out-box',
		'normal',
		'default'
	);
}

/**
 * Show key meta box: tells the author what 'key' value to use in the
 * shortcode to pull this template's defaults.
 */
function show_key_meta_box() {
	global $post;
	?>

	<p>
		<?php
			printf(
				/* translators: %s: the template's shortcode key, wrapped in <strong> tags. */
				esc_html__( 'Use the following key in the shortcode to use this template: %s', PLUGIN_TEXT_DOMAIN ),
				'<strong>' . esc_html( $post->post_name ) . '</strong>'
			);
		?>
	</p>

	<?php
}

/**
 * Show example meta box: a live preview of how the template will render.
 */
function show_example_meta_box() {
	global $post;

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true );

	$icon             = ( isset( $azrcrv_cob['icon'] ) && strlen( $azrcrv_cob['icon'] ) > 0 ) ? $azrcrv_cob['icon'] : $options['icon'];
	$heading          = ( isset( $azrcrv_cob['heading'] ) && strlen( $azrcrv_cob['heading'] ) > 0 ) ? $azrcrv_cob['heading'] : esc_html__( 'This is an example', PLUGIN_TEXT_DOMAIN );
	$width            = ( isset( $azrcrv_cob['width'] ) && strlen( $azrcrv_cob['width'] ) > 0 ) ? $azrcrv_cob['width'] : $options['width'];
	$margin           = ( isset( $azrcrv_cob['margin'] ) && strlen( $azrcrv_cob['margin'] ) > 0 ) ? $azrcrv_cob['margin'] : $options['margin'];
	$padding          = ( isset( $azrcrv_cob['padding'] ) && strlen( $azrcrv_cob['padding'] ) > 0 ) ? $azrcrv_cob['padding'] : $options['padding'];
	$border           = ( isset( $azrcrv_cob['border'] ) && strlen( $azrcrv_cob['border'] ) > 0 ) ? $azrcrv_cob['border'] : $options['border'];
	$border_radius    = ( isset( $azrcrv_cob['border-radius'] ) && strlen( $azrcrv_cob['border-radius'] ) > 0 ) ? $azrcrv_cob['border-radius'] : $options['border-radius'];
	$color            = ( isset( $azrcrv_cob['color'] ) && strlen( $azrcrv_cob['color'] ) > 0 ) ? $azrcrv_cob['color'] : $options['color'];
	$background_color = ( isset( $azrcrv_cob['background-color'] ) && strlen( $azrcrv_cob['background-color'] ) > 0 ) ? $azrcrv_cob['background-color'] : $options['background-color'];

	?>

	<p>
		<?php
			echo render_shortcode(
				array(
					'icon'              => $icon,
					'heading'           => $heading,
					'width'             => $width,
					'margin'            => $margin,
					'padding'           => $padding,
					'border'            => $border,
					'border-radius'     => $border_radius,
					'color'             => $color,
					'background-color'  => $background_color,
				),
				esc_html__( 'The meta box will look approximately like this example; your site\'s CSS can cause some differences.', PLUGIN_TEXT_DOMAIN )
			);
		?>
	</p>

	<?php
}

/**
 * Show settings meta box: per-template default overrides.
 */
function show_settings_meta_box() {
	global $post;

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true );

	?>

	<fieldset>

		<table class="form-table">

			<?php if ( is_plugin_active( 'azrcrv-icons/azrcrv-icons.php' ) && 1 == $options['icons-integration'] ) : ?>
				<tr>
					<th scope="row">
						<label for="icon"><?php esc_html_e( 'Icon', PLUGIN_TEXT_DOMAIN ); ?></label>
					</th>
					<td>
						<select name="icon">
						<?php
							$current_icon = ( isset( $azrcrv_cob['icon'] ) && strlen( $azrcrv_cob['icon'] ) > 0 ) ? $azrcrv_cob['icon'] : $options['icon'];
						?>
						<option value="" <?php selected( '', $current_icon ); ?>>&nbsp;</option>
						<?php
						$icons = \azrcrv_i_get_icons();

						foreach ( $icons as $icon_id => $icon ) {
							echo '<option value="' . esc_attr( $icon_id ) . '"';
							selected( $current_icon, $icon_id );
							echo '>' . esc_html( $icon_id ) . '</option>';
						}
						echo '</select>';
						if ( strlen( $current_icon ) > 0 ) {
							echo '&nbsp;' . \azrcrv_i_icon( array( esc_html( $current_icon ) ) );
						}
						?>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
				<th scope="row">
					<label for="heading"><?php esc_html_e( 'Heading', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $heading = isset( $azrcrv_cob['heading'] ) ? $azrcrv_cob['heading'] : ''; ?>
					<input type="text" name="heading" value="<?php echo esc_attr( $heading ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default heading which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>heading</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="width"><?php esc_html_e( 'Width', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $width = isset( $azrcrv_cob['width'] ) ? $azrcrv_cob['width'] : ''; ?>
					<input type="text" name="width" value="<?php echo esc_attr( $width ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default width which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>width</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="margin"><?php esc_html_e( 'Margin', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $margin = isset( $azrcrv_cob['margin'] ) ? $azrcrv_cob['margin'] : ''; ?>
					<input type="text" name="margin" value="<?php echo esc_attr( $margin ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default margin which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>margin</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="padding"><?php esc_html_e( 'Padding', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $padding = isset( $azrcrv_cob['padding'] ) ? $azrcrv_cob['padding'] : ''; ?>
					<input type="text" name="padding" value="<?php echo esc_attr( $padding ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default padding which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>padding</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="border"><?php esc_html_e( 'Border', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $border = isset( $azrcrv_cob['border'] ) ? $azrcrv_cob['border'] : ''; ?>
					<input type="text" name="border" value="<?php echo esc_attr( $border ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default border which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>border</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="border-radius"><?php esc_html_e( 'Border Radius', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $border_radius = isset( $azrcrv_cob['border-radius'] ) ? $azrcrv_cob['border-radius'] : ''; ?>
					<input type="text" name="border-radius" value="<?php echo esc_attr( $border_radius ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default border radius which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>border-radius</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="color"><?php esc_html_e( 'Color', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $color = isset( $azrcrv_cob['color'] ) ? $azrcrv_cob['color'] : ''; ?>
					<input type="text" name="color" value="<?php echo esc_attr( $color ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default text color which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>text-color</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="background-color"><?php esc_html_e( 'Background Color', PLUGIN_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<?php $background_color = isset( $azrcrv_cob['background-color'] ) ? $azrcrv_cob['background-color'] : ''; ?>
					<input type="text" name="background-color" value="<?php echo esc_attr( $background_color ); ?>" class="regular-text" />
					<p class="description"><?php printf( esc_html__( 'Default background color which can be overridden using the %s shortcode parameter.', PLUGIN_TEXT_DOMAIN ), '<strong>background-color</strong>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</td>
			</tr>

		</table>
	</fieldset>

	<?php
	wp_nonce_field( PLUGIN_UNDERSCORE . '_show_settings_meta_box_nonce', PLUGIN_UNDERSCORE . '_show_settings_meta_box' );
}

/**
 * Save the template settings metabox.
 *
 * @param int      $post_id The post ID.
 * @param \WP_Post $post    The post object.
 */
function save_settings_metabox( $post_id, $post ) {

	if ( 'call-out-box' !== $post->post_type ) {
		return;
	}

	if ( ! isset( $_POST[ PLUGIN_UNDERSCORE . '_show_settings_meta_box' ] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ PLUGIN_UNDERSCORE . '_show_settings_meta_box' ] ) ), PLUGIN_UNDERSCORE . '_show_settings_meta_box_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true );
	if ( ! is_array( $azrcrv_cob ) ) {
		$azrcrv_cob = array();
	}

	if ( is_plugin_active( 'azrcrv-icons/azrcrv-icons.php' ) && 1 == $options['icons-integration'] ) {
		if ( isset( $_POST['icon'] ) ) {
			$azrcrv_cob['icon'] = sanitize_text_field( wp_unslash( $_POST['icon'] ) );
		}
	}

	$fields = array( 'heading', 'width', 'margin', 'padding', 'border', 'border-radius', 'color', 'background-color' );
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			$azrcrv_cob[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
		}
	}

	update_post_meta( $post->ID, '_azrcrv_cob', $azrcrv_cob );
}

/**
 * Render the call-out-box shortcode ([call-out-box] and [cob]).
 *
 * @param array       $atts    Shortcode attributes.
 * @param string|null $content Shortcode content.
 *
 * @return string
 */
function render_shortcode( $atts, $content = null ) {

	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	$args = shortcode_atts(
		array(
			'key'               => '',
			'icon'              => '',
			'heading'           => '',
			'width'             => '',
			'margin'            => '',
			'padding'           => '',
			'border'            => '',
			'border-radius'     => '',
			'color'             => '',
			'background-color'  => '',
		),
		$atts
	);

	$key               = $args['key'];
	$icon              = $args['icon'];
	$heading           = $args['heading'];
	$width             = $args['width'];
	$margin            = $args['margin'];
	$padding           = $args['padding'];
	$border            = $args['border'];
	$border_radius     = $args['border-radius'];
	$color             = $args['color'];
	$background_color  = $args['background-color'];

	// Only look up a template when a key was actually supplied. Previously
	// this checked isset( $key ), which is always true because
	// shortcode_atts() always supplies a default of '' — meaning every
	// keyless [call-out-box] call ran get_page_by_path( '', ... )
	// unnecessarily. See PRD §6, bug #5.
	if ( '' !== $key ) {
		$template_post = get_page_by_path( $key, OBJECT, 'call-out-box' );
		if ( $template_post ) {
			$azrcrv_cob = get_post_meta( $template_post->ID, '_azrcrv_cob', true );

			if ( 0 === strlen( $icon ) && isset( $azrcrv_cob['icon'] ) && strlen( $azrcrv_cob['icon'] ) > 0 ) {
				$icon = $azrcrv_cob['icon'];
			}
			if ( 0 === strlen( $heading ) && isset( $azrcrv_cob['heading'] ) && strlen( $azrcrv_cob['heading'] ) > 0 ) {
				$heading = $azrcrv_cob['heading'];
			}
			if ( 0 === strlen( $width ) && isset( $azrcrv_cob['width'] ) && strlen( $azrcrv_cob['width'] ) > 0 ) {
				$width = $azrcrv_cob['width'];
			}
			if ( 0 === strlen( $margin ) && isset( $azrcrv_cob['margin'] ) && strlen( $azrcrv_cob['margin'] ) > 0 ) {
				$margin = $azrcrv_cob['margin'];
			}
			if ( 0 === strlen( $padding ) && isset( $azrcrv_cob['padding'] ) && strlen( $azrcrv_cob['padding'] ) > 0 ) {
				$padding = $azrcrv_cob['padding'];
			}
			if ( 0 === strlen( $border ) && isset( $azrcrv_cob['border'] ) && strlen( $azrcrv_cob['border'] ) > 0 ) {
				$border = $azrcrv_cob['border'];
			}
			if ( 0 === strlen( $border_radius ) && isset( $azrcrv_cob['border-radius'] ) && strlen( $azrcrv_cob['border-radius'] ) > 0 ) {
				$border_radius = $azrcrv_cob['border-radius'];
			}
			if ( 0 === strlen( $color ) && isset( $azrcrv_cob['color'] ) && strlen( $azrcrv_cob['color'] ) > 0 ) {
				$color = $azrcrv_cob['color'];
			}
			if ( 0 === strlen( $background_color ) && isset( $azrcrv_cob['background-color'] ) && strlen( $azrcrv_cob['background-color'] ) > 0 ) {
				$background_color = $azrcrv_cob['background-color'];
			}
		}
	}

	if ( 0 === strlen( $icon ) ) {
		$icon = $options['icon'];
	}
	if ( 0 === strlen( $width ) ) {
		$width = $options['width'];
	}
	if ( 0 === strlen( $margin ) ) {
		$margin = $options['margin'];
	}
	if ( 0 === strlen( $padding ) ) {
		$padding = $options['padding'];
	}
	if ( 0 === strlen( $border ) ) {
		$border = $options['border'];
	}
	if ( 0 === strlen( $border_radius ) ) {
		$border_radius = $options['border-radius'];
	}
	if ( 0 === strlen( $color ) ) {
		$color = $options['color'];
	}
	if ( 0 === strlen( $background_color ) ) {
		$background_color = $options['background-color'];
	}

	$output = '';

	if ( strlen( $content ) > 0 ) {

		// Build the inline style attribute. Fragments are escaped with
		// esc_attr() (correct for an HTML attribute context) rather than
		// esc_html__() (a translation function previously misused here for
		// non-translatable CSS value strings). See PRD §6, bug #4.
		$style = '';
		if ( strlen( $width ) > 0 ) {
			$style .= 'width: ' . esc_attr( $width ) . '; ';
		}
		if ( strlen( $margin ) > 0 ) {
			$style .= 'margin: ' . esc_attr( $margin ) . '; ';
		}
		if ( strlen( $padding ) > 0 ) {
			$style .= 'padding: ' . esc_attr( $padding ) . '; ';
		}
		if ( strlen( $border ) > 0 ) {
			$style .= 'border: ' . esc_attr( $border ) . '; ';
		}
		if ( strlen( $color ) > 0 ) {
			$style .= 'color: ' . esc_attr( $color ) . '; ';
		}
		if ( strlen( $background_color ) > 0 ) {
			$style .= 'background-color: ' . esc_attr( $background_color ) . '; ';
		}
		if ( strlen( $border_radius ) > 0 ) {
			$style .= 'border-radius: ' . esc_attr( $border_radius ) . '; ';
		}

		$output_content = '';
		if ( strlen( $heading ) > 0 ) {
			$output_content .= $options['heading-open'];
		}
		// Only render an icon when the "Integrate with Icons" setting is
		// actually turned on (and the Icons plugin is active). Previously
		// this only checked is_plugin_active() — inherited from the
		// original plugin, which had the same gap — so an icon would show
		// up (in both the front-end shortcode output and the admin example
		// previews on the Settings page and template edit screen) whenever
		// the Icons plugin happened to be active, regardless of whether the
		// site owner had actually opted in to integration.
		if ( strlen( $icon ) > 0 && 1 == $options['icons-integration'] && is_plugin_active( 'azrcrv-icons/azrcrv-icons.php' ) ) {
			$output_content .= \azrcrv_i_icon( array( esc_html( $icon ) ) ) . '&nbsp;';
		}
		if ( strlen( $heading ) > 0 ) {
			$output_content .= esc_html( $heading ) . $options['heading-close'];
		}
		$output_content .= '<p>' . $content . '</p>';

		$output = "<div class='azrcrv-cob' style='" . $style . "'>";
		if ( 1 == $options['do-shortcode'] ) {
			$output .= do_shortcode( $output_content );
		} else {
			$output .= $output_content;
		}
		$output .= '</div>';
	}

	return $output;
}
