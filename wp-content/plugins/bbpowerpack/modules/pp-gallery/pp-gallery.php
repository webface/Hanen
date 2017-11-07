<?php
/**
 * @class PPGalleryModule
 */
class PPGalleryModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
			'name'          => __('Photo Gallery', 'bb-powerpack'),
            'description'   => __('A module for photo gallery.', 'bb-powerpack'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-gallery/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-gallery/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true
        ));

    }

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts()
	{
		$this->add_js('jquery-masonry');

		$this->add_js( 'isotope', $this->url . 'js/isotope.pkgd.min.js', array('jquery'), BB_POWERPACK_VER, true );

		$this->add_css( 'fancybox-style', $this->url . 'css/jquery.fancybox.css', BB_POWERPACK_VER );
		$this->add_js( 'fancybox-script', $this->url . 'js/jquery.fancybox.js', array('jquery'), BB_POWERPACK_VER, true );

		$this->add_css( 'fancybox-thumbs', $this->url . 'helpers/jquery.fancybox-thumbs.css', BB_POWERPACK_VER );
		$this->add_js( 'fancybox-thumbs-script', $this->url . 'helpers/jquery.fancybox-thumbs.js', array('jquery'), BB_POWERPACK_VER, true );
		$this->add_js( 'fancybox-media-script', $this->url . 'helpers/jquery.fancybox-media.js', array('jquery'), BB_POWERPACK_VER, true );
	}


	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update($settings)
	{
		// Cache the photo data if using the WordPress media library.
		$settings->photo_data = $this->get_wordpress_photos();

		return $settings;
	}

	/**
	 * @method get_photos
	 */
	public function get_photos()
	{
		$default_order 	= $this->get_wordpress_photos();
		$photos_id 		= array();
		// WordPress

		if ( $this->settings->photo_order == 'random' && is_array( $default_order )) {

			$keys = array_keys( $default_order );
			shuffle($keys);

			foreach ($keys as $key) {
				$photos_id[$key] = $default_order[$key];
			}

		}else{
			$photos_id = $default_order;
		}

		return $photos_id;

	}

	/**
	 * @method get_wordpress_photos
	 */
	public function get_wordpress_photos()
	{
		$photos     = array();
		$ids        = $this->settings->gallery_photos;
		$medium_w   = get_option('medium_size_w');
		$large_w    = get_option('large_size_w');

		/* Template Cache */
		$photo_from_template = false;
		$photo_attachment_data = false;

		if(empty($this->settings->gallery_photos)) {
			return $photos;
		}

		/* Check if all photos are available on host */
		foreach ($ids as $id) {
			$photo_attachment_data[$id] = FLBuilderPhoto::get_attachment_data($id);

			if ( ! $photo_attachment_data[$id] ) {
				$photo_from_template = true;
			}

		}

		foreach($ids as $id) {

			$photo = $photo_attachment_data[$id];

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo && $photo_from_template ) {

				if ( ! isset( $this->settings->photo_data ) ) {
					continue;
				}
				else if ( is_array( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data[ $id ];
				}
				else if ( is_object( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data->{$id};
				}
				else {
					continue;
				}
			}


			// Only use photos who have the sizes object.
			if(isset($photo->sizes)) {

				$data = new stdClass();

				// Photo data object
				$data->id = $id;
				$data->alt = $photo->alt;
				$data->caption = $photo->caption;
				$data->description = $photo->description;
				$data->title = $photo->title;

				// Collage photo src
				if($this->settings->gallery_layout == 'masonry') {

					if($this->settings->photo_size == 'thumbnail' && isset($photo->sizes->thumbnail)) {
						$data->src = $photo->sizes->thumbnail->url;
					}
					elseif($this->settings->photo_size == 'medium' && isset($photo->sizes->medium)) {
						$data->src = $photo->sizes->medium->url;
					}
					else {
						$data->src = $photo->sizes->full->url;
					}
				}

				// Grid photo src
				else {

					if($this->settings->photo_size == 'thumbnail' && isset($photo->sizes->thumbnail)) {
						$data->src = $photo->sizes->thumbnail->url;
					}
					elseif($this->settings->photo_size == 'medium' && isset($photo->sizes->medium)) {
						$data->src = $photo->sizes->medium->url;
					}
					else {
						$data->src = $photo->sizes->full->url;
					}
				}

				// Photo Link
				if(isset($photo->sizes->large)) {
					$data->link = $photo->sizes->large->url;
				}
				else {
					$data->link = $photo->sizes->full->url;
				}

				/* Add Custom field attachment data to object */
	 			$cta_link = get_post_meta( $id, 'gallery_external_link', true );
				if(!empty($cta_link) && $this->settings->click_action == 'custom-link' ) {
		 			$data->cta_link = $cta_link;
				}

				$photos[$id] = $data;
			}

		}

		return $photos;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPGalleryModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
					'gallery_layout'        => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Layout', 'bb-powerpack' ),
						'default'       => 'grid',
						'options'       => array(
							'grid'          => __( 'Grid', 'bb-powerpack' ),
							'masonry'       => __( 'Masonry', 'bb-powerpack' ),
						),
					),
					'gallery_photos' => array(
					    'type'          => 'multiple-photos',
					    'label'         => __( 'Photos', 'bb-powerpack' ),
                        'connections'  => array('photo')
					),
					'photo_size'        => array(
						'type'          => 'photo-sizes',
						'label'         => __('Image Size', 'bb-powerpack'),
						'default'       => 'medium',
						'options'       => array(
							'thumb'          	=> __( 'Thumbnail', 'bb-powerpack' ),
							'medium'       		=> __( 'Medium', 'bb-powerpack' ),
							'full'       		=> __( 'Full', 'bb-powerpack' ),
						),
					),
					'photo_order'        => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Display Order', 'bb-powerpack' ),
						'default'       => 'normal',
						'options'       => array(
							'normal'     	=> __( 'Normal', 'bb-powerpack'),
							'random' 		=> __( 'Random', 'bb-powerpack' )
						),
					),
					'show_captions' => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Captions', 'bb-powerpack'),
						'default'       => 'no',
						'options'       => array(
							'no'             => __('Never', 'bb-powerpack'),
							'hover'         => __('On Hover', 'bb-powerpack'),
							'below'         => __('Always', 'bb-powerpack')
						),
						'toggle'	=> array(
							'hover'	=> array(
								'tabs'		=> array('caption_settings'),
							),
							'below'	=> array(
								'tabs'	=> array('caption_settings'),
								'section'	=> array('caption_style')
							)
						),
						'help'          => __('The caption pulls from whatever text you put in the caption area in the media manager for each image.', 'bb-powerpack')
					),
					'click_action'  => array(
						'type'          => 'pp-switch',
						'label'         => __('Click Action', 'bb-powerpack'),
						'default'       => 'lightbox',
						'options'       => array(
							'none'          => __( 'None', 'Click action.', 'bb-powerpack' ),
							'lightbox'      => __('Lightbox', 'bb-powerpack'),
							'custom-link'   => __('Custom URL', 'bb-powerpack')
						),
						'toggle'	=> array(
							'lightbox'	=> array(
								'fields'	=> array('show_lightbox_thumb'),
								'sections'	=> array('lightbox_style'),
							),
							'custom-link'	=> array(
								'fields'	=> array('custom_link_target')
							)
						),
						'preview'       => array(
							'type'          => 'none'
						)
					),
					'show_lightbox_thumb' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Show Thumbnail Navigation?', 'bb-powerpack'),
						'default'	=> 'no',
						'options'	=> array(
							'yes'		=> __('Yes', 'bb-powerpack'),
							'no'		=> __('No', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'none'
						)
					),
					'custom_link_target' => array(
						'type'		=> 'select',
						'label'		=> __('Link Target', 'bb-powerpack'),
						'default'	=> '_self',
						'options'	=> array(
							'_self'		=> __('Same Window', 'bb-powerpack'),
							'_blank'	=> __('New Window', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'none'
						)
					)
                )
            ),
			'overlay_settings'	=> array(
				'title'	=> __( 'Overlay', 'bb-powerpack' ),
				'fields'	=> array(
					'overlay_effects' => array(
						'type'          => 'select',
						'label'         => __('Overlay Effect', 'bb-powerpack'),
						'default'       => 'none',
						'options'       => array(
							'none' 			=> __('None', 'bb-powerpack'),
							'fade' 			=> __('Fade', 'bb-powerpack'),
							'from-left'		=> __('Overlay From Left', 'bb-powerpack'),
							'from-right'	=> __('Overlay From Right', 'bb-powerpack'),
							'from-top'		=> __('Overlay From Top', 'bb-powerpack'),
							'from-bottom'	=> __('Overlay From Bottom', 'bb-powerpack'),
							'framed'		=> __('Framed', 'bb-powerpack'),
						),
						'toggle'		=> array(
							'from-left'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed')
							),
							'from-right'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed')
							),
							'from-top'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed')
							),
							'from-bottom'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed')
							),
							'fade'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed')
							),
							'framed'	=> array(
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_animation_speed', 'overlay_border_width', 'overlay_border_color', 'overlay_spacing')
							),
						),
						'preview'	=> 'none',
					),
					'overlay_animation_speed' => array(
						'type'          => 'text',
						'label'         => __('Animation Speed', 'bb-powerpack'),
						'description'   => __('ms', 'bb-powerpack'),
						'default'       => 300,
						'size'          => 5,
					),
					'icon' => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Icon?', 'bb-powerpack'),
						'default'       => '0',
						'options'       => array(
							'1'				=> __('Yes', 'bb-powerpack'),
							'0' 			=> __('No', 'bb-powerpack'),
						),
						'toggle'		=> array(
							'1'	=> array(
								'sections' => array( 'icon_style' ),
								'fields'	=> array('overlay_icon')
							),
						),
						'preview'	=> 'none',
					),
					'overlay_icon'	=> array(
						'type'			=> 'icon',
						'label'			=> __('Icon', 'bb-powerpack'),
						'preview'		=> 'none',
						'show_remove' => true
					),
				)
			),
			'gallery_columns'	=> array(
				'title'	=> __( 'Columns Settings', 'bb-powerpack' ),
				'fields'	=> array(
					'photo_grid_count'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Number of Columns', 'bb-powerpack'),
                        'size'          => '5',
						'responsive' => array(
							'placeholder' => array(
								'default' => '4',
								'medium' => '2',
								'responsive' => '1',
							),
						),
                    ),
					'photo_spacing' => array(
						'type'          => 'text',
						'label'         => __('Spacing', 'bb-powerpack'),
						'default'       => 2,
						'size'          => 5,
						'description'   => _x( '%', 'bb-powerpack' )
					),
				)
			)
        )
    ),
	'style'	=> array(
		'title'	=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'general_style'	=> array(
				'title'	=> __( 'Image', 'bb-powerpack' ),
				'fields'	=> array(
					'hover_effects' => array(
						'type'          => 'select',
						'label'         => __('Image Hover Effect', 'bb-powerpack'),
						'default'       => 'none',
						'options'       => array(
							'none' 			=> __('None', 'bb-powerpack'),
							'zoom-in'		=> __('Zoom In', 'bb-powerpack'),
							'zoom-out'		=> __('Zoom Out', 'bb-powerpack'),
							'greyscale'		=> __('Greyscale', 'bb-powerpack'),
							'blur'			=> __('Blur', 'bb-powerpack'),
							'rotate'		=> __('Rotate', 'bb-powerpack'),
						),
						'toggle'	=> array(
							'zoom-in'	=> array(
								'fields'	=> array('image_animation_speed')
							),
							'zoom-out'	=> array(
								'fields'	=> array('image_animation_speed')
							),
							'greyscale'	=> array(
								'fields'	=> array('image_animation_speed')
							),
							'blur'	=> array(
								'fields'	=> array('image_animation_speed')
							),
							'rotate'	=> array(
								'fields'	=> array('image_animation_speed')
							),
						),
						'preview'	=> 'none',
					),
					'image_animation_speed' => array(
						'type'          => 'text',
						'label'         => __('Animation Speed', 'bb-powerpack'),
						'description'   => __('ms', 'bb-powerpack'),
						'default'       => 300,
						'size'          => 5,
					),
					'photo_border'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Border Style', 'bb-powerpack'),
                        'default'     => 'none',
                        'options'       => array(
                             'none'          => __('None', 'bb-powerpack'),
                             'solid'          => __('Solid', 'bb-powerpack'),
                             'dashed'          => __('Dashed', 'bb-powerpack'),
                             'dotted'          => __('Dotted', 'bb-powerpack'),
                         ),
                         'toggle'   => array(
                             'solid'    => array(
                                 'fields'   => array('photo_border_width', 'photo_border_color')
                             ),
                             'dashed'    => array(
                                 'fields'   => array('photo_border_width', 'photo_border_color')
                             ),
                             'dotted'    => array(
                                 'fields'   => array('photo_border_width', 'photo_border_color')
                             ),
                             'double'    => array(
                                 'fields'   => array('photo_border_width', 'photo_border_color')
                             )
                         )
                    ),
                    'photo_border_width'   => array(
                        'type'          => 'text',
                        'label'         => __('Border Width', 'bb-powerpack'),
                        'description'   => 'px',
						'size'      => 5,
                        'maxlength' => 3,
                        'default'       => '1',
                        'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-photo-gallery-item, .pp-gallery-masonry-item',
                            'property'        => 'border-width',
                            'unit'            => 'px'
                        )
                    ),
                    'photo_border_color'    => array(
						'type'      => 'color',
                        'label'     => __('Border Color', 'bb-powerpack'),
						'show_reset' => true,
                        'default'   => '',
						'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-photo-gallery-item, .pp-gallery-masonry-item',
                            'property'        => 'border-color',
                        )
                    ),
					'photo_border_radius'   => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => 0,
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-photo-gallery-item, .pp-gallery-masonry-item, .pp-photo-gallery-item img, .pp-gallery-masonry-item img, .pp-gallery-overlay',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
					'photo_padding'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Padding', 'bb-powerpack'),
						'description'	=> 'px',
                        'size'          => '5',
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.pp-photo-gallery-item, .pp-gallery-masonry-item',
							'property'	=> 'padding',
							'unit' 		=> 'px'
						),
                    ),
					'photo_border_radius'   => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '0',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-photo-gallery-item, .pp-gallery-masonry-item',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
				)
			),
			'image_shadow_style'	=> array(
				'title'		=> __( 'Image Shadow', 'bb-powerpack' ),
				'fields'	=> array(
					'show_image_shadow'   => array(
                        'type'                 => 'pp-switch',
                        'label'                => __('Enable Shadow', 'bb-powerpack'),
                        'default'              => 'no',
                        'options'              => array(
                            'yes'          	=> __('Yes', 'bb-powerpack'),
                            'no'            => __('No', 'bb-powerpack'),
                        ),
                        'toggle'    =>  array(
                            'yes'   => array(
                                'fields'    => array('image_shadow', 'image_shadow_color', 'image_shadow_opacity')
                            )
                        )
                    ),
                    'image_shadow' 		=> array(
						'type'              => 'pp-multitext',
						'label'             => __('Shadow', 'bb-powerpack'),
						'default'           => array(
							'vertical'			=> 0,
							'horizontal'		=> 2,
							'blur'				=> 8,
							'spread'			=> 0
						),
						'options'			=> array(
							'vertical'			=> array(
								'placeholder'		=> __('Vertical', 'bb-powerpack'),
								'tooltip'			=> __('Vertical', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-v'
							),
							'horizontal'		=> array(
								'placeholder'		=> __('Horizontal', 'bb-powerpack'),
								'tooltip'			=> __('Horizontal', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-h'
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
                    'image_shadow_color' => array(
                        'type'              => 'color',
                        'label'             => __('Shadow Color', 'bb-powerpack'),
                        'default'           => '000000',
                    ),
                    'image_shadow_opacity' => array(
                        'type'              => 'text',
                        'label'             => __('Shadow Opacity', 'bb-powerpack'),
                        'description'       => '%',
                        'size'             => 5,
                        'default'           => 30,
                    ),
				)
			),
			'image_shadow_hover_style'	=> array(
				'title'		=> __( 'Image Shadow on Hover', 'bb-powerpack' ),
				'fields'	=> array(
					'show_image_shadow_hover'   => array(
                        'type'                 => 'pp-switch',
                        'label'                => __('Enable Shadow', 'bb-powerpack'),
                        'default'              => 'no',
                        'options'              => array(
                            'yes'          	=> __('Yes', 'bb-powerpack'),
                            'no'            => __('No', 'bb-powerpack'),
                        ),
                        'toggle'    =>  array(
                            'yes'   => array(
                                'fields'    => array('image_shadow_hover', 'image_shadow_color_hover', 'image_shadow_opacity_hover', 'image_shadow_hover_speed')
                            )
                        )
                    ),
                    'image_shadow_hover' 		=> array(
						'type'              => 'pp-multitext',
						'label'             => __('Shadow', 'bb-powerpack'),
						'default'           => array(
							'vertical'			=> 0,
							'horizontal'		=> 2,
							'blur'				=> 15,
							'spread'			=> 0
						),
						'options'			=> array(
							'vertical'			=> array(
								'placeholder'		=> __('Vertical', 'bb-powerpack'),
								'tooltip'			=> __('Vertical', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-v'
							),
							'horizontal'		=> array(
								'placeholder'		=> __('Horizontal', 'bb-powerpack'),
								'tooltip'			=> __('Horizontal', 'bb-powerpack'),
								'icon'				=> 'fa-arrows-h'
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
                    'image_shadow_color_hover' => array(
                        'type'              => 'color',
                        'label'             => __('Shadow Color', 'bb-powerpack'),
                        'default'           => '000000',
                    ),
                    'image_shadow_opacity_hover' => array(
                        'type'              => 'text',
                        'label'             => __('Shadow Opacity', 'bb-powerpack'),
                        'description'       => '%',
                        'size'             => 5,
                        'default'           => 50,
                    ),
					'image_shadow_hover_speed' => array(
                        'type'              => 'text',
                        'label'             => __('Transition Speed', 'bb-powerpack'),
						'default'			=> '300',
                        'description'       => 'ms',
                        'size'             	=> 5,
                    ),
				)
			),
			'overlay_style'       => array(
				'title'         => __( 'Overlay', 'bb-powerpack' ),
				'fields'        => array(
					'overlay_type'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Type', 'bb-powerpack'),
                        'default'     => 'solid',
                        'options'       => array(
                            'solid'          => __('Solid', 'bb-powerpack'),
                            'gradient'          => __('Gradient', 'bb-powerpack'),
                        ),
                        'toggle'   => array(
                        	'solid'    => array(
                                 'fields'   => array('overlay_color')
                             ),
                             'gradient'    => array(
                                 'fields'   => array('overlay_primary_color', 'overlay_secondary_color')
                             ),
                         )
                    ),
					'overlay_color' => array(
						'type'       => 'color',
						'label'     => __('Color', 'bb-powerpack'),
						'default'	=> '',
						'show_reset' => true,
						'preview'	=> 'none',
					),
					'overlay_primary_color' => array(
						'type'       => 'color',
						'label'     => __('Primary Color', 'bb-powerpack'),
						'default'	=> '',
						'show_reset' => true,
						'preview'	=> 'none',
					),
					'overlay_secondary_color' => array(
						'type'       => 'color',
						'label'     => __('Secondary Color', 'bb-powerpack'),
						'default'	=> '',
						'show_reset' => true,
						'preview'	=> 'none',
					),
					'overlay_color_opacity'    => array(
						'type'        => 'text',
						'label'       => __('Opacity', 'bb-powerpack'),
						'default'     => '70',
						'description' => '%',
						'maxlength'   => '3',
						'size'        => '5',
					),
					'overlay_border_width'    => array(
						'type'        => 'text',
						'label'       => __('Border Width', 'bb-powerpack'),
						'default'     => '',
						'description' => 'px',
						'maxlength'   => '3',
						'size'        => '5',
					),
					'overlay_border_color' => array(
						'type'       => 'color',
						'label'     => __('Border Color', 'bb-powerpack'),
						'default'	=> '',
						'show_reset' => true,
						'preview'	=> 'none',
					),
					'overlay_spacing'    => array(
						'type'        => 'text',
						'label'       => __('Spacing', 'bb-powerpack'),
						'default'     => '',
						'description' => 'px',
						'maxlength'   => '3',
						'size'        => '5',
					),
				)
			),
			'icon_style'	=> array(
				'title'			=> __('Icon Style', 'bb-powerpack'),
				'fields'		=> array(
					'overlay_icon_size'     => array(
						'type'          => 'text',
						'label'         => __('Icon Size', 'bb-powerpack'),
						'default'   	=> '30',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
							'property'	=> 'font-size',
							'unit'		=> 'px'
						),
					),
					'overlay_icon_bg_color' => array(
						'type'       => 'color',
						'label'     => __('Background Color', 'bb-powerpack'),
						'default'    => '',
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
							'property'	=> 'color'
						),
					),
					'overlay_icon_color' => array(
						'type'       	=> 'color',
						'label'     	=> __('Color', 'bb-powerpack'),
						'default'    	=> '',
						'show_reset'	=> true,
						'preview'		=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
							'property'	=> 'color'
						),
					),
					'overlay_icon_radius'     => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
							'property'	=> 'border-radius',
							'unit'		=> 'px'
						),
					),
					'overlay_icon_horizotal_padding' 	=> array(
						'type'          => 'text',
						'label'         => __('Horizontal Padding', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
									'property'	=> 'padding-left',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
									'property'	=> 'padding-right',
									'unit'		=> 'px'
								),
							)
						),
                    ),
					'overlay_icon_vertical_padding' 	=> array(
						'type'          => 'text',
						'label'         => __('Vertical Padding', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
									'property'	=> 'padding-top',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
									'property'	=> 'padding-bottom',
									'unit'		=> 'px'
								),
							)
						),
                    ),
				)
			),
			'lightbox_style'	=> array(
				'title'	=> __( 'Lightbox', 'bb-powerpack' ),
				'fields'	=> array(
					'lightbox_overlay_color' => array(
						'type'       	=> 'color',
						'label'     	=> __('Overlay Color', 'bb-powerpack'),
						'default'    	=> 'rgba(0,0,0,0.5)',
						'show_reset'	=> true,
						'show_alpha'	=> true
					),
					'lightbox_border_width'     => array(
						'type'          => 'text',
						'label'         => __('Border Width', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
					),
					'lightbox_border_color' => array(
						'type'       	=> 'color',
						'label'     	=> __('Border Color', 'bb-powerpack'),
						'default'    	=> 'ffffff',
						'show_reset'	=> true,
					),
					'lightbox_border_radius'     => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
					),
				),
			)
		)
	),
	'caption_settings'	=> array(
		'title'	=> __( 'Caption', 'bb-powerpack' ),
		'sections'	=> array(
			'caption_style'	=> array(
				'title'		=> __('Style', 'bb-powerpack'),
				'fields'	=> array(
					'caption_bg_color'	=> array(
						'type'       	=> 'color',
						'label'     	=> __('Background Color', 'bb-powerpack'),
						'default'    	=> '',
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-photo-gallery-caption',
							'property'	=> 'background-color'
						),
					),
					'caption_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Text Alignment', 'bb-powerpack'),
						'default'	=> 'center',
						'options'       => array(
							'left'          => __('Left', 'bb-powerpack'),
							'center'         => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-photo-gallery-caption',
							'property'	=> 'text-align'
						),
					),
					'caption_padding' 	=> array(
                    	'type' 			=> 'pp-multitext',
                    	'label' 		=> __('Padding', 'bb-powerpack'),
                        'description'   => 'px',
                        'default'       => array(
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 0,
                            'left' => 0,
                        ),
                    	'options' 		=> array(
                    		'top' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Top', 'bb-powerpack'),
                                'tooltip'       => __('Top', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-up',
								'preview'         => array(
		                            'type'            => 'css',
		                            'selector'        => '.pp-photo-gallery-caption',
		                            'property'        => 'padding-top',
		                            'unit'            => 'px'
		                        )
                    		),
                            'bottom' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Bottom', 'bb-powerpack'),
                                'tooltip'       => __('Bottom', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-down',
								'preview'         => array(
		                            'type'            => 'css',
		                            'selector'        => '.pp-photo-gallery-caption',
		                            'property'        => 'padding-bottom',
		                            'unit'            => 'px'
		                        )
                    		),
                            'left' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Left', 'bb-powerpack'),
                                'tooltip'       => __('Left', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-left',
								'preview'         => array(
		                            'type'            => 'css',
		                            'selector'        => '.pp-photo-gallery-caption',
		                            'property'        => 'padding-left',
		                            'unit'            => 'px'
		                        )
                    		),
                            'right' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Right', 'bb-powerpack'),
                                'tooltip'       => __('Right', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-right',
								'preview'         => array(
		                            'type'            => 'css',
		                            'selector'        => '.pp-photo-gallery-caption',
		                            'property'        => 'padding-right',
		                            'unit'            => 'px'
		                        )
                    		),
                    	)
                    ),
				)
			),
			'general_typography'	=> array(
				'title'	=> __( 'Typography', 'bb-powerpack' ),
				'fields'	=> array(
					'caption_font'	=> array(
						'type'		=> 'font',
						'label'		=> __('Font', 'bb-powerpack'),
						'default'	=> array(
							'family'	=> 'Default',
							'weight'	=> '400',
						),
						'preview'       => array(
							'type'		=> 'font',
							'selector'        => '.pp-photo-gallery-caption, .pp-gallery-overlay .pp-caption',
						),
					),
					'caption_font_size_toggle' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Font Size', 'bb-powerpack'),
						'default'	=> 'default',
						'options'       => array(
							'default'          => __('Default', 'bb-powerpack'),
							'custom'         => __('Custom', 'bb-powerpack'),
						),
						'toggle'	=> array(
							'custom'	=> array(
								'fields'	=> array('caption_custom_font_size')
							)
						),
					),
					'caption_custom_font_size'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Custom Font Size', 'bb-powerpack'),
						'description'	=> 'px',
                        'size'          => '5',
						'responsive' => array(
							'placeholder' => array(
								'default' => '16',
								'medium' => '',
								'responsive' => '',
							),
						),
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.pp-photo-gallery-caption, .pp-gallery-overlay .pp-caption',
							'property'	=> 'font-size',
							'unit' 		=> 'px'
						),
                    ),
			        'caption_color'        => array(
			            'type'       => 'color',
			            'label'      => __('Color', 'bb-powerpack'),
			            'default'    => '',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-photo-gallery-caption, .pp-gallery-overlay .pp-caption',
							'property'	=> 'color'
						)
			        ),
				)
			)
		)
	)
));
