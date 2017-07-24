<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.expertonlinetraining.com
 * @since             1.0.0
 * @package           Eot_quiz
 *
 * @wordpress-plugin
 * Plugin Name:       EOT_QUIZ
 * Plugin URI:        http://www.expertonlinetraining.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Tommy Adeniyi
 * Author URI:        http://www.expertonlinetraining.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eot_quiz
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eot_quiz-activator.php
 */
function activate_eot_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eot_quiz-activator.php';
	Eot_quiz_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eot_quiz-deactivator.php
 */
function deactivate_eot_quiz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eot_quiz-deactivator.php';
	Eot_quiz_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_eot_quiz' );
register_deactivation_hook( __FILE__, 'deactivate_eot_quiz' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-eot_quiz.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_eot_quiz() {

	$plugin = new Eot_quiz();
	$plugin->run();

}
run_eot_quiz();
