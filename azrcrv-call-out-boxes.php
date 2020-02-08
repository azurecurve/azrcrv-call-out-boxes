<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Call-out Boxes
 * Description: Place configurable call-out box in posts, pages or other post types.
 * Version: 1.1.2
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/call-out-boxes
 * Text Domain: call-out-boxes
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname(__FILE__).'/pluginmenu/menu.php');
register_activation_hook(__FILE__, 'azrcrv_create_plugin_menu_cob');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
register_activation_hook(__FILE__, 'azrcrv_cob_set_default_options');

// add actions
add_action('admin_menu', 'azrcrv_cob_create_admin_menu');
add_action('admin_post_azrcrv_cob_save_options', 'azrcrv_cob_save_options');
add_action('network_admin_menu', 'azrcrv_cob_create_network_admin_menu');
add_action('network_admin_edit_azrcrv_cob_save_network_options', 'azrcrv_cob_save_network_options');
add_action('wp_enqueue_scripts', 'azrcrv_cob_load_css');
add_action('admin_enqueue_scripts', 'azrcrv_cob_load_css');
//add_action('the_posts', 'azrcrv_cob_check_for_shortcode');
add_action('plugins_loaded', 'azrcrv_cob_load_languages');

// add filters
add_filter('plugin_action_links', 'azrcrv_cob_add_plugin_action_link', 10, 2);

// add shortcodes
add_shortcode('call-out-box', 'azrcrv_cob_display_shortcode');
add_shortcode('cob', 'azrcrv_cob_display_shortcode');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('call-out-boxes', false, $plugin_rel_path);
}

