<?php

/**
 * @class PPCustomGridModule
 */
class PPCustomGridModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          	=> __('Custom Grid', 'bb-powerpack'),
			'description'   	=> __('Display a grid of your WordPress posts.', 'bb-powerpack'),
			'group'         	=> pp_get_modules_group(),
			'category'			=> pp_get_modules_cat( 'content' ),
            'dir'               => BB_POWERPACK_DIR . 'modules/pp-custom-grid/',
            'url'               => BB_POWERPACK_URL . 'modules/pp-custom-grid/',
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
			'icon'				=> 'schedule.svg',
		));

		add_filter( 'fl_builder_register_settings_form',   				__CLASS__ . '::presets_form_fields', 10, 2 );
		add_filter( 'fl_builder_after_control_pp-hidden-textarea',   	__CLASS__ . '::after_control', 10, 4 );
		add_filter( 'fl_builder_render_css',               				__CLASS__ . '::custom_grid_css', 10, 2 );
		//add_action( 'fl_page_data_add_properties', 						__CLASS__ . '::load_page_data' );
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts()
	{
		$this->add_js('jquery-imagesloaded');

		if(FLBuilderModel::is_builder_active()) {
			$this->add_css('font-awesome');
		}
		if(FLBuilderModel::is_builder_active() || ! $this->settings->match_height) {
			$this->add_js('jquery-masonry');
		}
		if(FLBuilderModel::is_builder_active() || $this->settings->pagination == 'scroll') {
			$this->add_js('jquery-infinitescroll');
		}

		// Jetpack sharing has settings to enable sharing on posts, post types and pages.
		// If pages are disabled then jetpack will still show the share button in this module
		// but will *not* enqueue its scripts and fonts.
		// This filter forces jetpack to enqueue the sharing scripts.
		add_filter( 'sharing_enqueue_scripts', '__return_true' );
	}

	/**
	 * Returns the slug for the posts layout.
	 *
	 * @since 1.3
	 * @return string
	 */
	public function get_layout_slug()
	{
		return 'grid';
	}

	/**
	 * Renders the CSS class for each post item.
	 *
	 * @since 1.3
	 * @return void
	 */
	public function render_post_class()
	{
		$settings   = $this->settings;
		$layout     = $this->get_layout_slug();
		$show_image = has_post_thumbnail();
		$classes    = array( 'pp-custom-' . $layout . '-post' );

		$classes[] = 'pp-custom-align-' . $settings->post_align;
		$classes[] = 'pp-custom-grid-preset-' . $settings->preset;

		post_class( apply_filters( 'pp_custom_grid_module_classes', $classes, $settings ) );
	}

	/**
	 * Renders the_content for a post.
	 *
	 * @since 1.3
	 * @return void
	 */
	public function render_content()
	{
		ob_start();
		the_content();
		$content = ob_get_clean();

		if ( ! empty( $this->settings->content_length ) ) {
			$content = wp_trim_words( $content, $this->settings->content_length, '...' );
		}

		echo $content;
	}

	/**
	 * Renders the_excerpt for a post.
	 *
	 * @since 1.3
	 * @return void
	 */
	public function render_excerpt()
	{
		if ( ! empty( $this->settings->content_length ) ) {
			add_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ) );
		}

		the_excerpt();

		if ( ! empty( $this->settings->content_length ) ) {
			remove_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ) );
		}
	}

	/**
	 * Renders the excerpt for a post.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function set_custom_excerpt_length( $length )
	{
		return $this->settings->content_length;
	}

	/**
	 * Renders the schema structured data for the current
	 * post in the loop.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function schema_meta()
	{
		// General Schema Meta
		echo '<meta itemscope itemprop="mainEntityOfPage" itemtype="http://schema.org/WebPage" itemid="' . esc_url( get_permalink() ) . '" content="' . the_title_attribute( array('echo' => false) ) . '" />';
		echo '<meta itemprop="datePublished" content="' . get_the_time('Y-m-d') . '" />';
		echo '<meta itemprop="dateModified" content="' . get_the_modified_date('Y-m-d') . '" />';

		// Publisher Schema Meta
		echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
		echo '<meta itemprop="name" content="' . get_bloginfo( 'name' ) . '">';

		if ( class_exists( 'FLTheme' ) && 'image' == FLTheme::get_setting( 'fl-logo-type' ) ) {
			echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
			echo '<meta itemprop="url" content="' . FLTheme::get_setting( 'fl-logo-image' ) . '">';
			echo '</div>';
		}

		echo '</div>';

		// Author Schema Meta
		echo '<div itemscope itemprop="author" itemtype="http://schema.org/Person">';
		echo '<meta itemprop="url" content="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" />';
		echo '<meta itemprop="name" content="' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '" />';
		echo '</div>';

		// Image Schema Meta
		if(has_post_thumbnail()) {

			$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');

			if ( is_array( $image ) ) {
				echo '<div itemscope itemprop="image" itemtype="http://schema.org/ImageObject">';
				echo '<meta itemprop="url" content="' . $image[0] . '" />';
				echo '<meta itemprop="width" content="' . $image[1] . '" />';
				echo '<meta itemprop="height" content="' . $image[2] . '" />';
				echo '</div>';
			}
		}

		// Comment Schema Meta
		echo '<div itemprop="interactionStatistic" itemscope itemtype="http://schema.org/InteractionCounter">';
		echo '<meta itemprop="interactionType" content="http://schema.org/CommentAction" />';
		echo '<meta itemprop="userInteractionCount" content="' . wp_count_comments(get_the_ID())->approved . '" />';
		echo '</div>';
	}

	/**
	 * Renders the schema itemtype for the current
	 * post in the loop.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function schema_itemtype()
	{
		global $post;

		if ( ! is_object( $post ) || ! isset( $post->post_type ) || 'post' != $post->post_type ) {
			echo 'http://schema.org/CreativeWork';
		}
		else {
			echo 'http://schema.org/BlogPosting';
		}
	}

	/**
	 * Get presets directory path.
	 *
	 * @since 1.2.7
	 * @param string  $preset
	 * @return string
	 */
	static public function get_preset_dir( $preset = '' )
	{
		$presets_dir = BB_POWERPACK_DIR . 'modules/pp-custom-grid/includes/presets/';

		if ( empty( $preset ) ) {
			return $presets_dir;
		}
		else {
			return $presets_dir . $preset . '/';
		}
	}

	/**
	 * Get presets data from file.
	 *
	 * @since 1.2.7
	 * @param string  $preset
	 * @param int  $id	Preset ID or file number.
	 * @param string  $type	HTML or CSS.
	 * @return mixed
	 */
	static public function get_preset_data( $preset, $id, $type )
	{
		if ( ! $preset || empty( $preset ) ) {
			return;
		}
		if ( ! $id || empty( $id ) ) {
			return;
		}
		if ( ! $type || empty( $type ) || ! in_array( $type, array( 'html', 'css' ) ) ) {
			return;
		}

		$preset_dir = self::get_preset_dir( $preset );
		$preset_file = $preset_dir . $preset . '-' . $id . '-' . $type . '.php';

		if ( file_exists( $preset_file ) ) {
			return file_get_contents( $preset_file );
		}
	}

	/**
	 * Get presets default data from file.
	 *
	 * @since 1.2.7
	 * @param string  $preset
	 * @param int  $id	Preset ID or file number.
	 * @param string  $type	HTML or CSS.
	 * @return mixed
	 */
	static public function get_preset_default( $preset, $id, $type )
	{
		$data = self::get_preset_data( $preset, $id, $type );

		if ( $data && ! empty( $data ) ) {
			// JSON encode the value and fix encoding conflicts.
			$data = str_replace( "'", '&#39;', json_encode( $data ) );
			$data = str_replace( '<wbr \/>', '<wbr>', $data );
		} else {
			$data = '';
		}

		return $data;
	}

	/**
	 * Get all presets by its type.
	 *
	 * @since 1.2.7
	 * @param string  $type
	 * @return array
	 */
	static public function get_presets( $type = 'post' )
	{
		$presets = array(
			'post' => array(
				'post_1'		=> __('Post 1', 'bb-powerpack'),
				'post_2'		=> __('Post 2', 'bb-powerpack'),
				'post_3'		=> __('Post 3', 'bb-powerpack'),
				'post_4'		=> __('Post 4', 'bb-powerpack'),
				'post_5'		=> __('Post 5', 'bb-powerpack'),
			),
			'woocommerce' => array(
				'woo_1'			=> 'WooCommerce 1',
				'woo_2'			=> 'WooCommerce 2',
				'woo_3'			=> 'WooCommerce 3',
			),
			'edd' => array(
				'edd_1'			=> 'EDD 1',
				'edd_2'			=> 'EDD 2',
			)
		);


		if ( isset( $presets[$type] ) ) {
			return $presets[$type];
		}

		return $presets;
	}

	/**
	 * Create options for preset field.
	 *
	 * @since 1.2.7
	 * @return array
	 */
	static public function get_presets_options()
	{
		// Posts and Custom Posts.
		$options = array(
			'optgroup-1'	=> array(
				'label'			=> __('Post', 'bb-powerpack'),
				'options'		=> self::get_presets( 'post' )
			),
		);

		// WooCommerce support.
		if ( class_exists( 'WooCommerce' ) ) {
			$options['optgroup-2'] = array(
				'label'			=> 'WooCommerce',
				'options'		=> self::get_presets( 'woocommerce' )
			);
		}

		// EDD support.
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			$options['optgroup-3'] = array(
				'label'			=> 'Easy Digital Downloads',
				'options'		=> self::get_presets( 'edd' )
			);
		}

		return $options;
	}

	/**
	 * Adds the custom code settings for custom post
	 * module layouts.
	 *
	 * @since 1.2.7
	 * @param array  $form
	 * @param string $slug
	 * @return array
	 */
	static public function presets_form_fields( $form, $slug )
	{
		if ( 'pp-custom-grid' != $slug ) {
			return $form;
		}

		$toggles = array();

		foreach ( self::get_presets( 'post' ) as $preset_name => $preset ) {
			$form['layout']['sections']['general']['fields'][$preset_name . '_preset'] = array(
				'type'          => 'form',
				'label'         => __( 'Preset', 'bb-powerpack' ),
				'form'          => $preset_name . '_preset',
				'preview_text'  => null,
				'multiple'		=> false,
			);

			$toggles[$preset_name] = array(
				'fields'	=> array( $preset_name . '_preset' ),
			);
		}

		if ( class_exists( 'WooCommerce' ) ) {
			foreach ( self::get_presets( 'woocommerce' ) as $preset_name => $preset ) {
				$form['layout']['sections']['general']['fields'][$preset_name . '_preset'] = array(
					'type'          => 'form',
					'label'         => __( 'Preset', 'bb-powerpack' ),
					'form'          => $preset_name . '_preset',
					'preview_text'  => null,
					'multiple'		=> false,
				);

				$toggles[$preset_name] = array(
					'fields'	=> array( $preset_name . '_preset' ),
				);
			}
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			foreach ( self::get_presets( 'edd' ) as $preset_name => $preset ) {
				$form['layout']['sections']['general']['fields'][$preset_name . '_preset'] = array(
					'type'          => 'form',
					'label'         => __( 'Preset', 'bb-powerpack' ),
					'form'          => $preset_name . '_preset',
					'preview_text'  => null,
					'multiple'		=> false,
				);

				$toggles[$preset_name] = array(
					'fields'	=> array( $preset_name . '_preset' ),
				);
			}
		}

		$form['layout']['sections']['general']['fields']['preset']['toggle'] = $toggles;

		return $form;
	}

	/**
	 * Renders a custom field after a specific field.
	 *
	 * @since 1.2.7
	 * @param string $name
	 * @param mixed  $value
	 * @param array  $field
	 * @param object  $settings
	 * @return void
	 */
	static public function after_control( $name, $value, $field, $settings )
	{
		if ( $name == 'original_html' ) {
			?>
			<a href="javascript:void(0)" class="pp-custom-grid-reset-html"><?php esc_html_e('Restore Default'); ?></a>
			<script>
			jQuery('.pp-custom-grid-reset-html').on('click', function(e) {
				e.preventDefault();
				var res = confirm( "<?php esc_html_e('Original HTML will be restored once you click on OK.', 'bb-powerpack'); ?>" );
				if ( res === true ) {
					var original_html = JSON.parse( jQuery('#fl-field-original_html textarea').val() );
					var editor = ace.edit( jQuery('#fl-field-html .ace_editor')[0] );
					editor.getSession().setValue(original_html);
				}
			});
			</script>
			<?php
		}
		elseif ( $name == 'original_css' ) {
			?>
			<a href="javascript:void(0)" class="pp-custom-grid-reset-css"><?php esc_html_e('Restore Default'); ?></a>
			<script>
			jQuery('.pp-custom-grid-reset-css').on('click', function(e) {
				e.preventDefault();
				var res = confirm( "<?php esc_html_e('Original CSS will be restored once you click on OK.', 'bb-powerpack'); ?>" );
				if ( res === true ) {
					var original_css = JSON.parse( jQuery('#fl-field-original_css textarea').val() );
					var editor = ace.edit( jQuery('#fl-field-css .ace_editor')[0] );
					editor.getSession().setValue(original_css);
				}
			});
			</script>
			<?php
		}
		else {
			return;
		}
	}

	/**
	 * Renders custom CSS for the custom grid module.
	 *
	 * @since 1.2.7
	 * @param string $css
	 * @param array  $nodes
	 * @return string
	 */
	static public function custom_grid_css( $css, $nodes ) {
		if ( ! class_exists( 'lessc' ) ) {
			require_once FL_THEME_BUILDER_DIR . 'classes/class-lessc.php';
		}

		foreach ( $nodes['modules'] as $module ) {

			if ( ! is_object( $module ) ) {
				continue;
			} elseif ( 'pp-custom-grid' != $module->settings->type ) {
				continue;
			}

			$preset = $module->settings->preset;
			$key	= $preset . '_preset';

			try {
				$less    = new lessc;
				$custom  = '.fl-node-' . $module->node . ' .pp-custom-grid-preset-' . $preset . ' { ';
				$custom .= $module->settings->{$key}->css;
				$custom .= ' }';
				$css    .= $less->compile( $custom );
			} catch ( Exception $e ) {
				$css .= $module->settings->{$key}->css;
			}
		}

		return $css;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPCustomGridModule', array(
	'layout'        => array(
		'title'         => __('Layout', 'bb-powerpack'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'preset'		=> array(
						'type'			=> 'select',
						'label'			=> __('Choose Preset', 'bb-powerpack'),
						'default'		=> 'post_1',
						'options'		=> PPCustomGridModule::get_presets_options(),
					),
				)
			),
			'posts'         => array(
				'title'         => __('Posts', 'bb-powerpack'),
				'fields'        => array(
					'match_height'  => array(
						'type'          => 'pp-switch',
						'label'         => __('Equal Heights', 'bb-powerpack'),
						'default'       => '0',
						'options'       => array(
							'1'             => __('Yes', 'bb-powerpack'),
							'0'             => __('No', 'bb-powerpack')
						),
						'toggle'		=> array(
							'1'				=> array(
								'fields'		=> array('post_columns')
							),
							'0'				=> array(
								'fields'		=> array('post_width')
							)
						)
					),
					'post_width'    => array(
						'type'          => 'text',
						'label'         => __('Post Width', 'bb-powerpack'),
						'default'       => '300',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
					'post_columns'  => array(
						'type'          => 'unit',
						'label'         => __( 'Columns', 'bb-powerpack' ),
						'responsive'  => array(
							'default' 	  => array(
								'default'    => '3',
								'medium'     => '2',
								'responsive' => '1',
							)
						)
					),
					'post_spacing' => array(
						'type'          => 'text',
						'label'         => __('Post Spacing', 'bb-powerpack'),
						'default'       => '30',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
					'post_align'    => array(
						'type'          => 'select',
						'label'         => __('Post Alignment', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       => __('Default', 'bb-powerpack'),
							'left'          => __('Left', 'bb-powerpack'),
							'center'        => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack')
						)
					),
				)
			),
		)
	),
	'content'   => array(
		'title'         => __('Content', 'bb-powerpack'),
		'file'          => FL_BUILDER_DIR . 'includes/loop-settings.php',
	),
	'pagination' => array(
		'title'      => __( 'Pagination', 'bb-powerpack' ),
		'sections'   => array(
			'pagination'   => array(
				'title'         => __('Pagination', 'bb-powerpack'),
				'fields'        => array(
					'pagination'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Pagination Style', 'bb-powerpack'),
						'default'       => 'numbers',
						'options'       => array(
							'numbers'       => __('Numbers', 'bb-powerpack'),
							'scroll'        => __('Scroll', 'bb-powerpack'),
							'none'          => _x( 'None', 'Pagination style.', 'bb-powerpack' ),
						)
					),
					'posts_per_page' => array(
						'type'          => 'text',
						'label'         => __('Posts Per Page', 'bb-powerpack'),
						'default'       => '10',
						'size'          => '4'
					),
					'no_results_message' => array(
						'type' 				=> 'text',
						'label'				=> __('No Results Message', 'bb-powerpack'),
						'default'			=> __('Sorry, we couldn\'t find any posts. Please try a different search.', 'bb-powerpack')
					),
					'show_search'    => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Search', 'bb-powerpack'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'bb-powerpack'),
							'0'             => __('Hide', 'bb-powerpack')
						),
						'help'          => __( 'Shows the search form if no posts are found.', 'bb-powerpack' )
					)
				)
			)
		)
	),
	'style'         => array(
		'title'         => __('Style', 'bb-powerpack'),
		'sections'      => array(
			'post_style'    => array(
				'title'         => __('Posts', 'bb-powerpack'),
				'fields'        => array(
					'bg_color'      => array(
						'type'          => 'color',
						'label'         => __('Background Color', 'bb-powerpack'),
						'show_alpha'	=> true,
						'show_reset'    => true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-custom-grid-post',
							'property'		=> 'background-color'
						)
					),
					'border_type'   => array(
						'type'          => 'select',
						'label'         => __('Border Type', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       => _x( 'Default', 'Border type.', 'bb-powerpack' ),
							'none'          => _x( 'None', 'Border type.', 'bb-powerpack' ),
							'solid'         => _x( 'Solid', 'Border type.', 'bb-powerpack' ),
							'dashed'        => _x( 'Dashed', 'Border type.', 'bb-powerpack' ),
							'dotted'        => _x( 'Dotted', 'Border type.', 'bb-powerpack' ),
							'double'        => _x( 'Double', 'Border type.', 'bb-powerpack' )
						),
						'toggle'        => array(
							'solid'         => array(
								'fields'        => array('border_color', 'border_size')
							),
							'dashed'        => array(
								'fields'        => array('border_color', 'border_size')
							),
							'dotted'        => array(
								'fields'        => array('border_color', 'border_size')
							),
							'double'        => array(
								'fields'        => array('border_color', 'border_size')
							)
						)
					),
					'border_color'  => array(
						'type'          => 'color',
						'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true
					),
					'border_size'  => array(
						'type'          => 'text',
						'label'         => __('Border Size', 'bb-powerpack'),
						'default'       => '1',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
					'post_shadow'	=> array(
                        'type'         	=> 'pp-switch',
                        'label'         => __('Enable Box Shadow', 'bb-powerpack'),
                        'default'       => 'no',
                        'options'       => array(
                            '1'          	=> __('Yes', 'bb-powerpack'),
                            '0'            	=> __('No', 'bb-powerpack'),
                        ),
                        'toggle'	=>  array(
                            '1'   		=> array(
                                'fields'    => array('post_shadow_options', 'post_shadow_color')
                            )
                        )
                    ),
                    'post_shadow_options' => array(
						'type'              => 'pp-multitext',
						'label'             => __('Box Shadow', 'bb-powerpack'),
						'default'           => array(
							'horizontal'		=> 0,
							'vertical'			=> 0,
							'blur'				=> 10,
							'spread'			=> 0
						),
						'options'			=> array(
							'horizontal'		=> array(
								'placeholder'		=> __('Horizontal', 'bb-powerpack'),
								'tooltip'			=> __('Horizontal', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-h'
							),
							'vertical'			=> array(
								'placeholder'		=> __('Vertical', 'bb-powerpack'),
								'tooltip'			=> __('Vertical', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-v'
							),
							'blur'				=> array(
								'placeholder'		=> __('Blur', 'bb-powerpack'),
								'tooltip'			=> __('Blur', 'bb-powerpack'),
								'icon'				=> 'fa-circle-o'
							),
							'spread'			=> array(
								'placeholder'		=> __('Spread', 'bb-powerpack'),
								'tooltip'			=> __('Spread', 'bb-powerpack'),
								'icon'				=> 'fa-paint-brush'
							),
						)
					),
                    'post_shadow_color' => array(
                        'type'              => 'color',
                        'label'             => __('Box Shadow Color', 'bb-powerpack'),
                        'default'           => 'dedede',
						'show_alpha'		=> true
                    ),
				)
			),
			'pagination_style'	=> array(
				'title'         	=> __('Pagination', 'bb-powerpack'),
				'fields'        	=> array(
					'pagination_bg_color'	=> array(
						'type'					=> 'color',
						'label'					=> __('Background Color', 'bb-powerpack'),
						'default'				=> '',
						'show_alpha'			=> true,
						'show_reset'    		=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'background'
						)
					),
					'pagination_bg_color_h'	=> array(
						'type'					=> 'color',
						'label'					=> __('Background Hover Color', 'bb-powerpack'),
						'default'				=> '',
						'show_alpha'			=> true,
						'show_reset'    		=> true,
						'preview'				=> array(
							'type'					=> 'none',
						)
					),
					'pagination_text_color'	=> array(
						'type'					=> 'color',
						'label'					=> __('Text Color', 'bb-powerpack'),
						'default'				=> '',
						'show_reset'    		=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'color'
						)
					),
					'pagination_text_color_h'	=> array(
						'type'						=> 'color',
						'label'						=> __('Text Hover Color', 'bb-powerpack'),
						'default'					=> '',
						'show_reset'    		=> true,
						'preview'					=> array(
							'type'						=> 'none',
						)
					),
					'pagination_border_type'   => array(
						'type'          => 'select',
						'label'         => __('Border Type', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       => _x( 'Default', 'Border type.', 'bb-powerpack' ),
							'none'          => _x( 'None', 'Border type.', 'bb-powerpack' ),
							'solid'         => _x( 'Solid', 'Border type.', 'bb-powerpack' ),
							'dashed'        => _x( 'Dashed', 'Border type.', 'bb-powerpack' ),
							'dotted'        => _x( 'Dotted', 'Border type.', 'bb-powerpack' ),
							'double'        => _x( 'Double', 'Border type.', 'bb-powerpack' )
						),
						'toggle'        => array(
							'solid'         => array(
								'fields'        => array('pagination_border_color', 'pagination_border_size')
							),
							'dashed'        => array(
								'fields'        => array('pagination_border_color', 'pagination_border_size')
							),
							'dotted'        => array(
								'fields'        => array('pagination_border_color', 'pagination_border_size')
							),
							'double'        => array(
								'fields'        => array('pagination_border_color', 'pagination_border_size')
							)
						),
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'border-style',
						)
					),
					'pagination_border_color'  => array(
						'type'          => 'color',
						'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'border-color'
						)
					),
					'pagination_border_size'  => array(
						'type'          => 'text',
						'label'         => __('Border Size', 'bb-powerpack'),
						'default'       => '1',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px',
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'border-width',
							'unit'					=> 'px'
						)
					),
					'pagination_border_radius'  => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'default'       => '',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px',
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-custom-grid-pagination li a.page-numbers, .pp-custom-grid-pagination li span.page-numbers',
							'property'				=> 'border-radius',
							'unit'					=> 'px'
						)
					),
				)
			)
		)
	),
));

include BB_POWERPACK_DIR . 'modules/pp-custom-grid/includes/settings-form.php';
