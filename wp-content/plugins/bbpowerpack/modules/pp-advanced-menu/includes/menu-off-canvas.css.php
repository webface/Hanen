/* Container */
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu {
	background-color: <?php echo pp_hex2rgba( '#'.$settings->responsive_overlay_bg_color, $settings->responsive_overlay_bg_opacity / 100 ); ?>;
	<?php
	    if ( $settings->enable_shadow == 'yes' ) {
	        $menu_shadow_h = empty($settings->menu_shadow['horizontal']) ? '0' : $settings->menu_shadow['horizontal'];
	        $menu_shadow_v = empty($settings->menu_shadow['vertical']) ? '0' : $settings->menu_shadow['vertical'];
	        $menu_shadow_b = empty($settings->menu_shadow['blur']) ? '0' : $settings->menu_shadow['blur'];
	        $menu_shadow_s = empty($settings->menu_shadow['spread']) ? '0' : $settings->menu_shadow['spread'];
	        ?>
			-webkit-box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( '#'.$settings->menu_shadow_color, $settings->menu_shadow_opacity / 100 ); ?>;
			-moz-box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( '#'.$settings->menu_shadow_color, $settings->menu_shadow_opacity / 100 ); ?>;
			box-shadow: <?php echo $menu_shadow_h; ?>px <?php echo $menu_shadow_v; ?>px <?php echo $menu_shadow_b; ?>px <?php echo $menu_shadow_s; ?>px <?php echo pp_hex2rgba( '#'.$settings->menu_shadow_color, $settings->menu_shadow_opacity / 100 ); ?>;
	        <?php
	    }
	?>
}

