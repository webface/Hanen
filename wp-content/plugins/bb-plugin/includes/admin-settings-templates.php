<div id="fl-templates-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e('Template Settings', 'fl-builder'); ?></h3>

	<form id="templates-form" action="<?php FLBuilderAdminSettings::render_form_action( 'templates' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php if(get_option('_fl_builder_enabled_templates')) echo 'checked="checked"'; ?> />
			<?php _e('Override network settings?', 'fl-builder'); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">

			<p><?php _e('Enable or disable templates using the options below.', 'fl-builder'); ?></p>
			<?php

			$enabled_templates = FLBuilderModel::get_enabled_templates();

			?>
			<select name="fl-template-settings">
				<option value="enabled" <?php selected( $enabled_templates, 'enabled' ); ?>><?php _e( 'Enable All Templates', 'fl-builder' ); ?></option>
				<option value="core" <?php selected( $enabled_templates, 'core' ); ?>><?php _e( 'Enable Core Templates Only', 'fl-builder' ); ?></option>
				<option value="user" <?php selected( $enabled_templates, 'user' ); ?>><?php _e( 'Enable User Templates Only', 'fl-builder' ); ?></option>
				<option value="disabled" <?php selected( $enabled_templates, 'disabled' ); ?>><?php _e( 'Disable All Templates', 'fl-builder' ); ?></option>
			</select>
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Template Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field('templates', 'fl-templates-nonce'); ?>
		</p>
	</form>
</div>