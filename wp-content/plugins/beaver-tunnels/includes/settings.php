<?php
/**
 * Beaver Tunnels Settings
 *
 * @package Beaver_Tunnels
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beaver Tunnels Settings class
 */
class Beaver_Tunnels_Settings {

	/**
	 * Class construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . plugin_basename( BEAVER_TUNNELS_PLUGIN_FILE ), array( $this, 'action_links' ) );

		if ( is_admin() && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], array( 'fl-builder-settings', 'fl-builder-multisite-settings' ), true ) ) {
			add_filter( 'fl_builder_admin_settings_nav_items', array( $this, 'fl_builder_admin_settings_nav_items' ) );
			add_action( 'fl_builder_admin_settings_render_forms', array( $this, 'fl_builder_admin_settings_render_forms' ) );
			add_action( 'fl_builder_admin_settings_save', array( $this, 'fl_builder_admin_settings_save' ) );
		}

		if ( Beaver_Tunnels()->is_network_active() ) {
			add_action( 'admin_init', array( $this, 'network_initialize' ) );
			add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ) );
			add_filter( 'network_admin_plugin_action_links_' . plugin_basename( BEAVER_TUNNELS_PLUGIN_FILE ), array( $this, 'action_links' ) );
		}
	}

	/**
	 * Add the Beaver Tunnels menu to the Page Builder's settings menu
	 *
	 * @since 2.0
	 *
	 * @param  array $settings Array of menu items.
	 *
	 * @return array
	 */
	public function fl_builder_admin_settings_nav_items( $settings ) {

		$settings['ft-bt'] = array(
			'title' 	=> Beaver_Tunnels_White_Label::get_branding(),
			'show'		=> true,
			'priority'	=> 2320984,
		);

		return $settings;

	}

	/**
	 * Settings form
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function fl_builder_admin_settings_render_forms() {
		include BEAVER_TUNNELS_PLUGIN_DIR . 'includes/settings/settings-beaver-tunnels.php';
	}

	/**
	 * Save the settings
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function fl_builder_admin_settings_save() {

		if ( ! isset( $_POST['fl-ft-bt-nonce'] ) || ! wp_verify_nonce( $_POST['fl-ft-bt-nonce'], 'ft-bt' ) ) {
			return;
		}

		$license_key = '';
		if ( isset( $_POST['ft-bt-license-key'] ) ) {
			$license_key = $_POST['ft-bt-license-key'];
		}

		$hide_hook_guide = '';
		if ( isset( $_POST['ft-bt-hide-hook-guide'] ) ) {
			$hide_hook_guide = $_POST['ft-bt-hide-hook-guide'];
		}

		$disable_template_override = '';
		if ( isset( $_POST['ft-bt-disable-template-override'] ) ) {
			$disable_template_override = $_POST['ft-bt-disable-template-override'];
		}

		$remove_data = '';
		if ( isset( $_POST['ft-bt-remove-date'] ) ) {
			$remove_data = $_POST['ft-bt-remove-date'];
		}

		$ft_bt = array(
			'license_key'				=> sanitize_text_field( $license_key ),
			'hide_hook_guide'			=> ( 'on' === $hide_hook_guide ) ? '1' : '',
			'disable_template_override'	=> ( 'on' === $disable_template_override ) ? '1' : '',
			'remove_data'				=> ( 'on' === $remove_data ) ? '1' : '',
		);

		if ( isset( $_POST['ft-bt-license-key'] ) && '' === $ft_bt['license_key'] ) {
			delete_option( 'beaver_tunnels_license_data' );
		}

		update_option( 'beaver_tunnels', $ft_bt, false );

	}

	/**
	 * Place actions links on the plugins screen
	 *
	 * @since 1.0
	 *
	 * @param  array $links Array of links.
	 *
	 * @return [type]        [description]
	 */
	public function action_links( $links = array() ) {

		if ( Beaver_Tunnels()->is_network_active() ) {
			$license_data = get_site_option( 'beaver_tunnels_license_data' );
		} else {
			$license_data = get_option( 'beaver_tunnels_license_data' );
		}

		$action_links = array();

		if ( ! is_object( $license_data ) || ! isset( $license_data->license ) || '' === $license_data->license ) {
			if ( Beaver_Tunnels()->is_network_active() ) {
				if ( is_network_admin() ) {
					$action_links[] = '<a href="' . network_admin_url( 'settings.php?page=beaver-tunnels' ) . '">' . __( 'Enter License Key', 'beaver-tunnels' ) . '</a>';
				}
			} else {
				$action_links[] = '<a href="' . admin_url( 'options-general.php?page=fl-builder-settings#ft-bt' ) . '">' . __( 'Enter License Key', 'beaver-tunnels' ) . '</a>';

			}
		} else {
			if ( Beaver_Tunnels()->is_network_active() && is_network_admin() ) {
				$action_links[] = '<a href="' . network_admin_url( 'settings.php?page=beaver-tunnels' ) . '">' . __( 'Settings', 'beaver-tunnels' ) . '</a>';
			} else {
				$action_links[] = '<a href="' . admin_url( 'options-general.php?page=fl-builder-settings#ft-bt' ) . '">' . __( 'Settings', 'beaver-tunnels' ) . '</a>';
			}
		}

		return array_merge( $action_links, $links );
	}

