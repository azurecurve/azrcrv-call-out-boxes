=== Call-out Boxes ===

Description:	Place configurable call-out box in posts, pages or other post types.
Version:		1.5.1
Tags:			call-out,information
Author:			azurecurve
Author URI:		https://development.azurecurve.co.uk/
Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/call-out-boxes/
Download link:	https://github.com/azurecurve/azrcrv-call-out-boxes/releases/download/v1.5.1/azrcrv-call-out-boxes.zip
Donate link:	https://development.azurecurve.co.uk/support-development/
Requires PHP:	5.6
Requires:		1.0.0
Tested:			4.9.99
Text Domain:	call-out-boxes
Domain Path:	/languages
License: 		GPLv2 or later
License URI: 	http://www.gnu.org/licenses/gpl-2.0.html

Place configurable call-out box in posts, pages or other post types.

== Description ==

# Description

Place configurable call-out box in posts, pages or other post types.

Integrates with the [Icons](https://development.azurecurve.co.uk/classicpress-plugins/code/) plugin from azurecurve for call-out box icons.

The following parameters can be used with the **call-out-boxes** shortcode:
* icon
* heading
* width
* margin
* padding
* border
* border-radius
* color
* background-color

This plugin is multisite compatible; each site will need settings to be configured in the admin dashboard.

== Installation ==

# Installation Instructions

 * Download the latest release of the plugin from [GitHub](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/latest/).
 * Upload the entire zip file using the Plugins upload function in your ClassicPress admin panel.
 * Activate the plugin.
 * Configure relevant settings via the configuration page in the admin control panel (azurecurve menu).

== Frequently Asked Questions ==

# Frequently Asked Questions

### Can I translate this plugin?
Yes, the .pot file is in the plugins languages folder; if you do translate this plugin, please sent the .po and .mo files to translations@azurecurve.co.uk for inclusion in the next version (full credit will be given).

### Is this plugin compatible with both WordPress and ClassicPress?
This plugin is developed for ClassicPress, but will likely work on WordPress.

== Changelog ==

# Changelog

### [Version 1.5.1](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.5.1)
 * Update azurecurve menu.
 * Update readme files.

### [Version 1.5.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.5.0)
 * Update translations to escape strings.
 * Update azurecurve menu and logo.
 
### [Version 1.4.2](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.4.2)
 * Fix bug with settings page not loading correctly due to incorrect function call.
 * Update azurecurve menu.
 
### [Version 1.4.1](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.4.1)
 * Fix bug with get options call.
 
### [Version 1.4.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.4.0)
 * Add call-out box templates using **call-out box** custom post type.
 * Add display of example call-out box to Settings page.
 * Add [Icons](https://development.azurecurve.co.uk/clasicpress-plugins/icons/) integration flag to settings.
 * Update [Icons](https://development.azurecurve.co.uk/clasicpress-plugins/icons/) plugin integration to allow selection of icon via drop down list.
 * Update azurecurve plugin menu.
 
### [Version 1.3.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.3.0)
 * Update Update Manager class to v2.0.0 (previous update was incorrect).
 * Fix bug with load of plugin icon and banner.

### [Version 1.2.1](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.2.1)
 * Fix bug with load of plugin icon and banner.
 
### [Version 1.2.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.2.0)
 * Fix plugin action link to use admin_url() function.
 * Rewrite option handling so defaults not stored in database on plugin initialisation.
 * Add plugin icon and banner.
 * Update azurecurve plugin menu.
 * Amend to only load css when shortcode on page.
 
### [Version 1.1.6](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.6)
 * Fix bug with setting of default options.
 * Fix bug with plugin menu.
 * Update plugin menu css.
 
### [Version 1.1.5](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.5)
 * Rewrite default option creation function to resolve several bugs.
 * Upgrade azurecurve plugin to store available plugins in options.

### [Version 1.1.4](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.4)
 * Fix version number bug.

### [Version 1.1.3](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.3)
 * Update Update Manager class to v2.0.0.
 * Update azurecurve menu icon with compressed image.

### [Version 1.1.2](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.2)
 * Fix bug with incorrect paragraph closing on content of call-out box.
 * Add azurecurve icon to plugins action link.

### [Version 1.1.1](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.1)
 * Fix bug with incorrect language load text domain.

### [Version 1.1.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.1.0)
 * Add integration with Update Manager for automatic updates.
 * Fix issue with display of azurecurve menu.
 * Change settings page heading.
 * Add load_plugin_textdomain to handle translations.

### [Version 1.0.1](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.0.1)
 * Update azurecurve menu for easier maintenance.
 * Move require of azurecurve menu below security check.
 * Localization fixes.

### [Version 1.0.0](https://github.com/azurecurve/azrcrv-call-out-boxes/releases/tag/v1.0.0)
 * Initial release.

== Other Notes ==

# About azurecurve

**azurecurve** was one of the first plugin developers to start developing for Classicpress; all plugins are available from [azurecurve Development](https://development.azurecurve.co.uk/) and are integrated with the [Update Manager plugin](https://directory.classicpress.net/plugins/update-manager) for fully integrated, no hassle, updates.

Some of the other plugins available from **azurecurve** are:
 * Loop Injection - [details](https://development.azurecurve.co.uk/classicpress-plugins/loop-injection/) / [download](https://github.com/azurecurve/azrcrv-loop-injection/releases/latest/)
 * Nearby - [details](https://development.azurecurve.co.uk/classicpress-plugins/nearby/) / [download](https://github.com/azurecurve/azrcrv-nearby/releases/latest/)
 * Redirect - [details](https://development.azurecurve.co.uk/classicpress-plugins/redirect/) / [download](https://github.com/azurecurve/azrcrv-redirect/releases/latest/)
 * Series Index - [details](https://development.azurecurve.co.uk/classicpress-plugins/series-index/) / [download](https://github.com/azurecurve/azrcrv-series-index/releases/latest/)
 * SMTP - [details](https://development.azurecurve.co.uk/classicpress-plugins/smtp/) / [download](https://github.com/azurecurve/azrcrv-smtp/releases/latest/)
 * Timelines - [details](https://development.azurecurve.co.uk/classicpress-plugins/timelines/) / [download](https://github.com/azurecurve/azrcrv-timelines/releases/latest/)
 * Toggle Show/Hide - [details](https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/) / [download](https://github.com/azurecurve/azrcrv-toggle-showhide/releases/latest/)
 * Update Admin Menu - [details](https://development.azurecurve.co.uk/classicpress-plugins/update-admin-menu/) / [download](https://github.com/azurecurve/azrcrv-update-admin-menu/releases/latest/)
 * URL Shortener - [details](https://development.azurecurve.co.uk/classicpress-plugins/url-shortener/) / [download](https://github.com/azurecurve/azrcrv-url-shortener/releases/latest/)
 * Widget Announcements - [details](https://development.azurecurve.co.uk/classicpress-plugins/widget-announcements/) / [download](https://github.com/azurecurve/azrcrv-widget-announcements/releases/latest/)
