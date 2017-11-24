<?php

/**
 * @class PPFilterableGalleryModule
 */
class PPFilterableGalleryModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
			'name'          => __('Filterable Gallery', 'bb-powerpack'),
            'description'   => __('A module for filterable gallery.', 'bb-powerpack'),
			'group'			=> pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-filterable-gallery/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-filterable-gallery/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true
        ));

		$this->add_js('jquery-magnificpopup');
		$this->add_css('jquery-magnificpopup');
		$this->add_js('jquery-masonry');

		$this->add_js( 'isotope', $this->url . 'js/isotope.pkgd.min.js', array('jquery'), '', true );
		$this->add_js( 'jquery-imagesloaded', $this->url . 'js/jquery.imagesloaded.js', array('jquery'), rand(), false );

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
     * @method get_gallery_filter_ids
     * @param $filters_data {object}
     * @param $get_labels {boolean}
     */
	public function get_gallery_filter_ids( $filters_data, $get_labels = false )
	{
		$array_big = array();
		$filter_labels = array();

		if ( ! count( (array)$filters_data ) ) {
			return $array_big;
		}

		foreach ( $filters_data as $filter_key => $filter ) {

			if ( !is_object( $filter ) ) {
				continue;
			}

			$gphotos = str_replace( str_split('[]'), "", $filter->gallery_photos);
			$gphotos = explode(',', $gphotos);

			if ( is_array( $gphotos ) && count( $gphotos ) ) {
				foreach ( $gphotos as $gphoto ) {
					$array_big[] = $gphoto;
				}
				$filter_group_label = 'pp-group-' . ($filter_key+1);
				$filter_labels[$filter_group_label] = $gphotos;
			}
		}

		if ( ! count( $array_big ) ) {
			return $array_big;
		}

		$unique = array_unique( $array_big );

		if ( $get_labels ) :

			$labels = array();

			foreach ( $unique as $unique_id ) {
				if ( empty($unique_id) ) {
					continue;
				}
				foreach ( $filter_labels as $key => $filter_label ) {
					if ( in_array( $unique_id, $filter_label ) ) {
						if ( isset( $labels[$unique_id] ) ) {
							$labels[$unique_id] = $labels[$unique_id]  . ' ' . str_replace(" ", "-", strtolower($key));
						}
						else {
							$labels[$unique_id] = str_replace(" ", "-", strtolower($key));
						}
					}
				}
			}

			return $labels;

		endif;

		return $unique;
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
		$filters    = $this->settings->gallery_filter;
		$medium_w   = get_option('medium_size_w');
		$large_w    = get_option('large_size_w');
		$ids		= array();
		$custom_link = $this->settings->click_action;

		if ( ! count( $filters ) ) {
			return $photos;
		}

		$filter_ids = $this->get_gallery_filter_ids($this->settings->gallery_filter);

		/* Template Cache */
		$photo_from_template = false;
		$photo_attachment_data = false;

		/* Check if all photos are available on host */
		foreach ($filter_ids as $id) {
			if ( empty( $id ) ) {
				continue;
			}
			$photo_attachment_data[$id] = FLBuilderPhoto::get_attachment_data($id);

			if ( ! $photo_attachment_data[$id] ) {
				$photo_from_template = true;
			}

		}

		foreach($filter_ids as $id) {
			if ( empty( $id ) ) {
				continue;
			}
			$photo = $photo_attachment_data[$id];

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo && $photo_from_template ) {

				if ( ! isset( $this->settings->photo_data ) ) {
					continue;
				}
				else if ( is_array( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data[ $id ];
					preg_match("\{(.*)\}", $photos[ $id ], $photos);
				}
				else if ( is_object( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data->{$id};
					preg_match("\{(.*)\}", $photos[ $id ], $photos);
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
				$data->cta_link = $cta_link;

				$photos[$id] = $data;

				//preg_match("\{(.*)\}", $photos[ $id ], $photos);
			}

		}

		return $photos;
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPFilterableGalleryModule', array(
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
					'photo_size'        => array(
						'type'          => 'photo-sizes',
						'label'         => __('Image Size', 'bb-powerpack'),
						'default'       => 'medium',
					),
					'photo_order'        => array(
						'type'          => 'select',
						'label'         => __( 'Display Order', 'bb-powerpack' ),
						'default'       => 'normal',
						'options'       => array(
							'normal'     	=> __( 'Normal', 'bb-powerpack'),
							'random' 		=> __( 'Random', 'bb-powerpack' )
						),
					),
					'show_captions' => array(
						'type'          => 'select',
						'label'         => __('Show Captions', 'bb-powerpack'),
						'default'       => '0',
						'options'       => array(
							'0'             => __('Never', 'bb-powerpack'),
							'hover'         => __('On Hover', 'bb-powerpack'),
							'below'         => __('Below Photo', 'bb-powerpack')
						),
						'toggle'	=> array(
							'below'	=> array(
								'sections'	=> array('caption_style')
							)
						),
						'help'          => __('The caption pulls from whatever text you put in the caption area in the media manager for each image.', 'bb-powerpack')
					),
					'click_action'  => array(
						'type'          => 'select',
						'label'         => __('Click Action', 'bb-powerpack'),
						'default'       => 'lightbox',
						'options'       => array(
							'none'          => __( 'None', 'Click action.', 'bb-powerpack' ),
							'lightbox'      => __('Lightbox', 'bb-powerpack'),
							'custom-link'   => __('Custom URL', 'bb-powerpack')
						),
						'preview'       => array(
							'type'          => 'none'
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
						),
						'toggle'		=> array(
							'from-left'	=> array(
								'sections' => array( 'overlay_style' ),
							),
							'from-right'	=> array(
								'sections' => array( 'overlay_style' ),
							),
							'from-top'	=> array(
								'sections' => array( 'overlay_style' ),
							),
							'from-bottom'	=> array(
								'sections' => array( 'overlay_style' ),
							),
							'fade'	=> array(
								'sections' => array( 'overlay_style' ),
							),
						),
						'preview'	=> 'none',
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
			'filters_settings'	=> array(
				'title'	=> __( 'Filters', 'bb-powerpack' ),
				'fields'	=> array(
					'show_custom_all_text' => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Custom All Text?', 'bb-powerpack'),
						'default'       => 'no',
						'options'       => array(
							'yes'			=> __('Yes', 'bb-powerpack'),
							'no' 			=> __('No', 'bb-powerpack'),
						),
						'toggle'		=> array(
							'yes'	=> array(
								'fields'	=> array('custom_all_text')
							),
						),
						'preview'	=> 'none',
					),
					'custom_all_text' => array(
						'type'          => 'text',
						'label'         => __('Custom All Text', 'bb-powerpack'),
						'default'       => '',
						'preview'         => array(
                            'type'            => 'text',
                            'selector'        => '.pp-gallery-filters li.all',
                        )
					),
				)
			),
			'gallery_columns'	=> array(
				'title'	=> __( 'Columns Settings', 'bb-powerpack' ),
				'fields'	=> array(
					'photo_grid_count'    => array(
						'type' 			=> 'pp-multitext',
						'label' 		=> __('Number of Columns', 'bb-powerpack'),
						'default'		=> array(
							'desktop'	=> 4,
							'tablet'	=> 2,
							'mobile'	=> 1,
						),
						'options' 		=> array(
							'desktop' => array(
								'icon'		=> 'fa-desktop',
								'tooltip'	=> __('Desktop', 'bb-powerpack'),
							),
							'tablet' => array(
								'icon'		=> 'fa-tablet',
								'tooltip'	=> __('Tablet', 'bb-powerpack'),
							),
							'mobile' => array(
								'icon'		=> 'fa-mobile',
								'tooltip'	=> __('Mobile', 'bb-powerpack'),
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
	'gallery_filters'	=> array(
		'title'	=> __( 'Photos', 'bb-powerpack' ),
		'sections'	=> array(
			'gallery_filter'	=> array(
				'title'		=> '',
				'fields'	=> array(
					'gallery_filter'     => array(
						'type'         => 'form',
						'label'        => __('Photo Group', 'bb-powerpack'),
						'form'         => 'pp_gallery_filter_form',
						'preview_text' => 'filter_label',
						'multiple'     => true
					),
				)
			)
		)
	),
	'style'	=> array(
		'title'	=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'general_style'	=> array(
				'title'	=> '',
				'fields'	=> array(
					'hover_effects' => array(
						'type'          => 'select',
						'label'         => __('Image Hover Effect', 'bb-powerpack'),
						'default'       => 'zoom',
						'options'       => array(
							'none' 			=> __('None', 'bb-powerpack'),
							'zoom-in'		=> __('Zoom', 'bb-powerpack'),
						),
						'preview'	=> 'none',
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
                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
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
                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
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
							'selector'        => '.pp-photo-gallery-item, .pp-masonry-item, .pp-photo-gallery-item img, .pp-masonry-item img, .pp-gallery-overlay',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
					'photo_padding' 	=> array(
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
		                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
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
		                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
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
		                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
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
		                            'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
		                            'property'        => 'padding-right',
		                            'unit'            => 'px'
		                        )
                    		),
                    	)
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
							'selector'        => '.pp-photo-gallery-item, .pp-masonry-item',
							'property'        => 'border-radius',
							'unit'            => 'px'
						)
					),
				)
			),
			'overlay_style'       => array(
				'title'         => __( 'Overlay', 'bb-powerpack' ),
				'fields'        => array(
					'overlay_color' => array(
						'type'       => 'color',
						'label'     => __('Color', 'bb-powerpack'),
						'default'	=> '000000',
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
						'label'         => __('Border Radius', 'bb-powerpack'),
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
					'overlay_icon_padding' 	=> array(
						'type'          => 'text',
						'label'         => __('Padding', 'bb-powerpack'),
						'default'   	=> '',
						'maxlength'     => 5,
						'size'          => 6,
						'description'   => 'px',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-gallery-overlay .pp-overlay-icon span',
							'property'	=> 'padding',
							'unit'		=> 'px'
						),
                    ),
				)
			),
			'caption_style'	=> array(
				'title'		=> __('Caption', 'bb-powerpack'),
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
			'filters_style'	=> array(
				'title'		=> __( 'Filters', 'bb-powerpack' ),
				'fields'	=> array(
					'filter_background'      => array(
						'type'      => 'pp-color',
                        'label'     => __('Background Color', 'bb-powerpack'),
						'show_reset' => true,
                        'default'   => array(
							'primary'	=> 'eeeeee',
							'secondary'	=> 'bbbbbb'
						),
						'options'	=> array(
							'primary'	=> __('Default', 'bb-powerpack'),
							'secondary' => __('Active', 'bb-powerpack')
						)
                    ),
					'filter_color'	=>array(
						'type'      => 'pp-color',
						'label'     => __('Text Color', 'bb-powerpack'),
						'show_reset' => true,
						'default'   => array(
							'primary'	=> '000000',
							'secondary'	=> 'ffffff'
						),
						'options'	=> array(
							'primary'	=> __('Default', 'bb-powerpack'),
							'secondary' => __('Active', 'bb-powerpack')
						)
					),
					'filter_border'    => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Border Style', 'bb-powerpack'),
                        'default'   => 'none',
                        'options'   => array(
                            'none'  => __('None', 'bb-powerpack'),
                            'solid'  => __('Solid', 'bb-powerpack'),
							'dashed'  => __('Dashed', 'bb-powerpack'),
							'dotted'  => __('Dotted', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'solid'   => array(
                                'fields'    => array('filter_border_width', 'filter_border_color')
                            ),
							'dashed'   => array(
                                'fields'    => array('filter_border_width', 'filter_border_color')
                            ),
							'dotted'   => array(
                                'fields'    => array('filter_border_width', 'filter_border_color')
                            ),
                        ),
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-gallery-filters li',
							'property'        => 'border-style',
                        ),
                    ),
                    'filter_border_width'   => array(
						'type' 			=> 'pp-multitext',
                    	'label' 		=> __('Border Width', 'bb-powerpack'),
                        'description'   => __( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'default'       => array(
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left' => 1,
                        ),
                    	'options' 		=> array(
                    		'top' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Top', 'bb-powerpack'),
                                'tooltip'       => __('Top', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-up',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'border-top-width',
									'unit'            => 'px'
		                        ),
                    		),
                            'bottom' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Bottom', 'bb-powerpack'),
                                'tooltip'       => __('Bottom', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-down',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
		                        ),
                    		),
                            'left' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Left', 'bb-powerpack'),
                                'tooltip'       => __('Left', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-left',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'border-left-width',
									'unit'            => 'px'
		                        ),
                    		),
                            'right' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Right', 'bb-powerpack'),
                                'tooltip'       => __('Right', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-right',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'border-right-width',
									'unit'            => 'px'
		                        ),
                    		),
                    	)
                    ),
                    'filter_border_color'   => array(
						'type'      => 'pp-color',
                        'label'     => __('Border Color', 'bb-powerpack'),
						'show_reset' => true,
                        'default'   => array(
							'primary'	=> '000000',
							'secondary'	=> 'eeeeee'
						),
						'options'	=> array(
							'primary'	=> __('Default', 'bb-powerpack'),
							'secondary' => __('Hover', 'bb-powerpack')
						)
                    ),
					'filter_border_radius'   => array(
                        'type'      => 'text',
                        'label'     => __('Round Corners', 'bb-powerpack'),
                        'size'      => 5,
                        'maxlength' => 3,
                        'default'   => 0,
                        'description'   => 'px',
                        'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-gallery-filters li',
							'property'        => 'border-radius',
							'unit'            => 'px'
                        ),
                    ),
					'filter_padding' 	=> array(
                    	'type' 			=> 'pp-multitext',
                    	'label' 		=> __('Padding', 'bb-powerpack'),
                        'description'   => __( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'default'       => array(
                            'top' => 8,
                            'right' => 10,
                            'bottom' => 8,
                            'left' => 10,
                        ),
                    	'options' 		=> array(
                    		'top' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Top', 'bb-powerpack'),
                                'tooltip'       => __('Top', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-up',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'padding-top',
									'unit'            => 'px'
		                        ),
                    		),
                            'bottom' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Bottom', 'bb-powerpack'),
                                'tooltip'       => __('Bottom', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-down',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'padding-bottom',
									'unit'            => 'px'
		                        ),
                    		),
                            'left' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Left', 'bb-powerpack'),
                                'tooltip'       => __('Left', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-left',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'padding-left',
									'unit'            => 'px'
		                        ),
                    		),
                            'right' => array(
                                'maxlength' => 3,
                                'placeholder'   => __('Right', 'bb-powerpack'),
                                'tooltip'       => __('Right', 'bb-powerpack'),
                    			'icon'		=> 'fa-long-arrow-right',
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'padding-right',
									'unit'            => 'px'
		                        ),
                    		),
                    	)
                    ),
					'filter_margin' 	=> array(
						'type'      => 'text',
                        'label'     => __('Horizontal Spacing', 'bb-powerpack'),
                        'size'      => 5,
                        'maxlength' => 3,
                        'default'   => 10,
                        'description'   => 'px',
                        'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-gallery-filters li',
							'property'        => 'margin-right',
							'unit'            => 'px'
                        ),
                    ),
					'filter_margin_bottom' 	=> array(
						'type'      => 'text',
                        'label'     => __('Vertical Spacing', 'bb-powerpack'),
                        'size'      => 5,
                        'maxlength' => 3,
                        'default'   => 30,
                        'description'   => 'px',
                        'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-gallery-filters',
							'property'        => 'margin-bottom',
							'unit'            => 'px'
                        ),
                    ),
					'filter_alignment'    => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Alignment', 'bb-powerpack'),
                        'default'   => 'left',
                        'options'   => array(
                            'left'  => __('Left', 'bb-powerpack'),
                            'center'  => __('Center', 'bb-powerpack'),
                            'Right'  => __('Right', 'bb-powerpack'),
                        ),
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-gallery-filters',
							'property'        => 'text-align',
                        ),
                    ),
				)
			)
		)
	),
	'typography'	=> array(
		'title'	=> __( 'Typography', 'bb-powerpack' ),
		'sections'	=> array(
			'general_typography'	=> array(
				'title'	=> __( 'Caption', 'bb-powerpack' ),
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
					'caption_custom_font_size'	=> array(
						'type' 		=> 'pp-multitext',
						'label'		=> __('Custom Font Size', 'bb-powerpack'),
						'default'		=> array(
							'desktop'	=> 16,
							'tablet'	=> '',
							'mobile'	=> '',
						),
						'options' 		=> array(
							'desktop' => array(
								'icon'		=> 'fa-desktop',
								'placeholder'	=> __('Desktop', 'bb-powerpack'),
								'tooltip'	=> __('Desktop', 'bb-powerpack'),
								'preview'       => array(
									'selector'        => '.pp-photo-gallery-caption, .pp-gallery-overlay .pp-caption',
									'property'        => 'font-size',
									'unit'            => 'px'
		                        ),
							),
							'tablet' => array(
								'icon'		=> 'fa-tablet',
								'placeholder'	=> __('Tablet', 'bb-powerpack'),
								'tooltip'	=> __('Tablet', 'bb-powerpack'),
							),
							'mobile' => array(
								'icon'		=> 'fa-mobile',
								'placeholder'	=> __('Mobile', 'bb-powerpack'),
								'tooltip'	=> __('Mobile', 'bb-powerpack'),
							),

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
			),
			'filter_typography'  => array(
                'title' => __('Filter', 'bb-powerpack'),
                'fields'    => array(
                    'filter_font'    => array(
                        'type'      => 'font',
						'label'         => __('Font', 'bb-powerpack'),
                        'default'		=> array(
                            'family'		=> 'Default',
                            'weight'		=> 300
                        ),
						'preview'       => array(
							'type'		=> 'font',
							'selector'        => '.pp-gallery-filters li',
						),
                    ),
                    'filter_font_size' 	=> array(
                    	'type' 			=> 'pp-multitext',
                    	'label' 		=> __('Font Size', 'bb-powerpack'),
                        'default'       => array(
                            'desktop' => 14,
                            'tablet' => '',
                            'mobile' => ''
                        ),
                    	'options' 		=> array(
                    		'desktop' => array(
                                'maxlength' => 3,
                    			'icon'		=> 'fa-desktop',
								'placeholder'	=> __('Desktop', 'bb-powerpack'),
								'tooltip'	=> __('Desktop', 'bb-powerpack'),
								'preview'       => array(
									'selector'        => '.pp-gallery-filters li',
									'property'        => 'font-size',
									'unit'			=> 'px'
								),
                    		),
                            'tablet' => array(
                                'maxlength' => 3,
                    			'icon'		=> 'fa-tablet',
								'placeholder'	=> __('Tablet', 'bb-powerpack'),
								'tooltip'	=> __('Tablet', 'bb-powerpack'),
                    		),
                            'mobile' => array(
                                'maxlength' => 3,
                    			'icon'		=> 'fa-mobile',
								'placeholder'	=> __('Mobile', 'bb-powerpack'),
								'tooltip'	=> __('Mobile', 'bb-powerpack'),
                    		),
                    	)
                    ),
					'filter_text_transform' => array(
						'type'		=> 'select',
						'label'		=> __('Text Transform', 'bb-powerpack'),
						'default'	=> 'default',
						'options'       => array(
							'default'          => __('Default', 'bb-powerpack'),
							'lowercase'         => __('lowercase', 'bb-powerpack'),
							'uppercase'         => __('UPPERCASE', 'bb-powerpack'),
						),
					),
                ),
            ),
		)
	)
));

FLBuilder::register_settings_form('pp_gallery_filter_form', array(
	'title' => __( 'Add Filter', 'bb-powerpack' ),
	'tabs'	=> array(
		'general'	=> array(
			'title'	=> __( 'General', 'bb-powerpack' ),
			'sections'	=> array(
				'filters'	=> array(
					'title'		=> '',
					'fields'	=> array(
						'filter_label'     => array(
							'type'          => 'text',
							'label'         => __( 'Filter Label', 'bb-powerpack' ),
							'placeholder'   => '',
							'connections'	=> array('string')
						),
						'gallery_photos' => array(
						    'type'          => 'multiple-photos',
						    'label'         => __( 'Photos', 'bb-powerpack' ),
                            'connections'   => array('photo')
						),
					)
				)
			)
		)
	)
));
