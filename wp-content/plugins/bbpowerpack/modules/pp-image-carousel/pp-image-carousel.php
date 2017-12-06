<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class PPImageCarouselModule
 */
class PPImageCarouselModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
			'name'          => __('Image Carousel', 'bb-powerpack'),
			'description'   => __('A module for image carousel.', 'bb-powerpack'),
			'group'         => pp_get_modules_group(),
			'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-image-carousel/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-image-carousel/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true
        ));

		$this->add_js('jquery-magnificpopup');
		$this->add_css('jquery-magnificpopup');

		$this->add_css( 'swiper-style', $this->url . 'css/swiper.min.css' );
	
		$this->add_js( 'swiper-script', $this->url . 'js/swiper.jquery.min.js', array('jquery'), BB_POWERPACK_VER );

		$this->add_css('font-awesome');
    }


	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update($settings)
	{
		// Cache the photo data if using the WordPress media library.
		$settings->image_data = $this->get_wordpress_photos();

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

		if ( $this->settings->image_order == 'random' && is_array( $default_order )) {

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
		$ids        = $this->settings->carousel_photos;
		$medium_w   = get_option('medium_size_w');
		$large_w    = get_option('large_size_w');

		/* Template Cache */
		$image_from_template = false;
		$image_attachment_data = false;

		if(empty($this->settings->carousel_photos)) {
			return $photos;
		}

		/* Check if all photos are available on host */
		foreach ($ids as $id) {
			$image_attachment_data[$id] = FLBuilderPhoto::get_attachment_data($id);

			if ( ! $image_attachment_data[$id] ) {
				$image_from_template = true;
			}

		}

		foreach($ids as $id) {

			$photo = $image_attachment_data[$id];

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo && $image_from_template ) {

				if ( ! isset( $this->settings->image_data ) ) {
					continue;
				}
				else if ( is_array( $this->settings->image_data ) ) {
					$photos[ $id ] = $this->settings->image_data[ $id ];
				}
				else if ( is_object( $this->settings->image_data ) ) {
					$photos[ $id ] = $this->settings->image_data->{$id};
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


				// Grid photo src
				if($this->settings->image_size == 'thumbnail' && isset($photo->sizes->thumbnail)) {
					$data->src = $photo->sizes->thumbnail->url;
				}
				elseif($this->settings->image_size == 'medium' && isset($photo->sizes->medium)) {
					$data->src = $photo->sizes->medium->url;
				}
				else {
					$data->src = $photo->sizes->full->url;
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
FLBuilder::register_module('PPImageCarouselModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
					'carousel_photos' => array(
					    'type'          => 'multiple-photos',
						'label'         => __( 'Photos', 'bb-powerpack' ),
						'connections'  	=> array('multiple-photos')
					),
					'carousel_type'        => array(
						'type'          => 'select',
						'label'         => __( 'Type', 'bb-powerpack' ),
						'default'       => 'carousel',
						'options'       => array(
							'carousel'     	=> __( 'Carousel', 'bb-powerpack' ),
							'slideshow' 	=> __( 'Slideshow', 'bb-powerpack' ),
							'coverflow' 	=> __( 'Coverflow', 'bb-powerpack' )
						),
						'toggle'	=> array(
							'carousel'	=> array(
								'fields'	=> array('columns', 'pagination_type')
							),
							'slideshow'	=> array(
								'sections'	=> array('thumbnails_columns')
							),
							'coverflow'	=> array(
								'fields'	=> array('columns', 'pagination_type')
							)
						)
					),
					'columns'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Slider Per View', 'bb-powerpack'),
						'size'          => '5',
						'default'		=> 3,
						'responsive' => array(
							'placeholder' => array(
								'default' => '3',
								'medium' => '2',
								'responsive' => '1',
							),
						),
					),
					'spacing' => array(
						'type' 			=> 'unit',
						'label' 		=> __('Spacing', 'bb-powerpack'),
						'description'	=> 'px',
						'size'          => '5',
						'default'		=> 20,
						'responsive' => array(
							'placeholder' => array(
								'default' => '20',
								'medium' => '20',
								'responsive' => '20',
							),
						),
					),
					'carousel_height' => array(
						'type' 			=> 'unit',
						'label' 		=> __('Height', 'bb-powerpack'),
						'description'	=> 'px',
						'size'          => '5',
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
						'preview'       => array(
							'type'          => 'css',
                            'selector'      => '.pp-image-carousel.pp-image-carousel-slideshow, .pp-image-carousel',
							'property'      => 'height',
							'unit'			=> 'px'
						)
					),
                )
            ),
			'overlay_settings'	=> array(
				'title'	=> __( 'Overlay', 'bb-powerpack' ),
				'fields'	=> array(
					'overlay'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Overlay', 'bb-powerpack' ),
						'default'       => 'none',
						'options'       => array(
							'none'				=> __( 'None', 'bb-powerpack' ),
							'text' 				=> __( 'Caption', 'bb-powerpack' ),
							'icon' 				=> __( 'Icon', 'bb-powerpack' ),
						),
						'toggle'		=> array(
							'text'	=> array(
								'tabs'		=> array( 'typography' ),
								'sections' 	=> array( 'overlay_style' ),
								'fields'	=> array('overlay_effects', 'overlay_animation_speed')
							),
							'icon'	=> array(
								'sections' 	=> array( 'overlay_style', 'icon_style' ),
								'fields'	=> array('overlay_effects', 'overlay_animation_speed', 'overlay_icon')
							),
						),
						'preview'	=> 'none',
					),
					'overlay_icon'          => array(
						'type'          => 'icon',
						'label'         => __( 'Icon', 'bb-powerpack' ),
						'preview'	=> 'none',
						'show_remove' => true
					),
					'overlay_effects' => array(
						'type'          => 'select',
						'label'         => __('Effect', 'bb-powerpack'),
						'default'       => 'fade',
						'options'       => array(
							'fade' 			=> __('Fade', 'bb-powerpack'),
							'from-left'		=> __('Overlay From Left', 'bb-powerpack'),
							'from-right'	=> __('Overlay From Right', 'bb-powerpack'),
							'from-top'		=> __('Overlay From Top', 'bb-powerpack'),
							'from-bottom'	=> __('Overlay From Bottom', 'bb-powerpack'),
							'framed'		=> __('Framed', 'bb-powerpack'),
							'zoom-in'		=> __('Zoom In', 'bb-powerpack'),
						),
						'toggle'		=> array(
							'framed'	=> array(
								'fields'	=> array('overlay_border_width', 'overlay_border_color', 'overlay_spacing')
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
				)
			),
			'thumbnails_columns' => array(
				'title'	=> __( 'Thumbnails', 'bb-powerpack' ),
				'fields'	=> array(
					'thumb_columns'    => array(
						'type' 			=> 'unit',
						'label' 		=> __('Slider Per View', 'bb-powerpack'),
						'size'          => '5',
						'default'		=> 5,
						'responsive' => array(
							'placeholder' => array(
								'default' => '5',
								'medium' => '2',
								'responsive' => '1',
							),
						),
					),
					'thumb_ratio' => array(
						'type'          => 'select',
						'label'         => __('Ratio', 'bb-powerpack'),
						'default'       => '43',
						'options'       => array(
							'11' 			=> __('1:1', 'bb-powerpack'),
							'43'			=> __('4:3', 'bb-powerpack'),
							'169'			=> __('16:9', 'bb-powerpack'),
							'219'			=> __('21:9', 'bb-powerpack'),
						),
					),	
				)
			)
        )
    ),
	'carousel_settings'      => array(
		'title'         => __( 'Settings', 'bb-powerpack' ),
		'sections'      => array(
			'general'	=> array(
				'title'		=> '',
				'fields'	=> array(
					'image_size'        => array(
						'type'          => 'photo-sizes',
						'label'         => __( 'Image Size', 'bb-powerpack' ),
						'default'       => 'medium',
					),
					'image_fit'        => array(
						'type'          => 'select',
						'label'         => __( 'Image Fit', 'bb-powerpack' ),
						'default'       => 'normal',
						'options'       => array(
							'cover'     	=> __( 'Cover', 'bb-powerpack' ),
							'contain' 		=> __( 'Contain', 'bb-powerpack' ),
							'auto' 			=> __( 'Auto', 'bb-powerpack' )
						),
					),
					'image_order'        => array(
						'type'          => 'select',
						'label'         => __( 'Display Order', 'bb-powerpack' ),
						'default'       => 'normal',
						'options'       => array(
							'normal'     	=> __( 'Normal', 'bb-powerpack' ),
							'random' 		=> __( 'Random', 'bb-powerpack' )
						),
					),
					'click_action'  => array(
						'type'          => 'select',
						'label'         => __( 'Click Action', 'bb-powerpack' ),
						'default'       => 'none',
						'options'       => array(
							'none'          => __( 'None', 'Click action.', 'bb-powerpack' ),
							'lightbox'      => __( 'Lightbox', 'bb-powerpack' ),
							'custom-link'   => __( 'Custom Link', 'bb-powerpack' )
						),
						'preview'       => array(
							'type'          => 'none'
						),
						'toggle'	=> array(
							'custom-link'	=> array(
								'fields'	=> array('custom_link_target')
							)
						),
						'help'	=> __('Custom Link: You can set link to images directly in media modal where you upload them.', 'bb-powerpack')
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
					),
				)
			),
			'slide_settings'    => array(
				'title'         => __('Slide Settings', 'bb-powerpack'),
				'fields'        => array(
					'effect'   => array(
						'type'          => 'select',
						'label'         => __( 'Effect', 'bb-powerpack' ),
						'default'       => 'slide',
						'options'       => array(
							'slide'       	=> __( 'Slide', 'bb-powerpack' ),
							'fade'			=> __( 'Fade', 'bb-powerpack' ),
							'cube'			=> __( 'Cube', 'bb-powerpack' ),
						),
					),
					'transition_speed' => array(
						'type'          => 'text',
						'label'         => __( 'Transition Speed', 'bb-powerpack' ),
						'default'       => '1000',
						'size'          => '5',
						'description'   => _x( 'ms', 'Value unit for form field of time in mili seconds. Such as: "500 ms"', 'bb-powerpack' )
					),
					'autoplay'     => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Auto Play', 'bb-powerpack' ),
						'default'       => 'yes',
						'options'       => array(
							'yes'          => __( 'Yes', 'bb-powerpack' ),
							'no'         => __( 'No', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'yes'	=> array(
								'fields'	=> array('autoplay_speed')
							)
						)
					),
					'autoplay_speed' => array(
						'type'          => 'text',
						'label'         => __( 'Auto Play Speed', 'bb-powerpack' ),
						'default'       => '5000',
						'size'          => '5',
						'description'   => _x( 'ms', 'Value unit for form field of time in mili seconds. Such as: "500 ms"', 'bb-powerpack' )
					),
					'pause_on_interaction'     => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Pause on Interaction', 'bb-powerpack' ),
						'default'       => 'yes',
						'options'       => array(
							'yes'          	=> __( 'Yes', 'bb-powerpack' ),
							'no'         	=> __( 'No', 'bb-powerpack' ),
						)
					),
				)
			),
            'navigation'   => array( // Section
                'title' => __( 'Navigation', 'bb-powerpack' ), // Section Title
				'fields' => array( // Section Fields
					'slider_navigation'     => array(
						'type'          => 'pp-switch',
						'label'         => __( 'Show Navigation Arrows?', 'bb-powerpack' ),
						'default'       => 'no',
						'options'       => array(
							'yes'        	=> __( 'Yes', 'bb-powerpack' ),
							'no'            => __( 'No', 'bb-powerpack' ),
						),
						'toggle'		=> array(
							'yes'			=> array(
								'sections'		=> array( 'arrow_style' )
							)
						)
					),
					'pagination_type'   => array(
						'type'          	=> 'pp-switch',
						'label'         	=> __( 'Pagination Type', 'bb-powerpack' ),
						'default'       	=> 'bullets',
						'options'       	=> array(
							'none'				=> __( 'None', 'bb-powerpack' ),
							'bullets'       	=> __( 'Dots', 'bb-powerpack' ),
							'fraction'			=> __( 'Fraction', 'bb-powerpack' ),
							'progress'			=> __( 'Progress', 'bb-powerpack' ),
						),
						'toggle'			=> array(
							'bullets'			=> array(
								'sections'			=> array('pagination_style'),
								'fields'			=> array('bullets_width', 'bullets_border_radius')
							),
							'fraction'			=> array(
								'sections'			=> array('pagination_style'),
							),
							'progress'			=> array(
								'sections'			=> array('pagination_style'),
							)
						)
					),
                )
            )
		)
	),
	'style'	=> array(
		'title'	=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'general_style'	=> array(
				'title'	=> __('Image', 'bb-powerpack'),
				'fields'	=> array(
					'image_border'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __( 'Border Style', 'bb-powerpack' ),
                        'default'     => 'none',
                        'options'       => array(
                            'none'          => __( 'None', 'bb-powerpack' ),
                            'solid'         => __( 'Solid', 'bb-powerpack' ),
                            'dashed'        => __( 'Dashed', 'bb-powerpack' ),
                            'dotted'        => __( 'Dotted', 'bb-powerpack' ),
                         ),
                         'toggle'   => array(
                            'solid'    => array(
                                'fields'   => array( 'image_border_width', 'image_border_color' )
                            ),
                            'dashed'    => array(
                                'fields'   => array( 'image_border_width', 'image_border_color' )
                            ),
                            'dotted'    => array(
                                'fields'   => array( 'image_border_width', 'image_border_color' )
                            ),
                            'double'    => array(
                                'fields'   => array( 'image_border_width', 'image_border_color' )
                            )
                        )
                    ),
                    'image_border_width'   => array(
                        'type'          => 'text',
                        'label'         => __( 'Border Width', 'bb-powerpack' ),
                        'description'   => 'px',
						'size'      	=> 5,
                        'maxlength' 	=> 3,
                        'default'       => '1',
                        'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-image-carousel-item',
                            'property'        => 'border-width',
                            'unit'            => 'px'
                        )
                    ),
                    'image_border_color'    => array(
						'type'      	=> 'color',
                        'label'     	=> __( 'Border Color', 'bb-powerpack' ),
						'show_reset' 	=> true,
                        'default'   	=> '',
						'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-image-carousel-item',
                            'property'        => 'border-color',
                        )
                    ),
					'image_border_radius'   => array(
						'type'          => 'text',
						'label'         => __( 'Round Corners', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => 0,
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-image-carousel-item, .pp-image-carousel .pp-image-carousel-content .pp-carousel-img',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
					'image_padding'    => array(
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
							'selector'	=> '.pp-image-carousel-item',
							'property'	=> 'padding',
							'unit' 		=> 'px'
						),
                    ),
					'image_border_radius'	=> array(
						'type'          => 'text',
						'label'         => __( 'Round Corners', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '0',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-image-carousel-item',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
				)
			),
			'overlay_style'	=> array(
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
							'selector'	=> '.pp-image-overlay .pp-overlay-icon span',
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
							'selector'	=> '.pp-image-overlay .pp-overlay-icon span',
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
							'selector'	=> '.pp-image-overlay .pp-overlay-icon span',
							'property'	=> 'color'
						),
					),
					'overlay_icon_radius'     => array(
						'type'          => 'text',
						'label'         => __('Border Radius', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-image-overlay .pp-overlay-icon span',
							'property'	=> 'border-radius',
							'unit'		=> 'px'
						),
					),
					'overlay_icon_padding' 	=> array(
						'type'          => 'text',
						'label'         => __('Padding', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-image-overlay .pp-overlay-icon span',
							'property'	=> 'padding',
							'unit'		=> 'px'
						),
                    ),
				)
			),
			'arrow_style'   => array( // Section
                'title' => __( 'Arrow', 'bb-powerpack' ), // Section Title
				'fields' => array( // Section Fields
					'arrow_font_size'   => array(
						'type'          => 'text',
						'label'         => __( 'Arrow Size', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      => 5,
						'maxlength' => 3,
						'default'       => '24',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-image-carousel .pp-swiper-button',
							'property'        => 'font-size',
							'unit'            => 'px'
						)
					),
                    'arrow_bg_color'       => array(
						'type'      => 'color',
                        'label'     => __( 'Background Color', 'bb-powerpack' ),
						'show_reset' => true,
                        'default'   => 'eaeaea',
					),
                    'arrow_bg_hover'       => array(
						'type'      => 'color',
                        'label'     => __( 'Background Hover Color', 'bb-powerpack' ),
						'show_reset' => true,
                        'default'   => '4c4c4c'
					),
					'arrow_color'       => array(
						'type'      => 'color',
						'label'     => __( 'Arrow Color', 'bb-powerpack' ),
						'show_reset' => true,
						'default'   => '000000'
					),
					'arrow_color_hover'       => array(
						'type'      => 'color',
						'label'     => __( 'Arrow Hover Color', 'bb-powerpack' ),
						'show_reset' => true,
						'default'   => 'eeeeee'
					),
                    'arrow_border_style'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __( 'Border Style', 'bb-powerpack' ),
                        'default'     => 'none',
                        'options'       => array(
                             'none'          => __( 'None', 'bb-powerpack' ),
                             'solid'         => __( 'Solid', 'bb-powerpack' ),
                             'dashed'        => __( 'Dashed', 'bb-powerpack' ),
                             'dotted'        => __( 'Dotted', 'bb-powerpack' ),
                         ),
                         'toggle'   => array(
                             'solid'    => array(
                                 'fields'   => array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' )
                             ),
                             'dashed'    => array(
                                 'fields'   => array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' )
                             ),
                             'dotted'    => array(
                                 'fields'   => array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' )
                             ),
                             'double'    => array(
                                 'fields'   => array( 'arrow_border_width', 'arrow_border_color', 'arrow_border_hover' )
                             )
                         )
                    ),
                    'arrow_border_width'   => array(
                        'type'          => 'text',
                        'label'         => __( 'Border Width', 'bb-powerpack' ),
                        'description'   => 'px',
						'size'      => 5,
                        'maxlength' => 3,
                        'default'       => '1',
                        'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-image-carousel .pp-swiper-button',
                            'property'        => 'border-width',
                            'unit'            => 'px'
                        )
                    ),
                    'arrow_border_color'    => array(
						'type'      => 'color',
                        'label'     => __( 'Border Color', 'bb-powerpack' ),
						'show_reset' => true,
                        'default'   => '',
					),
                    'arrow_border_hover'    => array(
						'type'      => 'color',
                        'label'     => __( 'Border Hover Color', 'bb-powerpack' ),
						'show_reset' => true,
                        'default'   => '',
					),
					'arrow_border_radius'   => array(
						'type'          => 'text',
						'label'         => __( 'Round Corners', 'bb-powerpack' ),
						'description'   => 'px',
						'size'      	=> 5,
						'maxlength' 	=> 3,
						'default'       => '100',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-image-carousel .pp-swiper-button',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
					'arrow_horizontal_padding' 	=> array(
                    	'type'          => 'text',
						'label'         => __('Horizontal Padding', 'bb-powerpack'),
						'default'   	=> '13',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-image-carousel .pp-swiper-button',
									'property'	=> 'padding-left',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-image-carousel .pp-swiper-button',
									'property'	=> 'padding-right',
									'unit'		=> 'px'
								),
							)
						),
                    ),
					'arrow_vertical_padding' 	=> array(
                    	'type'          => 'text',
						'label'         => __('Vertical Padding', 'bb-powerpack'),
						'default'   	=> '5',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-image-carousel .pp-swiper-button',
									'property'	=> 'padding-top',
									'unit'		=> 'px'
								),
								array(
									'selector'	=> '.pp-image-carousel .pp-swiper-button',
									'property'	=> 'padding-bottom',
									'unit'		=> 'px'
								),
							)
						),
                    ),
                )
			),
			'pagination_style'	=> array(
				'title'				=> __('Pagination', 'bb-powerpack'),
				'fields'			=> array(
					'pagination_position'	=> array(
						'type'          => 'pp-switch',
						'label'         => __( 'Pagination Position', 'bb-powerpack' ),
						'default'       => 'outside',
						'options'       => array(
							'outside'        	=> __( 'Outside', 'bb-powerpack' ),
							'inside'            => __( 'Inside', 'bb-powerpack' ),
						),
					),
					'pagination_bg_color'  => array(
						'type'          => 'color',
						'label'         => __( 'Background Color', 'bb-powerpack' ),
						'default'       => '999999',
						'show_reset'    => true,
						'preview'       => array(
							'type'          => 'css',
                            'selector'        => '.pp-image-carousel .swiper-pagination-bullet, .pp-image-carousel.swiper-container-horizontal>.swiper-pagination-progress',
                            'property'        => 'background',
						)
					),
                    'pagination_bg_hover'      => array(
						'type'          => 'color',
						'label'         => __( 'Active Background Color', 'bb-powerpack' ),
						'default'       => '000000',
						'show_reset'    => true,
						'preview'       => array(
                            'type'          => 'css',
                            'selector'        => '.pp-image-carousel .swiper-pagination-bullet:hover, .pp-image-carousel .swiper-pagination-bullet-active, .pp-image-carousel .swiper-pagination-progress .swiper-pagination-progressbar',
                            'property'        => 'background',
						)
					),
                    'bullets_width'   => array(
                        'type'          => 'text',
                        'label'         => __( 'Bullets Size', 'bb-powerpack' ),
                        'description'   => 'px',
						'size'      => 5,
                        'maxlength' => 3,
                        'default'       => '10',
                        'preview'         => array(
                            'type'            => 'css',
                            'rules'           => array(
                               array(
                                   'selector'        => '.pp-image-carousel .swiper-pagination-bullet',
                                   'property'        => 'width',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-image-carousel .swiper-pagination-bullet',
                                   'property'        => 'height',
                                   'unit'            => 'px'
                               ),
                           ),
                        )
                    ),
                    'bullets_border_radius'   => array(
                        'type'          => 'text',
                        'label'         => __( 'Bullets Round Corners', 'bb-powerpack' ),
                        'description'   => 'px',
						'size'      => 5,
                        'maxlength' => 3,
                        'default'       => '100',
                        'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-image-carousel .swiper-pagination-bullet',
                            'property'        => 'border-radius',
                            'unit'            => 'px'
                        )
                    ),
				)
			)
		)
	),
	'typography'	=> array(
		'title'	=> __( 'Typography', 'bb-powerpack' ),
		'sections'	=> array(
			'general_typography'	=> array(
				'title'		=> __( 'Caption', 'bb-powerpack' ),
				'fields'	=> array(
					'caption_font'	=> array(
						'type'		=> 'font',
						'label'		=> __( 'Font', 'bb-powerpack' ),
						'default'	=> array(
							'family'	=> 'Default',
							'weight'	=> '400',
						),
						'preview'   => array(
							'type'		=> 'font',
							'selector'  => '.pp-image-carousel-caption, .pp-image-overlay .pp-caption',
						),
					),
					'caption_font_size_toggle' => array(
						'type'		=> 'pp-switch',
						'label'		=> __( 'Font Size', 'bb-powerpack' ),
						'default'	=> 'default',
						'options'       => array(
							'default'        => __( 'Default', 'bb-powerpack' ),
							'custom'         => __( 'Custom', 'bb-powerpack' ),
						),
						'toggle'	=> array(
							'custom'	=> array(
								'fields'	=> array( 'caption_custom_font_size' )
							)
						),
					),
					'caption_custom_font_size'	=> array(
						'type' 			=> 'unit',
						'label' 		=> __('Custom Font Size', 'bb-powerpack'),
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
							'selector'	=> '.pp-image-carousel-caption, .pp-image-overlay .pp-caption',
							'property'	=> 'font-size',
							'unit' 		=> 'px'
						),
					),
			        'caption_color'        => array(
			            'type'       => 'color',
			            'label'      => __( 'Color', 'bb-powerpack' ),
			            'default'    => '',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-image-carousel-caption, .pp-image-overlay .pp-caption',
							'property'	=> 'color'
						)
			        ),
				)
			)
		)
	)
));