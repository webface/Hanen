<?php
/**
 *
 *
 * @link       https://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/includes
 */
/**
 * The get_option functionality of the plugin.
 *
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/includes
 * @author     Your Name <email@example.com>
 */


class EOT_LMS_Option {

	/**
	 * Get an option
	 *
	 * Looks to see if the specified setting exists, returns default if not.
	 *
	 * @since 	1.0.0
	 * @return 	mixed 	$value 	Value saved / $default if key if not exist
	 */
	static public function get_option( $key, $default = false ) {

		if ( empty( $key ) ) {
			return $default;
		}

		// @TODO: change EOT_LMS_settings
		$plugin_options = get_option( 'EOT_LMS_settings', array() );

		$value = isset( $plugin_options[ $key ] ) ? $plugin_options[ $key ] : $default;

		return $value;
	}
}
