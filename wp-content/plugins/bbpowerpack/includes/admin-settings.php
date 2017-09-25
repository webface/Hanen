<?php
/**
 * PowerPack admin settings page.
 *
 * @since 1.0.0
 * @package bb-powerpack
 */

?>

<?php

$license 	  = self::get_option( 'bb_powerpack_license_key' );
$status 	  = self::get_option( 'bb_powerpack_license_status' );
$current_tab  = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'general';

if ( is_multisite() && ! is_network_admin() ) {
    if ( $current_tab !== 'templates' ) {
        $current_tab = 'templates';
    }
}

?>

<div class="wrap">

    <h2>
        <?php
            $admin_label = self::get_option( 'ppwl_admin_label' );
            $admin_label = trim( $admin_label ) !== '' ? trim( $admin_label ) : esc_html__( 'PowerPack', 'bb-powerpack' );
            echo sprintf( esc_html__( '%s Settings', 'bb-powerpack' ), $admin_label );
        ?>
    </h2>

    <?php BB_PowerPack_Admin_Settings::render_update_message(); ?>

    <form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">

        <div class="icon32 icon32-pp-settings" id="icon-pp"><br /></div>

        <h2 class="nav-tab-wrapper pp-nav-tab-wrapper">

            <?php if ( is_network_admin() || ! is_multisite() ) { ?>
                <a href="<?php echo self::get_form_action( '&tab=general' ); ?>" class="nav-tab<?php echo ( $current_tab == 'general' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'General', 'bb-powerpack' ); ?></a>
                <?php if ( ! self::get_option( 'ppwl_hide_form' ) || self::get_option( 'ppwl_hide_form' ) == 0 ) { ?>
                    <a href="<?php echo self::get_form_action( '&tab=white-label' ); ?>" class="nav-tab<?php echo ( $current_tab == 'white-label' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'White Label', 'bb-powerpack' ); ?></a>
                <?php } ?>
                <?php if ( ! self::get_option( 'ppwl_hide_modules_tab' ) || self::get_option( 'ppwl_hide_modules_tab' ) == 0 ) { ?>
                    <a href="<?php echo self::get_form_action( '&tab=modules' ); ?>" class="nav-tab<?php echo ( $current_tab == 'modules' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Modules', 'bb-powerpack' ); ?></a>
                <?php } ?>
            <?php } ?>

            <!--<a href="<?php echo self::get_form_action( '&tab=row-templates' ); ?>" class="nav-tab<?php echo ( $current_tab == 'row-templates' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Row Templates', 'bb-powerpack' ); ?></a>-->
            <?php if ( ! self::get_option( 'ppwl_hide_templates_tab' ) || self::get_option( 'ppwl_hide_templates_tab' ) == 0 ) { ?>
                <a href="<?php echo self::get_form_action( '&tab=templates' ); ?>" class="nav-tab<?php echo ( $current_tab == 'templates' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Templates', 'bb-powerpack' ); ?> <span class="pp-count title-count"></span></a>
            <?php } ?>
            <?php if ( is_network_admin() || ! is_multisite() ) { ?>
                <?php if ( ! self::get_option( 'ppwl_hide_extensions_tab' ) || self::get_option( 'ppwl_hide_extensions_tab' ) == 0 ) { ?>
                    <a href="<?php echo self::get_form_action( '&tab=extensions' ); ?>" class="nav-tab<?php echo ( $current_tab == 'extensions' ? ' nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Extensions', 'bb-powerpack' ); ?></a>
                <?php } ?>
            <?php } ?>

        </h2>

        <?php

        // General settings.
        if ( ! isset($_GET['tab']) || 'general' == $current_tab ) {
            include BB_POWERPACK_DIR . 'includes/admin-settings-license.php';
        }

        // White Label settings.
        if ( 'white-label' == $current_tab ) {
            include BB_POWERPACK_DIR . 'includes/admin-settings-wl.php';
        }

        // Modules settings.
        if ( 'modules' == $current_tab && ( ! self::get_option( 'ppwl_hide_modules_tab' ) || self::get_option( 'ppwl_hide_modules_tab' ) == 0 ) ) {
            include BB_POWERPACK_DIR . 'includes/admin-settings-modules.php';
        }

        // Row templates settings.
        // if ( ( 'row-templates' == $current_tab || ( ! is_network_admin() && is_multisite() ) ) && 'templates' != $current_tab ) {
        //     include BB_POWERPACK_DIR . 'includes/admin-settings-row-templates.php';
        // }

        // Page templates settings.
        if ( 'templates' == $current_tab && ( ! self::get_option( 'ppwl_hide_templates_tab' ) || self::get_option( 'ppwl_hide_templates_tab' ) == 0 ) ) {
            include BB_POWERPACK_DIR . 'includes/admin-settings-templates.php';
        }

        // Extensions settings.
        if ( 'extensions' == $current_tab && ( ! self::get_option( 'ppwl_hide_extensions_tab' ) || self::get_option( 'ppwl_hide_extensions_tab' ) == 0 ) ) {
            include BB_POWERPACK_DIR . 'includes/admin-settings-extensions.php';
        }

        ?>

    </form>

    <?php if ( ! self::get_option( 'ppwl_hide_support_msg' ) || self::get_option( 'ppwl_hide_support_msg' ) == 0 ) { ?>
    <hr />

    <h2><?php esc_html_e('Support', 'bb-powerpack'); ?></h2>
    <p>
        <?php
            $support_link = self::get_option( 'ppwl_support_link' );
            $support_link = !empty( $support_link ) ? $support_link : 'https://wpbeaveraddons.com/contact/';
            esc_html_e('For submitting any support queries, feedback, bug reports or feature requests, please visit', 'bb-powerpack'); ?> <a href="<?php echo $support_link; ?>" target="_blank"><?php esc_html_e('this link', 'bb-powerpack'); ?></a>
    </p>
    <?php } ?>

</div>
