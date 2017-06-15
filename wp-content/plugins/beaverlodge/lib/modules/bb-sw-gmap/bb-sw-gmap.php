<?php
/**
 * Plugin Name: SW GMap
 * Plugin URI: http://www.beaverlodgehq.com
 * Description: A indepth Google Map for the Beaver Builder plugin.
 * Version: 1.0.2
 * Author: Jon Mather
 * Author URI: http://www.simplewebsiteinaday.com.au
 */

define( 'SW_GMAP_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SW_GMAP_URL', plugins_url( '/', __FILE__ ) );

function sw_gmap_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        require_once 'includes/bb-sw-gmap-module.php';
    }
}
add_action( 'init', 'sw_gmap_module' );