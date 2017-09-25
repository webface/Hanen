<?php



FLBuilder::render_module_css('button', $id, array(
	'bg_color'          => $settings->btn_bg_color,
	'bg_hover_color'    => $settings->btn_bg_hover_color,
	'bg_opacity'        => $settings->btn_bg_opacity,
	'bg_hover_opacity'  => $settings->btn_bg_hover_opacity,
	'button_transition' => $settings->btn_button_transition,
	'border_radius'     => $settings->btn_border_radius,
	'border_size'       => $settings->btn_border_size,
	'icon'              => $settings->btn_icon,
	'icon_position'     => $settings->btn_icon_position,
	'link'              => '#',
	'link_target'       => '_self',
	'style'             => $settings->btn_style,
	'text'              => $settings->btn_text,
	'text_color'        => $settings->btn_text_color,
	'text_hover_color'  => $settings->btn_text_hover_color,
	'width'             => $settings->btn_width,
	'align'				=> $settings->btn_align,
	'icon_animation'	=> $settings->btn_icon_animation
));
?>

<?php if ('right' == $settings->btn_align): ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success-msg {
	float: right;
}
<?php endif; ?>

<?php if ('center' == $settings->btn_align): ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-send-error,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success-none,
.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form .pp-success-msg {
	display: block;
	text-align: center;
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .pp-contact-form .fl-button-wrap {
	margin-top: <?php echo $settings->button_margin; ?>px;
}

.fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button,
.fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button:visited {
	<?php if( $settings->button_font_size['desktop'] && $settings->button_size == 'custom' ) { ?>
	font-size: <?php echo $settings->button_font_size['desktop']; ?>px;
	<?php } ?>
	<?php if( $settings->button_font_family['family'] != 'Default' ) { ?>
    <?php FLBuilderFonts::font_css( $settings->button_font_family ); ?>
    <?php } ?>
	text-transform: <?php echo $settings->button_text_transform; ?>;
	<?php if( $settings->button_padding['top'] >= 0 ) { ?>
	padding-top: <?php echo $settings->button_padding['top']; ?>px;
	<?php } ?>
	<?php if( $settings->button_padding['right'] >= 0 ) { ?>
	padding-right: <?php echo $settings->button_padding['right']; ?>px;
	<?php } ?>
	<?php if( $settings->button_padding['bottom'] >= 0 ) { ?>
	padding-bottom: <?php echo $settings->button_padding['bottom']; ?>px;
	<?php } ?>
	<?php if( $settings->button_padding['left'] >= 0 ) { ?>
	padding-left: <?php echo $settings->button_padding['left']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form {
	background-color: <?php echo $settings->form_bg_color && $settings->form_bg_type == 'color' ? pp_hex2rgba('#' . $settings->form_bg_color, $settings->form_background_opacity / 100) : 'transparent'; ?>;
    <?php if( $settings->form_bg_image && $settings->form_bg_type == 'image' ) { ?>
	background-image: url('<?php echo $settings->form_bg_image_src; ?>');
    <?php } ?>
    <?php if( $settings->form_bg_size ) { ?>
    background-size: <?php echo $settings->form_bg_size; ?>;
    <?php } ?>
    <?php if( $settings->form_bg_repeat ) { ?>
    background-repeat: <?php echo $settings->form_bg_repeat; ?>;
    <?php } ?>
    <?php if( $settings->form_border_width >= 0 ) { ?>
    border-width: <?php echo $settings->form_border_width; ?>px;
    <?php } ?>
    <?php if( $settings->form_border_color ) { ?>
    border-color: #<?php echo $settings->form_border_color; ?>;
    <?php } ?>
    <?php if( $settings->form_border_style ) { ?>
    border-style: <?php echo $settings->form_border_style; ?>;
    <?php } ?>
    <?php if( $settings->form_border_radius >= 0 ) { ?>
    border-radius: <?php echo $settings->form_border_radius; ?>px;
    <?php } ?>
    <?php if ( 'yes' == $settings->form_shadow_display ) { ?>
        -webkit-box-shadow: <?php echo $settings->form_shadow['horizontal']; ?>px <?php echo $settings->form_shadow['vertical']; ?>px <?php echo $settings->form_shadow['blur']; ?>px <?php echo $settings->form_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->form_shadow_color, $settings->form_shadow_opacity / 100 ); ?>;
        -moz-box-shadow: <?php echo $settings->form_shadow['horizontal']; ?>px <?php echo $settings->form_shadow['vertical']; ?>px <?php echo $settings->form_shadow['blur']; ?>px <?php echo $settings->form_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->form_shadow_color, $settings->form_shadow_opacity / 100 ); ?>;
        -o-box-shadow: <?php echo $settings->form_shadow['horizontal']; ?>px <?php echo $settings->form_shadow['vertical']; ?>px <?php echo $settings->form_shadow['blur']; ?>px <?php echo $settings->form_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->form_shadow_color, $settings->form_shadow_opacity / 100 ); ?>;
        box-shadow: <?php echo $settings->form_shadow['horizontal']; ?>px <?php echo $settings->form_shadow['vertical']; ?>px <?php echo $settings->form_shadow['blur']; ?>px <?php echo $settings->form_shadow['spread']; ?>px <?php echo pp_hex2rgba( '#'.$settings->form_shadow_color, $settings->form_shadow_opacity / 100 ); ?>;
    <?php } ?>
    <?php if( $settings->form_padding['top'] >= 0 ) { ?>
	padding-top: <?php echo $settings->form_padding['top']; ?>px;
	<?php } ?>
	<?php if( $settings->form_padding['right'] >= 0 ) { ?>
	padding-right: <?php echo $settings->form_padding['right']; ?>px;
	<?php } ?>
	<?php if( $settings->form_padding['bottom'] >= 0 ) { ?>
	padding-bottom: <?php echo $settings->form_padding['bottom']; ?>px;
	<?php } ?>
	<?php if( $settings->form_padding['left'] >= 0 ) { ?>
	padding-left: <?php echo $settings->form_padding['left']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-title,
.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-description {
	<?php if( $settings->form_custom_title_desc == 'no' ) { ?>
		display: none;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-title {
	<?php if( $settings->title_color ) { ?>
   color: #<?php echo $settings->title_color; ?>;
   <?php } ?>
   <?php if( $settings->title_font_size['desktop'] && $settings->title_size == 'custom' ) { ?>
   font-size: <?php echo $settings->title_font_size['desktop']; ?>px;
   <?php } ?>
   <?php if( $settings->title_line_height['desktop'] ) { ?>
   line-height: <?php echo $settings->title_line_height['desktop']; ?>;
   <?php } ?>
   <?php if( $settings->title_font_family['family'] != 'Default' ) { ?>
   <?php FLBuilderFonts::font_css( $settings->title_font_family ); ?>
   <?php } ?>
   <?php if( $settings->title_alignment ) { ?>
   text-align: <?php echo $settings->title_alignment; ?>;
   <?php } ?>
   <?php if( $settings->title_margin['top'] >= 0 ) { ?>
   margin-top: <?php echo $settings->title_margin['top']; ?>px;
   <?php } ?>
   <?php if( $settings->title_margin['bottom'] >= 0 ) { ?>
   margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
   <?php } ?>
   text-transform: <?php echo $settings->title_text_transform; ?>;
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-description {
	<?php if( $settings->description_font_family['family'] != 'Default' ) { ?>
    <?php FLBuilderFonts::font_css( $settings->description_font_family ); ?>
    <?php } ?>
    <?php if( $settings->description_color ) { ?>
    color: #<?php echo $settings->description_color; ?>;
    <?php } ?>
    <?php if( $settings->description_font_size['desktop'] && $settings->description_size == 'custom' ) { ?>
    font-size: <?php echo $settings->description_font_size['desktop']; ?>px;
    <?php } ?>
    <?php if( $settings->description_line_height['desktop'] ) { ?>
    line-height: <?php echo $settings->description_line_height['desktop']; ?>;
    <?php } ?>
    <?php if( $settings->description_alignment ) { ?>
    text-align: <?php echo $settings->description_alignment; ?>;
    <?php } ?>
    <?php if( $settings->description_margin['top'] >= 0 ) { ?>
	margin-top: <?php echo $settings->description_margin['top']; ?>px;
	<?php } ?>
	<?php if( $settings->description_margin['bottom'] >= 0 ) { ?>
	margin-bottom: <?php echo $settings->description_margin['bottom']; ?>px;
	<?php } ?>
    text-transform: <?php echo $settings->description_text_transform; ?>;
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-input-group {
    <?php if( $settings->input_field_margin >= 0 ) { ?>
	margin-bottom: <?php echo $settings->input_field_margin; ?>px;
    <?php } ?>
}


<?php if( $settings->name_toggle == 'show' || $settings->email_toggle == 'show' || $settings->phone_toggle == 'show' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group {
		width: 100%;
		padding-left: 0;
	}
<?php } ?>
<?php if( ($settings->name_toggle == 'show' && $settings->email_toggle == 'show') ||
			($settings->name_toggle == 'show' && $settings->phone_toggle == 'show') ||
			($settings->email_toggle == 'show' && $settings->phone_toggle == 'show') ) { ?>
	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group {
		width: 50%;
		padding-left: 10px;
	}

	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group:first-child {
		padding-left: 0;
	}
<?php } ?>

<?php if( $settings->name_toggle == 'show' && $settings->email_toggle == 'show' && $settings->phone_toggle == 'show' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group {
		width: 33.33%;
		padding-left: 10px;
	}

	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group:first-child {
		padding-left: 0;
	}
<?php } ?>



.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group.pp-message,
.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group.pp-subject {
	width: 100%;
	padding-left: 0;
}

.fl-node-<?php echo $id; ?> .pp-contact-form label {
	<?php if( $settings->form_label_color ) { ?>
	color: #<?php echo $settings->form_label_color; ?>;
    <?php } ?>
    <?php if( $settings->display_labels ) { ?>
    display: <?php echo $settings->display_labels; ?>;
    <?php } ?>
    <?php if( $settings->label_font_size['desktop'] && $settings->label_size == 'custom' ) { ?>
    font-size: <?php echo $settings->label_font_size['desktop']; ?>px;
    <?php } ?>
    <?php if( $settings->label_font_family['family'] != 'Default' ) { ?>
    <?php FLBuilderFonts::font_css( $settings->label_font_family ); ?>
    <?php } ?>
    text-transform: <?php echo $settings->label_text_transform; ?>;
}

.fl-node-<?php echo $id; ?> .pp-contact-form textarea,
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text],
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel],
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email] {
	<?php if( $settings->input_field_text_color ) { ?>
    color: #<?php echo $settings->input_field_text_color; ?>;
    <?php } ?>
	background-color: <?php echo $settings->input_field_bg_color ? pp_hex2rgba('#' . $settings->input_field_bg_color, $settings->input_field_background_opacity / 100 ) : 'transparent'; ?>;
	border-width: 0;
	border-color: <?php echo $settings->input_field_border_color ? '#' . $settings->input_field_border_color : 'transparent'; ?>;
    <?php if( $settings->input_field_border_radius >= 0 ) { ?>
	border-radius: <?php echo $settings->input_field_border_radius; ?>px;
    -moz-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
    -webkit-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
    -ms-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
    -o-border-radius: <?php echo $settings->input_field_border_radius; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_border_width >= 0 ) { ?>
    <?php echo $settings->input_field_border_position; ?>-width: <?php echo $settings->input_field_border_width; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_box_shadow == 'yes' ) { ?>
        box-shadow: <?php echo ($settings->input_shadow_direction == 'inset') ? $settings->input_shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->input_shadow_color; ?>;
        -moz-box-shadow: <?php echo ($settings->input_shadow_direction == 'inset') ? $settings->input_shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->input_shadow_color; ?>;
        -webkit-box-shadow: <?php echo ($settings->input_shadow_direction == 'inset') ? $settings->input_shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->input_shadow_color; ?>;
        -ms-box-shadow: <?php echo ($settings->input_shadow_direction == 'inset') ? $settings->input_shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->input_shadow_color; ?>;
        -o-box-shadow: <?php echo ($settings->input_shadow_direction == 'inset') ? $settings->input_shadow_direction : ''; ?> 0 0 10px #<?php echo $settings->input_shadow_color; ?>;
    <?php } ?>
    <?php if( $settings->input_field_padding['top'] >= 0 ) { ?>
    padding-top: <?php echo $settings->input_field_padding['top']; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_padding['bottom'] >= 0 ) { ?>
    padding-bottom: <?php echo $settings->input_field_padding['bottom']; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_padding['left'] >= 0 ) { ?>
    padding-left: <?php echo $settings->input_field_padding['left']; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_padding['right'] >= 0 ) { ?>
    padding-right: <?php echo $settings->input_field_padding['right']; ?>px;
    <?php } ?>
    <?php if( $settings->input_field_text_alignment ) { ?>
    text-align: <?php echo $settings->input_field_text_alignment; ?>;
    <?php } ?>
    <?php if( $settings->input_font_family['family'] != 'Default' ) { ?>
    <?php FLBuilderFonts::font_css( $settings->input_font_family ); ?>
    <?php } ?>
    <?php if( $settings->input_font_size['desktop'] && $settings->input_size == 'custom' ) { ?>
    font-size: <?php echo $settings->input_font_size['desktop']; ?>px;
    <?php } ?>
    text-transform: <?php echo $settings->input_text_transform; ?>;
}

.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text],
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel],
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email] {
	<?php if( $settings->input_field_height ) { ?>
    height: <?php echo $settings->input_field_height; ?>px;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form textarea {
	<?php if( $settings->input_textarea_height ) { ?>
    height: <?php echo $settings->input_textarea_height; ?>px;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form textarea:focus,
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text]:focus,
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel]:focus,
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email]:focus {
	border-color: <?php echo $settings->input_field_focus_color ? '#' . $settings->input_field_focus_color : 'transparent'; ?>;
}

.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text]::-webkit-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text]:-moz-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text]::-moz-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text]:-ms-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel]::-webkit-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel]:-moz-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel]::-moz-placeholder {
    <?php if( $settings->input_placeholder_color ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
	color: transparent;
	opacity: 0;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel]:-ms-input-placeholder {
    <?php if( $settings->input_placeholder_color ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
	<?php } else { ?>
	color: transparent;
	opacity: 0;
	<?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email]::-webkit-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email]:-moz-placeholder {
    <?php if( $settings->input_placeholder_color ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email]::-moz-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email]:-ms-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form textarea::-webkit-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
    <?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form textarea:-moz-placeholder {
    <?php if( $settings->input_placeholder_color ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form textarea::-moz-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}
.fl-node-<?php echo $id; ?> .pp-contact-form textarea:-ms-input-placeholder {
    <?php if( $settings->input_placeholder_color && $settings->input_placeholder_display == 'block' ) { ?>
    color: #<?php echo $settings->input_placeholder_color; ?>;
	<?php } else { ?>
    color: transparent;
	opacity: 0;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-contact-error {
    <?php if( $settings->validation_message_color ) { ?>
	color: #<?php echo $settings->validation_message_color; ?>;
    <?php } ?>
	<?php if( $settings->label_font_family['family'] != 'Default' ) { ?>
    <?php FLBuilderFonts::font_css( $settings->label_font_family ); ?>
    <?php } ?>
	<?php if( $settings->validation_error_font_size['desktop'] && $settings->validation_error_size == 'custom' ) { ?>
    font-size: <?php echo $settings->validation_error_font_size['desktop']; ?>px;
    <?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-contact-form .pp-error textarea,
.fl-node-<?php echo $id; ?> .pp-contact-form .pp-error input[type=text],
.fl-node-<?php echo $id; ?> .pp-contact-form .pp-error input[type=tel],
.fl-node-<?php echo $id; ?> .pp-contact-form .pp-error input[type=email] {
	<?php if( $settings->validation_field_border_color ) { ?>
	border-color: #<?php echo $settings->validation_field_border_color; ?>;
    <?php } ?>
}

.fl-builder-content .fl-node-<?php echo $id; ?> .pp-success-msg {
	<?php if( $settings->success_message_font_size['desktop'] && $settings->success_message_size == 'custom' ) { ?>
    font-size: <?php echo $settings->success_message_font_size['desktop']; ?>px;
    <?php } ?>
	<?php if( $settings->success_message_color ) { ?>
	color: #<?php echo $settings->success_message_color; ?>;
    <?php } ?>
}


@media only screen and (max-width: 768px) {
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button,
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button:visited {
		<?php if( $settings->button_font_size['tablet'] && $settings->button_size == 'custom' ) { ?>
		font-size: <?php echo $settings->button_font_size['tablet']; ?>px;
		<?php } ?>
		display: block;
		text-align: center;
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-title {
	   <?php if( $settings->title_font_size['tablet'] && $settings->title_size == 'custom' ) { ?>
	   font-size: <?php echo $settings->title_font_size['tablet']; ?>px;
	   <?php } ?>
	   <?php if( $settings->title_line_height['tablet'] ) { ?>
	   line-height: <?php echo $settings->title_line_height['tablet']; ?>;
	   <?php } ?>
   	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-description {
	    <?php if( $settings->description_font_size['tablet'] && $settings->description_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->description_font_size['tablet']; ?>px;
	    <?php } ?>
	    <?php if( $settings->description_line_height['tablet'] ) { ?>
	    line-height: <?php echo $settings->description_line_height['tablet']; ?>;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form label {
	    <?php if( $settings->label_font_size['tablet'] && $settings->label_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->label_font_size['tablet']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form textarea,
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text],
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel],
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email] {
		<?php if( $settings->input_font_size['tablet'] && $settings->input_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->input_font_size['tablet']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-contact-error {
		<?php if( $settings->validation_error_font_size['tablet'] && $settings->validation_error_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->validation_error_font_size['tablet']; ?>px;
	    <?php } ?>
	}
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-success-msg {
		<?php if( $settings->success_message_font_size['tablet'] && $settings->success_message_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->success_message_font_size['tablet']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form.pp-form-inline .pp-input-group {
		width: 100% !important;
		padding-left: 0;
	}
}

@media only screen and (max-width: 480px) {
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button,
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-contact-form a.fl-button:visited {
		<?php if( $settings->button_font_size['mobile'] && $settings->button_size == 'custom' ) { ?>
		font-size: <?php echo $settings->button_font_size['mobile']; ?>px;
		<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-title {
	   <?php if( $settings->title_font_size['mobile'] && $settings->title_size == 'custom' ) { ?>
	   font-size: <?php echo $settings->title_font_size['mobile']; ?>px;
	   <?php } ?>
	   <?php if( $settings->title_line_height['mobile'] ) { ?>
	   line-height: <?php echo $settings->title_line_height['mobile']; ?>;
	   <?php } ?>
   	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-form-description {
	    <?php if( $settings->description_font_size['mobile'] && $settings->description_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->description_font_size['mobile']; ?>px;
	    <?php } ?>
	    <?php if( $settings->description_line_height['mobile'] ) { ?>
	    line-height: <?php echo $settings->description_line_height['mobile']; ?>;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form label {
	    <?php if( $settings->label_font_size['mobile'] && $settings->label_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->label_font_size['mobile']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form textarea,
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=text],
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=tel],
	.fl-node-<?php echo $id; ?> .pp-contact-form input[type=email] {
		<?php if( $settings->input_font_size['mobile'] && $settings->input_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->input_font_size['mobile']; ?>px;
	    <?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-contact-form .pp-contact-error {
		<?php if( $settings->validation_error_font_size['mobile'] && $settings->validation_error_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->validation_error_font_size['mobile']; ?>px;
	    <?php } ?>
	}
	.fl-builder-content .fl-node-<?php echo $id; ?> .pp-success-msg {
		<?php if( $settings->success_message_font_size['mobile'] && $settings->success_message_size == 'custom' ) { ?>
	    font-size: <?php echo $settings->success_message_font_size['mobile']; ?>px;
	    <?php } ?>
	}
}
