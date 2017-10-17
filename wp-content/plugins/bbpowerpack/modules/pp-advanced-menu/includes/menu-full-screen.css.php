/* Overlay */
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay {
	background-color: <?php echo ( $settings->responsive_overlay_bg_color ) ? pp_hex2rgba('#'.$settings->responsive_overlay_bg_color, ($settings->responsive_overlay_bg_opacity / 100)) : 'rgba(0,0,0,0.8)'; ?>;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay > ul.menu {
	padding-top: <?php if ( $settings->responsive_overlay_padding['top'] >= 0 ) { echo $settings->responsive_overlay_padding['top'] . 'px'; } ?>;
	padding-bottom: <?php if ( $settings->responsive_overlay_padding['bottom'] >= 0 ) { echo $settings->responsive_overlay_padding['bottom'] . 'px'; } ?>;
	padding-left: <?php if ( $settings->responsive_overlay_padding['left'] >= 0 ) { echo $settings->responsive_overlay_padding['left'] . 'px'; } ?>;
	padding-right: <?php if ( $settings->responsive_overlay_padding['right'] >= 0 ) { echo $settings->responsive_overlay_padding['right'] . 'px'; } ?>;
}

/* Sub Menu */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu {
	box-shadow: none;
	border: none;
}

/* Links */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu > li,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li {
	display: block;
}

<?php if( $settings->responsive_alignment == 'center' ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-arrows .pp-has-submenu-container > a > span,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .pp-has-submenu-container > a > span  {
	padding-right: 0;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .sub-menu > li > .pp-has-submenu-container > a:focus {
	background-color: transparent;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a {
	<?php if( $settings->responsive_link_bg_color ) { ?>background-color: #<?php echo $settings->responsive_link_bg_color; ?>;<?php } ?>
	<?php if( $settings->responsive_link_color ) { ?>color: #<?php echo $settings->responsive_link_color; ?>;<?php } ?>
	-webkit-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
	padding: 0;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li a span.menu-item-text,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a span.menu-item-text {
	display: inline-block;
	border-style: solid;
	border-top-width: <?php echo ( $settings->responsive_link_border_width['top'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['top'] : '0'; ?>px;
	border-bottom-width: <?php echo ( $settings->responsive_link_border_width['bottom'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['bottom'] : '0'; ?>px;
	border-left-width: <?php echo ( $settings->responsive_link_border_width['left'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['left'] : '0'; ?>px;
	border-right-width: <?php echo ( $settings->responsive_link_border_width['right'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['right'] : '0'; ?>px;
	border-color: <?php echo ($settings->responsive_link_border_color) ? '#' . $settings->responsive_link_border_color : 'transparent'; ?>;
	padding-top: <?php if ( $settings->responsive_link_padding['top'] >= 0 ) { echo $settings->responsive_link_padding['top'] . 'px'; } ?>;
	padding-bottom: <?php if ( $settings->responsive_link_padding['bottom'] >= 0 ) { echo $settings->responsive_link_padding['bottom'] . 'px'; } ?>;
	padding-left: <?php if ( $settings->responsive_link_padding['left'] >= 0 ) { echo $settings->responsive_link_padding['left'] . 'px'; } ?>;
	padding-right: <?php if ( $settings->responsive_link_padding['right'] >= 0 ) { echo $settings->responsive_link_padding['right'] . 'px'; } ?>;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .menu li .pp-has-submenu-container a:focus {
	<?php if( $settings->responsive_link_bg_hover_color ) { ?>background-color: #<?php echo $settings->responsive_link_bg_hover_color; ?>;<?php } ?>
	<?php if( $settings->responsive_link_hover_color ) { ?>color: #<?php echo $settings->responsive_link_hover_color; ?>;<?php } ?>
}

<?php if( '' != $settings->animation_speed ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-fade,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.menu-open .pp-overlay-fade,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-corner,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.menu-open .pp-overlay-corner,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-slide-down,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.menu-open .pp-overlay-slide-down,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-scale,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.menu-open .pp-overlay-scale,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-door,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.menu-open .pp-overlay-door,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-door > ul,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-overlay-door .pp-menu-close-btn {
		transition-duration: <?php echo ( $settings->animation_speed / 1000 ) . 's'; ?>;
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn {
	<?php if( $settings->close_icon_size ) { ?>
		width: <?php echo  $settings->close_icon_size ; ?>px;
		height: <?php echo  $settings->close_icon_size ; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:before,
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-menu-overlay .pp-menu-close-btn:after {
	<?php if( $settings->close_icon_size ) { ?>
		height: <?php echo  $settings->close_icon_size ; ?>px;
	<?php } ?>
	<?php if( $settings->close_icon_color ) { ?>
		background-color: #<?php echo $settings->close_icon_color; ?>;
	<?php } ?>
}

<?php if( !empty( $settings->responsive_link_color ) ) { ?>
		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-arrows .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-none .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-arrows .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-none .sub-menu .pp-menu-toggle:before {
			border-color: #<?php echo $settings->responsive_link_color; ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu .pp-menu-toggle:after {
			border-color: #<?php echo $settings->responsive_link_color; ?>;
		}
		<?php } ?>
<?php } ?>

<?php if( !empty( $settings->responsive_link_hover_color ) ) { ?>

		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-arrows li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-none li:hover .pp-menu-toggle:before
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-none .sub-menu li:hover .pp-menu-toggle:before {
			border-color: #<?php echo $settings->responsive_link_hover_color; ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus li:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after {
			border-color: #<?php echo $settings->responsive_link_hover_color; ?>;
		}
		<?php } ?>
<?php } ?>

<?php if ( $module->get_media_breakpoint() ) { ?>
		@media ( max-width: <?php echo $module->get_media_breakpoint() ?>px ) {
	<?php } ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.pp-menu-default {
			display: none;
		}
	<?php if ( $module->get_media_breakpoint() ) { ?>
		}
<?php } ?>

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-advanced-menu.full-screen .pp-menu-overlay ul.menu {
		width: 80%;
	}
}
