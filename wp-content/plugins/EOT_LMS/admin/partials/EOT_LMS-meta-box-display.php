<?php
/**
 * Provide a meta box view for the settings page
 *
 * @link       https://www.expertonlinetraining.com
 * @since      1.0.0
 *
 * @package    EOT_LMS
 * @subpackage EOT_LMS/admin/partials
 */

/**
 * Meta Box
 *
 * Renders a single meta box.
 *
 * @since       1.0.0
*/
?>

<form action="options.php" method="POST">
	<?php settings_fields( $this->snake_cased_EOT_LMS . '_settings' ); ?>
	<?php do_settings_sections( $this->snake_cased_EOT_LMS . '_settings_' . $active_tab ); ?>
	<?php submit_button(); ?>
</form>
<br class="clear" />
