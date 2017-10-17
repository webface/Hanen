.fl-node-<?php echo $id; ?> .pp-animated-text-wrap {
	text-align: <?php echo $settings->alignment; ?>;
	<?php if ( $settings->effect_type == 'type' ) { ?>
	min-height: <?php echo $settings->min_height; ?>px;
	<?php } ?>
}

<?php if( !empty( $settings->prefix ) ) {?>
.fl-node-<?php echo $id; ?> .pp-animated-text-prefix {
	margin-right:<?php echo $settings->space_prefix; ?>px;
}
<?php } ?>

<?php if( !empty( $settings->suffix ) ) {?>
.fl-node-<?php echo $id; ?> .pp-animated-text-suffix {
	margin-left:<?php echo $settings->space_suffix; ?>px;
}
<?php } ?>

/* Prefix - Suffix Typography */
.fl-node-<?php echo $id; ?> .pp-animated-text-prefix,
.fl-node-<?php echo $id; ?> .pp-animated-text-suffix {
	<?php if ( $settings->font_family['family'] != 'Default' ) {
        FLBuilderFonts::font_css( $settings->font_family );
    } ?>

	<?php if( $settings->font_size == 'custom' && $settings->font_size_custom['desktop'] != '' ) : ?>
		font-size: <?php echo $settings->font_size_custom['desktop']; ?>px;
	<?php endif; ?>
	<?php if( $settings->line_height == 'custom' && $settings->line_height_custom['desktop'] != '' ) : ?>
		line-height: <?php echo $settings->line_height_custom['desktop']; ?>;
	<?php endif; ?>

	<?php if( $settings->color != '' ) : ?>
	color: #<?php echo $settings->color; ?>;
	<?php endif; ?>
}

/* animated Text Typography */
.fl-node-<?php echo $id; ?> .pp-animated-text-main {
	<?php if ( $settings->animated_font_family['family'] != 'Default' ) {
        FLBuilderFonts::font_css( $settings->animated_font_family );
    } ?>

	<?php if( $settings->animated_font_size == 'custom' && $settings->animated_font_size_custom['desktop'] != '' ) : ?>
		font-size: <?php echo $settings->animated_font_size_custom['desktop']; ?>px;
	<?php endif; ?>
	<?php if( $settings->animated_line_height == 'custom' && $settings->animated_line_height_custom['desktop'] != '' ) : ?>
		line-height: <?php echo $settings->animated_line_height_custom['desktop']; ?>;
	<?php endif; ?>

	<?php if( $settings->animated_color != '' ) : ?>
	color: #<?php echo $settings->animated_color; ?>;
	<?php endif; ?>
}

<?php
if( $settings->effect_type == 'type' && $settings->show_cursor == 'yes' && $settings->cursor_blink == 'yes' ) { ?>
	.pp-animated-text-wrap .typed-cursor{
	    opacity: 1;
	    -webkit-animation: blink-cursor 0.7s infinite;
	       -moz-animation: blink-cursor 0.7s infinite;
	            animation: blink-cursor 0.7s infinite;
	}
	@keyframes blink-cursor{
	    0% { opacity:1; }
	    50% { opacity:0; }
	    100% { opacity:1; }
	}
	@-webkit-keyframes blink-cursor{
	    0% { opacity:1; }
	    50% { opacity:0; }
	    100% { opacity:1; }
	}
	@-moz-keyframes blink-cursor{
	    0% { opacity:1; }
	    50% { opacity:0; }
	    100% { opacity:1; }
	}
<?php } ?>


/* Typography responsive layout starts here */


@media only screen and ( min-width: 768px ) {
	.fl-node-<?php echo $id; ?> span.pp-slide_text {
	    white-space: nowrap;
	}

}

@media only screen and ( max-width: 768px ) {
	.fl-node-<?php echo $id; ?> .pp-animated-text-prefix,
	.fl-node-<?php echo $id; ?> .pp-animated-text-suffix{
		<?php if( $settings->font_size == 'custom' && $settings->font_size_custom['tablet'] != '' ) : ?>
			font-size: <?php echo $settings->font_size_custom['tablet']; ?>px;
		<?php endif; ?>
		<?php if( $settings->line_height == 'custom' && $settings->line_height_custom['tablet'] != '' ) : ?>
			line-height: <?php echo $settings->line_height_custom['tablet']; ?>;
		<?php endif; ?>
	}

	.fl-node-<?php echo $id; ?> .pp-animated-text-main {
		<?php if( $settings->animated_font_size == 'custom' && $settings->animated_font_size_custom['tablet'] != '' ) : ?>
			font-size: <?php echo $settings->animated_font_size_custom['tablet']; ?>px;
		<?php endif; ?>
		<?php if( $settings->animated_line_height == 'custom' && $settings->animated_line_height_custom['tablet'] != '' ) : ?>
			line-height: <?php echo $settings->animated_line_height_custom['tablet']; ?>;
		<?php endif; ?>
	}
}

@media only screen and ( max-width: 600px ) {
	.fl-node-<?php echo $id; ?> .pp-animated-text-prefix,
	.fl-node-<?php echo $id; ?> .pp-animated-text-suffix{
		<?php if( $settings->font_size == 'custom' && $settings->font_size_custom['mobile'] != '' ) : ?>
			font-size: <?php echo $settings->font_size_custom['mobile']; ?>px;
		<?php endif; ?>
		<?php if( $settings->line_height == 'custom' && $settings->line_height_custom['mobile'] != '' ) : ?>
			line-height: <?php echo $settings->line_height_custom['mobile']; ?>;
		<?php endif; ?>
	}
	.fl-node-<?php echo $id; ?> .pp-animated-text-main {
		<?php if( $settings->animated_font_size == 'custom' && $settings->animated_font_size_custom['mobile'] != '' ) : ?>
			font-size: <?php echo $settings->animated_font_size_custom['mobile']; ?>px;
		<?php endif; ?>
		<?php if( $settings->animated_line_height == 'custom' && $settings->animated_line_height_custom['mobile'] != '' ) : ?>
			line-height: <?php echo $settings->animated_line_height_custom['mobile']; ?>;
		<?php endif; ?>
	}
}