/**
 * Check if shortcode on current page and then load css.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_check_for_shortcode($posts){
    if (empty($posts)){
        return $posts;
	}
	
	
	// array of shortcodes to search for
	$shortcodes = array(
						'call-out-box','cob'
						);
	
    // loop through posts
    $found = false;
    foreach ($posts as $post){
		// loop through shortcodes
		foreach ($shortcodes as $shortcode){
			// check the post content for the shortcode
			if (has_shortcode($post->post_content, $shortcode)){
				$found = true;
				// break loop as shortcode found in page content
				break 2;
			}
		}
	}
 
    if ($found){
		// as shortcode found call functions to load css
        azrcrv_cob_load_css();
    }
    return $posts;
}

/**
 * Load CSS.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_load_css(){
	wp_enqueue_style('azrcrv-cob', plugins_url('assets/css/style.css', __FILE__), '', '1.0.0');
}

/**
 * Set default options for plugin.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_set_default_options($networkwide){
	
	$option_name = 'azrcrv-cob';
	
	$new_options = array(
						'icon' => 'lightbulb',
						'heading-open' => '<h4>',
						'heading-close' => '</h4>',
						'color' => '#000',
						'background-color' => '#99CBFF',
						'width' => '75%',
						'margin' => 'auto',
						'padding' => '5px 10px',
						'border' => '1px solid #007FFF',
						'border-radius' => '15px',
			);
	
	// set defaults for multi-site
	if (function_exists('is_multisite') && is_multisite()){
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide){
			global $wpdb;

			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			$original_blog_id = get_current_blog_id();

			foreach ($blog_ids as $blog_id){
				switch_to_blog($blog_id);

				if (get_option($option_name) === false){
					add_option($option_name, $new_options);
				}
			}

			switch_to_blog($original_blog_id);
		}else{
			if (get_option($option_name) === false){
				add_option($option_name, $new_options);
			}
		}
		if (get_site_option($option_name) === false){
			add_option($option_name, $new_options);
		}
	}
	//set defaults for single site
	else{
		if (get_option($option_name) === false){
			add_option($option_name, $new_options);
		}
	}
}

/**
 * Add Call-out Box action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=azrcrv-cob"><img src="'.plugins_url('/pluginmenu/images/Favicon-16x16.png', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'get-github-file').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_create_admin_menu(){
	//global $admin_page_hooks;
	
	add_submenu_page("azrcrv-plugin-menu"
						,"Call-out Box Settings"
						,"Call-out Boxes"
						,'manage_options'
						,'azrcrv-cob'
						,'azrcrv_cob_display_options');
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'call-out-boxes'));
    }
	
	// Retrieve plugin configuration options from database
	$options = get_option('azrcrv-cob');
	?>
	<div id="azrcrv-cob-general" class="wrap">
	
		<fieldset>
		
			<h2><?php esc_html_e('Call-out Box Settings', 'call-out-boxes'); ?></h2>
			
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'call-out-boxes'); ?></strong></p>
				</div>
			<?php } ?>
			
			<form method="post" action="admin-post.php">
			
				<input type="hidden" name="action" value="azrcrv_cob_save_options" />
				<input name="page_options" type="hidden" value="icon,heading-open,heading-close,color,background-color,width,margin,padding,border,border-radius" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-cob-nonce', 'azrcrv-cob-nonce'); ?>
				<table class="form-table">
				
					<tr>
						<th scope="row" colspan="2">
							<p>
								<?php printf(esc_html__('Call-out Boxes are added to posts or pages using the %s shortcode. ', 'call-out-boxes'), '<strong>call-out-boxes</strong>'); ?>
							</p>
						</th>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="icon"><?php esc_html_e('Icon', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<?php
							if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php')){
								echo do_shortcode('[icon='.esc_html(stripslashes($options['icon'])).']&nbsp;');
							?>
								<input type="text" name="icon" value="<?php echo esc_html(stripslashes($options['icon'])); ?>" class="regular-text" />
								<p class="description">
									<?php esc_html_e('Default icon which can be overridden using a shortcode parameter', 'call-out-boxes'); ?>
								</p>
								<p class="description">
									<?php esc_html_e('Commonly used icons are:', 'call-out-boxes'); ?>
								</p>
								<ul style='list-style-type: none; padding-left: 10px; '>
									<li><?php echo do_shortcode("[icon=lightbulb] lightbulb"); ?></li>
									<li><?php echo do_shortcode("[icon=bug] bug"); ?></li>
									<li><?php echo do_shortcode("[icon=bullet_error] bullet_error"); ?></li>
									<li><?php echo do_shortcode("[icon= exclamation]  exclamation"); ?></li>
									<li><?php echo do_shortcode("[icon=note] note"); ?></li>
									<li><?php echo do_shortcode("[icon=page] page"); ?></li>
								</ul>
								<p class="description">
									<?php
									printf(esc_html__('See the full list of %s.', 'call-out-boxes'), ' <a href="http://test.azurecurve.co.uk/wp-admin/admin.php?page=azrcrv-i">'.esc_html__('available icons', 'call-out-boxes').'</a>');
									?>
								</p>
							<?php
							}else{
								printf(esc_html__('Install and activate the %s plugin to get access to 1,000 16x16 icons.', 'call-out-boxes'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/icons/">Icons</a>');
							}
							?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="heading-open"><?php esc_html_e('Heading Open', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="heading-open" value="<?php echo esc_html(stripslashes($options['heading-open'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Opening tag for heading shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="heading-close"><?php esc_html_e('Heading Close', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="heading-close" value="<?php echo esc_html(stripslashes($options['heading-close'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Closing tag for heading shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="width"><?php esc_html_e('Width', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="width" value="<?php echo esc_html(stripslashes($options['width'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default width which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="margin"><?php esc_html_e('Margin', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="margin" value="<?php echo esc_html(stripslashes($options['margin'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default margin which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="padding"><?php esc_html_e('Padding', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="padding" value="<?php echo esc_html(stripslashes($options['padding'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default padding which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="border"><?php esc_html_e('Border', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="border" value="<?php echo esc_html(stripslashes($options['border'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default border which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="border-radius"><?php esc_html_e('Border Radius', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="border-radius" value="<?php echo esc_html(stripslashes($options['border-radius'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default border radius which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="color"><?php esc_html_e('Color', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="color" value="<?php echo esc_html(stripslashes($options['color'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default text color which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="background-color"><?php esc_html_e('Background Color', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="background-color" value="<?php echo esc_html(stripslashes($options['background-color'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e('Default background color which can be overridden using a shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
				
					<tr>
						<th scope="row">&nbsp;</th>
						<td>
							<p>
								<?php esc_html_e('The following parameters can be supplied to the shortcode:', 'call-out-boxes'); ?>
								<ul style='list-style-type: none; padding-left: 10px; '>
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
					
				</table>
				
				<input type="submit" value="Save Changes" class="button-primary"/>
				
			</form>
			
		</fieldset>
		
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'call-out-boxes'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-cob-nonce', 'azrcrv-cob-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-cob');
		
		$option_name = 'icon';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$allowed = azrcrv_cob_get_allowed_tags();
		
		$option_name = 'heading-open';
		if (isset($_POST[$option_name])){
			$options[$option_name] = wp_kses(stripslashes($_POST[$option_name]), $allowed);
		}
		
		$option_name = 'heading-close';
		if (isset($_POST[$option_name])){
			$options[$option_name] = wp_kses(stripslashes($_POST[$option_name]), $allowed);
		}
		
		$option_name = 'width';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'margin';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'padding';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'border';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'border-radius';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'color';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		$option_name = 'background-color';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		// Store updated options array to database
		update_option('azrcrv-cob', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-cob&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 * Get allowed tags.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_get_allowed_tags() {
	
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
    $allowed_tags['div']['class'] = 1;
    $allowed_tags['div']['style'] = 1;
    $allowed_tags['span']['class'] = 1;
    $allowed_tags['span']['style'] = 1;
	
    return $allowed_tags;
}

/**
 * Check if function active (included due to standard function failing due to order of load).
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_is_plugin_active($plugin){
    return in_array($plugin, (array) get_option('active_plugins', array()));
}

/**
 * Output call-out box shortcode.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_display_shortcode($atts, $content = null){
	
	$options = get_option('azrcrv-cob');
	
	// extract attributes from shortcode
	$args = shortcode_atts(array(
		'icon' => $options['icon'],
		'heading' => '',
		'width' => $options['width'],
		'margin' => $options['margin'],
		'padding' => $options['padding'],
		'border' => $options['border'],
		'border-radius' => $options['border-radius'],
		'color' => $options['color'],
		'background-color' => $options['background-color'],
	), $atts);
	$icon = $args['icon'];
	$heading = $args['heading'];
	$width = $args['width'];
	$margin = $args['margin'];
	$padding = $args['padding'];
	$border = $args['border'];
	$borderradius = $args['border-radius'];
	$color = $args['color'];
	$backgroundcolor = $args['background-color'];
	
	$output = '';
	if (strlen($content) > 0){
		if (strlen($width) > 0){ $outputwidth = "width: $width; "; }
		if (strlen($margin) > 0){ $outputmargin = "margin: $margin; "; }
		if (strlen($padding) > 0){ $outputpadding = "padding: $padding; "; }
		if (strlen($border) > 0){ $outputborder = "border: $border; " ; }
		if (strlen($borderradius) > 0){ $outputborderradius = "border-radius: $borderradius; "; }
		if (strlen($color) > 0){ $outputcolor = "color: $color; "; }
		if (strlen($backgroundcolor) > 0){ $outputbackgroundcolor = "background-color: $backgroundcolor; "; }
		
		$outputcontent = '';
		if (strlen($heading) > 0){
			$outputcontent .= stripslashes($options['heading-open']);
		}
		if (strlen($icon) > 0){
			if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php')){
				$outputcontent .= do_shortcode('[icon='.esc_html($icon).']&nbsp;');
			}
		}
		if (strlen($heading) > 0){
			$outputcontent .= esc_html($heading).stripslashes($options['heading-close']);
		}
		$outputcontent .= '<p>'.$content.'</p>';
		
		$output = "<div class='azrcrv-cob' style='".esc_html__($outputwidth).esc_html__($outputmargin).esc_html__($outputpadding).esc_html__($outputborder).esc_html__($outputcolor).esc_html__($outputbackgroundcolor).esc_html__($outputborderradius)."'>";
		if (options['do-shortcode'] == 1){
			$output .= do_shortcode($outputcontent);
		}else{		
			$output .= $outputcontent;
		}
		$output .= "</div>";
	}
	return $output;
	
}



?>