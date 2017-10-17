<?php

FLBuilderModel::default_settings($settings, array(
	'data_source'		=> 'custom_query',
	'post_type' 		=> 'post',
	'order_by'  		=> 'date',
	'order'     		=> 'DESC',
	'offset'    		=> 0,
	'users'     		=> '',
	'show_image' 		=> 'yes',
	'show_author'		=> 'yes',
	'show_date'			=> 'yes',
	'show_categories'	=> 'no',
	'meta_separator'	=> ' | ',
	'show_content'		=> 'yes',
	'content_type'		=> 'excerpt',
	'content_length'	=> 300,
	'more_link_type'	=> 'box',
	'more_link_text'	=> __('Read More', 'bb-powerpack'),
	'post_grid_filters_display' => 'no',
	'post_grid_filters'	=> 'none',
	'all_filter_label'	=> __('All', 'bb-powerpack'),
	'post_taxonomies'	=> 'none',
	'product_rating'	=> 'yes',
	'product_price'		=> 'yes',
	'product_button'	=> 'yes',
	'product_button_text'	=> __('Add to Cart', 'bb-powerpack')
));

$settings = apply_filters( 'pp_cg_loop_settings', $settings );
do_action( 'pp_cg_loop_settings_before_form', $settings ); // e.g Add custom FLBuilder::render_settings_field()

?>
<div id="fl-builder-settings-section-source" class="fl-loop-data-source-select fl-builder-settings-section">

	<table class="fl-form-table">
	<?php

	// Data Source
	FLBuilder::render_settings_field('data_source', array(
		'type'          => 'select',
		'label'         => __('Source', 'bb-powerpack'),
		'default'		=> 'custom_query',
		'options'       => array(
			'custom_query'  => __('Custom Query', 'bb-powerpack'),
			'main_query'    => __('Main Query', 'bb-powerpack'),
		),
		'toggle'        => array(
			'custom_query'  => array(
				'sections'		=> array( 'general', 'filter' ),
				'fields'        => array( 'posts_per_page' )
			)
		)
	), $settings);
	?>
	</table>
