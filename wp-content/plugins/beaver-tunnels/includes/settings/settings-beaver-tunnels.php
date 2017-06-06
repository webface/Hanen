<?php
/**
 * Beaver Tunnels Settings
 *
 * @package Beaver_Tunnels
 */

$ft_bt_branding = Beaver_Tunnels_White_Label::get_branding();

$ft_bt_page_builder_branding = 'Page Builder';
if ( class_exists( 'FLBuilderModel' ) ) {
	$ft_bt_page_builder_branding = FLBuilderModel::get_branding();
}

$ft_bt = get_option( 'beaver_tunnels' );
// If the option does not exist, then add it.
if ( false === $ft_bt ) {
	add_option( 'beaver_tunnels' );
}

$ft_bt_defaults = array(
	'license_key' 				=> '',
	'hide_hook_guide'			=> '',
	'disable_template_override'	=> '',
	'remove_data'				=> '',
);

$ft_bt = wp_parse_args( $ft_bt, $ft_bt_defaults );

if ( Beaver_Tunnels()->is_network_active() ) {
	$ft_bt_license_data = get_site_option( 'beaver_tunnels_license_data' );
} else {
	$ft_bt_license_data = get_option( 'beaver_tunnels_license_data' );
}
?>
<div id="fl-ft-bt-form" class="fl-settings-form">
	<form id="ft-bt-form" action="<?php FLBuilderAdminSettings::render_form_action( 'ft-bt' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<h3 class="fl-settings-form-header"><?php esc_html_e( 'Getting Started', 'beaver-tunnels' ); ?></h3>
			<p>
				<?php printf( '%1s %2s', esc_html( $ft_bt_branding ), esc_html( __( 'display conditions are available when editing a saved template, row, or module from the Builder menu.' ), 'beaver-tunnels' ) ); ?>
			</p>
			<p>
				<img src="<?php echo BEAVER_TUNNELS_PLUGIN_URL; ?>/assets/img/getting-started.jpg" style="max-width: 100%; display: inline-block;" />
			</p>
			<br />
			<?php if ( ! Beaver_Tunnels()->is_network_active() ) : ?>
				<h3 class="fl-settings-form-header"><?php esc_html_e( 'License Key', 'beaver-tunnels' ); ?></h3>
				<p>
					<?php esc_html_e( 'Enter your license key to enable remote updates and support.', 'beaver-tunnels' ); ?>
				</p>
				<p>
					<input type="password" name="ft-bt-license-key" value="<?php echo esc_attr( $ft_bt['license_key'] ); ?>" class="regular-text" />
				</p>
				<?php if ( is_object( $ft_bt_license_data ) && isset( $ft_bt_license_data->license ) && 'valid' === $ft_bt_license_data->license ) : ?>
					<p>
						<input type="submit" class="button-secondary" name="beaver_tunnels_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'beaver-tunnels' ); ?>">
					</p>
				<?php endif; ?>
				<br />
			<?php endif; ?>
			<h3 class="fl-settings-form-header"><?php esc_html_e( 'Options', 'beaver-tunnels' ); ?></h3>
			<h4><?php esc_html_e( 'Hook Guide', 'beaver-tunnels' ); ?></h4>
			<p>
				<?php esc_html_e( 'Hide the Hook Guide in the Admin bar menu?', 'beaver-tunnels' ); ?>
			</p>
			<p>
				<label>
					<input type="checkbox" name="ft-bt-hide-hook-guide" <?php checked( $ft_bt['hide_hook_guide'], '1', true ); ?>>
					<?php esc_html_e( 'Hide the Hook Guide', 'beaver-tunnels' ); ?>
				</label>
			</p>
			<h4><?php esc_html_e( 'Template Override', 'beaver-tunnels' ); ?></h4>
			<p>
				<?php printf( '%1s %2s %3s?', esc_html( __( 'Disable the minimal page template when viewing saved', 'beaver-tunnels' ) ), esc_html( $ft_bt_page_builder_branding ), esc_html( __( 'Templates', 'beaver-tunnels' ) ) ); ?>
			</p>
			<p>
				<label>
					<input type="checkbox" name="ft-bt-disable-template-override" <?php checked( $ft_bt['disable_template_override'], '1', true ); ?>>
					<?php esc_html_e( 'Disable Template Override', 'beaver-tunnels' ); ?>
				</label>
			</p>
			<?php if ( ! Beaver_Tunnels()->is_network_active() ) : ?>
			<h4><?php esc_html_e( 'Uninstall', 'beaver-tunnels' ); ?></h4>
			<p>
				<?php printf( '%1s <strong>%2s</strong> %3s', esc_html( __( 'Would you like', 'beaver-tunnels' ) ), esc_html( $ft_bt_branding ), esc_html( __( 'to completely remove all of its data when the plugin is deleted?', 'beaver-tunnels' ) ) ); ?>
			</p>
			<p>
				<label>
					<input type="checkbox" name="ft-bt-remove-data" <?php checked( $ft_bt['remove_data'], '1', true ); ?>>
					<?php esc_html_e( 'Remove Data', 'beaver-tunnels' ); ?>
				</label>
			</p>
			<?php endif; ?>
		</div>
		<p class="submit">
			<input type="submit" name="fl-save-ft-bt" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'ft-bt', 'fl-ft-bt-nonce' ); ?>
		</p>
	</form>
</div>
