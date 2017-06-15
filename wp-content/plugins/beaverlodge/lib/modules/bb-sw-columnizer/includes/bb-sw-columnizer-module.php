<?php


class SWColumnizerClass extends FLBuilderModule {

    public function __construct()
    {
        
        parent::__construct(array(
            'name'              => __( 'Paragraph Columnizer', 'fl-builder' ),
            'description'       => __( 'A module to split text into multiple columns', 'fl-builder' ),
            'category'          => BRANDING,
            'partial_refresh'   => true,
            'dir'               => SW_COLUMNIZER_MODULE_DIR . '/',
            'url'               => SW_COLUMNIZER_MODULE_URL . '/',
        ));
        

        $this->add_js('plugin.columnizer.js', $this->url . 'includes/plugin.columnizer.js', array(), '', true);
        $this->add_js('equal.heights.js', $this->url . 'includes/equal.heights.js', array(), '', true);
        
    }    
    
}

FLBuilder::register_module( 'SWColumnizerClass', array(
    
    'content-tab'      => array(
        
        'title'         => __( 'Content', 'fl-builder' ),
        'sections'      => array( 
            
              'type'  => array(
                'title'         => __( 'Content', 'fl-builder' ),
                'fields'        => array(

                    
                    'content' => array(
                        'type'          => 'editor',
                        'rows'          => '10',
                    ), // end column_content
                    
                ) // end fields
                  
            ), // end axis-content
            
        ) // end sections
        
    ), // end content-tab
    
    'layout-tab'      => array(
        
        'title'         => __( 'Layout', 'fl-builder' ),
        'sections'      => array( 
            
              'type'  => array(
                'title'         => __( 'Layout', 'fl-builder' ),
                'fields'        => array(
                    
                    'desktop'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Desktop Columns', 'fl-builder' ),
                        'default'   => '3',
                        'size'      => '4',
                        'maxlength' => '5',
                        'help'          => __('Specifies the number of colums that should be created', 'fl-builder'),
                    ),  // end desktop
                    
                    'tablet'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Tablet Columns', 'fl-builder' ),
                        'default'   => '2',
                        'size'      => '4',
                        'maxlength' => '5',
                        'help'          => __('Specifies the number of colums that should be created', 'fl-builder'),
                    ),  // end tablet
                    
                    'phone'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Phone Columns', 'fl-builder' ),
                        'default'   => '1',
                        'size'      => '4',
                        'maxlength' => '5',
                        'help'          => __('Specifies the number of colums that should be created', 'fl-builder'),
                    ),  // end phone
                    
                    'heights' => array(
                        'type'          => 'select',
                        'label'         => __('Equal Column Heights', 'fl-builder'),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'       => __('Yes', 'fl-builder'),
                            'no'        => __('No', 'fl-builder'),
                        ),
                    ), // end heights
                    
                    'alignment' => array(
                        'type'          => 'select',
                        'label'         => __('Column Content Alignment', 'fl-builder'),
                        'default'       => 'left',
                        'options'       => array(
                            'left'      => __('Left', 'fl-builder'),
                            'center'    => __('Center', 'fl-builder'),
                            'right'     => __('Right', 'fl-builder'),
                            'justify'   => __('Justified', 'fl-builder'),
                        ),
                    ), // end alignment
                    
                    'reading' => array(
                        'type'          => 'select',
                        'label'         => __('Reading Direction', 'fl-builder'),
                        'default'       => 'left',
                        'options'       => array(
                            'left'      => __('Left to Right', 'fl-builder'),
                            'right'     => __('Right to Left', 'fl-builder'),
                        ),
                    ), // end reading
                    
                    'padding'    => array(
                        'type'      => 'text',
                        'label'     => __( 'Column Padding', 'fl-builder' ),
                        'default'   => '10',
                        'size'      => '5',
                        'maxlength' => '5',
                        'description'   => 'px',
                        'help'      => __('Left and right column padding', 'fl-builder'),
                    ),  // end padding
                    
                    'border' => array(
                        'type'          => 'select',
                        'label'         => __('Column Border', 'fl-builder'),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'       => __('Yes', 'fl-builder'),
                            'no'        => __('No', 'fl-builder'),
                        ),
                        'toggle'        => array(
                            'yes'       => array(
                                'fields'    => array('borderWidth', 'borderColor'),
                            ) ,
                        ),
                    ), // end border
                    
                    'borderWidth'    => array(
                        'type'      => 'text',
                        'label'     => __( 'Border Width', 'fl-builder' ),
                        'default'   => '2',
                        'size'      => '5',
                        'maxlength' => '5',
                        'description'   => 'px',
                    ),  // end borderWidth
                    
                    'borderColor'    => array(
                        'type'      => 'color',
                        'label'     => __( 'Border Color', 'fl-builder' ),
                        'default'   => '',
                    ),  // end borderColor
                    
                ) // end fields
                  
            ), // end axis-content
            
        ) // end sections
        
    ), // end layout-tab
    
    'custom-tab'      => array(
        
        'title'         => __( 'Defaults', 'fl-builder' ),
        'sections'      => array( 
            
              'defaults'  => array(
                'title'         => __( 'Defaults', 'fl-builder' ),
                'fields'        => array(
                    
                    'dontsplit'    => array(
                        'type'      => 'textarea',
                        'label'     => __( 'Dont Split Elements', 'fl-builder' ),
                        'default'   => 'table, thead, tbody, tfoot, colgroup, caption, label, legend, script, style, textarea, button, object, embed, tr, th, td, li, h1, h2, h3, h4, h5, h6, form',
                        'desctiption'   => __('Seperate each one with a comma', 'fl-builder'),
                        'help'      => __('Elements here will not be split into multiple columns', 'fl-builder'),
                    ),  // end dontsplit
                    
                    'dontend'    => array(
                        'type'      => 'textarea',
                        'label'     => __( 'Dont End Elements', 'fl-builder' ),
                        'default'   => 'h1, h2, h3, h4, h5, h6',
                        'desctiption'   => __('Seperate each one with a comma', 'fl-builder'),
                        'help'      => __('Elements here will not be placed at the end of a column', 'fl-builder'),
                    ),  // end dontend
                    
                    'iffirst'    => array(
                        'type'      => 'textarea',
                        'label'     => __( 'Remove if First', 'fl-builder' ),
                        'default'   => 'br',
                        'desctiption'   => __('Seperate each one with a comma', 'fl-builder'),
                        'help'      => __('Elements here will not be rendered if they are at the start of a column', 'fl-builder'),
                    ),  // end iffirst
                    
                    'iflast'    => array(
                        'type'      => 'textarea',
                        'label'     => __( 'Remove if Last', 'fl-builder' ),
                        'default'   => 'br',
                        'desctiption'   => __('Seperate each one with a comma', 'fl-builder'),
                        'help'      => __('Elements here will not be rendered if they are at the end of a column', 'fl-builder'),
                    ),  // end iffirst
                    
                ) // end fields
                  
            ), // end defaults
            
              'breakpoints'  => array(
                'title'         => __( 'Breakpoints', 'fl-builder' ),
                'fields'        => array(
                    
                    'large'    => array(
                        'type'      => 'text',
                        'label'     => __( 'Desktop Breakpoint', 'fl-builder' ),
                        'default'   => '992',
                        'size'      => '5',
                        'maxlength' => '5',
                        'description'   => 'px',
                    ),  // end large
                    
                    'medium'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Tablet Breakpoint', 'fl-builder' ),
                        'default'   => '767',
                        'size'      => '5',
                        'maxlength' => '5',
                        'description'   => 'px',
                    ),  // end medium
                    
                ) // end fields
                  
            ), // end breakpoints
            
        ) // end sections
        
    ), // end breakpoint-tab
    
) ); 