</div>
<div class="fl-custom-query fl-loop-data-source" data-source="custom_query">
	<div id="fl-builder-settings-section-general" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title"><?php _e('Custom Query', 'bb-powerpack'); ?></h3>
		<table class="fl-form-table">
		<?php
		// Post type
		FLBuilder::render_settings_field('post_type', array(
			'type'          => 'post-type',
			'label'         => __('Post Type', 'bb-powerpack'),
			'toggle'		=> array(
				'product'	=> array(
					'sections'	=> array('product-settings'),
					'fields'	=> array('product_rating')
					//'fields'	=> array('product_rating', 'product_price', 'product_button')
				),
				'download'	=> array(
					'sections'	=> array('product-settings')
					//'fields'	=> array('product_price', 'product_button')
				)
			)
		), $settings);

		// Order by
		FLBuilder::render_settings_field('order_by', array(
			'type'          => 'select',
			'label'         => __('Order By', 'bb-powerpack'),
			'options'       => array(
				'author'         => __('Author', 'bb-powerpack'),
				'comment_count'  => __('Comment Count', 'bb-powerpack'),
				'date'           => __('Date', 'bb-powerpack'),
				'modified'       => __('Date Last Modified', 'bb-powerpack'),
				'ID'             => __('ID', 'bb-powerpack'),
				'menu_order'     => __('Menu Order', 'bb-powerpack'),
				'meta_value'     => __('Meta Value (Alphabetical)', 'bb-powerpack'),
				'meta_value_num' => __('Meta Value (Numeric)', 'bb-powerpack'),
				'rand'        	 => __('Random', 'bb-powerpack'),
				'title'          => __('Title', 'bb-powerpack'),
			),
			'toggle'		=> array(
				'meta_value' 	=> array(
					'fields'		=> array( 'order_by_meta_key' )
				),
				'meta_value_num' => array(
					'fields'		=> array( 'order_by_meta_key' )
				)
			)
		), $settings);

		// Meta Key
		FLBuilder::render_settings_field('order_by_meta_key', array(
			'type'          => 'text',
			'label'         => __('Meta Key', 'bb-powerpack'),
		), $settings);

		// Order
		FLBuilder::render_settings_field('order', array(
			'type'          => 'select',
			'label'         => __('Order', 'bb-powerpack'),
			'options'       => array(
				'DESC'          => __('Descending', 'bb-powerpack'),
				'ASC'           => __('Ascending', 'bb-powerpack'),
			)
		), $settings);

		// Offset
		FLBuilder::render_settings_field('offset', array(
			'type'          => 'text',
			'label'         => _x('Offset', 'How many posts to skip.', 'bb-powerpack'),
			'default'       => '0',
			'size'          => '4',
			'help'          => __('Skip this many posts that match the specified criteria.', 'bb-powerpack')
		), $settings);

		?>
		</table>
	</div>
	<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
		<h3 class="fl-builder-settings-title"><?php esc_html_e('Filter', 'bb-powerpack'); ?></h3>
		<?php foreach(FLBuilderLoop::post_types() as $slug => $type) : ?>
			<table class="fl-form-table fl-custom-query-filter fl-custom-query-<?php echo $slug; ?>-filter fl-loop-builder-filter fl-loop-builder-<?php echo $slug; ?>-filter" <?php if($slug == $settings->post_type) echo 'style="display:table;"'; ?>>
			<?php

			// Posts
			FLBuilder::render_settings_field('posts_' . $slug, array(
				'type'          => 'suggest',
				'action'        => 'fl_as_posts',
				'data'          => $slug,
				'label'         => $type->label,
				'help'          => sprintf(__('Enter a comma separated list of %s. Only these %s will be shown.', 'bb-powerpack'), $type->label, $type->label),
				'matching'      => true
			), $settings);

			// Taxonomies
			$taxonomies = FLBuilderLoop::taxonomies($slug);

			foreach($taxonomies as $tax_slug => $tax) {

				FLBuilder::render_settings_field('tax_' . $slug . '_' . $tax_slug, array(
					'type'          => 'suggest',
					'action'        => 'fl_as_terms',
					'data'          => $tax_slug,
					'label'         => $tax->label,
					'help'          => sprintf(__('Enter a comma separated list of %s. Only posts with these %s will be shown.', 'bb-powerpack'), $tax->label, $tax->label),
					'matching'      => true
				), $settings);
			}

			?>
			</table>
		<?php endforeach; ?>
		<table class="fl-form-table">
		<?php

		// Author
		FLBuilder::render_settings_field('users', array(
			'type'          => 'suggest',
			'action'        => 'fl_as_users',
			'label'         => __('Authors', 'bb-powerpack'),
			'help'          => __('Enter a comma separated list of authors usernames. Only posts with these authors will be shown.', 'bb-powerpack'),
			'matching'      => true
		), $settings);

		?>
		</table>
	</div>
