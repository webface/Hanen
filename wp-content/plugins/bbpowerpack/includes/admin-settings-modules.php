<?php
/**
 * PowerPack admin settings modules tab.
 *
 * @since 1.0.0
 * @package bb-powerpack
 */

?>

<p style="max-width: 800px;">
    <?php
    $settings_page = is_network_admin() ? 'settings.php' : 'options-general.php';
    $builder_label = FLBuilderModel::get_branding();
    $builder_label = ( ! $builder_label || '' == $builder_label ) ? 'Builder' : $builder_label;
    ?>
    You can manage <?php echo BB_POWERPACK_CAT; ?> from <a href="<?php echo admin_url( $settings_page . '?page=fl-builder-settings#modules' ); ?>"><?php echo $builder_label; ?> settings</a>. We have contributed an enhancement to the BB core and after BB 1.9 comes out, you will be able to manage <?php echo BB_POWERPACK_CAT; ?> separately from BB Modules. Right here, on this page.<br /><br />
    Thanks for your cooperation.<br /><br />
</p>
