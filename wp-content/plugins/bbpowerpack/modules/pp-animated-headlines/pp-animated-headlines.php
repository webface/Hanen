<?php

/**
 * @class PPAnimatedHeadlines2Module
 */
class PPAnimatedHeadlinesModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Animated Headlines', 'bb-powerpack'),
			'description'   => __('Awesome Animated Headlines module.', 'bb-powerpack'),
			'group'         => pp_get_modules_group(),
			'category'      => pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-animated-headlines/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-animated-headlines/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
		));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPAnimatedHeadlinesModule', array(
	'general'       => array(
		'title'         => __('General', 'bb-powerpack'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'headline_style'	=> array(
						'type'			=> 'select',
						'label'			=> __('Style', 'bb-powerpack'),
						'default'		=> 'highlight',
						'options'		=> array(
							'highlight'		=> __('Highlighted', 'bb-powerpack'),
							'rotate'		=> __('Rotating', 'bb-powerpack')
						),
						'toggle'		=> array(
							'highlight'		=> array(
								'tabs'			=> array('style'),
								'fields'		=> array('headline_shape', 'highlighted_text')
							),
							'rotate'		=> array(
								'fields'		=> array('rotating_text', 'animation_type')
							)
						)
					),
					'headline_shape'	=> array(
						'type'				=> 'select',
						'label'				=> __('Shape', 'bb-powerpack'),
						'default'			=> 'circle',
						'options'			=> array(
							'circle'			=> __('Circle', 'bb-powerpack'),
							'curly'				=> __('Curly', 'bb-powerpack'),
							'strikethrough'		=> __('Strikethrough', 'bb-powerpack'),
							'underline'			=> __('Underline', 'bb-powerpack'),
							'underline_zigzag'	=> __('Underline Zigzag', 'bb-powerpack'),
						)
					),
					'animation_type'	=> array(
						'type'				=> 'select',
						'label'				=> __('Animation', 'bb-powerpack'),
						'default'			=> 'typing',
						'options'			=> array(
							'typing' 			=> __('Typing', 'bb-powerpack'),
							'clip' 				=> __('Clip', 'bb-powerpack'),
							'flip' 				=> __('Flip', 'bb-powerpack'),
							'swirl' 			=> __('Swirl', 'bb-powerpack'),
							'blinds' 			=> __('Blinds', 'bb-powerpack'),
							'drop-in' 			=> __('Drop-in', 'bb-powerpack'),
							'wave' 				=> __('Wave', 'bb-powerpack'),
							'slide' 			=> __('Slide', 'bb-powerpack'),
							'slide-down' 		=> __('Slide Down', 'bb-powerpack'),
						)
					),
					'before_text'  	=> array(
						'type'          => 'text',
						'label'         => __('Before Text', 'bb-powerpack'),
						'default'       => __('This is', 'bb-powerpack'),
						'help'			=> __('Text placed before animated text.', 'bb-powerpack')
					),
					'highlighted_text'	=> array(
						'type'				=> 'text',
						'label'				=> __('Highlighted Text', 'bb-powerpack'),
						'default'			=> __('Awesome', 'bb-powerpack')
					),
					'rotating_text'	=> array(
						'type'          => 'textarea',
						'label'         => __('Rotating Text', 'bb-powerpack'),
						'default'       => __("Awesome\nCreative\nRotating", 'bb-powerpack'),
						'rows'          => '5',
						'help'			=> __('Text with animated effects. You can add multiple text by adding each on a new line.', 'bb-powerpack')
					),
					'after_text'	=> array(
						'type'           => 'text',
						'label'          => __('After Text', 'bb-powerpack'),
						'default'        => __('Headline!', 'bb-powerpack'),
						'help'			 => __('Text placed at the end of animated text.', 'bb-powerpack')
					),
					'alignment'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Alignment', 'bb-powerpack'),
						'default'       => 'left',
						'options'       => array(
							'left'      =>  __('Left', 'bb-powerpack'),
							'center'    =>  __('Center', 'bb-powerpack'),
							'right'     =>  __('Right', 'bb-powerpack')
						),
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-headline',
							'property'        => 'text-align'
						),
					),
				)
			),
		)
	),
	'style'		=> array(
		'title'		=> __('Style', 'bb-powerpack'),
		'sections'	=> array(
			'shape_style'	=> array(
				'title'			=> __('Shape', 'bb-powerpack'),
				'fields'		=> array(
					'shape_color'	=> array(
						'type'			=> 'color',
						'label'			=> __('Color', 'bb-powerpack'),
						'default'		=> '',
						'show_reset'	=> true,
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-headline-dynamic-wrapper path',
							'property'        => 'stroke'
						),
					),
					'shape_width'	=> array(
						'type'			=> 'text',
						'label'			=> __('Width', 'bb-powerpack'),
						'default'		=> '',
						'size'			=> 5,
						'description'	=> 'px',
						'preview'       => array(
							'type'            => 'css',
							'selector'        => '.pp-headline-dynamic-wrapper path',
							'property'        => 'stroke-width'
						),
					)
				)
			)
		)
	),
	'typography'	=> array(
		'title'			=> __('Typography', 'bb-powerpack'),
		'sections'		=> array(
			'text_tag'	=> array(
				'title'		=> '',
				'fields'	=> array(
					'headline_tag'   => array(
		                'type'          => 'select',
		                'label'         => __('Title Tag', 'bb-powerpack'),
		                'default'       => 'h3',
		                'options'       => array(
		                	'h1'	  => __('H1', 'bb-powerpack'),
		                    'h2'      => __('H2', 'bb-powerpack'),
		                    'h3'      => __('H3', 'bb-powerpack'),
		                    'h4'      => __('H4', 'bb-powerpack'),
		                    'h5'      => __('H5', 'bb-powerpack'),
		                    'h6'      => __('H6', 'bb-powerpack'),
		                )
		            ),
				)
			),
			'headline_typography' => array(
				'title' 			=> __('Headline Text', 'bb-powerpack' ),
                'fields'   			=> array(
                    'font_family'       => array(
                        'type'          => 'font',
                        'label'         => __('Font Family', 'bb-powerpack'),
                        'default'       => array(
                            'family'        => 'Default',
                            'weight'        => 'Default'
                        ),
                        'preview'	=> array(
                            'type'		=> 'font',
                            'selector'	=> '.pp-headline'
                    	),
                    ),
					'font_size'     => array(
                        'type'          => 'pp-switch',
						'label'         => __('Font Size', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('font_size_custom')
							)
						)
                    ),
                    'font_size_custom'  => array(
                        'type'              => 'unit',
						'label'             => __('Custom Font Size', 'bb-powerpack'),
						'description' 		=> 'px',
						'responsive' 		=> array(
							'placeholder' 		=> array(
								'default' 			=> '',
								'medium' 			=> '',
								'responsive' 		=> '',
							),
						),
						'preview'			=> array(
							'type' 				=> 'css',
							'selector'			=> '.pp-headline',
							'property'      	=> 'font-size',
							'unit'				=> 'px',
						),
                    ),
					'line_height'   => array(
                        'type'          => 'pp-switch',
						'label'         => __('Line Height', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('line_height_custom')
							)
						)
                    ),
                    'line_height_custom' => array(
						'type'              => 'unit',
						'label'             => __('Line Height Custom', 'bb-powerpack'),
						'responsive' 		=> array(
							'placeholder' 		=> array(
								'default' 			=> '',
								'medium' 			=> '',
								'responsive' 		=> '',
							),
						),
						'preview'			=> array(
							'type' 				=> 'css',
							'selector'			=> '.pp-headline',
							'property'      	=> 'line-height',
						),
                    ),
                    'color'        	=> array(
                        'type'       	=> 'color',
                        'label'      	=> __('Color', 'bb-powerpack'),
                        'default'    	=> '',
                        'show_reset' 	=> true,
                    	'preview'		=> array(
                            'type'			=> 'css',
                            'selector'		=> '.pp-headline',
                            'property'		=> 'color'
                    	),
                    ),
                )
            ),
			'animated_text_typography' => array(
				'title' => __('Animating Text', 'bb-powerpack' ),
                'fields'    => array(
                    'animated_font_family'       => array(
                        'type'          => 'font',
                        'label'         => __('Font Family', 'bb-powerpack'),
                        'default'       => array(
                            'family'        => 'Default',
                            'weight'        => 'Default'
                        ),
                        'preview'	=> array(
                            'type'		=> 'font',
                            'selector'	=> '.pp-headline-dynamic-wrapper'
                    	),
                    ),
					'animated_font_size'     => array(
                        'type'          => 'pp-switch',
						'label'         => __('Font Size', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('animated_font_size_custom')
							)
						)
                    ),
					'animated_font_size_custom'  => array(
                        'type'              => 'unit',
						'label'             => __('Custom Font Size', 'bb-powerpack'),
						'description' 		=> 'px',
						'responsive' 		=> array(
							'placeholder' 		=> array(
								'default' 			=> '',
								'medium' 			=> '',
								'responsive' 		=> '',
							),
						),
						'preview'			=> array(
							'type' 				=> 'css',
							'selector'			=> '.pp-headline-dynamic-wrapper',
							'property'      	=> 'font-size',
							'unit'				=> 'px',
						),
                    ),
					'animated_line_height'   => array(
                        'type'          => 'pp-switch',
						'label'         => __('Line Height', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('animated_line_height_custom')
							)
						)
                    ),
					'animated_line_height_custom'  => array(
                        'type'              => 'unit',
						'label'             => __('Line Height Custom', 'bb-powerpack'),
						'description' 		=> 'px',
						'responsive' 		=> array(
							'placeholder' 		=> array(
								'default' 			=> '',
								'medium' 			=> '',
								'responsive' 		=> '',
							),
						),
						'preview'			=> array(
							'type' 				=> 'css',
							'selector'			=> '.pp-headline-dynamic-wrapper',
							'property'      	=> 'font-size',
							'unit'				=> 'px',
						),
                    ),
                    'animated_color'        => array(
                        'type'       => 'color',
                        'label'      => __('Color', 'bb-powerpack'),
                        'default'    => '',
                        'show_reset' => true,
                    	'preview'	=> array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-headline-dynamic-wrapper',
                            'property'	=> 'color'
                    	),
                    ),
                )
            ),
		)
	)
));