</div>
<div id="fl-builder-settings-section-post-content" class="fl-builder-settings-section">
	<h3 class="fl-builder-settings-title"><?php esc_html_e('Content Settings', 'bb-powerpack'); ?></h3>
	<table class="fl-form-table">
		<?php
		FLBuilder::render_settings_field('show_content', array(
			'type'          => 'pp-switch',
			'label'         => __('Show Content', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'	=> array(
					'fields'	=> array('content_type')
				)
			)
		),$settings);

		FLBuilder::render_settings_field('content_type',  array(
			'type'          => 'select',
			'label'         => __('Content Type', 'bb-powerpack'),
			'default'       => 'excerpt',
			'options'       => array(
				'excerpt'       => __('Excerpt', 'bb-powerpack'),
				'content'       => __('Limited Content', 'bb-powerpack'),
				'full'          => __('Full Content', 'bb-powerpack'),
			),
			'toggle'		=> array(
				'content' 		=> array(
					'fields' 		=> array('content_length'),
				)
			)
		),$settings);

		FLBuilder::render_settings_field('content_length', array(
			'type'		=> 'text',
			'label'		=> __('Content Limit', 'bb-powerpack'),
			'help'		=> __('Number of words to be displayed from the post content.', 'bb-powerpack'),
			'default'	=> '300',
			'maxlenght'	=> 4,
			'size'		=> 5,
			'description' => __('words', 'bb-powerpack'),
		),$settings);

		FLBuilder::render_settings_field('more_link_type', array(
			'type'          => 'select',
			'label'         => __('Link Type', 'bb-powerpack'),
			'default'       => 'box',
			'options'       => array(
				'none'         => __( 'None', 'bb-powerpack' ),
				'title'         => __( 'Title', 'bb-powerpack' ),
				'thumb'         => __( 'Thumbnail', 'bb-powerpack' ),
				'title_thumb'   => __( 'Title + Thumbnail', 'bb-powerpack' ),
				'button'        => __( 'Button', 'bb-powerpack' ),
				'box'         	=> __( 'Box', 'bb-powerpack' ),
			),
			'toggle'		=> array(
				'button' 	=> array(
					'sections'	=> array('button_colors', 'button_typography'),
					'fields'	=> array('more_link_text')
				)
			)
		),$settings);

		FLBuilder::render_settings_field('more_link_text', array(
			'type'          => 'text',
			'label'         => __('Button Text', 'bb-powerpack'),
			'default'       => __('Read More', 'bb-powerpack'),
			'connections'	=> array('string'),
			'preview'		=> array(
				'type'	=> 'text',
				'selector'	=> '.pp-content-grid-more'
			)
		), $settings);


		FLBuilder::render_settings_field('post_grid_filters_display', array(
			'type'		=> 'pp-switch',
			'label'		=> __('Enable Post Filter', 'bb-powerpack'),
			'default'	=> 'no',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'	=> array(
					'fields'	=> array('post_grid_filters', 'all_filter_label'),
					'tabs'		=> array('filters_style'),
					'sections'	=> array('filter_typography')
				)
			),
			'help'	=> __('By enabling this option, post filters will be appeared on frontend.', 'bb-powerpack')
		), $settings);

		FLBuilder::render_settings_field('post_grid_filters', array(
			'type'		=> 'select',
			'label'		=> __('Select Post Filter', 'bb-powerpack'),
			'default'	=> '',
			'options'       => array()
		), $settings);

		FLBuilder::render_settings_field('all_filter_label', array(
			'type'	=> 'text',
			'label'	=> __('"All" Filter Label', 'bb-powerpack'),
			'size'	=> 8,
			'connections'	=> array('string')
		), $settings);
		?>
	</table>
</div>

<?php if ( class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' ) ) : ?>
<div id="fl-builder-settings-section-product-settings" class="fl-builder-settings-section">
	<h3 class="fl-builder-settings-title"><?php esc_html_e('Product Info', 'bb-powerpack'); ?></h3>
	<table class="fl-form-table">
		<?php
		FLBuilder::render_settings_field('product_rating', array(
			'type'          => 'pp-switch',
			'label'         => __('Product Rating', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          	=> __('Yes', 'bb-powerpack'),
				'no'         	=> __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'		=> array(
					'sections'	=> array('product_info_style'),
					'fields'	=> array('product_rating_color')
				)
			),
		), $settings);

		FLBuilder::render_settings_field('product_price', array(
			'type'          => 'pp-switch',
			'label'         => __('Product Price', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          	=> __('Yes', 'bb-powerpack'),
				'no'        	=> __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'	=> array(
					'sections'	=> array('product_info_style'),
					'fields'	=> array('product_price_color')
				)
			),
		), $settings);

		FLBuilder::render_settings_field('product_button', array(
			'type'          => 'pp-switch',
			'label'         => __('Add to Cart Button', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          	=> __('Yes', 'bb-powerpack'),
				'no'         	=> __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'		=> array(
					'sections'	=> array('button_colors', 'button_typography'),
				)
			)
		), $settings);

		?>
	</table>
</div>
<?php endif; ?>

