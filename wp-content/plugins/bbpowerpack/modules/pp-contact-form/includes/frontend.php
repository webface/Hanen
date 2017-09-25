<form class="pp-contact-form pp-form-<?php echo $settings->form_layout; ?>" <?php if ( isset( $module->template_id ) ) echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"'; ?>>
    <h3 class="pp-form-title">
	<?php if ( $settings->custom_title ) {
	 	echo $settings->custom_title;
	} ?>
	</h3>
	<p class="pp-form-description">
	<?php if ( $settings->custom_description ) {
		echo $settings->custom_description;
	} ?>
	</p>
    <div class="pp-contact-form-inner pp-clearfix">
        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
            <div class="pp-contact-form-fields-left">
        <?php } ?>
    	<?php if ($settings->name_toggle == 'show') : ?>
    	<div class="pp-input-group pp-name">
    		<label for="pp-name"><?php _ex( 'Name', 'Contact form field label.', 'bb-powerpack' );?></label>
    		<span class="pp-contact-error"><?php esc_html_e('Please enter your name.', 'bb-powerpack');?></span>
    		<input type="text" name="pp-name" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php esc_attr_e( 'Name', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

    	<?php if ($settings->email_toggle == 'show') : ?>
    	<div class="pp-input-group pp-email">
    		<label for="pp-email"><?php esc_html_e('Email', 'bb-powerpack');?></label>
    		<span class="pp-contact-error"><?php esc_html_e('Please enter a valid email.', 'bb-powerpack');?></span>
    		<input type="email" name="pp-email" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php esc_attr_e( 'Email', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

    	<?php if ($settings->phone_toggle == 'show') : ?>
    	<div class="pp-input-group pp-phone">
    		<label for="pp-phone"><?php esc_html_e('Phone', 'bb-powerpack');?></label>
    		<span class="pp-contact-error"><?php esc_html_e('Please enter a valid phone number.', 'bb-powerpack');?></span>
    		<input type="tel" name="pp-phone" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php esc_attr_e( 'Phone', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
        </div>
        <?php } ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
            <div class="pp-contact-form-fields-right">
        <?php } ?>

    	<?php if ($settings->subject_toggle == 'show') : ?>
    	<div class="pp-input-group pp-subject">
    		<label for="pp-subject"><?php esc_html_e('Subject', 'bb-powerpack');?></label>
    		<span class="pp-contact-error"><?php esc_html_e('Please enter a subject.', 'bb-powerpack');?></span>
    		<input type="text" name="pp-subject" value="" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php esc_attr_e( 'Subject', 'bb-powerpack' ); ?>" <?php } ?> />
    	</div>
    	<?php endif; ?>

        <?php if ($settings->message_toggle == 'show') : ?>
    	<div class="pp-input-group pp-message">
    		<label for="pp-message"><?php esc_html_e('Your Message', 'bb-powerpack');?></label>
    		<span class="pp-contact-error"><?php esc_html_e('Please enter a message.', 'bb-powerpack');?></span>
    		<textarea name="pp-message" <?php if( $settings->input_placeholder_display == 'block' ) { ?>placeholder="<?php esc_attr_e( 'Message', 'bb-powerpack' ); ?>" <?php } ?>></textarea>
    	</div>
        <?php endif; ?>

        <?php if( $settings->form_layout == 'stacked-inline' ) { ?>
        </div>
        <?php } ?>
    </div>
	<?php

	FLBuilder::render_module_html( 'button', array(
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
	<?php if ($settings->success_action == 'redirect') : ?>
		<input type="text" value="<?php echo $settings->success_url; ?>" style="display: none;" class="pp-success-url">
	<?php elseif($settings->success_action == 'none') : ?>
		<span class="pp-success-none" style="display:none;"><?php esc_html_e( 'Message Sent!', 'bb-powerpack' ); ?></span>
	<?php endif; ?>

	<span class="pp-send-error" style="display:none;"><?php esc_html_e( 'Message failed. Please try again.', 'bb-powerpack' ); ?></span>
</form>
<?php if($settings->success_action == 'show_message') : ?>
  <span class="pp-success-msg" style="display:none;"><?php echo $settings->success_message; ?></span>
<?php endif; ?>
