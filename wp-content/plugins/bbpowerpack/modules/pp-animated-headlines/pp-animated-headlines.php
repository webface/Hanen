<?php

/**
 * @class PPAnimatedHeadlinesModule
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
			'group'         => 'PowerPack Modules',
			'category'      => pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-animated-headlines/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-animated-headlines/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
		));

		$this->add_js('jquery-waypoints');
    }

	public function enqueue_scripts()
	{
		if( class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active() ){
        	$this->add_js('typed', $this->url . 'js/typed.js', array(), '', true);
        	$this->add_js('vticker', $this->url . 'js/rvticker.js', array(), '', true);
		}else{
			if( $this->settings && $this->settings->effect_type == 'type' ) {
	        	$this->add_js('typed', $this->url . 'js/typed.js', array(), '', true);
		    }
		    if ( $this->settings && $this->settings->effect_type == 'slide_up' ) {
	        	$this->add_js('vticker', $this->url . 'js/rvticker.js', array(), '', true);
		    }
		}
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
					'prefix'        => array(
						'type'            => 'text',
						'label'           => __('Prefix', 'bb-powerpack'),
						'default'         => '',
						'help'			=> __('String placed before animated text.', 'bb-powerpack')
					),
					'animated_text'        => array(
						'type'          => 'textarea',
						'label'           => __('Animated Text', 'bb-powerpack'),
						'default'         => '',
						'rows'          => '5',
						'help'			=> __('String with animated effects. You can add multiple strings by adding each string on a new line.', 'bb-powerpack')
					),
					'suffix'        => array(
						'type'            => 'text',
						'label'           => __('Suffix', 'bb-powerpack'),
						'default'         => '',
						'help'			=> __('String placed at the end of animated text.', 'bb-powerpack')
					)
				)
			),
			'effect'          => array(
				'title'         => __('Effect', 'bb-powerpack'),
				'fields'        => array(
					'effect_type'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Effect', 'bb-powerpack'),
						'default'       => 'type',
						'options'       => array(
							'type'      	=>  __('Type', 'bb-powerpack'),
							'slide_up'    	=>  __('Slide', 'bb-powerpack'),
							'fade'    		=>  __('Fade', 'bb-powerpack'),
						),
						'toggle'        => array(
							'type'        => array(
								'fields'        => array('typing_speed', 'back_speed', 'start_delay', 'back_delay', 'enable_loop', 'show_cursor', 'cursor_text', 'cursor_blink', 'min_height')
							),
							'slide_up'		=> array(
								'fields'        => array('animation_speed', 'pause_time', 'show_items', 'pause_hover')
							),
							'fade'			=> array(
								'fields'		=> array('animation_speed')
							)
						),
						'help'			=> __('Select the effect for animated text.', 'bb-powerpack')

					),
					'typing_speed' => array(
						'type'          => 'text',
						'label'         => __('Typing Speed', 'bb-powerpack'),
						'default'       => '80',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'ms',
						'help'   => __('Speed of typing effect. The time to appear single character of word.','bb-powerpack'),
					),
					'back_speed' => array(
						'type'          => 'text',
						'label'         => __('Backspeed', 'bb-powerpack'),
						'default'       => '50',
						'maxlength'     => '6',
						'description'   => 'ms',
						'size'          => '8',
						'help'   		=> __('Speed of backspace effect. The time to disappear single character of word.','bb-powerpack'),
					),
					'start_delay' => array(
						'type'          => 'text',
						'label'         => __('Start Delay', 'bb-powerpack'),
						'default'       => '0',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'ms',
						'help'   		=> __('Delay for the start of type effect. If set to 5000, the first string will appear after 5 seconds.','bb-powerpack'),
					),
					'back_delay' => array(
						'type'          => 'text',
						'label'         => __('Back Delay', 'bb-powerpack'),
						'default'       => '2000',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'ms',
						'help'   		=> __('Delay for the start of backspace effect. If set to 5000, the string will remain visible for 5 seconds before backspace effect.','bb-powerpack'),
					),
					'enable_loop'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Enable Loop', 'bb-powerpack'),
						'default'       => 'yes',
						'options'       => array(
							'yes'      =>  __('Yes', 'bb-powerpack'),
							'no'    =>  __('No', 'bb-powerpack'),
						),
						'help'			=> __("Select 'Yes' if type effect should be played continuously.", 'bb-powerpack' )
					),
					'show_cursor'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Show Cursor', 'bb-powerpack'),
						'default'       => 'yes',
						'options'       => array(
							'yes'      =>  __('Yes', 'bb-powerpack'),
							'no'    =>  __('No', 'bb-powerpack'),
						),
						'toggle'        => array(
							'yes'        => array(
								'fields'        => array('cursor_text', 'cursor_blink'),
							),
						),
						'help'			=> __( "Select 'Yes' if you want to display cursor at the end of animated text & before suffix.", 'bb-powerpack' )
					),
					'cursor_text' => array(
						'type'          => 'text',
						'label'         => __('Cursor Text', 'bb-powerpack'),
						'default'       => '|',
						'maxlength'     => '2',
						'size'          => '8',
						'help'			=> __('Enter the text / symbol for your cursor. e.g. Vertical Pipe Symbol ( | )', 'bb-powerpack')
					),
					'cursor_blink' => array(
						'type'          => 'pp-switch',
						'label'         => __('Cursor Blink Effect', 'bb-powerpack'),
						'default'       => 'yes',
						'options'       => array(
							'yes'      =>  __('Yes', 'bb-powerpack'),
							'no'    =>  __('No', 'bb-powerpack'),
						),
					),


					'animation_speed' => array(
						'type'          => 'text',
						'label'         => __('Animation Speed', 'bb-powerpack'),
						'default'       => '500',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'ms',
						'help'			=> __('Speed of animated text transition.', 'bb-powerpack')
					),
					'pause_time' => array(
						'type'          => 'text',
						'label'         => __('Pause Time', 'bb-powerpack'),
						'default'       => '2000',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'ms',
						'help'			=> __('Delay before scrolling to next animated text.', 'bb-powerpack')
					),
					'pause_hover'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Pause on Hover', 'bb-powerpack'),
						'default'       => 'yes',
						'options'       => array(
							'yes'      =>  __('Yes', 'bb-powerpack'),
							'no'    =>  __('No', 'bb-powerpack'),
						),
						'help'   		=> __('When mouse is over animated text, it should pause slide effect.','bb-powerpack'),
					),
				)
			)
		)
	),
	'style'         => array(
		'title'         => __('Style', 'bb-powerpack'),
		'sections'      => array(
			'structure'     => array(
				'title'         => __('Structure', 'bb-powerpack'),
				'fields'        => array(
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
							'selector'        => '.pp-animated-text-wrap',
							'property'        => 'text-align'
						),
						'help'			=> __('Select alignment for complete element.', 'bb-powerpack')
					),
					'space_prefix' => array(
						'type'          => 'text',
						'label'         => __('Space After Prefix', 'bb-powerpack'),
						'default'       => '5',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'px',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-animated-text-prefix',
							'property'        => 'margin-right',
							'unit'			  => 'px'
						),
						'help'			=> __('Space between Prefix and animated Text.', 'bb-powerpack')
					),
					'space_suffix' => array(
						'type'          => 'text',
						'label'         => __('Space Before Suffix', 'bb-powerpack'),
						'default'       => '5',
						'maxlength'     => '6',
						'size'          => '8',
						'description'   => 'px',
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-animated-text-suffix',
							'property'        => 'margin-left',
							'unit'			  => 'px'
						),
						'help'			=> __('Space between animated Text and Suffix.', 'bb-powerpack')
					),
					'min_height'          => array(
						'type'          => 'text',
		                'label'         => __('Minimum Height', 'bb-powerpack'),
		                'description'   => 'px',
		                'maxlength'     => '4',
		                'size'          => '5',
		                'placeholder'   => 'auto',
		                'help'          => __('If your text is long and dropping down to next line then apply minimum height to prevent page to jump. Keep it empty for default', 'bb-powerpack'),
					),
				)
			),
		)
	),
	'typography'	=> array(
		'title'			=> __('Typography', 'bb-powerpack'),
		'sections'		=> array(
			'text_tag'	=> array(
				'title'		=> '',
				'fields'	=> array(
					'text_tag_selection'   => array(
		                'type'          => 'select',
		                'label'         => __('Title Tag', 'bb-powerpack'),
		                'default'       => 'h2',
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
			'static_text_typography' => array(
				'title' 	=> __('Prefix / Suffix Text', 'bb-powerpack' ),
                'fields'    => array(
                    'font_family'       => array(
                        'type'          => 'font',
                        'label'         => __('Font Family', 'bb-powerpack'),
                        'default'       => array(
                            'family'        => 'Default',
                            'weight'        => 'Default'
                        ),
                        'preview'	=> array(
                            'type'		=> 'font',
                            'selector'	=> '.pp-animated-text-prefix, .pp-animated-text-suffix'
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
                        'type'              => 'pp-multitext',
						'label'             => __('Custom Font Size', 'bb-powerpack'),
						'default'	        => array(
							'desktop'	        => 30,
							'tablet'	        => '',
							'mobile'	        => '',
						),
						'options' 		    => array(
							'desktop'           => array(
								'icon'		        => 'fa-desktop',
								'placeholder'	    => __('Desktop', 'bb-powerpack'),
								'tooltip'	        => __('Desktop', 'bb-powerpack'),
								'preview'           => array(
									'selector'          => '.pp-animated-heading-title',
									'property'          => 'font-size',
									'unit'			    => 'px'
								),
							),
							'tablet'             => array(
								'icon'		         => 'fa-tablet',
								'placeholder'	     => __('Tablet', 'bb-powerpack'),
								'tooltip'	         => __('Tablet', 'bb-powerpack'),
							),
							'mobile'             => array(
								'icon'		         => 'fa-mobile',
								'placeholder'	     => __('Mobile', 'bb-powerpack'),
								'tooltip'	         => __('Mobile', 'bb-powerpack'),
							),
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
                        'type'                => 'pp-multitext',
						'label'               => __('Custom Line Height', 'bb-powerpack'),
						'default'	        => array(
							'desktop'	        => 1.4,
							'tablet'	        => '',
							'mobile'	        => '',
						),
						'options' 		    => array(
							'desktop'           => array(
								'icon'		        => 'fa-desktop',
								'placeholder'	    => __('Desktop', 'bb-powerpack'),
								'tooltip'	        => __('Desktop', 'bb-powerpack'),
								'preview'           => array(
									'selector'          => '.pp-animated-heading-title',
									'property'          => 'line-height',
								),
							),
							'tablet'             => array(
								'icon'		         => 'fa-tablet',
								'placeholder'	     => __('Tablet', 'bb-powerpack'),
								'tooltip'	         => __('Tablet', 'bb-powerpack'),
							),
							'mobile'             => array(
								'icon'		         => 'fa-mobile',
								'placeholder'	     => __('Mobile', 'bb-powerpack'),
								'tooltip'	         => __('Mobile', 'bb-powerpack'),
							),
						),
                    ),
                    'color'        => array(
                        'type'       => 'color',
                        'label'      => __('Color', 'bb-powerpack'),
                        'default'    => '',
                        'show_reset' => true,
                    	'preview'	=> array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-animated-text-prefix, .pp-animated-text-suffix',
                            'property'	=> 'color'
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
                            'selector'	=> '.pp-animated-text-main'
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
                        'type'              => 'pp-multitext',
						'label'             => __('Custom Font Size', 'bb-powerpack'),
						'default'	        => array(
							'desktop'	        => 30,
							'tablet'	        => '',
							'mobile'	        => '',
						),
						'options' 		    => array(
							'desktop'           => array(
								'icon'		        => 'fa-desktop',
								'placeholder'	    => __('Desktop', 'bb-powerpack'),
								'tooltip'	        => __('Desktop', 'bb-powerpack'),
								'preview'           => array(
									'selector'          => '.pp-animated-text-main',
									'property'          => 'font-size',
									'unit'			    => 'px'
								),
							),
							'tablet'             => array(
								'icon'		         => 'fa-tablet',
								'placeholder'	     => __('Tablet', 'bb-powerpack'),
								'tooltip'	         => __('Tablet', 'bb-powerpack'),
							),
							'mobile'             => array(
								'icon'		         => 'fa-mobile',
								'placeholder'	     => __('Mobile', 'bb-powerpack'),
								'tooltip'	         => __('Mobile', 'bb-powerpack'),
							),
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
                    'animated_line_height_custom' => array(
                        'type'                => 'pp-multitext',
						'label'               => __('Custom Line Height', 'bb-powerpack'),
						'default'	        => array(
							'desktop'	        => 1.4,
							'tablet'	        => '',
							'mobile'	        => '',
						),
						'options' 		    => array(
							'desktop'           => array(
								'icon'		        => 'fa-desktop',
								'placeholder'	    => __('Desktop', 'bb-powerpack'),
								'tooltip'	        => __('Desktop', 'bb-powerpack'),
								'preview'           => array(
									'selector'          => '.pp-animated-text-main',
									'property'          => 'line-height',
								),
							),
							'tablet'             => array(
								'icon'		         => 'fa-tablet',
								'placeholder'	     => __('Tablet', 'bb-powerpack'),
								'tooltip'	         => __('Tablet', 'bb-powerpack'),
							),
							'mobile'             => array(
								'icon'		         => 'fa-mobile',
								'placeholder'	     => __('Mobile', 'bb-powerpack'),
								'tooltip'	         => __('Mobile', 'bb-powerpack'),
							),
						),
                    ),
                    'animated_color'        => array(
                        'type'       => 'color',
                        'label'      => __('Color', 'bb-powerpack'),
                        'default'    => '',
                        'show_reset' => true,
                    	'preview'	=> array(
                            'type'		=> 'css',
                            'selector'	=> '.pp-animated-text-main',
                            'property'	=> 'color'
                    	),
                    ),
                )
            ),
		)
	)
));
