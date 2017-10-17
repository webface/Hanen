<?php if ( ! $settings->match_height ) : ?>
.fl-node-<?php echo $id; ?> .pp-custom-grid-post {
    margin-bottom: <?php echo $settings->post_spacing; ?>px;
    width: <?php echo $settings->post_width; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-custom-grid-sizer {
    width: <?php echo $settings->post_width; ?>px;
}
@media screen and (max-width: <?php echo $settings->post_width + $settings->post_spacing; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-custom-grid,
	.fl-node-<?php echo $id; ?> .pp-custom-grid-post,
	.fl-node-<?php echo $id; ?> .pp-custom-grid-sizer {
		width: 100% !important;
	}
}
<?php endif; ?>
<?php if ( $settings->match_height ) : ?>
.fl-node-<?php echo $id; ?> .pp-custom-grid {
	margin-left: -<?php echo $settings->post_spacing / 2; ?>px;
	margin-right: -<?php echo $settings->post_spacing / 2; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-custom-grid-column {
	padding-bottom: <?php echo $settings->post_spacing; ?>px;
	padding-left: <?php echo $settings->post_spacing / 2; ?>px;
	padding-right: <?php echo $settings->post_spacing / 2; ?>px;
	width: <?php echo 100 / $settings->post_columns; ?>%;
}
.fl-node-<?php echo $id; ?> .pp-custom-grid-column:nth-child(<?php echo $settings->post_columns; ?>n + 1) {
	clear: both;
}
@media screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column {
		width: <?php echo 100 / $settings->post_columns_medium; ?>%;
	}
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column:nth-child(<?php echo $settings->post_columns; ?>n + 1) {
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column:nth-child(<?php echo $settings->post_columns_medium; ?>n + 1) {
		clear: both;
	}
}
@media screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column {
		width: <?php echo 100 / $settings->post_columns_responsive; ?>%;
	}
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column:nth-child(<?php echo $settings->post_columns_medium; ?>n + 1) {
		clear: none;
	}
	.fl-node-<?php echo $id; ?> .pp-custom-grid-column:nth-child(<?php echo $settings->post_columns_responsive; ?>n + 1) {
		clear: both;
	}
}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .pp-custom-grid-post {

	<?php if( ! empty( $settings->bg_color ) ) : ?>
	background: <?php echo ( false === strpos( $settings->bg_color, 'rgb' ) ) ? '#' . $settings->bg_color : $settings->bg_color; ?>;
	<?php endif; ?>

	<?php if( $settings->border_type != 'default' && $settings->border_type != 'none' && ! empty( $settings->border_color ) ) : ?>
	border: <?php echo $settings->border_size; ?>px <?php echo $settings->border_type; ?> #<?php echo $settings->border_color; ?>;
	<?php endif; ?>

	<?php if( $settings->border_type == 'none' ) : ?>
	border: none;
	<?php endif; ?>

	<?php if( $settings->post_align != 'default' ) : ?>
	text-align: <?php echo $settings->post_align; ?>;
	<?php endif; ?>

    <?php if( $settings->post_shadow == '1' ) : ?>
        <?php $shadow_color = ( false === strpos( $settings->post_shadow_color, 'rgb' ) ) ? '#' . $settings->post_shadow_color : $settings->post_shadow_color; ?>
        -webkit-box-shadow: <?php echo $settings->post_shadow_options['horizontal']; ?>px <?php echo $settings->post_shadow_options['vertical']; ?>px <?php echo $settings->post_shadow_options['blur']; ?>px <?php echo $settings->post_shadow_options['spread']; ?>px <?php echo $shadow_color; ?>;
        -moz-box-shadow: <?php echo $settings->post_shadow_options['horizontal']; ?>px <?php echo $settings->post_shadow_options['vertical']; ?>px <?php echo $settings->post_shadow_options['blur']; ?>px <?php echo $settings->post_shadow_options['spread']; ?>px <?php echo $shadow_color; ?>;
        box-shadow: <?php echo $settings->post_shadow_options['horizontal']; ?>px <?php echo $settings->post_shadow_options['vertical']; ?>px <?php echo $settings->post_shadow_options['blur']; ?>px <?php echo $settings->post_shadow_options['spread']; ?>px <?php echo $shadow_color; ?>;
    <?php endif; ?>
}

.fl-node-<?php echo $id; ?> .pp-custom-grid-pagination li a.page-numbers,
.fl-node-<?php echo $id; ?> .pp-custom-grid-pagination li span.page-numbers {
    <?php if ( $settings->pagination_bg_color ) : ?>
        background: <?php echo ( false === strpos( $settings->pagination_bg_color, 'rgb' ) ) ? '#' . $settings->pagination_bg_color : $settings->pagination_bg_color; ?>;
    <?php endif; ?>
    <?php if ( $settings->pagination_text_color ) : ?>
        color: <?php echo $settings->pagination_text_color; ?>;
    <?php endif; ?>
    <?php if ( 'default' != $settings->pagination_border_type ) : ?>
        border-style: <?php echo $settings->pagination_border_type; ?>;
        border-width: <?php echo $settings->pagination_border_size; ?>px;
        border-color: #<?php echo $settings->pagination_border_color; ?>;
    <?php endif; ?>
    border-radius: <?php echo $settings->pagination_border_radius; ?>px;
}
.fl-node-<?php echo $id; ?> .pp-custom-grid-pagination li a.page-numbers:hover,
.fl-node-<?php echo $id; ?> .pp-custom-grid-pagination li span.current {
    <?php if ( $settings->pagination_bg_color_h ) : ?>
        background: <?php echo ( false === strpos( $settings->pagination_bg_color_h, 'rgb' ) ) ? '#' . $settings->pagination_bg_color_h : $settings->pagination_bg_color_h; ?>;
    <?php endif; ?>
    <?php if ( $settings->pagination_text_color_h ) : ?>
        color: <?php echo $settings->pagination_text_color_h; ?>;
    <?php endif; ?>
}