	/**
	 * Network Admin Menu
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function network_admin_menu() {

		$branding = Beaver_Tunnels_White_Label::get_branding();

		add_submenu_page(
			'settings.php',
			$branding,
			$branding,
			'manage_network_options',
			'beaver-tunnels',
			array( $this, 'network_display' )
		);

	}

	/**
	 * Display The Network Settings
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function network_display() {

		if ( isset( $_POST['beaver_tunnels'] ) ) {

			if ( ! isset( $_POST['beaver_tunnels-nonce'] ) || ! wp_verify_nonce( $_POST['beaver_tunnels-nonce'], 'beaver_tunnels-nonce' )  ) {
				wp_die( 'That is not allowed.' );
			}

			$beaver_tunnels = $_POST['beaver_tunnels'];

			// use array map function to sanitize option values.
			$beaver_tunnels = array_map( function( $input ) {
				if ( isset( $input['license_key'] ) && 0 === strlen( trim( $input['license_key'] ) ) ) {
					delete_site_option( 'beaver_tunnels_license_data' );
				}
				return $input;
			}, $beaver_tunnels );

			// save option values.
			update_site_option( 'beaver_tunnels', $beaver_tunnels );

			// just assume it all went according to plan.
			echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

		}

		$branding = Beaver_Tunnels_White_Label::get_branding();
	    ?>
	    <div class="wrap">
			<h1><?php echo esc_html( $branding ); ?></h1>
			<form method="post" action="settings.php?page=beaver-tunnels">
		    	<table class="form-table">
					<?php
					settings_fields( 'beaver_tunnels_network' );
					do_settings_sections( 'beaver_tunnels_network' );
					submit_button();
					?>
				</table>
			</form>
	    </div>
	    <?php
	}

	/**
	 * Initialize the Network Settings
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function network_initialize() {

		$branding = Beaver_Tunnels_White_Label::get_branding();

		$beaver_tunnels = get_site_option( 'beaver_tunnels' );
		// If the option does not exist, then add it.
		if ( false === $beaver_tunnels ) {
			add_site_option( 'beaver_tunnels', array() );
		}

		// First, we register a section. This is necessary since all future options must belong to one.
		add_settings_section(
			'beaver_tunnels_license_section',
			__( 'License Key', 'beaver-tunnels' ),
			array( $this, 'license_section_callback' ),
			'beaver_tunnels_network'
		);

		add_settings_field(
			'license_key',
			'<strong>' . __( 'License Key', 'beaver-tunnels' ) . '</strong>',
			array( $this, 'license_key_callback' ),
			'beaver_tunnels_network',
			'beaver_tunnels_license_section',
			array(
				'field_id'  => 'license_key',
				'page_id'   => 'beaver_tunnels',
				'size'      => 'regular',
			)
		);

		add_settings_section(
			'beaver_tunnels_options_section',
			__( 'Options', 'beaver-tunnels' ),
			array( $this, 'options_section_callback' ),
			'beaver_tunnels_network'
		);

		add_settings_field(
			'remove_data',
			'<strong>' . __( 'Remove Data on Uninstall?', 'beaver-tunnels' ) . '</strong>',
			array( $this, 'checkbox_callback' ),
			'beaver_tunnels_network',
			'beaver_tunnels_options_section',
			array(
				'field_id'  => 'remove_data',
				'page_id'   => 'beaver_tunnels',
				'label'     => sprintf( '%1s %2s %3s', __( 'Would you like', 'beaver-tunnels' ), $branding, __( 'to completely remove all of its data when the plugin is deleted?', 'beaver-tunnels' ) ),
			)
		);

		// Finally, we register the fields with WordPress.
		register_setting(
			'beaver_tunnels_network',			// The group name of the settings being registered.
			'beaver_tunnels',					// The name of the set of options being registered.
			array( $this, 'sanitize_callback' )	// The name of the function responsible for validating the fields.
		);

	}

	/**
	 * Render the license section
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function license_section_callback() {
		$branding = Beaver_Tunnels_White_Label::get_branding();
		$default = __( 'Beaver Tunnels', 'beaver-tunnels' );
		if ( $branding !== $default ) {
			return;
		}
		echo '<p>' . sprintf( '<a href="https://beavertunnels.com/account/" target="_blank">%1s</a> %2s', esc_html( __( 'Log in to your account', 'beaver-tunnels' ) ), esc_html( __( 'to lookup your license.', 'beaver-tunnels' ) ) ) . '</p>';
	}

	/**
	 * Render the options section
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function options_section_callback() {
		echo sprintf( '<p>%1s %2s.</p>', esc_html( __( 'Here you will find various settings for', 'beaver-tunnels' ) ), esc_html( Beaver_Tunnels_White_Label::get_branding() ) );
	}

	/**
	 * Sanitize the settings
	 *
	 * @since 1.0
	 *
	 * @param  array $input Settings.
	 *
	 * @return array        Sanitized settings
	 */
	public function sanitize_callback( $input ) {
		// Define all of the variables that we'll be using.
		$output = array();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {

				if ( 'license_key' === $key && 0 === strlen( trim( $value ) ) ) {
					delete_option( 'beaver_tunnels_license_data' );
				}

				// Strip all HTML and PHP tags and properly handle quoted strings.
				$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );

			}
		}

		// Return the array.
		return $output;

	}

	/**
	 * License key field
	 *
	 * @since 1.0
	 *
	 * @param  array $args Arguments.
	 *
	 * @return void
	 */
	public function license_key_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id'		=> null,
			'page_id'		=> null,
			'label'      	=> null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		if ( Beaver_Tunnels()->is_network_active() ) {
			$options = get_site_option( $args['page_id'] );
			$license_data = get_site_option( 'beaver_tunnels_license_data' );
		} else {
			$options = get_option( $args['page_id'] );
			$license_data = get_option( 'beaver_tunnels_license_data' );
		}

		// Start the output buffer.
		ob_start();
		?>
		<?php wp_nonce_field( 'beaver_tunnels-nonce', 'beaver_tunnels-nonce' ); ?>
		<input type="password" id="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo ( isset( $options[ $args['field_id'] ] ) ? esc_attr( $options[ $args['field_id'] ] ) : '' ); ?>" class="regular-text" />
		<?php if ( is_object( $license_data ) && isset( $license_data->license ) && 'valid' === $license_data->license ) : ?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( 'beaver_tunnels_license_deactivate' ); ?>" value="<?php echo esc_attr( __( 'Deactivate License', 'beaver-tunnels' ) ); ?>">
		<?php endif; ?>
		<?php if ( '' !== $args['label'] ) : ?>
			<p class="description"><?php echo esc_html( $args['label'] ); ?></p>
		<?php endif; ?>
		<?php if ( true === false && is_object( $license_data ) && isset( $license_data->license ) && isset( $license_data->expires ) && isset( $license_data->customer_email ) ) : ?>
		<br /><br />
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th style="padding: 8px 10px;"><?php esc_html_e( 'License', 'beaver-tunnels' ); ?></th>
					<th style="padding: 8px 10px;"><?php esc_html_e( 'Expires', 'beaver-tunnels' ); ?></th>
					<th style="padding: 8px 10px;"><?php esc_html_e( 'Customer Email', 'beaver-tunnels' ); ?></th>
				</tr>
				<tr>
					<td><?php echo esc_html( ucwords( $license_data->license ) ); ?></td>
					<td><?php echo esc_html( ucwords( $license_data->expires ) ); ?></td>
					<td><?php echo esc_html( $license_data->customer_email ); ?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<?php
		endif;
		// Print the output.
		echo ob_get_clean();

	} // license_key_callback()

	/**
	 * Checkbox field
	 *
	 * @since 1.0
	 *
	 * @param  array $args Arguments.
	 *
	 * @return void
	 */
	public function checkbox_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id'		=> null,
			'page_id'		=> null,
			'value'			=> '1',
			'label'      	=> null,
			'before'        => null,
			'after'         => null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		if ( Beaver_Tunnels()->is_network_active() ) {
			$options = get_site_option( $args['page_id'] );
		} else {
			$options = get_option( $args['page_id'] );
		}

		// Start the output buffer.
		ob_start();
		?>
		<?php echo $args['before']; ?>
		<input type="checkbox" id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo esc_attr( $args['value'] ); ?>" <?php isset( $options[ $args['field_id'] ] ) ? checked( $options[ $args['field_id'] ] ) : '' ?>/>
		<?php if ( '' !== $args['label'] ) : ?>
			<label for="<?php echo esc_attr( $args['field_id'] ); ?>" class="description"><?php echo esc_html( $args['label'] ); ?></label>
		<?php endif; ?>
		<?php echo $args['after']; ?>

		<?php
		// Print the output.
		echo ob_get_clean();

	} // input_callback()

	/**
	 * Text
	 *
	 * @package    Beaver_Tunnels
	 * @since      1.0.0
	 *
	 * @param	array $args	Arguments to pass to the function. (See below).
	 *
	 * @return	void
	 */
	public function text_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'header'	=> 'h2',
			'title'		=> null,
			'content'	=> null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		ob_start();
		// Check that the title and header_type are not blank.
		if ( ! is_null( $args['title'] ) ) {
			echo '<' . esc_html( $args['header'] ) . '>' . esc_html( $args['title'] ) . '</' . esc_html( $args['header'] ) . '>';
	    }

	    // Check that the content is not blank.
		if ( ! is_null( $args['content'] ) ) {
			echo $args['content'];
	    }

		// Print the output.
	    echo ob_get_clean();

	} // text_callback()

}
new Beaver_Tunnels_Settings();
