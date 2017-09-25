<?php

/**
 * @class PPFlipBoxModule
 */
class PPFlipBoxModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Flip Box', 'bb-powerpack'),
            'description'   => __('Addon to display flip box.', 'bb-powerpack'),
            'category'		=> BB_POWERPACK_CAT,
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-flipbox/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-flipbox/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh'   => true
        ));

        /**
         * Use these methods to enqueue css and js already
         * registered or to register and enqueue your own.
         */
        // Already registered
		$this->add_css('font-awesome');
    }

    /**
     * Use this method to work with settings data before
     * it is saved. You must return the settings object.
     *
     * @method update
     * @param $settings {object}
     */
    public function update($settings)
    {
        return $settings;
    }

    /**
     * This method will be called by the builder
     * right before the module is deleted.
     *
     * @method delete
     */
    public function delete()
    {

    }

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPFlipBoxModule', array(
	'general'      => array( // Tab
		'title'         => __('General', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
            'type'      => array(
                'title'     => __('Icon Type', 'bb-powerpack'),
                'fields'    => array(
                    'icon_type'      => array(
                        'type'      => 'select',
                        'label'     => __('Type', 'bb-powerpack'),
                        'default'   => 'icon',
                        'options'   => array(
                            'none'      => __('None', 'bb-powerpack'),
                            'icon'      => __('Icon', 'bb-powerpack'),
                            'image'      => __('Image', 'bb-powerpack'),
                        ),
                        'toggle'        => array(
                            'icon'      => array(
                                'fields'        => array('icon_select', 'icon_color', 'icon_font_size', 'icon_color_hover', 'icon_background', 'icon_background_hover', 'icon_width'),
                                'tabs'      => array('icon_styles')
                            ),
                            'image'      => array(
                                'fields'        => array('image_select', 'image_width'),
                                'tabs'      => array('icon_styles')
                            ),
                        ),
                    ),
                    'icon_select'       => array(
                        'type'      => 'icon',
                        'label'     => __('Icon', 'bb-powerpack'),
                    ),
                    'image_select'       => array(
                        'type'      => 'photo',
                        'label'     => __('Image Icon', 'bb-powerpack'),
                    ),
                ),
            ),
            'flip_type'     => array(
                'title'     => __('Flip Type', 'bb-powerpack'),
                'fields'    => array(
                    'flip_type'     => array(
                        'type'      => 'select',
                        'label'     => __('Flip Type', 'bb-powerpack'),
                        'default'     => 'left',
                        'options'   => array(
                            'left'  => __('Flip horizontally from left', 'bb-powerpack'),
                            'right'  => __('Flip horizontally from right', 'bb-powerpack'),
                            'top'  => __('Flip vertically from top', 'bb-powerpack'),
                            'bottom'  => __('Flip vertically from bottom', 'bb-powerpack'),
                        ),
                    ),
                    'flip_duration'     => array(
                        'type'      => 'text',
                        'label'     => __('Flip Duration', 'bb-powerpack'),
                        'size'      => '5',
                        'maxlength' => '4',
                        'description'   => __('ms', 'bb-powerpack'),
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-flipbox',
                            'property'  => 'transition-duration'
                        )
                    ),
                ),
            ),
            'box_setting'     => array(
                'title'     => __('Box Styling', 'bb-powerpack'),
                'fields'    => array(
                    'box_border_style'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Border Style', 'bb-powerpack'),
                        'default'     => 'none',
                        'options'   => array(
                            'none'  => __('None', 'bb-powerpack'),
                            'solid'  => __('Solid', 'bb-powerpack'),
                            'dashed' => __('Dashed', 'bb-powerpack'),
                            'dotted'  => __('Dotted', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'solid'     => array(
                                'fields'    => array('box_border_width')
                            ),
                            'dashed'     => array(
                                'fields'    => array('box_border_width')
                            ),
                            'dotted'     => array(
                                'fields'    => array('box_border_width')
                            )
                        ),
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-flipbox',
                            'property'  => 'border-style'
                        ),
                    ),
                    'box_border_width'     => array(
                        'type'      => 'text',
                        'label'     => __('Border Width', 'bb-powerpack'),
                        'default'     => 1,
                        'size'      => 5,
                        'maxlength' => 2,
                        'description'   => 'px',
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-flipbox',
                            'property'  => 'border-width',
                            'unit'      => 'px'
                        ),
                    ),
                    'top_padding'   => array(
                        'type'      => 'text',
                        'label'     => __('Top/Bottom Padding', 'bb-powerpack'),
                        'default'   => '20',
                        'size'      => '5',
                        'maxlength' => '3',
                        'description'   => 'px',
                        'preview'   => array(
                            'type'  => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-flipbox',
                                    'property'  => 'padding-top',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-flipbox',
                                    'property'  => 'padding-bottom',
                                    'unit'      => 'px'
                                ),
                            ),
                        ),
                    ),
                    'side_padding'   => array(
                        'type'      => 'text',
                        'label'     => __('Left/Right Padding', 'bb-powerpack'),
                        'default'   => '20',
                        'size'      => '5',
                        'maxlength' => '3',
                        'description'   => 'px',
                        'preview'   => array(
                            'type'  => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-flipbox',
                                    'property'  => 'padding-left',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-flipbox',
                                    'property'  => 'padding-right',
                                    'unit'      => 'px'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
		)
	),
    'content_front'       => array(
        'title'     => __('Front Content', 'bb-powerpack'),
        'sections'  => array(
            'content_front'     => array(
                'title'             => __('Front Content', 'bb-powerpack'),
                'fields'            => array(
                    'front_title'       => array(
                        'label'             => __('Title', 'bb-powerpack'),
                        'type'              => 'text',
                        'default'           => __('Front Title', 'bb-powerpack'),
                        'preview'           => array(
                            'type'              => 'text',
                            'selector'          => '.pp-flipbox-front .pp-flipbox-title h3'
                        )
                    ),
                    'front_description'     => array(
                        'type'                  => 'editor',
                        'label'                 => '',
                        'default'               => __('Front Description', 'bb-powerpack'),
                        'preview'               => array(
                            'type'                  => 'text',
                            'selector'              => '.pp-flipbox-front .pp-flipbox-description'
                        )
                    ),
                ),
            ),
            'front_style'     => array(
                'title'     => __('Box Style', 'bb-powerpack'),
                'fields'    => array(
                    'front_background'    => array(
						'type'          => 'color',
						'label'         => __('Background Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front',
                            'property'      => 'background',
                        )
					),
                    'front_border_color'    => array(
						'type'          => 'color',
						'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front',
                            'property'      => 'border-color',
                        )
					),
                )
            ),
            'front_typography'      => array(
                'title'                 => __('Typography', 'bb-powerpack'),
                'fields'                => array(
                    'front_title_tag'   => array(
                        'type'              => 'select',
                        'label'             => __('HTML Tag', 'bb-powerpack'),
                        'default'           => 'h3',
                        'options'           => array(
                            'h1'                => 'H1',
                            'h2'                => 'H2',
                            'h3'                => 'H3',
                            'h4'                => 'H4',
                            'h5'                => 'H5',
                            'h6'                => 'H6',
                        )
                    ),
                    'front_title_font'          => array(
                        'type'          => 'font',
                        'default'		=> array(
                            'family'		=> 'Default',
                            'weight'		=> 300
                        ),
                        'label'         => __('Title Font', 'bb-powerpack'),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.pp-flipbox-front .pp-flipbox-title h3'
                        )
                    ),
                    'front_title_font_size'    => array(
						'type'          => 'text',
                        'label'         => __('Title Font Size', 'bb-powerpack'),
                        'size'          => '5',
                        'maxlength'     => '2',
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front .pp-flipbox-title h3',
                            'property'      => 'font-size',
                            'unit'          => 'px'
                        )
					),
                    'front_title_color' => array(
						'type'          => 'color',
						'label'         => __('Title Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front .pp-flipbox-title h3',
                            'property'      => 'color',
                        )
					),
                    'front_text_font'   => array(
                        'type'              => 'font',
                        'default'		    => array(
                            'family'		  => 'Default',
                            'weight'		  => 300
                        ),
                        'label'             => __('Description Font', 'bb-powerpack'),
                        'preview'           => array(
                            'type'            => 'font',
                            'selector'        => '.pp-flipbox-front .pp-flipbox-description'
                        )
                    ),
                    'front_text_font_size'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '2',
						'label'         => __('Description Font Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front .pp-flipbox-description',
                            'property'      => 'font-size',
                            'unit'          => 'px'
                        )
					),
                    'front_text_color'    => array(
						'type'          => 'color',
						'label'         => __('Description Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-front .pp-flipbox-description',
                            'property'      => 'color',
                        )
					),
                ),
            )

        ),
    ),
    'content_back'      => array( // Tab
		'title'         => __('Back Content', 'bb-powerpack'), // Tab title
		'sections'      => array( // Tab Sections
            'content_back'     => array(
                'title'     => __('Back Content', 'bb-powerpack'),
                'fields'    => array(
                    'back_title'     => array(
                        'label'     => __('Title', 'bb-powerpack'),
                        'type'      => 'text',
                        'preview'       => array(
                            'type'          => 'text',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-title h3'
                        )
                    ),
                    'back_description'     => array(
                        'type'      => 'editor',
                        'label'     => '',
                        'default'     => '',
                        'preview'       => array(
                            'type'          => 'text',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-description'
                        )
                    ),
                ),
            ),
            'link_type'     => array(
                'title'     => __('Link', 'bb-powerpack'),
                'fields'    => array(
                    'link_type'     => array(
                        'type'      => 'select',
                        'label'     => __('Link Type', 'bb-powerpack'),
                        'default'     => 'none',
                        'options'   => array(
                            'none'  => __('None', 'bb-powerpack'),
                            'custom'  => __('Button', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'custom'     => array(
                                'fields'    => array('link_text', 'link_color', 'link_color_hover', 'link_background', 'link_background_hover', 'link_font', 'link_font_size', 'link', 'link_target')
                            ),
                        )
                    ),
                    'link_text'     => array(
                        'type'      => 'text',
                        'label'         => __('Text', 'bb-powerpack'),
                        'default'       => __('Know More', 'bb-powerpack'),
                        'preview'       => array(
                            'type'      => 'text',
                            'selector'  => '.pp-more-link'
                        ),
                    ),
                    'link'  => array(
                        'type'          => 'link',
						'label'         => __('Link', 'bb-powerpack'),
						'placeholder'   => __( 'http://www.example.com', 'bb-powerpack' ),
						'preview'       => array(
							'type'          => 'none'
						)
                    ),
                    'link_target'   => array(
						'type'          => 'select',
						'label'         => __('Link Target', 'bb-powerpack'),
						'default'       => '_self',
						'options'       => array(
							'_self'         => __('Same Window', 'bb-powerpack'),
							'_blank'        => __('New Window', 'bb-powerpack')
						),
						'preview'         => array(
							'type'            => 'none'
						)
					),
                    'link_color'    => array(
                        'type'      => 'color',
                        'label'     => __('Text Color', 'bb-powerpack'),
                        'default'   => '000000',
                        'show_reset'    => true,
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link',
                            'property'  => 'color'
                        ),
                    ),
                    'link_color_hover'    => array(
                        'type'      => 'color',
                        'label'     => __('Text Hover Color', 'bb-powerpack'),
                        'default'   => 'dddddd',
                        'show_reset'    => true,
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link:hover',
                            'property'  => 'color'
                        ),
                    ),
                    'link_background'    => array(
                        'type'      => 'color',
                        'label'     => __('Background Color', 'bb-powerpack'),
                        'default'   => 'ffffff',
                        'show_reset'    => true,
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link',
                            'property'  => 'background'
                        ),
                    ),
                    'link_background_hover'    => array(
                        'type'      => 'color',
                        'label'     => __('Background Color Hover', 'bb-powerpack'),
                        'default'   => 'ffffff',
                        'show_reset'    => true,
                        'preview'   => array(
                            'type'  => 'css',
                            'selector'  => '.pp-more-link:hover',
                            'property'  => 'background'
                        ),
                    ),
                ),
            ),
            'back_style'     => array(
                'title'     => __('Box Style', 'bb-powerpack'),
                'fields'    => array(
                    'back_background'    => array(
						'type'          => 'color',
						'label'         => __('Background Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back',
                            'property'      => 'background',
                        )
					),
                    'back_border_color'    => array(
						'type'          => 'color',
						'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back',
                            'property'      => 'border-color',
                        )
					),
                )
            ),
            'back_typography'       => array(
                'title'                 => __('Typography', 'bb-powerpack'),
                'fields'                => array(
                    'back_title_tag'   => array(
                        'type'              => 'select',
                        'label'             => __('HTML Tag', 'bb-powerpack'),
                        'default'           => 'h3',
                        'options'           => array(
                            'h1'                => 'H1',
                            'h2'                => 'H2',
                            'h3'                => 'H3',
                            'h4'                => 'H4',
                            'h5'                => 'H5',
                            'h6'                => 'H6',
                        )
                    ),
                    'back_title_font'          => array(
                        'type'          => 'font',
                        'default'		=> array(
                            'family'		=> 'Default',
                            'weight'		=> 300
                        ),
                        'label'         => __('Title Font', 'bb-powerpack'),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.pp-flipbox-back .pp-flipbox-title h3'
                        )
                    ),
                    'back_title_font_size'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '2',
						'label'         => __('Title Font Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-title h3',
                            'property'      => 'font-size',
                            'unit'          => 'px'
                        )
					),
                    'back_title_color'    => array(
						'type'          => 'color',
						'label'         => __('Title Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-title h3',
                            'property'      => 'color',
                        )
					),
                    'back_text_font'          => array(
                        'type'          => 'font',
                        'default'		=> array(
                            'family'		=> 'Default',
                            'weight'		=> 300
                        ),
                        'label'         => __('Description Font', 'bb-powerpack'),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.pp-flipbox-back .pp-flipbox-description'
                        )
                    ),
                    'back_text_font_size'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '2',
						'label'         => __('Description Font Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-description',
                            'property'      => 'font-size',
                            'unit'          => 'px'
                        )
					),
                    'back_text_color'    => array(
						'type'          => 'color',
						'label'         => __('Description Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-back .pp-flipbox-description',
                            'property'      => 'color',
                        )
					),
                    'link_font'          => array(
                        'type'          => 'font',
                        'default'		=> array(
                            'family'		=> 'Default',
                            'weight'		=> 300
                        ),
                        'label'         => __('Link Font', 'bb-powerpack'),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.pp-more-link'
                        )
                    ),
                    'link_font_size'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '2',
						'label'         => __('Link Font Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-more-link',
                            'property'      => 'font-size',
                            'unit'          => 'px'
                        )
					),
                ),
            ),
		)
	),
    'icon_styles'   => array(
        'title'     => __('Icon Style', 'bb-powerpack'),
        'sections'  => array(
            'icon_sizes'   => array(
                'title'     => __('Sizes', 'bb-powerpack'),
                'fields'    => array(
                    'icon_font_size'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '2',
						'label'         => __('Icon Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'Value unit for font size. Such as: "14 px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon-inner span.pp-icon',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-icon-inner span.pp-icon:before',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
					),
                    'icon_width'    => array(
                        'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '3',
                        'default'       => '0',
						'label'         => __('Icon Box Size', 'bb-powerpack'),
                        'description'   => _x( 'px', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'           => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon-inner',
                                    'property'     => 'width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-icon-inner',
                                    'property'     => 'height',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'image_width'    => array(
						'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '3',
                        'default'     => '100',
						'label'         => __('Icon Size', 'bb-powerpack'),
						'description'   => _x( 'px', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'           => array(
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'     => 'width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'     => 'height',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
					),
                )
            ),
            'icon_colors'   => array(
                'title'         => __('Colors', 'bb-powerpack'),
                'fields'        => array(
                    'icon_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-icon-inner',
                            'property'      => 'color',
                        )
					),
                    'icon_color_hover'    => array(
						'type'          => 'color',
						'label'         => __('Color Hover', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-icon:hover-inner',
                            'property'      => 'color',
                        )
					),
                    'icon_background'    => array(
						'type'          => 'color',
						'label'         => __('Background', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-icon-inner',
                            'property'      => 'background',
                        )
					),
                    'icon_background_hover'    => array(
						'type'          => 'color',
						'label'         => __('Background Hover', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-flipbox-icon-inner:hover',
                            'property'      => 'background',
                        )
					),
                )
            ),
            'icon_border'   => array(
                'title'         => __('Border', 'bb-powerpack'),
                'fields'        => array(
                    'show_border'   => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Show Border', 'bb-powerpack'),
                        'default'   => 'no',
                        'options'   => array(
                            'no'    => __('No', 'bb-powerpack'),
                            'yes'    => __('Yes', 'bb-powerpack'),
                        ),
                        'toggle'    => array(
                            'yes'   => array(
                                'fields'    => array('icon_border_width', 'icon_border_color', 'icon_border_color_hover', 'icon_box_size')
                            )
                        ),
                    ),
                    'icon_border_width'    => array(
						'type'          => 'text',
						'label'         => __('Border Width', 'bb-powerpack'),
						'default'       => 1,
                        'size'          => 5,
                        'maxlength'     => 2,
                        'description'   => _x( 'px', 'Value unit for border width. Such as: "10px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
					),
                    'icon_border_color'    => array(
						'type'          => 'color',
						'label'         => __('Border Color', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon',
                                    'property'      => 'border-color',
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
					),
                    'icon_border_color_hover'    => array(
						'type'          => 'color',
						'label'         => __('Border Color Hover', 'bb-powerpack'),
						'show_reset'    => true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon:hover',
                                    'property'      => 'border-color',
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-image img:hover',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
					),
                    'icon_box_size'     => array(
                        'type'          => 'text',
                        'size'          => '5',
                        'maxlength'     => '3',
                        'default'     => '0',
                        'label'         => __('Inside Spacing', 'bb-powerpack'),
						'description'   => _x( 'px', 'bb-powerpack' ),
                        'help'      => __('The space between icon and the border', 'bb-powerpack'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'           => array(
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-icon',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'icon_border_radius'    => array(
						'type'          => 'text',
						'label'         => __('Round Corners', 'bb-powerpack'),
						'default'       => 0,
                        'size'          => 5,
                        'maxlength'     => 3,
                        'description'   => _x( 'px', 'Value unit for border radius. Such as: "10px"', 'bb-powerpack' ),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-flipbox-icon',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-icon-inner',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-flipbox-image img',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
					),
                ),
            ),
        ),
    )
));
