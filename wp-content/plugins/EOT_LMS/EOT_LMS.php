<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.expertonlinetraining.com
 * @since             1.0.0
 * @package           EOT_LMS
 *
 * @wordpress-plugin
 * Plugin Name:       EOT_LMS
 * Plugin URI:        https://www.expertonlinetraining.com
 * Description:       The EOT LMS plugin
 * Version:           1.0.0
 * Author:            Expert Online Training
 * Author URI:        https://www.expertonlinetraining.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       EOT_LMS
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constant
 */

define( 'PW_SAMPLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-EOT_LMS-activator.php
 */
function activate_EOT_LMS() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-EOT_LMS-activator.php';
	EOT_LMS_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-EOT_LMS-deactivator.php
 */
function deactivate_EOT_LMS() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-EOT_LMS-deactivator.php';
	EOT_LMS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_EOT_LMS' );
register_deactivation_hook( __FILE__, 'deactivate_EOT_LMS' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-EOT_LMS.php';

//cron job for processing users that were uploaded from a spreadsheet and were not processed
if ( ! wp_next_scheduled( 'processUsersHook' ) ) {
  wp_schedule_event( time(), 'hourly', 'processUsersHook' );
}
add_action( 'processUsersHook', 'processUsersCron' );

//cron job for processing emails that were scheduled to send but not processed
if ( ! wp_next_scheduled( 'processEmailsHook' ) ) {
  wp_schedule_event( time(), 'hourly', 'processEmailsHook' );
}
add_action( 'processEmailsHook', 'processEmailsCron' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_EOT_LMS() {

	$plugin = new EOT_LMS();
	$plugin->run();

}
run_EOT_LMS();
