<?php
/**
 * Plugin Name: SW Columnizer
 * Plugin URI: http://www.beaverlodgehq.com
 * Description: A module to split text into multiple columns Beaver Builder.
 * Version: 1.0.0
 * Author: Jon Mather
 * Author URI: http://www.simplewebsiteinaday.com.au
 */

define( 'SW_COLUMNIZER_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SW_COLUMNIZER_MODULE_URL', plugins_url( '/', __FILE__ ) );

function sw_columnizer_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        require_once 'includes/bb-sw-columnizer-module.php';
    }
}
add_action( 'init', 'sw_columnizer_module' );