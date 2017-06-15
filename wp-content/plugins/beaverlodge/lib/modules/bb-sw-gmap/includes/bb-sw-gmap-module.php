<?php


class SWGmapClass extends FLBuilderModule {

    public function __construct()
    {
       
        parent::__construct(array(
            'name'          => __( 'GMap', 'fl-builder' ),
            'description'   => __( 'A indepth Google Map module', 'fl-builder' ),
            'category'      => BRANDING,
            'dir'           => SW_GMAP_MODULE_DIR . '/',
            'url'           => SW_GMAP_URL . '/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));

    }    
    
}

FLBuilder::register_module( 'SWGmapClass', array(
    
    'content-tab'      => array(
        
        'title'         => __( 'Content', 'fl-builder' ),
        'sections'      => array( 
            
              'content_select'  => array(
                'title'         => __( 'GMap Setup', 'fl-builder' ),
                'fields'        => array(
                    
                    'apikey'     => array(
                        'type'      => 'text',
                        'label'     => __( 'API Key', 'fl-builder' ),
                        'help'     => __( 'To obtain a API Key visit <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" style="color: red;">Google Maps</a>', 'fl-builder' ),
                        'description'     => __( 'To obtain a API Key visit <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" style="color: red;">Google Maps</a>', 'fl-builder' ),
                    ),  // end apikey
                    
                    'address'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Center Map Address', 'fl-builder' ),
                        'placeholder'  => 'Perth, Western Australia',
                        'default'  => 'Perth, Western Australia',
                    ),  // end address
                    
                    'address_fields'    => array(
                        'type'          => 'form',
                        'label'        => __('Marked Addresses', 'fl-builder'),
                        'form'          => 'content_address_form', 
                        'preview_text'  => 'label',
                        'multiple'      => true
                    ),
                    
                ) // end fields
                  
            ) // end content_select
            
        ) // end sections
        
    ), // end content-tab 
    
    'style-tab'      => array(
        
        'title'         => __( 'Style', 'fl-builder' ),
        'sections'      => array( 
            
              'content_select'  => array(
                'title'         => __( 'GMap Styling', 'fl-builder' ),
                'fields'        => array(
                    
                    'marker' => array(
                        'type'          => 'photo',
                        'label'         => __('Custom Marker', 'fl-builder'),
                        'show_remove'	=> true,
                        'description'   => 'Size should be 32 x 32 px',
                    ),
                    
                    'height'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Height', 'fl-builder' ),
                        'default'   => '300',
                        'maxlength'  => '4',
                        'size'  => '5',
                        'description'  => 'px',                        
                        'help' => __( 'Leave blank for 300px height', 'fl-builder'),
                    ),  // end height
                    
                    'width'     => array(
                        'type'      => 'text',
                        'label'     => __( 'Width', 'fl-builder' ),
                        'maxlength'  => '4',
                        'size'  => '5',
                        'description'  => 'px',
                        'help' => __( 'Leave blank for 100% width', 'fl-builder'),
                    ),  // end width
                    
                    'zoom'     => array(
                        'type'      => 'select',
                        'label'     => __( 'Zoom Level', 'fl-builder' ),
                        'default'       => '10',
                        'options'       => array(
                            '1'      => __('World', 'fl-builder'),
                            '5'      => __('Country', 'fl-builder'),
                            '10'      => __('City', 'fl-builder'),
                            '15'      => __('Streets', 'fl-builder'),
                            '20'      => __('Buildings', 'fl-builder'),
                        ),
                    ),  // end width
                    
                ) // end fields
                  
            ) // end content_select
            
        ) // end sections
        
    ), // end content-tab 

) ); 

FLBuilder::register_settings_form('content_address_form', array(
	'title' => __('Panel Settings', 'fl-builder'),
	'tabs'  => array(
		'general'        => array( // Tab
			'title'         => __('Addresses', 'fl-builder'), // Tab title
			'sections'      => array( // Tab Sections
				'general'       => array(
					'title'     => '',
					'fields'    => array(
						'label'         => array(
							'type'          => 'text',
							'label'         => __('Address Label', 'fl-builder'),
							'help'          => __('A label to identify this entry', 'fl-builder')
						)
					)
				),
                
				'background' => array(
					'title'     => __('Choose Address and Custom Image', 'fl-builder'),
					'fields'    => array(
                        
                        'extra_address'     => array(
                            'type'      => 'text',
                            'label'     => __( 'Address', 'fl-builder' ),
                            'default'  => 'Perth, Western Australia',
                        ),  // end address 
                        
                        'info_address'     => array(
                            'type'      => 'editor',
                            'label'     => __( 'Content Description', 'fl-builder' ),
                            'default'  => 'Perth, capital of Western Australia, sits where the Swan River meets the southwest coast. Sandy beaches line its suburbs, and the huge, riverside Kings Park and Botanic Garden on Mt. Eliza offer sweeping views of the city. The Perth Cultural Centre houses the state ballet and opera companies, and occupies its own central precinct, including a theatre, art galleries and the Western Australian Museum.',
                        ),  // end address 
						
					)
				),
				
			)
		),
		

	)
));
    