/* Close Button */
.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu .pp-menu-close-btn {
	font-size: <?php echo ( $settings->close_icon_size ) ? $settings->close_icon_size : '30'; ?>px;
	<?php if( $settings->close_icon_color ) { ?>color: #<?php echo $settings->close_icon_color; ?>;<?php } ?>
}

/* Menu */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu {
	<?php if ( $settings->responsive_alignment_vertical == 'center' ) { ?>
		position: absolute;
	    left: 0;
	    top: 50%;
	    -moz-transform: translate(0, -50%);
	    -ms-transform: translate(0, -50%);
	    -webkit-transform: translate(0, -50%);
	    transform: translate(0, -50%);
		width: 100%;
		margin-top: 0;
	<?php } ?>
	padding-top: <?php if ( $settings->responsive_overlay_padding['top'] >= 0 ) { echo $settings->responsive_overlay_padding['top'] . 'px'; } ?>;
	padding-bottom: <?php if ( $settings->responsive_overlay_padding['bottom'] >= 0 ) { echo $settings->responsive_overlay_padding['bottom'] . 'px'; } ?>;
	padding-left: <?php if ( $settings->responsive_overlay_padding['left'] >= 0 ) { echo $settings->responsive_overlay_padding['left'] . 'px'; } ?>;
	padding-right: <?php if ( $settings->responsive_overlay_padding['right'] >= 0 ) { echo $settings->responsive_overlay_padding['right'] . 'px'; } ?>;
}

/* Sub Menu */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu {
	box-shadow: none;
	border: none;
}

/* Links */
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li {
	display: block;
}

<?php if( $settings->alignment != 'right' ) { ?>
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
	padding-right: 0;
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .sub-menu > li > .pp-has-submenu-container > a:focus {
	background-color: transparent;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a {
	<?php if( $settings->responsive_link_bg_color ) { ?>background-color: #<?php echo $settings->responsive_link_bg_color; ?>;<?php } ?>
	<?php if( $settings->responsive_link_color ) { ?>color: #<?php echo $settings->responsive_link_color; ?>;<?php } ?>
		border-style: solid;
		border-top-width: <?php echo ( $settings->responsive_link_border_width['top'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['top'] : '0'; ?>px;
		border-bottom-width: <?php echo ( $settings->responsive_link_border_width['bottom'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['bottom'] : '0'; ?>px;
		border-left-width: <?php echo ( $settings->responsive_link_border_width['left'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['left'] : '0'; ?>px;
		border-right-width: <?php echo ( $settings->responsive_link_border_width['right'] != '' && $settings->responsive_link_border_color ) ? $settings->responsive_link_border_width['right'] : '0'; ?>px;
	border-bottom-color: <?php echo ($settings->responsive_link_border_color) ? '#' . $settings->responsive_link_border_color : 'transparent'; ?>;
	-webkit-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
	padding-top: <?php if ( $settings->responsive_link_padding['top'] >= 0 ) { echo $settings->responsive_link_padding['top'] . 'px'; } ?>;
	padding-bottom: <?php if ( $settings->responsive_link_padding['bottom'] >= 0 ) { echo $settings->responsive_link_padding['bottom'] . 'px'; } ?>;
	padding-left: <?php if ( $settings->responsive_link_padding['left'] >= 0 ) { echo $settings->responsive_link_padding['left'] . 'px'; } ?>;
	padding-right: <?php if ( $settings->responsive_link_padding['right'] >= 0 ) { echo $settings->responsive_link_padding['right'] . 'px'; } ?>;
}

.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li a:focus,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:hover,
.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .menu li .pp-has-submenu-container a:focus {
	<?php if( $settings->responsive_link_bg_hover_color ) { ?>background-color: #<?php echo $settings->responsive_link_bg_hover_color; ?>;<?php } ?>
	<?php if( $settings->responsive_link_hover_color ) { ?>color: #<?php echo $settings->responsive_link_hover_color; ?>;<?php } ?>
}

<?php if( '' != $settings->animation_speed ) { ?>
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-left,
	.fl-node-<?php echo $id; ?> .menu-open.pp-advanced-menu .pp-off-canvas-menu.pp-menu-left,
	.fl-node-<?php echo $id; ?> .pp-advanced-menu .pp-off-canvas-menu.pp-menu-right,
	.fl-node-<?php echo $id; ?> .menu-open.pp-advanced-menu .pp-off-canvas-menu.pp-menu-right {
		transition-duration: <?php echo ( $settings->animation_speed / 1000 ) . 's'; ?>;
	}
<?php } ?>

<?php if( !empty( $settings->responsive_link_color ) ) { ?>
		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu .pp-menu-toggle:before {
			border-color: #<?php echo $settings->responsive_link_color; ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu .pp-menu-toggle:after {
			border-color: #<?php echo $settings->responsive_link_color; ?>;
		}
		<?php } ?>
<?php } ?>

<?php if( !empty( $settings->responsive_link_hover_color ) ) { ?>

		<?php if( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'arrows' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .sub-menu li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-none .sub-menu li a:hover .pp-menu-toggle:before {
			border-color: #<?php echo $settings->responsive_link_hover_color; ?>;
		}
		<?php } elseif( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && $settings->submenu_hover_toggle == 'plus' ) || ( $settings->menu_layout == 'accordion' && $settings->submenu_click_toggle == 'plus' ) ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus li a:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li:hover .pp-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:hover .pp-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .sub-menu li a:hover .pp-menu-toggle:after {
			border-color: #<?php echo $settings->responsive_link_hover_color; ?>;
		}
		<?php } ?>
<?php } ?>

<?php if ( 'always' != $module->get_media_breakpoint() ) { ?>
	@media ( max-width: <?php echo $module->get_media_breakpoint() ?>px ) {
<?php } ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.pp-menu-default {
			display: none;
		}
		<?php if( $settings->responsive_alignment == 'right' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-arrows .pp-has-submenu-container > a > span,
		.fl-node-<?php echo $id; ?> .pp-advanced-menu.off-canvas .pp-toggle-plus .pp-has-submenu-container > a > span {
			padding-right: 28px;
		}
		<?php } ?>
<?php if ( 'always' != $module->get_media_breakpoint() ) { ?>
	}
<?php } ?>