<div id="fl-builder-settings-section-image-settings" class="fl-builder-settings-section">
	<h3 class="fl-builder-settings-title"><?php esc_html_e('Featured Image', 'bb-powerpack'); ?></h3>
	<table class="fl-form-table">
		<?php
		FLBuilder::render_settings_field('show_image', array(
			'type'          => 'pp-switch',
			'label'         => __('Featured Image', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'	=> array(
					'fields'	=> array('image_thumb_size', 'image_thumb_crop')
				)
			)
		),$settings);

		FLBuilder::render_settings_field('image_thumb_size', array(
			'type'          => 'photo-sizes',
			'label'         => __( 'Image Size', 'bb-powerpack' ),
			'default'       => 'large',
		),$settings);

		FLBuilder::render_settings_field('image_thumb_crop', array(
			'type'          => 'select',
			'label'         => __( 'Image Crop', 'bb-powerpack' ),
			'default'       => '',
			'options'       => array(
				''              => _x( 'None', 'Photo Crop.', 'bb-powerpack' ),
				'landscape'     => __( 'Landscape', 'bb-powerpack' ),
				'panorama'      => __( 'Panorama', 'bb-powerpack' ),
				'portrait'      => __( 'Portrait', 'bb-powerpack' ),
				'square'        => __( 'Square', 'bb-powerpack' ),
				'circle'        => __( 'Circle', 'bb-powerpack' )
			)
		),$settings);
		?>
	</table>
</div>

<div id="fl-builder-settings-section-meta-settings" class="fl-builder-settings-section">
	<h3 class="fl-builder-settings-title"><?php esc_html_e('Meta Settings', 'bb-powerpack'); ?></h3>
	<table class="fl-form-table">
		<?php

		FLBuilder::render_settings_field('show_author', array(
			'type'          => 'pp-switch',
			'label'         => __('Author', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
		),$settings);

		FLBuilder::render_settings_field('show_date',  array(
			'type'          => 'pp-switch',
			'label'         => __('Date', 'bb-powerpack'),
			'default'       => 'yes',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
		),$settings);

		FLBuilder::render_settings_field('show_categories', array(
			'type'			=> 'pp-switch',
			'label'			=> __('Show Taxonomies', 'bb-powerpack'),
			'default'		=> 'no',
			'options'       => array(
				'yes'          => __('Yes', 'bb-powerpack'),
				'no'         => __('No', 'bb-powerpack'),
			),
			'toggle'	=> array(
				'yes'	=> array(
					'fields'	=> array('post_taxonomies')
				)
			)
		),$settings);

		FLBuilder::render_settings_field('post_taxonomies', array(
			'type'		=> 'select',
			'label'		=> __('Select Taxonomy', 'bb-powerpack'),
			'default'	=> '',
			'options'   => array()
		), $settings);

		// Separators
		FLBuilder::render_settings_field('meta_separator', array(
			'type'          => 'text',
			'label'         => __('Meta Separator', 'bb-powerpack'),
			'default'       => ' | ',
			'size'			=> 5
		), $settings);
		?>
	</table>
</div>

<?php
do_action( 'pp_cg_loop_settings_after_form', $settings ); // e.g Add custom FLBuilder::render_settings_field()
?>

<script type="text/javascript">
	;(function($) {
		$('.fl-builder-pp-content-grid-settings select[name="post_type"]').on('change', function() {
			var post_type_slug = $(this).val();
			var post_grid_filters = $('.fl-builder-pp-content-grid-settings select[name="post_grid_filters"]');
			var post_taxonomies = $('.fl-builder-pp-content-grid-settings select[name="post_taxonomies"]');
			var selected_filter = '<?php echo $settings->post_grid_filters; ?>';
			var selected_taxonomy = '<?php echo $settings->post_taxonomies; ?>';
			$.ajax({
				type: 'post',
				data: {action: 'get_post_tax', post_type_slug: post_type_slug},
				url: ajaxurl,
				success: function(res) {
					if ( res !== 'undefined' || res !== '' ) {
						post_grid_filters.html(res);
						post_grid_filters.find('option[value="'+selected_filter+'"]').attr('selected', 'selected');
						post_taxonomies.html(res);
						post_taxonomies.find('option[value="'+selected_taxonomy+'"]').attr('selected', 'selected');
					}
				}
			});
		});
	})(jQuery);
</script>
