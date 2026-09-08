<?php
/*
	instructions tab on settings page
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
 * Instructions tab.
 */
$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-cob' );
$tab_instructions       = '
<table class="form-table azrcrv-cob-settings">

	<tr>

		<td scope="row" colspan="2">

			<p>' . sprintf( esc_html__( 'Call-out Boxes places a configurable, styled box around some content, using the %1$s or %2$s shortcode on posts, pages, or any other post type.', 'azrcrv-cob' ), '<strong>[call-out-box]</strong>', '<strong>[cob]</strong>' ) . '</p>

			<h4>' . esc_html__( 'Basic usage', 'azrcrv-cob' ) . '</h4>

			<p>' . esc_html__( 'Wrap the content you want inside the box between opening and closing shortcode tags, for example:', 'azrcrv-cob' ) . '</p>

			<p><code>[call-out-box heading="Note"]This is important.[/call-out-box]</code></p>

			<p>' . esc_html__( 'Any parameter left out falls back to the defaults configured on the Settings tab.', 'azrcrv-cob' ) . '</p>

			<h4>' . esc_html__( 'Using templates', 'azrcrv-cob' ) . '</h4>

			<p>' . esc_html__( 'Templates let you define a reusable set of defaults (icon, heading, colours, sizing) once, then reuse them by key across many call-out boxes, without having to repeat every parameter each time.', 'azrcrv-cob' ) . '</p>

			<p>' . esc_html__( 'Create a template under Call-out Boxes → Templates. Each template has a Key shown in its Key meta box; use that key with the key parameter to apply the template\'s settings:', 'azrcrv-cob' ) . '</p>

			<p><code>[call-out-box key="warning"]Proceed carefully.[/call-out-box]</code></p>

			<p>' . esc_html__( 'Values are resolved in this order of precedence, highest first:', 'azrcrv-cob' ) . '</p>

			<ol>
				<li>' . esc_html__( 'A parameter supplied directly on the shortcode.', 'azrcrv-cob' ) . '</li>
				<li>' . esc_html__( 'The value stored on the template referenced by the key parameter, if any.', 'azrcrv-cob' ) . '</li>
				<li>' . esc_html__( 'The site-wide default configured on the Settings tab.', 'azrcrv-cob' ) . '</li>
			</ol>

			<h4>' . esc_html__( 'Icons integration', 'azrcrv-cob' ) . '</h4>

			<p>' . sprintf( esc_html__( 'If the %1$s plugin from %2$s is active, enabling integration on the Settings tab adds an icon picker to the site-wide defaults and to each template, and to the icon shortcode parameter.', 'azrcrv-cob' ), 'Icons', 'azurecurve' ) . '</p>

			<h4>' . esc_html__( 'Nesting other shortcodes inside a call-out box', 'azrcrv-cob' ) . '</h4>

			<p>' . esc_html__( 'By default, content inside a call-out box is displayed as-is. If you need another shortcode processed inside a call-out box\'s content, turn on "Process shortcodes within content" on the Settings tab.', 'azrcrv-cob' ) . '</p>

		</td>

	</tr>

</table>';
