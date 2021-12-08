<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Call-out Boxes
 * Description: Place configurable call-out box in posts, pages or other post types.
 * Version: 1.5.1
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/call-out-boxes/
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
add_action('admin_init', 'azrcrv_create_plugin_menu_cob');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
add_action('init', 'azrcrv_cob_create_custom_post_type');
add_action('admin_menu', 'azrcrv_cob_create_admin_menu');
add_action('admin_post_azrcrv_cob_save_options', 'azrcrv_cob_save_options');
add_action('network_admin_menu', 'azrcrv_cob_create_network_admin_menu');
add_action('network_admin_edit_azrcrv_cob_save_network_options', 'azrcrv_cob_save_network_options');
add_action('plugins_loaded', 'azrcrv_cob_load_languages');
add_action('add_meta_boxes', 'azrcrv_cob_add_meta_boxes');
add_action( 'save_post', 'azrcrv_cob_save_settings_metabox', 1, 2 );

// add filters
add_filter('plugin_action_links', 'azrcrv_cob_add_plugin_action_link', 10, 2);
add_filter('the_posts', 'azrcrv_cob_check_for_shortcode', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_cob_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_cob_custom_image_url');

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
							'call-out-box',
							'cob',
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
 * Custom plugin image path.
 *
 * @since 1.2.0
 *
 */
function azrcrv_cob_custom_image_path($path){
    if (strpos($path, 'azrcrv-call-out-boxes') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.2.0
 *
 */
function azrcrv_cob_custom_image_url($url){
    if (strpos($url, 'azrcrv-call-out-boxes') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Create custom snippet post type.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_create_custom_post_type(){
	register_post_type('call-out-box',
		array(
				'labels' => array(
									'name' => esc_html__('Templates', 'call-out-boxes'),
									'singular_name' => esc_html__('Template', 'call-out-boxes'),
									'menu_name' => esc_html__( 'Call-out Boxes', 'call-out-boxes' ),
									'name_admin_bar' => esc_html__( 'Call-out Box Template', 'call-out-boxes' ),
									'all_items' => esc_html__('All Templates', 'call-out-boxes'),
									'add_new' => esc_html__('Add New Template', 'call-out-boxes'),
									'add_new_item' => esc_html__('Add New Call-out Box Template', 'call-out-boxes'),
									'edit' => esc_html__('Edit Template', 'call-out-boxes'),
									'edit_item' => esc_html__('Edit Call-out Box Template', 'call-out-boxes'),
									'new_item' => esc_html__('New Call-out Box Template', 'call-out-boxes'),
									'view' => esc_html__('View Template', 'call-out-boxes'),
									'view_item' => esc_html__('View Call-out Box Template', 'call-out-boxes'),
									'search_items' => esc_html__('Search Call-out Box Templates', 'call-out-boxes'),
									'not_found' => esc_html__('No Call-out Box Templates found', 'call-out-boxes'),
									'not_found_in_trash' => esc_html__('No Call-out Box Templates found in Trash', 'call-out-boxes'),
									'parent' => esc_html__('Parent Call-out Box', 'call-out-boxes')
								),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'menu_position' => 50,
			'supports' => array('title'),
			'taxonomies' => array(''),
			'menu_icon' => 'dashicons-testimonial',
			'has_archive' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => false,
			'show_in_rest' => false,
		)
	);
}

/**
 * Add meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_add_meta_boxes(){
	
	// add key meta box
	add_meta_box(
		'azrcrv_cob_key_meta_box', // $id
		'Key', // $title
		'azrcrv_cob_show_key_meta_box', // $callback
		'call-out-box', // $screen
		'normal', // $context
		'default' // $priority
	);
	
	// add example meta box
	add_meta_box(
		'azrcrv_cob_example_meta_box', // $id
		'Example', // $title
		'azrcrv_cob_show_example_meta_box', // $callback
		'call-out-box', // $screen
		'normal', // $context
		'default' // $priority
	);
	
	// add settings meta box
	add_meta_box(
		'azrcrv_cob_settings_meta_box', // $id
		'Template Settings', // $title
		'azrcrv_cob_show_settings_meta_box', // $callback
		'call-out-box', // $screen
		'normal', // $context
		'default' // $priority
	);
}


/**
 * Show key meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_show_key_meta_box(){
	global $post;
	
	?>

	<p>
		<?php
			printf(esc_html__('Use the following key in the shortcode to use this template: %s' ,'call-out-boxes'), '<strong>'.$post->post_name.'</strong>');
		?>
	</p>

<?php

}


/**
 * Show example meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_show_example_meta_box(){
	global $post;
	
	$options = azrcrv_cob_get_option('azrcrv-cob');
	
	$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true ); // Get the saved values
	
	if (isset($azrcrv_cob['icon']) AND strlen($azrcrv_cob['icon']) > 0){ $icon = $azrcrv_cob['icon']; }else{ $icon = $options['icon']; }
	if (isset($azrcrv_cob['heading']) AND strlen($azrcrv_cob['heading']) > 0){ $heading = $azrcrv_cob['heading']; }else{ $heading = 'This is an example'; }
	if (isset($azrcrv_cob['width']) AND strlen($azrcrv_cob['width']) > 0){ $width = $azrcrv_cob['width']; }else{ $width = $options['width']; }
	if (isset($azrcrv_cob['margin']) AND strlen($azrcrv_cob['margin']) > 0){ $margin = $azrcrv_cob['margin']; }else{ $margin = $options['margin']; }
	if (isset($azrcrv_cob['padding']) AND strlen($azrcrv_cob['padding']) > 0){ $padding = $azrcrv_cob['padding']; }else{ $padding = $options['padding']; }
	if (isset($azrcrv_cob['border']) AND strlen($azrcrv_cob['border']) > 0){ $border = $azrcrv_cob['border']; }else{ $border = $options['border']; }
	if (isset($azrcrv_cob['border-radius']) AND strlen($azrcrv_cob['border-radius']) > 0){ $border_radius = $azrcrv_cob['border-radius']; }else{ $border_radius = $options['border-radius']; }
	if (isset($azrcrv_cob['color']) AND strlen($azrcrv_cob['color']) > 0){ $color = $azrcrv_cob['color']; }else{ $color = $options['color']; }
	if (isset($azrcrv_cob['background-color']) AND strlen($azrcrv_cob['background-color']) > 0){ $background_color = $azrcrv_cob['background-color']; }else{ $background_color = $options['background-color']; }
	
	?>

	<p>
		<?php
			echo azrcrv_cob_display_shortcode(
													array(
																'icon' => $icon,
																'heading' => $heading,
																'width' => $width,
																'margin' => $margin,
																'padding' => $padding,
																'border' => $border,
																'border-radius' => $border_radius,
																'color' => $color,
																'background-color' => $background_color,
															), esc_html__('The meta box will look approximately like this example; your sites CSS can cause some differences.', 'call-out-boxes'),
												);
		?>
	</p>

<?php

}


/**
 * Show settings meta box.
 *
 * @since 1.0.0
 *
 */
function azrcrv_cob_show_settings_meta_box(){
	// Variables
	global $post; // Get the current post data
	
	$options = azrcrv_cob_get_option('azrcrv-cob');
	
	$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true ); // Get the saved values
	
	?>

	<fieldset>
		
		<table class="form-table">
			
			<?php if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php') AND $options['icons-integration'] == 1){ ?>
				<tr>
					<th scope="row">
						<label for="timeline-signifier">
							<label for="icon"><?php esc_html_e('Icon', 'call-out-boxes'); ?></label>
						</label>
					</th>
					<td>
						<select name="icon">
						<?php
							if (isset($azrcrv_cob['icon']) AND strlen($azrcrv_cob['icon']) > 0){ $current_icon = $azrcrv_cob['icon']; }else{ $current_icon = $options['icon']; }
						?>
						<option value="" <?php if($current_icon == ''){ echo ' selected="selected"'; } ?>>&nbsp;</option>
						<?php						
						$icons = azrcrv_i_get_icons();
						
						foreach ($icons as $icon_id => $icon){
							echo '<option value="'.esc_html($icon_id).'" ';
							if($current_icon == esc_html($icon_id)){ echo ' selected="selected"'; }
							echo '>'.esc_html($icon_id).'</option>';
						}
						echo '</select>';
						if (strlen($current_icon) > 0){
							echo '&nbsp;'.azrcrv_i_icon(array(esc_html(stripslashes($current_icon))));
						}
						?>
					</td>
				</tr>
			<?php } ?>
			
			<tr>
				<th scope="row">
					<label for="heading"><?php esc_html_e('Heading', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['heading'])){ $heading = $azrcrv_cob['heading']; }else{ $heading = ''; } ?>
					<input type="text" name="heading" value="<?php echo esc_html(stripslashes($heading)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default heading which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>heading</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="width"><?php esc_html_e('Width', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['width'])){ $width = $azrcrv_cob['width']; }else{ $width = ''; } ?>
					<input type="text" name="width" value="<?php echo esc_html(stripslashes($width)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default width which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>width</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="margin"><?php esc_html_e('Margin', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['margin'])){ $margin = $azrcrv_cob['margin']; }else{ $margin = ''; } ?>
					<input type="text" name="margin" value="<?php echo esc_html(stripslashes($margin)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default margin which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>margin</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="padding"><?php esc_html_e('Padding', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['padding'])){ $padding = $azrcrv_cob['padding']; }else{ $padding = ''; } ?>
					<input type="text" name="padding" value="<?php echo esc_html(stripslashes($padding)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default padding which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>padding</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="border"><?php esc_html_e('Border', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['border'])){ $border = $azrcrv_cob['border']; }else{ $border = ''; } ?>
					<input type="text" name="border" value="<?php echo esc_html(stripslashes($border)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default border which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>border</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="border-radius"><?php esc_html_e('Border Radius', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['border-radius'])){ $border_radius = $azrcrv_cob['border-radius']; }else{ $border_radius = ''; } ?>
					<input type="text" name="border-radius" value="<?php echo esc_html(stripslashes($border_radius)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default border radius which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>border-radius</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="color"><?php esc_html_e('Color', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['color'])){ $color = $azrcrv_cob['color']; }else{ $color = ''; } ?>
					<input type="text" name="color" value="<?php echo esc_html(stripslashes($color)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default text color which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>text-color</strong>'); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="background-color"><?php esc_html_e('Background Color', 'call-out-boxes'); ?></label>
				</th>
				<td>
					<?php if (isset($azrcrv_cob['background-color'])){ $background_color = $azrcrv_cob['background-color']; }else{ $background_color = ''; } ?>
					<input type="text" name="background-color" value="<?php echo esc_html(stripslashes($background_color)); ?>" class="regular-text" />
					<p class="description"><?php printf(esc_html__('Default background color which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>background-color</strong>'); ?></p>
				</td>
			</tr>
			
		</table>
	</fieldset>

	<?php
	// Security field
	wp_nonce_field( 'azrcrv_cob_show_settings_meta_box_nonce', 'azrcrv_cob_show_settings_meta_box' );
}

/**
 * Save the metabox
 * @param  Number $post_id The post ID
 * @param  Array  $post    The post data
 */
function azrcrv_cob_save_settings_metabox( $post_id, $post ) {

	// Verify that our security field exists. If not, bail.
	if ( !isset( $_POST['azrcrv_cob_show_settings_meta_box'] ) ) return;

	// Verify data came from edit/dashboard screen
	if ( !wp_verify_nonce( $_POST['azrcrv_cob_show_settings_meta_box'], 'azrcrv_cob_show_settings_meta_box_nonce' ) ) {
		return $post->ID;
	}

	// Verify user has permission to edit post
	if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
	
	$options = azrcrv_cob_get_option('azrcrv-cob');
	
	if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php') AND $options['icons-integration'] == 1){
		$setting_name = 'icon';
		if (isset($_POST[$setting_name])){
			$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
		}
	}
	
	$setting_name = 'heading';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'width';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'margin';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'padding';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'border';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'border-radius';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'color';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	$setting_name = 'background-color';
	if (isset($_POST[$setting_name])){
		$azrcrv_cob[$setting_name] = sanitize_text_field($_POST[$setting_name]);
	}
	
	// Save our submissions to the database
	update_post_meta( $post->ID, '_azrcrv_cob', $azrcrv_cob );

}

/**
 * Get options including defaults.
 *
 * @since 1.2.0
 *
 */
function azrcrv_cob_get_option($option_name){
 
	$defaults = array(
						'icons-integration' => 0,
						'icon' => 'lightbulb',
						'heading-open' => '<h4 class="azrcrv-cob">',
						'heading-close' => '</h4>',
						'color' => '#000',
						'background-color' => '#99CBFF',
						'width' => '75%',
						'margin' => 'auto',
						'padding' => '5px 10px',
						'border' => '1px solid #007FFF',
						'border-radius' => '15px',
						'do-shortcode' => 0,
					);

	$options = get_option($option_name, $defaults);

	$options = wp_parse_args($options, $defaults);

	return $options;

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
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-cob').'"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'get-github-file').'</a>';
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
	
	// add settings to snippets submenu
	add_submenu_page(
						'edit.php?post_type=call-out-box'
						,esc_html__('Call-out Boxes Settings', 'call-out-boxes')
						,esc_html__('Settings', 'call-out-boxes')
						,'manage_options'
						,'azrcrv-cob'
						,'azrcrv_cob_display_options'
					);
	
	// add settings to azurecurve menu
	add_submenu_page("azrcrv-plugin-menu"
						,esc_html__('Call-out Boxes Settings', 'call-out-boxes')
						,esc_html__('Call-out Boxes', 'call-out-boxes')
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
	$options = azrcrv_cob_get_option('azrcrv-cob');
	?>
	<div id="azrcrv-cob-general" class="wrap">
	
		<fieldset>
		
			<h1>
				<?php
					echo '<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
					esc_html_e(get_admin_page_title());
				?>
			</h1>
			
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
						<th scope="row">
							<p>
								<?php printf(__('Call-out Boxes are added to posts or pages using the %s shortcode. ', 'call-out-boxes'), '<strong>call-out-boxes</strong>'); ?>
							</p>
						</th>
					</tr>
				</table>
				
				<table class="form-table">
					<tr scope="row">
						<th>
							<?php esc_html_e('Example call-out box', 'call-out-boxes'); ?>
						</th>
					</tr>
					
					<tr>
						<td scope="row">
							<?php
								echo azrcrv_cob_display_shortcode(array('heading' => 'This is an example call-out box'), 'The call-out box will look like this example.');
							?>
						</td>
					</tr>
				
				</table>
				
				<table class="form-table">
							
					<tr>
						<th scope="row">
							<label for="icons-integration">
								<?php printf(__('Integrate with %s from %s', 'call-out-boxes'), '<a href="https://development.azurecurve.co.uk/classicpress-plugins/icons/">Icons</a>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve</a>'); ?>
							</label>
						</th>
						<td>
							<?php
								if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php')){ ?>
									<label for="icons-integration"><input name="icons-integration" type="checkbox" id="icons-integration" value="1" <?php checked('1', $options['icons-integration']); ?> /><?php printf(esc_html__('Enable integration with %s from %s?', 'call-out-boxes'), 'Icons', 'azurecurve'); ?></label>
								<?php }else{
									printf(__('%s from %s is not installed/activated.', 'call-out-boxes'), 'Icons', 'azurecurve');
								}
								?>
						</td>
					</tr>
							
					<?php if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php') AND $options['icons-integration'] == 1){ ?>
						<tr>
							<th scope="row">
								<label for="timeline-signifier">
									<label for="icon"><?php esc_html_e('Icon', 'call-out-boxes'); ?></label>
								</label>
							</th>
							<td>
								<select name="icon">
								<option value="" <?php if($options['icon'] == ''){ echo ' selected="selected"'; } ?>>&nbsp;</option>
								<?php						
								$icons = azrcrv_i_get_icons();
								
								foreach ($icons as $icon_id => $icon){
									echo '<option value="'.esc_html($icon_id).'" ';
									if($options['icon'] == esc_html($icon_id)){ echo ' selected="selected"'; }
									echo '>'.esc_html($icon_id).'</option>';
								}
								echo '</select>';
								if (strlen($options['icon']) > 0){
									echo '&nbsp;'.azrcrv_i_icon(array(esc_html(stripslashes($options['icon']))));
								}
								?>
							</td>
						</tr>
					<?php } ?>
					
					<tr>
						<th scope="row">
							<label for="heading-open"><?php esc_html_e('Heading Open', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="heading-open" value="<?php echo esc_html(stripslashes($options['heading-open'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html__('Opening tag for heading shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="heading-close"><?php esc_html_e('Heading Close', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="heading-close" value="<?php echo esc_html(stripslashes($options['heading-close'])); ?>" class="regular-text" />
							<p class="description"><?php esc_html__('Closing tag for heading shortcode parameter.', 'call-out-boxes'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="width"><?php esc_html_e('Width', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="width" value="<?php echo esc_html(stripslashes($options['width'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default width which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>width</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="margin"><?php esc_html_e('Margin', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="margin" value="<?php echo esc_html(stripslashes($options['margin'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default margin which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>margin</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="padding"><?php esc_html_e('Padding', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="padding" value="<?php echo esc_html(stripslashes($options['padding'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default padding which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>padding</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="border"><?php esc_html_e('Border', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="border" value="<?php echo esc_html(stripslashes($options['border'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default border which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>border</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="border-radius"><?php esc_html_e('Border Radius', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="border-radius" value="<?php echo esc_html(stripslashes($options['border-radius'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default border radius which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>border-radius</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="color"><?php esc_html_e('Color', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="color" value="<?php echo esc_html(stripslashes($options['color'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default text color which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>text-color</strong>'); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="background-color"><?php esc_html_e('Background Color', 'call-out-boxes'); ?></label>
						</th>
						<td>
							<input type="text" name="background-color" value="<?php echo esc_html(stripslashes($options['background-color'])); ?>" class="regular-text" />
							<p class="description"><?php printf(__('Default background color which can be overridden using the %s shortcode parameter.', 'call-out-boxes'), '<strong>background-color</strong>'); ?></p>
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
		
		$option_name = 'icons-integration';
		if (isset($_POST[$option_name])){
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
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
	
	$options = azrcrv_cob_get_option('azrcrv-cob');
	
	// extract attributes from shortcode
	$args = shortcode_atts(array(
		'key' => '',
		'icon' => '',
		'heading' => '',
		'width' => '',
		'margin' => '',
		'padding' => '',
		'border' => '',
		'border-radius' => '',
		'color' => '',
		'background-color' => '',
	), $atts);
	$key = $args['key'];
	$icon = $args['icon'];
	$heading = $args['heading'];
	$width = $args['width'];
	$margin = $args['margin'];
	$padding = $args['padding'];
	$border = $args['border'];
	$border_radius = $args['border-radius'];
	$color = $args['color'];
	$background_color = $args['background-color'];
	
	if (isset($key)){
		if ($post = get_page_by_path($key, OBJECT, 'call-out-box')){
			$azrcrv_cob = get_post_meta( $post->ID, '_azrcrv_cob', true ); // Get the saved values
			
			if (strlen($icon) == 0){
				if (isset($azrcrv_cob['icon']) AND strlen($azrcrv_cob['icon']) > 0){ $icon = $azrcrv_cob['icon']; }
			}
			if (strlen($heading) == 0){
				if (isset($azrcrv_cob['heading']) AND strlen($azrcrv_cob['heading']) > 0){ $heading = $azrcrv_cob['heading']; }
			}
			if (strlen($width) == 0){
				if (isset($azrcrv_cob['width']) AND strlen($azrcrv_cob['width']) > 0){ $width = $azrcrv_cob['width']; }
			}
			if (strlen($margin) == 0){
				if (isset($azrcrv_cob['margin']) AND strlen($azrcrv_cob['margin']) > 0){ $margin = $azrcrv_cob['margin']; }
			}
			if (strlen($padding) == 0){
				if (isset($azrcrv_cob['padding']) AND strlen($azrcrv_cob['padding']) > 0){ $padding = $azrcrv_cob['padding']; }
			}
			if (strlen($border) == 0){
				if (isset($azrcrv_cob['border']) AND strlen($azrcrv_cob['border']) > 0){ $border = $azrcrv_cob['border']; }
			}
			if (strlen($border_radius) == 0){
				if (isset($azrcrv_cob['border-radius']) AND strlen($azrcrv_cob['border-radius']) > 0){ $border_radius = $azrcrv_cob['border-radius']; }
			}
			if (strlen($color) == 0){
				if (isset($azrcrv_cob['color']) AND strlen($azrcrv_cob['color']) > 0){ $color = $azrcrv_cob['color']; }
			}
			if (strlen($background_color) == 0){
				if (isset($azrcrv_cob['background-color']) AND strlen($azrcrv_cob['background-color']) > 0){ $background_color = $azrcrv_cob['background-color']; }
			}
		}
	}
	if (strlen($icon) == 0){ $icon = $options['icon']; }
	if (strlen($heading) == 0){ $heading = ''; }
	if (strlen($width) == 0){ $width = $options['width']; }
	if (strlen($margin) == 0){ $margin = $options['margin']; }
	if (strlen($padding) == 0){ $padding = $options['padding']; }
	if (strlen($border) == 0){ $border = $options['border']; }
	if (strlen($border_radius) == 0){ $border_radius = $options['border-radius']; }
	if (strlen($color) == 0){ $color = $options['color']; }
	if (strlen($background_color) == 0){ $background_color = $options['background-color']; }
	
	$output = '';
	if (strlen($content) > 0){
		if (strlen($width) > 0){ $output_width = "width: $width; "; }
		if (strlen($margin) > 0){ $output_margin = "margin: $margin; "; }
		if (strlen($padding) > 0){ $output_padding = "padding: $padding; "; }
		if (strlen($border) > 0){ $output_border = "border: $border; " ; }
		if (strlen($border_radius) > 0){ $output_border_radius = "border-radius: $border_radius; "; }
		if (strlen($color) > 0){ $output_color = "color: $color; "; }
		if (strlen($background_color) > 0){ $output_background_color = "background-color: $background_color; "; }
		
		$output_content = '';
		if (strlen($heading) > 0){
			$output_content .= stripslashes($options['heading-open']);
		}
		if (strlen($icon) > 0){
			if (azrcrv_cob_is_plugin_active('azrcrv-icons/azrcrv-icons.php')){
				$output_content .= azrcrv_i_icon(array(esc_html($icon))).'&nbsp;';
			}
		}
		if (strlen($heading) > 0){
			$output_content .= esc_html($heading).stripslashes($options['heading-close']);
		}
		$output_content .= '<p>'.$content.'</p>';
		
		$output = "<div class='azrcrv-cob' style='".esc_html__($output_width).esc_html__($output_margin).esc_html__($output_padding).esc_html__($output_border).esc_html__($output_color).esc_html__($output_background_color).esc_html__($output_border_radius)."'>";
		if ($options['do-shortcode'] == 1){
			$output .= do_shortcode($output_content);
		}else{		
			$output .= $output_content;
		}
		$output .= "</div>";
	}
	return $output;
	
}
