<?php
/*
	settings tab on settings page
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
 * Settings tab.
 */

$icons_plugin_active = is_plugin_active( 'azrcrv-icons/azrcrv-icons.php' );

if ( $icons_plugin_active ) {
	$icons_integration_field = '<label for="icons-integration"><input name="icons-integration" type="checkbox" id="icons-integration" value="1"' . checked( 1, $options['icons-integration'], false ) . ' />' . sprintf( esc_html__( 'Enable integration with %1$s from %2$s?', 'azrcrv-cob' ), 'Icons', 'azurecurve' ) . '</label>';
} else {
	$icons_integration_field = sprintf( esc_html__( '%1$s from %2$s is not installed/activated.', 'azrcrv-cob' ), 'Icons', 'azurecurve' );
}

$icon_field_row = '';
if ( $icons_plugin_active && 1 == $options['icons-integration'] ) {

	$icon_options = '<option value="" ' . selected( '', $options['icon'], false ) . '>&nbsp;</option>';
	if ( function_exists( '\\azrcrv_i_get_icons' ) ) {
		$icons = \azrcrv_i_get_icons();
		foreach ( $icons as $icon_id => $icon ) {
			$icon_options .= '<option value="' . esc_attr( $icon_id ) . '"' . selected( $options['icon'], $icon_id, false ) . '>' . esc_html( $icon_id ) . '</option>';
		}
	}

	$icon_preview = '';
	if ( strlen( $options['icon'] ) > 0 && function_exists( '\\azrcrv_i_icon' ) ) {
		$icon_preview = '&nbsp;' . \azrcrv_i_icon( array( esc_html( $options['icon'] ) ) );
	}

	$icon_field_row = '
	<tr>

		<th scope="row">

			<label for="icon">' . esc_html__( 'Icon', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<select name="icon" id="icon">' . $icon_options . '</select>' . $icon_preview . '

		</td>

	</tr>';
}

$example_box = render_shortcode( array( 'heading' => esc_html__( 'This is an example call-out box', 'azrcrv-cob' ) ), esc_html__( 'The call-out box will look like this example.', 'azrcrv-cob' ) );

$do_shortcode_checked = ! empty( $options['do-shortcode'] ) ? ' checked="checked"' : '';

$tab_settings_label = esc_html__( 'Settings', 'azrcrv-cob' );
$tab_settings       = '
<table class="form-table azrcrv-cob-settings">

	<tr>

		<th scope="row" colspan="2">

			<label for="explanation">
				' . sprintf( esc_html__( 'Call-out Boxes are added to posts, pages or other post types using the %s shortcode.', 'azrcrv-cob' ), '<strong>call-out-box</strong>' ) . '
			</label>

		</th>

	</tr>

	<tr>

		<th scope="row">
			' . esc_html__( 'Example call-out box', 'azrcrv-cob' ) . '
		</th>

	</tr>

	<tr>

		<td scope="row" class="azrcrv-cob-example">
			' . $example_box . '
		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="icons-integration">
				' . sprintf( esc_html__( 'Integrate with %1$s from %2$s', 'azrcrv-cob' ), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/icons/">Icons</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>' ) . '
			</label>

		</th>

		<td>
			' . $icons_integration_field . '
		</td>

	</tr>' . $icon_field_row . '

	<tr>

		<th scope="row">

			<label for="heading-open">' . esc_html__( 'Heading Open', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="heading-open" id="heading-open" value="' . esc_attr( $options['heading-open'] ) . '" class="regular-text" />

			<p class="description">' . esc_html__( 'Opening tag for heading shortcode parameter.', 'azrcrv-cob' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="heading-close">' . esc_html__( 'Heading Close', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="heading-close" id="heading-close" value="' . esc_attr( $options['heading-close'] ) . '" class="regular-text" />

			<p class="description">' . esc_html__( 'Closing tag for heading shortcode parameter.', 'azrcrv-cob' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="width">' . esc_html__( 'Width', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="width" id="width" value="' . esc_attr( $options['width'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default width which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>width</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="margin">' . esc_html__( 'Margin', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="margin" id="margin" value="' . esc_attr( $options['margin'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default margin which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>margin</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="padding">' . esc_html__( 'Padding', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="padding" id="padding" value="' . esc_attr( $options['padding'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default padding which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>padding</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="border">' . esc_html__( 'Border', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="border" id="border" value="' . esc_attr( $options['border'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default border which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>border</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="border-radius">' . esc_html__( 'Border Radius', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="border-radius" id="border-radius" value="' . esc_attr( $options['border-radius'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default border radius which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>border-radius</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="color">' . esc_html__( 'Color', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="color" id="color" value="' . esc_attr( $options['color'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default text color which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>text-color</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="background-color">' . esc_html__( 'Background Color', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<input type="text" name="background-color" id="background-color" value="' . esc_attr( $options['background-color'] ) . '" class="regular-text" />

			<p class="description">' . sprintf( esc_html__( 'Default background color which can be overridden using the %s shortcode parameter.', 'azrcrv-cob' ), '<strong>background-color</strong>' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">

			<label for="do-shortcode">' . esc_html__( 'Process shortcodes within content', 'azrcrv-cob' ) . '</label>

		</th>

		<td>

			<label for="do-shortcode">
				<input name="do-shortcode" type="checkbox" id="do-shortcode"' . $do_shortcode_checked . ' />
				' . esc_html__( 'Run the content inside a call-out box through do_shortcode(), so other shortcodes nested inside it are also processed', 'azrcrv-cob' ) . '
			</label>

			<p class="description">' . esc_html__( 'Leave this off unless you specifically place other shortcodes inside a call-out box\'s content.', 'azrcrv-cob' ) . '</p>

		</td>

	</tr>

	<tr>

		<th scope="row">&nbsp;</th>

		<td>

			<p>
				' . esc_html__( 'The following parameters can be supplied to the shortcode:', 'azrcrv-cob' ) . '
				<ul style="list-style-type: none; padding-left: 10px;">
					<li>key</li>
					<li>icon</li>
					<li>heading</li>
					<li>width</li>
					<li>margin</li>
					<li>padding</li>
					<li>border</li>
					<li>border-radius</li>
					<li>color</li>
					<li>background-color</li>
				</ul>
			</p>

		</td>

	</tr>

</table>';
