<?php
/**
 * Plugin Name: Dual Callout
 * Plugin URI: https://www.beaverlodgehq.com
 * Description: A double button callout in Beaver Builder.
 * Version: 1.0.0
 * Author: Beaverlodge HQ
 * Author URI: https://www.beaverlodgehq.com
 */

define( 'SW_DUALCALLOUT_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SW_DUALCALLOUT_MODULE_URL', plugins_url( '/', __FILE__ ) );

function sw_dualcallout_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        require_once 'includes/bb-sw-dualcallout-module.php';
    }
}
add_action( 'init', 'sw_dualcallout_module' );