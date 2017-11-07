<?php

/**
 * @class PPTableModule
 */
class PPTableModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Table', 'bb-powerpack'),
            'description'   => __('A module for table.', 'bb-powerpack'),
			'group'			=> pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-table/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-table/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true
        ));

        $this->add_css('tablesaw-style', $this->url . 'css/tablesaw.css');
		$this->add_js('tablesaw-script', $this->url . 'js/tablesaw.js', array(), '', true);
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPTableModule', array(
	'header'       => array(
        'title'         => __('Table Headers', 'bb-powerpack'),
        'sections'      => array(
            'headers'       => array(
                'title'         => __('Column Headers', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'header'     => array(
                        'type'          => 'text',
                        'label'         => __('Header', 'bb-powerpack'),
                        'multiple'       => true,
                    ),
                )
            ),
            'sort'       => array(
                'title'         => __('Sortable Table', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'sortable'     => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Sort', 'bb-powerpack'),
                        'default'       => 'data-tablesaw-sortable data-tablesaw-sortable-switch',
                        'options'       => array(
                            'data-tablesaw-sortable data-tablesaw-sortable-switch'	=> __('Yes', 'bb-powerpack'),
                            ''    => __('No', 'bb-powerpack'),
                        ),
                    ),
                )
            ),
            'scroll'       => array(
                'title'         => __('Scrollable Table', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'scrollable'     => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Scroll', 'bb-powerpack'),
                        'default'       => 'swipe',
                        'options'       => array(
                            'swipe'     => __('Yes', 'bb-powerpack'),
                            'stack'     => __('No', 'bb-powerpack')
                        ),
                        'toggle'        => array(
                            'swipe'         => array(
                                'fields'        => array('custom_breakpoint')
                            )
                        ),
                        'help'         => __('This will disable stacking and enable swipe/scroll when below the breakpoint', 'bb-powerpack'),
                    ),
                    'custom_breakpoint' => array(
                        'type'              => 'text',
                        'label'             => __('Define Custom Breakpoint', 'bb-powerpack'),
                        'default'           => '',
                        'size'              => 5,
                        'help'              => __('Devices equal or below the defined screen width will have this feature.', 'bb-powerpack')
                    )
                )
            ),
        )
    ),
	'row'       => array(
        'title'         => __('Table Rows', 'bb-powerpack'),
        'sections'      => array(
            'Cells'       => array(
                'title'         => __('Row Cells', 'bb-powerpack'),
                'fields'        => array( // Section Fields
                    'rows'     => array(
                        'type'          => 'form',
                        'label'        => __('Rows', 'bb-powerpack'),
                        'form'          => 'pp_content_table_row',
                        'preview_text'  => 'label',
                        'multiple'      => true
                    ),
                )
            ),

        )
    ),
	'style'	=> array(
		'title'	=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'header_style'	=> array(
				'title'	=> __('Header', 'bb-powerpack'),
				'fields'	=> array(
					'header_background'     => array(
                        'type'          => 'color',
                        'default'          => '404040',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'help'          => __('Change the table header background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead',
							'property'	=> 'background'
						)
                    ),
                    'header_border'     => array(
                        'type'          => 'color',
                        'default'       => 'ffffff',
                        'label'         => __('Border Color', 'bb-powerpack'),
                        'help'          => __('Change the table header border color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr:first-child th',
							'property'	=> 'border-right-color'
						)
                    ),
					'header_text_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Horizontal Alignment', 'bb-powerpack'),
						'default'	=> 'left',
						'options'       => array(
							'left'          => __('Left', 'bb-powerpack'),
							'center'         => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th, .tablesaw-sortable .tablesaw-sortable-head button',
							'property'	=> 'text-align'
						)
					),
					'header_vertical_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Vertical Alignment', 'bb-powerpack'),
						'default'	=> 'middle',
						'options'       => array(
							'top'          => __('Top', 'bb-powerpack'),
							'middle'         => __('Center', 'bb-powerpack'),
							'bottom'         => __('Bottom', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th',
							'property'	=> 'vertical-align'
						)
					),
					'header_padding'   => array(
                        'type'      => 'pp-multitext',
                        'label'     => __( 'Padding', 'bb-powerpack' ),
                        'description'   => 'px',
						'default'       => array(
                            'top' => 8,
                            'right' => 8,
                            'bottom' => 8,
                            'left' => 8,
                        ),
                    	'options' 		=> array(
                    		'top' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __( 'Top', 'bb-powerpack' ),
                                'tooltip'       => __( 'Top', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-up',
								'preview'              => array(
									'selector'	=> '.pp-table-content thead tr th',
									'property'	=> 'padding-top',
									'unit'		=> 'px'
		                        )
                    		),
                            'bottom' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Bottom', 'bb-powerpack'),
                                'tooltip'       => __( 'Bottom', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-down',
								'preview'              => array(
									'selector'	=> '.pp-table-content thead tr th',
									'property'	=> 'padding-bottom',
									'unit'		=> 'px'
		                        )
                    		),
                            'left' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Left', 'bb-powerpack'),
                                'tooltip'       => __( 'Left', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-left',
								'preview'              => array(
									'selector'	=> '.pp-table-content thead tr th',
									'property'	=> 'padding-left',
									'unit'		=> 'px'
		                        )
                    		),
                            'right' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Right', 'bb-powerpack'),
                                'tooltip'       => __( 'Right', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-right',
								'preview'              => array(
									'selector'	=> '.pp-table-content thead tr th',
									'property'	=> 'padding-right',
									'unit'		=> 'px'
		                        )
                    		),
                    	)
                    ),
				)
			),
			'row_style'	=> array(
				'title'	=> __( 'Rows', 'bb-powerpack' ),
				'fields'	=> array(
					'rows_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'help'          => __('Change the table row background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr',
							'property'	=> 'background'
						)
                    ),
                    'rows_even_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Even Rows Background Color', 'bb-powerpack'),
                        'help'          => __('Change the tables even rows background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .even',
							'property'	=> 'background'
						)
                    ),
                    'rows_odd_background'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Odd Rows Background Color', 'bb-powerpack'),
                        'help'          => __('Change the tables odd rows background color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .odd',
							'property'	=> 'background'
						)
                    ),
					'rows_text_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Horizontal Alignment', 'bb-powerpack'),
						'default'	=> 'left',
						'options'       => array(
							'left'          => __('Left', 'bb-powerpack'),
							'center'         => __('Center', 'bb-powerpack'),
							'right'         => __('Right', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'text-align'
						)
					),
					'rows_vertical_alignment' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Vertical Alignment', 'bb-powerpack'),
						'default'	=> 'middle',
						'options'       => array(
							'top'          => __('Top', 'bb-powerpack'),
							'middle'         => __('Center', 'bb-powerpack'),
							'bottom'         => __('Bottom', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'vertical-align'
						)
					),
					'rows_padding'   => array(
                        'type'      => 'pp-multitext',
                        'label'     => __( 'Padding', 'bb-powerpack' ),
                        'description'   => 'px',
						'default'       => array(
                            'top' => 10,
                            'right' => 10,
                            'bottom' => 10,
                            'left' => 10,
                        ),
                    	'options' 		=> array(
                    		'top' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __( 'Top', 'bb-powerpack' ),
                                'tooltip'       => __( 'Top', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-up',
								'preview'              => array(
									'selector'	=> '.pp-table-content tbody tr td',
									'property'	=> 'padding-top',
									'unit'		=> 'px'
		                        )
                    		),
                            'bottom' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Bottom', 'bb-powerpack'),
                                'tooltip'       => __( 'Bottom', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-down',
								'preview'              => array(
									'selector'	=> '.pp-table-content tbody tr td',
									'property'	=> 'padding-bottom',
									'unit'		=> 'px'
		                        )
                    		),
                            'left' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Left', 'bb-powerpack'),
                                'tooltip'       => __( 'Left', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-left',
								'preview'              => array(
									'selector'	=> '.pp-table-content tbody tr td',
									'property'	=> 'padding-left',
									'unit'		=> 'px'
		                        )
                    		),
                            'right' => array(
                                'maxlength' => 3,
                                'placeholder'   =>  __('Right', 'bb-powerpack'),
                                'tooltip'       => __( 'Right', 'bb-powerpack' ),
                    			'icon'		=> 'fa-long-arrow-right',
								'preview'              => array(
									'selector'	=> '.pp-table-content tbody tr td',
									'property'	=> 'padding-right',
									'unit'		=> 'px'
		                        )
                    		),
                    	)
                    ),
				)
			),
			'cells_style'	=> array(
				'title'	=> __('Cell', 'bb-powerpack'),
				'fields'	=> array(
					'cells_border' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Border', 'bb-powerpack'),
						'default'	=> 'default',
						'options'       => array(
							'default'          	=> __('Default', 'bb-powerpack'),
							'horizontal'        => __('Horizontal', 'bb-powerpack'),
							'vertical'         	=> __('Vertical', 'bb-powerpack'),
						),
					),
					'rows_border'     => array(
                        'type'          => 'color',
                        'default'       => 'efefef',
                        'label'         => __('Border Color', 'bb-powerpack'),
                        'help'          => __('Change the table row border color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'rules'		=> array(
								array(
									'selector'	=> '.pp-table-content tbody',
									'property'	=> 'border-top-color'
								),
								array(
									'selector'	=> '.pp-table-content tbody tr',
									'property'	=> 'border-bottom-color'
								),
								array(
									'selector'	=> '.pp-table-content tbody, .pp-table-content tbody tr td',
									'property'	=> 'border-left-color'
								),
								array(
									'selector'	=> '.pp-table-content tbody',
									'property'	=> 'border-right-color'
								),
							)
						)
                    ),
				)
			)
		)
	),
	'typography'	=> array(
		'title'	=> __('Typography', 'bb-powerpack'),
		'sections'	=> array(
			'header_typography'	=> array(
				'title'	=>	__('Header', 'bb-powerpack'),
				'fields'	=> array(
					'header_font'          => array(
						'type'          => 'font',
						'default'		=> array(
							'family'		=> 'Default',
							'weight'		=> 300
						),
						'label'         => __('Font', 'bb-powerpack'),
						'preview'         => array(
							'type'            => 'font',
							'selector'        => '.pp-table-content thead tr th'
						)
					),
					'header_font_size'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Font Size', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('header_custom_font_size')
							)
						)
					),
					'header_custom_font_size'	=> array(
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
									'selector'        => '.pp-table-content thead tr th',
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
                    'header_font_color'     => array(
                        'type'          => 'color',
                        'default'          => 'ffffff',
                        'label'         => __('Text Color', 'bb-powerpack'),
                        'help'          => __('Change the table header font color', 'bb-powerpack'),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th',
							'property'	=> 'color'
						)
                    ),
					'header_text_transform' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Text Transform', 'bb-powerpack'),
						'default'	=> 'none',
						'options'       => array(
							'none'          => __('None', 'bb-powerpack'),
							'lowercase'     => __('lowercase', 'bb-powerpack'),
							'uppercase'     => __('UPPERCASE', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content thead tr th',
							'property'	=> 'text-transform'
						)
					),
				)
			),
			'rows_typography'	=> array(
				'title'	=> __('Rows', 'bb-powerpack'),
				'fields'	=> array(
					'row_font'          => array(
						'type'          => 'font',
						'default'		=> array(
							'family'		=> 'Default',
							'weight'		=> 300
						),
						'label'         => __('Font', 'bb-powerpack'),
						'preview'         => array(
							'type'            => 'font',
							'selector'        => '.pp-table-content tbody tr td'
						)
					),
					'row_font_size'     => array(
						'type'          => 'pp-switch',
						'label'         => __('Font Size', 'bb-powerpack'),
						'default'       => 'default',
						'options'       => array(
							'default'       =>  __('Default', 'bb-powerpack'),
							'custom'        =>  __('Custom', 'bb-powerpack')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('row_custom_font_size')
							)
						)
					),
					'row_custom_font_size'	=> array(
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
									'selector'        => '.pp-table-content tbody tr td',
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
                    'rows_font_color'     => array(
                        'type'          => 'color',
                        'default'       => '',
                        'label'         => __('Text Color', 'bb-powerpack'),
                        'help'          => __('Change the table row text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'color'
						)
                    ),
                    'rows_font_even'     => array(
                        'type'          => 'color',
                        'default'       => '',
                        'label'         => __('Even Rows Text Color', 'bb-powerpack'),
                        'help'          => __('Change the tables even rows text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .even td',
							'property'	=> 'color'
						)
                    ),
                    'rows_font_odd'     => array(
                        'type'          => 'color',
                        'default'       => '',
                        'label'         => __('Odd Rows Text Color', 'bb-powerpack'),
                        'help'          => __('Change the tables odd rows text color', 'bb-powerpack'),
						'show_reset'	=> true,
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content .odd td',
							'property'	=> 'color'
						)
                    ),
					'rows_text_transform' => array(
						'type'		=> 'pp-switch',
						'label'		=> __('Text Transform', 'bb-powerpack'),
						'default'	=> 'none',
						'options'       => array(
							'none'          => __('None', 'bb-powerpack'),
							'lowercase'     => __('lowercase', 'bb-powerpack'),
							'uppercase'     => __('UPPERCASE', 'bb-powerpack'),
						),
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.pp-table-content tbody tr td',
							'property'	=> 'text-transform'
						)
					),
				)
			)

		)
	)
));

FLBuilder::register_settings_form('pp_content_table_row', array(
	'title' => __('Row Settings', 'bb-powerpack'),
	'tabs'  => array(

        'general'        => array( // Tab
			'title'         => __('Content', 'bb-powerpack'), // Tab title
			'sections'      => array( // Tab Sections
				'general'       => array(
					'title'     => '',
					'fields'    => array(
						'label'         => array(
							'type'          => 'text',
							'label'         => __('Row Label', 'bb-powerpack'),
							'help'          => __('A label to identify this panel on the Custom Panel tab.', 'bb-powerpack'),
							'connections'	=> array('string')
						),
                        'cell'         => array(
							'type'          => 'textarea',
							'label'         => __('Cell', 'bb-powerpack'),
                            'multiple'      => true,
						),
					)
				),

			)
		),

	)
));
