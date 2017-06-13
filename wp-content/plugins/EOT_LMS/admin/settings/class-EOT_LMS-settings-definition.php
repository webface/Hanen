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
 * The Settings definition of the plugin.
 *
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/includes
 * @author     Your Name <email@example.com>
 */
class EOT_LMS_Settings_Definition {

	// @TODO: change EOT_LMS
	public static $EOT_LMS = 'EOT_LMS';

	/**
	 * Sanitize EOT LMS.
	 *
	 * Lowercase alphanumeric characters and underscores are allowed.
	 * Uppercase characters will be converted to lowercase.
	 * Dashes characters will be converted to underscores.
	 *
	 * @access    private
	 * @return    string            Sanitized snake cased EOT LMS
	 */
	private static function get_snake_cased_EOT_LMS() {

		return str_replace( '-', '_', sanitize_key( self::$EOT_LMS ) );

	}

	/**
	 * [apply_tab_slug_filters description]
	 *
	 * @param  array $default_settings [description]
	 *
	 * @return array                   [description]
	 */
	static private function apply_tab_slug_filters( $default_settings ) {

		$extended_settings[] = array();
		$extended_tabs       = self::get_tabs();

		foreach ( $extended_tabs as $tab_slug => $tab_desc ) {

			$options = isset( $default_settings[ $tab_slug ] ) ? $default_settings[ $tab_slug ] : array();

			$extended_settings[ $tab_slug ] = apply_filters( self::get_snake_cased_EOT_LMS() . '_settings_' . $tab_slug, $options );
		}

		return $extended_settings;
	}

	/**
	 * [get_default_tab_slug description]
	 * @return [type] [description]
	 */
	static public function get_default_tab_slug() {

		return key( self::get_tabs() );
	}

	/**
	 * Retrieve settings tabs
	 *
	 * @since    1.0.0
	 * @return    array    $tabs    Settings tabs
	 */
	static public function get_tabs() {

		$tabs                = array();
		$tabs['default_tab'] = __( 'Default Tab', self::$EOT_LMS );
		$tabs['second_tab']  = __( 'Second Tab', self::$EOT_LMS );
		
		return apply_filters( self::get_snake_cased_EOT_LMS() . '_settings_tabs', $tabs );
	}

	/**
	 * 'Whitelisted' EOT_LMS settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 *
	 *
	 * @since    1.0.0
	 * @return    mixed    $value    Value saved / $default if key if not exist
	 */
	static public function get_settings() {

		$settings[] = array();

		$settings = array(
			'default_tab' => array(
				'default_tab_settings'       => array(
					'name' => '<strong>' . __( 'Header', self::$EOT_LMS ) . '</strong>',
					'type' => 'header'
				),
				'checkbox'                   => array(
					'name' => __( 'Checkbox', self::$EOT_LMS ),
					'desc' => __( 'Checkbox', self::$EOT_LMS ),
					'type' => 'checkbox'
				),
				'multicheck'                 => array(
					'name'    => __( 'Multicheck', self::$EOT_LMS ),
					'desc'    => __( 'Multicheck with 3 options', self::$EOT_LMS ),
					'options' => array(
						'wp-human'   => __( "Apple", self::$EOT_LMS ),
						'tang-rufus' => __( "Google", self::$EOT_LMS ),
						'Filter'     => __( 'You can apply filters on this option!', self::$EOT_LMS )
					),
					'type'    => 'multicheck'
				),
				'radio'                      => array(
					'name'    => __( 'Radio', self::$EOT_LMS ),
					'desc'    => __( 'Radio with 3 options', self::$EOT_LMS ),
					'options' => array(
						'wp-human'   => __( "Apple", self::$EOT_LMS ),
						'tang-rufus' => __( "Google", self::$EOT_LMS ),
						'Filter'     => __( 'You can apply filters on this option!', self::$EOT_LMS )
					),
					'type'    => 'radio'
				),
				'text'                       => array(
					'name' => __( 'Text', self::$EOT_LMS ),
					'desc' => __( 'Text', self::$EOT_LMS ),
					'type' => 'text'
				),
				'text_with_std'              => array(
					'name' => __( 'Text with std', self::$EOT_LMS ),
					'desc' => __( 'Text with std', self::$EOT_LMS ),
					'std'  => __( 'std will be saved!', self::$EOT_LMS ),
					'type' => 'text'
				),
				'email'                      => array(
					'name' => __( 'Email', self::$EOT_LMS ),
					'desc' => __( 'Email', self::$EOT_LMS ),
					'type' => 'email'
				),
				'url'                        => array(
					'name' => __( 'URL', self::$EOT_LMS ),
					'desc' => __( 'By default, only http & https are allowed', self::$EOT_LMS ),
					'type' => 'url'
				),
				'password'                   => array(
					'name' => __( 'Password', self::$EOT_LMS ),
					'desc' => __( 'Password', self::$EOT_LMS ),
					'type' => 'password'
				),
				'number'                     => array(
					'name' => __( 'Number', self::$EOT_LMS ),
					'desc' => __( 'Number', self::$EOT_LMS ),
					'type' => 'number'
				),
				'number_with_attributes'     => array(
					'name' => __( 'Number', self::$EOT_LMS ),
					'desc' => __( 'Max: 1000, Min: 20, Step: 30', self::$EOT_LMS ),
					'max'  => 1000,
					'min'  => 20,
					'step' => 30,
					'type' => 'number'
				),
				'textarea'                   => array(
					'name' => __( 'Textarea', self::$EOT_LMS ),
					'desc' => __( 'Textarea', self::$EOT_LMS ),
					'type' => 'textarea'
				),
				'textarea_with_std'          => array(
					'name' => __( 'Textarea with std', self::$EOT_LMS ),
					'desc' => __( 'Textarea with std', self::$EOT_LMS ),
					'std'  => __( 'std will be saved!', self::$EOT_LMS ),
					'type' => 'textarea'
				),
				'select'                     => array(
					'name'    => __( 'Select', self::$EOT_LMS ),
					'desc'    => __( 'Select with 3 options', self::$EOT_LMS ),
					'options' => array(
						'wp-human'   => __( "Apple", self::$EOT_LMS ),
						'tang-rufus' => __( "Google", self::$EOT_LMS ),
						'Filter'     => __( 'You can apply filters on this option!', self::$EOT_LMS )
					),
					'type'    => 'select'
				),
				'rich_editor'                => array(
					'name' => __( 'Rich Editor', self::$EOT_LMS ),
					'desc' => __( 'Rich Editor save as HTML markups', self::$EOT_LMS ),
					'type' => 'rich_editor'
				),
			),
			'second_tab'  => array(
				'template_one_field'                   => array(
					'name' => __( 'Template One Text Field', self::$EOT_LMS ),
					'desc' => __( 'This will display on the Template One page Template.', self::$EOT_LMS ),
					'type' => 'text'
				),
				'template_two_field'                   => array(
					'name' => __( 'Template Two Text Field', self::$EOT_LMS ),
					'desc' => __( 'This will display on the Template Two page Template.', self::$EOT_LMS ),
					'type' => 'text'
				)
			)
		);

		return self::apply_tab_slug_filters( $settings );
	}
}
