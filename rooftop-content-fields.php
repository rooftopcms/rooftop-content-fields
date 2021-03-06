<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rooftopcms.com
 * @since             1.0.0
 * @package           Rooftop_Content_Fields
 *
 * @wordpress-plugin
 * Plugin Name:       Rooftop Content Fields
 * Plugin URI:        http://github.com/rooftopcms
 * Description:       rooftop-content-fields presents additional content in an API response. This includes menus, menu items and taxonomy terms
 * Version:           1.2.1
 * Author:            RooftopCMS
 * Author URI:        https://www.rooftopcms.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       rooftop-content-fields
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rooftop-content-fields-activator.php
 */
function activate_rooftop_content_fields() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-content-fields-activator.php';
	Rooftop_Content_Fields_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rooftop-content-fields-deactivator.php
 */
function deactivate_rooftop_content_fields() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-content-fields-deactivator.php';
	Rooftop_Content_Fields_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rooftop_content_fields' );
register_deactivation_hook( __FILE__, 'deactivate_rooftop_content_fields' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-content-fields.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rooftop_content_fields() {

	$plugin = new Rooftop_Content_Fields();
	$plugin->run();

}
run_rooftop_content_fields();
