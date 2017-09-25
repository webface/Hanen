<?php

$modules = array(
    'modules/pp-highlight-box/pp-highlight-box.php',
    'modules/pp-testimonials/pp-testimonials.php',
    'modules/pp-info-banner/pp-info-banner.php',
    'modules/pp-infolist/pp-infolist.php',
    'modules/pp-flipbox/pp-flipbox.php',
    'modules/pp-infobox/pp-infobox.php',
    'modules/pp-modal-box/pp-modal-box.php',
    'modules/pp-column-separator/pp-column-separator.php',
    'modules/pp-dotnav/pp-dotnav.php',
    'modules/pp-line-separator/pp-line-separator.php',
    'modules/pp-heading/pp-heading.php',
    'modules/pp-logos-grid/pp-logos-grid.php',
    'modules/pp-announcement-bar/pp-announcement-bar.php',
    'modules/pp-hover-cards/pp-hover-cards.php',
    'modules/pp-timeline/pp-timeline.php',
    'modules/pp-notifications/pp-notifications.php',
    'modules/pp-pullquote/pp-pullquote.php',
    'modules/pp-content-grid/pp-content-grid.php',
    'modules/pp-smart-button/pp-smart-button.php',
    'modules/pp-image-panels/pp-image-panels.php',
    'modules/pp-dual-button/pp-dual-button.php',
    'modules/pp-image/pp-image.php',
    'modules/pp-advanced-tabs/pp-advanced-tabs.php',
    'modules/pp-contact-form/pp-contact-form.php',
    'modules/pp-spacer/pp-spacer.php',
    'modules/pp-team/pp-team.php',
    'modules/pp-advanced-accordion/pp-advanced-accordion.php',
    'modules/pp-subscribe-form/pp-subscribe-form.php',
    'modules/pp-social-icons/pp-social-icons.php',
    'modules/pp-iconlist/pp-iconlist.php',
    'modules/pp-content-tiles/pp-content-tiles.php',
    'modules/pp-restaurant-menu/pp-restaurant-menu.php',
    'modules/pp-fancy-heading/pp-fancy-heading.php',
	'modules/pp-pricing-table/pp-pricing-table.php',
	'modules/pp-business-hours/pp-business-hours.php',
	'modules/pp-hover-cards-2/pp-hover-cards-2.php',
);

/* Form Modules */
if ( class_exists( 'GFForms' ) ) {
    $modules[] = 'modules/pp-gravity-form/pp-gravity-form.php';
}
if ( class_exists( 'WPCF7_ContactForm' ) ) {
    $modules[] = 'modules/pp-contact-form-7/pp-contact-form-7.php';
}
if ( function_exists( 'wpforms' ) ) {
    $modules[] = 'modules/pp-wpforms/pp-wpforms.php';
}
if ( class_exists( 'Caldera_Forms_Forms' ) ) {
    $modules[] = 'modules/pp-caldera-form/pp-caldera-form.php';
}
if ( class_exists( 'FrmForm' ) ) {
    $modules[] = 'modules/pp-formidable-form/pp-formidable-form.php';
}
if ( function_exists( 'Ninja_Forms' ) ) {
    $modules[] = 'modules/pp-ninja-form/pp-ninja-form.php';
}

foreach ( $modules as $module ) {
    if ( is_child_theme() && file_exists( get_stylesheet_directory() . '/bb-powerpack/' . $module ) ) {
        require_once get_stylesheet_directory() . '/bb-powerpack/' . $module;
    }
    elseif ( file_exists( get_template_directory() . '/bb-powerpack/' . $module ) ) {
        require_once get_template_directory() . '/bb-powerpack/' . $module;
    }
    else {
        require_once BB_POWERPACK_DIR . $module;
    }
}
