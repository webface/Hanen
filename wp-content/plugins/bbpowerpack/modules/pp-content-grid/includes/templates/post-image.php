<div class="pp-content-grid-image pp-post-image">
    <?php if ( has_post_thumbnail() ) { ?>
        <?php echo $module->pp_render_img( get_the_id(), $settings->image_thumb_crop ); ?>
    <?php } else {
        $first_img = $module->pp_catch_image( get_the_content() );
        $img_src = '' != $first_img ? $first_img : apply_filters( 'pp_cg_placeholder_img', $module->url .'/images/placeholder.jpg' );
        ?>
        <img src="<?php echo $img_src; ?>" />
    <?php } ?>

    <?php if(($settings->show_categories == 'yes' && taxonomy_exists($settings->post_taxonomies) && !empty($terms_list)) && ('style-3' == $settings->post_grid_style_select) ) : ?>
        <?php include $module->dir . 'includes/templates/post-meta.php'; ?>
    <?php endif; ?>

    <?php if( 'style-4' == $settings->post_grid_style_select ) { ?>
        <<?php echo $settings->title_tag; ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
            <?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
                <a href="<?php the_permalink(); ?>">
            <?php } ?>
                    <?php the_title(); ?>
            <?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
                </a>
            <?php } ?>
        </<?php echo $settings->title_tag; ?>>
    <?php } ?>

    <?php if('style-6' == $settings->post_grid_style_select && 'yes' == $settings->show_date) { ?>
    <div class="pp-content-post-date pp-post-meta">
        <span class="pp-post-month"><?php echo get_the_date('M'); ?></span>
        <span class="pp-post-day"><?php echo get_the_date('d'); ?></span>
    </div>
    <?php } ?>
</div>
