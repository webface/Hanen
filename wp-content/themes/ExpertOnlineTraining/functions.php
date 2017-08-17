<?php
/**
 * Bootstrap Basic theme
 * 
 * @package bootstrap-basic
 */
//require_once(dirname( __FILE__ ) . '/bootstrap-functions.php');

$eot_dashboard_url = get_bloginfo ('url') . "/dashboard";
$eot_login_url = wp_login_url ($eot_dashboard_url);
$eot_logout_url = wp_logout_url( home_url() );
$restricted_pages = array (
	'dashboard',
	'new-subscription'
);
$states = array ("Alabama" => "Alabama", "Alaska" => "Alaska", "Arizona" => "Arizona", "Arkansas" => "Arkansas", "California" => "California", "Colorado" => "Colorado", "Connecticut" => "Connecticut", "Delaware" => "Delaware", "District of Columbia" => "District of Columbia", "Florida" => "Florida", "Georgia" => "Georgia", "Hawaii" => "Hawaii", "Idaho" => "Idaho", "Illinois" => "Illinois", "Indiana" => "Indiana", "Iowa" => "Iowa", "Kansas" => "Kansas", "Kentucky" => "Kentucky", "Louisiana" => "Louisiana", "Maine" => "Maine", "Maryland" => "Maryland", "Massachusetts" => "Massachusetts", "Michigan" => "Michigan", "Minnesota" => "Minnesota", "Mississippi" => "Mississippi", "Missouri" => "Missouri", "Montana" => "Montana", "Nebraska" => "Nebraska", "Nevada" => "Nevada", "New Hampshire" => "New Hampshire", "New Jersey" => "New Jersey", "New Mexico" => "New Mexico", "New York" => "New York", "North Carolina" => "North Carolina", "North Dakota" => "North Dakota", "Ohio" => "Ohio", "Oklahoma" => "Oklahoma", "Oregon" => "Oregon", "Pennsylvania" => "Pennsylvania", "Rhode Island" => "Rhode Island", "South Carolina" => "South Carolina", "South Dakota" => "South Dakota", "Tennessee" => "Tennessee", "Texas" => "Texas", "Utah" => "Utah", "Vermont" => "Vermont", "Virginia" => "Virginia", "Washington" => "Washington", "West Virginia" => "West Virginia", "Wisconsin" => "Wisconsin", "Wyoming" => "Wyoming", "Alberta" => "Alberta", "British Columbia" => "British Columbia", "Manitoba" => "Manitoba", "New Brunswick" => "New Brunswick", "Newfoundland" => "Newfoundland", "Northwest Territories" => "Northwest Territories", "Nova Scotia" => "Nova Scotia", "Nunavut" => "Nunavut", "Ontario" => "Ontario", "Prince Edward Island" => "Prince Edward Island", "Quebec" => "Quebec", "Saskatchewan" => "Saskatchewan", "Yukon" => "Yukon", "Other" => "Other");
$countries = array ("US" => "United States", "CAN" => "Canada", "Afghanistan" => "Afghanistan", "Albania" => "Albania", "Algeria" => "Algeria", "Andorra" => "Andorra", "Angola" => "Angola", "Antigua and Barbuda" => "Antigua and Barbuda", "Argentina" => "Argentina", "Armenia" => "Armenia", "Aruba" => "Aruba", "Australia" => "Australia", "Austria" => "Austria", "Azerbaijan" => "Azerbaijan", "Bahamas" => "Bahamas", "Bahrain" => "Bahrain", "Bangladesh" => "Bangladesh", "Barbados" => "Barbados", "Belarus" => "Belarus", "Belgium" => "Belgium", "Belize" => "Belize", "Benin" => "Benin", "Bhutan" => "Bhutan", "Bolivia" => "Bolivia", "Bosnia and Herzegovina" => "Bosnia and Herzegovina", "Botswana" => "Botswana", "Brazil" => "Brazil", "Brunei " => "Brunei ", "Bulgaria" => "Bulgaria", "Burkina Faso" => "Burkina Faso", "Burma" => "Burma", "Burundi" => "Burundi", "Cambodia" => "Cambodia", "Cameroon" => "Cameroon", "Canada" => "Canada", "Cabo Verde" => "Cabo Verde", "Central African Republic" => "Central African Republic", "Chad" => "Chad", "Chile" => "Chile", "China" => "China", "Colombia" => "Colombia", "Comoros" => "Comoros", "Congo" => "Congo", "Costa Rica" => "Costa Rica", "Croatia" => "Croatia", "Cuba" => "Cuba", "Curacao" => "Curacao", "Cyprus" => "Cyprus", "Czech Republic" => "Czech Republic", "Denmark" => "Denmark", "Djibouti" => "Djibouti", "Dominica" => "Dominica", "Dominican Republic" => "Dominican Republic", "Ecuador" => "Ecuador", "Egypt" => "Egypt", "El Salvador" => "El Salvador", "Equatorial Guinea" => "Equatorial Guinea", "Eritrea" => "Eritrea", "Estonia" => "Estonia", "Ethiopia" => "Ethiopia", "Fiji" => "Fiji", "Finland" => "Finland", "France" => "France", "Gabon" => "Gabon", "Gambia" => "Gambia", "Georgia" => "Georgia", "Germany" => "Germany", "Ghana" => "Ghana", "Greece" => "Greece", "Grenada" => "Grenada", "Guatemala" => "Guatemala", "Guinea" => "Guinea", "Guinea-Bissau" => "Guinea-Bissau", "Guyana" => "Guyana", "Haiti" => "Haiti", "Honduras" => "Honduras", "Hong Kong" => "Hong Kong", "Hungary" => "Hungary", "Iceland" => "Iceland", "India" => "India", "Indonesia" => "Indonesia", "Iran" => "Iran", "Iraq" => "Iraq", "Ireland" => "Ireland", "Israel" => "Israel", "Italy" => "Italy", "Jamaica" => "Jamaica", "Japan" => "Japan", "Jordan" => "Jordan", "Kazakhstan" => "Kazakhstan", "Kenya" => "Kenya", "Kiribati" => "Kiribati", "Korea North" => "Korea North", "Korea South" => "Korea South", "Kosovo" => "Kosovo", "Kuwait" => "Kuwait", "Kyrgyzstan" => "Kyrgyzstan", "Laos" => "Laos", "Latvia" => "Latvia", "Lebanon" => "Lebanon", "Lesotho" => "Lesotho", "Liberia" => "Liberia", "Libya" => "Libya", "Liechtenstein" => "Liechtenstein", "Lithuania" => "Lithuania", "Luxembourg" => "Luxembourg", "Macau" => "Macau", "Macedonia" => "Macedonia", "Madagascar" => "Madagascar", "Malawi" => "Malawi", "Malaysia" => "Malaysia", "Maldives" => "Maldives", "Mali" => "Mali", "Malta" => "Malta", "Marshall Islands" => "Marshall Islands", "Mauritania" => "Mauritania", "Mauritius" => "Mauritius", "Mexico" => "Mexico", "Micronesia" => "Micronesia", "Moldova" => "Moldova", "Monaco" => "Monaco", "Mongolia" => "Mongolia", "Montenegro" => "Montenegro", "Morocco" => "Morocco", "Mozambique" => "Mozambique", "Namibia" => "Namibia", "Nauru" => "Nauru", "Nepal" => "Nepal", "Netherlands" => "Netherlands", "New Zealand" => "New Zealand", "Nicaragua" => "Nicaragua", "Niger" => "Niger", "Nigeria" => "Nigeria", "North Korea" => "North Korea", "Norway" => "Norway", "Oman" => "Oman", "Pakistan" => "Pakistan", "Palau" => "Palau", "Panama" => "Panama", "Papua New Guinea" => "Papua New Guinea", "Paraguay" => "Paraguay", "Peru" => "Peru", "Philippines" => "Philippines", "Poland" => "Poland", "Portugal" => "Portugal", "Qatar" => "Qatar", "Romania" => "Romania", "Russia" => "Russia", "Rwanda" => "Rwanda", "Saint Kitts and Nevis" => "Saint Kitts and Nevis", "Saint Lucia" => "Saint Lucia", "Saint Vincent and the Grenadines" => "Saint Vincent and the Grenadines", "Samoa " => "Samoa ", "San Marino" => "San Marino", "Sao Tome and Principe" => "Sao Tome and Principe", "Saudi Arabia" => "Saudi Arabia", "Senegal" => "Senegal", "Serbia" => "Serbia", "Seychelles" => "Seychelles", "Sierra Leone" => "Sierra Leone", "Singapore" => "Singapore", "Sint Maarten" => "Sint Maarten", "Slovakia" => "Slovakia", "Slovenia" => "Slovenia", "Solomon Islands" => "Solomon Islands", "Somalia" => "Somalia", "South Africa" => "South Africa", "South Korea" => "South Korea", "South Sudan" => "South Sudan", "Spain " => "Spain ", "Sri Lanka" => "Sri Lanka", "Sudan" => "Sudan", "Suriname" => "Suriname", "Swaziland " => "Swaziland ", "Sweden" => "Sweden", "Switzerland" => "Switzerland", "Syria" => "Syria", "Taiwan" => "Taiwan", "Tajikistan" => "Tajikistan", "Tanzania" => "Tanzania", "Thailand " => "Thailand ", "Timor-Leste" => "Timor-Leste", "Togo" => "Togo", "Tonga" => "Tonga", "Trinidad and Tobago" => "Trinidad and Tobago", "Tunisia" => "Tunisia", "Turkey" => "Turkey", "Turkmenistan" => "Turkmenistan", "Tuvalu" => "Tuvalu", "Uganda" => "Uganda", "Ukraine" => "Ukraine", "United Arab Emirates" => "United Arab Emirates", "United Kingdom" => "United Kingdom", "US" => "United States", "Uruguay" => "Uruguay", "Uzbekistan" => "Uzbekistan", "Vanuatu" => "Vanuatu", "Venezuela" => "Venezuela", "Vietnam" => "Vietnam", "Yemen" => "Yemen", "Zambia" => "Zambia", "Zimbabwe" => "Zimbabwe");

/**
 * Prevents the admin bar being shown on the front end of the site
 */
add_filter('show_admin_bar', '__return_false');

include_once ('user_functions.php');
include_once ('eot_functions.php');
include_once ('organization_functions.php');
include_once ('individual_functions.php');
//include_once ('learnupon_functions.php');

add_action('after_setup_theme', 'eot_setup');
/**
 * Setup theme and register support wp features.
 */
function eot_setup() {
	add_theme_support('automatic-feed-links');
	
	add_theme_support('post-thumbnails');
	
	register_nav_menus(array(
		'primary' => __('Primary Menu Logged Out', 'eot'),
		'primary_login' => __('Primary Menu Logged In', 'eot'),
		'sidebar' => __('Sidebar Menu', 'eot'),
		'footer' => __('Footer Menu', 'eot'),
	));

}

/**
 * Register Woocommerce Support
 */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() 
{
    add_theme_support( 'woocommerce' );
}

add_action('widgets_init', 'bootstrapBasicWidgetsInit');
/**
 * Register widget areas
 */
function bootstrapBasicWidgetsInit() 
{
	register_sidebar(array(
		'name'          => __('Header right', 'bootstrap-basic'),
		'id'            => 'header-right',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	));
	
	register_sidebar(array(
		'name'          => __('Navigation bar right', 'bootstrap-basic'),
		'id'            => 'navbar-right',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	));
	
	register_sidebar(array(
		'name'          => __('Sidebar left', 'bootstrap-basic'),
		'id'            => 'sidebar-left',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	));
	
	register_sidebar(array(
		'name'          => __('Sidebar right', 'bootstrap-basic'),
		'id'            => 'sidebar-right',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	));
	
	register_sidebar(array(
		'name'          => __('Footer left', 'bootstrap-basic'),
		'id'            => 'footer-left',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	));
	
	register_sidebar(array(
		'name'          => __('Footer right', 'bootstrap-basic'),
		'id'            => 'footer-right',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	));
}


add_action ('wp', 'user_must_be_logged_in');
/**
 * Ensures the user is logged in on specified pages, otherwise redirected to login page
 */
function user_must_be_logged_in () {
	global $post, $restricted_pages;
	if (isset($post->post_name) && in_array($post->post_name, $restricted_pages) && !is_user_logged_in ()) {
		header ('Location: ' . wp_login_url (get_permalink()));
	}
}

add_action('wp_enqueue_scripts', 'eot_enqueue_scripts');
/**
 * Enqueue scripts & styles for eot
 */
function eot_enqueue_scripts() 
{
	global $wp_styles, $eot_dashboard_url;

	wp_enqueue_style('jquery-ui-css', get_template_directory_uri() . '/css/jquery-ui.css', '', '1.12.0');
        wp_enqueue_style('jquery-ui-style', get_template_directory_uri() . '/css/jquery-ui-style.css', '', '1.0.0');
	wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/css/light_bootstrap.min.css', '', '3.0');
	wp_enqueue_style('facebox-style', get_template_directory_uri() . '/css/facebox.css', '', '1.0.0');
	wp_enqueue_style('eot-style', get_stylesheet_uri());
	//wp_enqueue_style('light-style', get_template_directory_uri() . '/css/light4.css', '', '1.0.0');
	wp_enqueue_style('pageslide-style', get_template_directory_uri() . '/css/jquery.pageslide.css', '', '1.0.0');
	wp_enqueue_style('help-button-style', get_template_directory_uri() . '/css/help_button.css', '', '1.0.0');
	wp_enqueue_style( 'eot-style-ie-6', get_stylesheet_directory_uri() . "/css/ie.6.css", '', '1.0.0' );
	$wp_styles->add_data( 'eot-style-ie-6', 'conditional', 'lte IE 6' );
	wp_enqueue_style( 'eot-style-ie-7', get_stylesheet_directory_uri() . "/css/ie.7.css", '', '1.0.0' );
	$wp_styles->add_data( 'eot-style-ie-7', 'conditional', 'lte IE 7' );
	wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css', '', '4.6.1');
	wp_enqueue_style('videojs-style', 'https://vjs.zencdn.net/5.8.8/video-js.css', '', '5.8.8');
	wp_enqueue_style('help-button-style', get_template_directory_uri() . '/css/help_button.css', '', '1.0.0');
        wp_enqueue_style('datatable-buttons','https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css', '', '1.4');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-js', get_template_directory_uri() . '/js/jquery-ui.min.js', '', '1.12.0');
        wp_enqueue_script('bootstrapjs', get_template_directory_uri() . '/js/bootstrap.min.js', '', '3.3.7');
	wp_enqueue_script('facebox-js', get_template_directory_uri() . '/js/facebox.js', '', '1.0.0');
	wp_enqueue_script('videojsie8', 'https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js', '', '1.1.2');
	wp_enqueue_script('videojs', 'https://vjs.zencdn.net/5.8.8/video.js', '', '5.8.8');
	wp_enqueue_script('eot-progress-bar-js', get_template_directory_uri() . '/js/jquery.eotprogressbar.js', '', '1.0.0');
	wp_enqueue_script('jquery-pageslide-js', get_template_directory_uri() . '/js/jquery.pageslide.js', '', '1.0.0');
	wp_enqueue_script('datatables-js', get_template_directory_uri() . '/js/jquery.dataTables.min.js', '', '1.10.13');
        wp_enqueue_script('datatables-js-buttons', 'https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js', '', '1.4');




	

	/* register main js file and register local variables to be used in javascript */
	wp_register_script('target-js', get_template_directory_uri() . '/js/target.js', '', '1.0.0');
	wp_localize_script('target-js', 'target', 
		array (
			'eoturl' => get_bloginfo ('url'),
			'themeurl' => get_bloginfo ('stylesheet_directory'),
			'dashboard' => $eot_dashboard_url,
			'ajaxurl' => admin_url ('admin-ajax.php')
		)
	);
	wp_enqueue_script ('target-js');
	wp_enqueue_script( 'password-strength-meter' );


	/* register the style and js for the slider on home page */
	if (is_front_page ()) {
		wp_enqueue_style('slick-style', get_template_directory_uri() . '/css/slick.css', '', '1.3.7');
		wp_enqueue_script('slick-js', get_template_directory_uri() . '/js/slick.min.js', array ('jquery'), '1.3.7', true);
	}

	/*<!--[if lte IE 6]>
		<script type="text/javascript" src="/templates/rt_afterburner_j15/js/ie_suckerfish.js"></script>
	<![endif]-->*/
}// eot_enqueue_scripts

add_action( 'init', 'custom_post_types_init' );
/**
 * Registers the custom post types with the system
 */
function custom_post_types_init() {
	$labels = array(
		'name'               => _x( 'Presenters', 'post type general name', 'eot' ),
		'singular_name'      => _x( 'Presenter', 'post type singular name', 'eot' ),
		'menu_name'          => _x( 'Presenters', 'admin menu', 'eot' ),
		'name_admin_bar'     => _x( 'Presenter', 'add new on admin bar', 'eot' ),
		'add_new'            => _x( 'Add New', 'book', 'eot' ),
		'add_new_item'       => __( 'Add New Presenter', 'eot' ),
		'new_item'           => __( 'New Presenter', 'eot' ),
		'edit_item'          => __( 'Edit Presenter', 'eot' ),
		'view_item'          => __( 'View Presenter', 'eot' ),
		'all_items'          => __( 'All Presenters', 'eot' ),
		'search_items'       => __( 'Search Presenters', 'eot' ),
		'parent_item_colon'  => __( 'Parent Presenters:', 'eot' ),
		'not_found'          => __( 'No presenters found.', 'eot' ),
		'not_found_in_trash' => __( 'No presenters found in Trash.', 'eot' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'rewrite'            => array( 'slug' => 'presenter' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' )
	);

	register_post_type( 'presenter', $args );
}

/**
 * Maybe bbpress
 */
function maybe_bbpress() {

	if ( function_exists('is_bbpress') ) {
		return is_bbpress();
	} else {
		return false;
	}

}

/**
 * Maybe woocommerce
 */
function maybe_woocommerce() {

	if ( function_exists('is_woocommerce') ) {
		if ( is_woocommerce() || is_checkout() || is_order_received_page() || is_cart() || is_account_page() )
			return true;
	} else {
		return false;
	}

}


/**
 * Customize the registration URL to create a director account.
 */
add_filter( 'register_url', 'custom_register_url' );
function custom_register_url( $register_url )
{
    $register_url = get_permalink( $register_page_id = 101 );
    return $register_url;
}

/*
add_action('wp_footer', 'dump_data');
function dump_data(){
	global $post;
	echo "<pre>";
	print_r($post);
	echo "</pre>";
}
*/


/*
The next two functions add two columns to the users view on wordpress dashboard:
User ID and Created Date
*/
function add_user_fields( $column )
{
	$column['user_id'] = 'User ID';
	$column['created_date'] = 'Created Date';
	return $column;
}
add_filter( 'manage_users_columns', 'add_user_fields');

function add_user_fields_row( $val, $column_name, $user_id)
{
	switch ($column_name)
	{
		case 'user_id' :
			return $user_id;
			break;
		case 'created_date' :
			return get_user_by('ID', $user_id)->user_registered;
			break;
		default;
	}
	return $val;
}
add_filter( 'manage_users_custom_column', 'add_user_fields_row', 10, 3 );

//add login/logout menu items
add_filter( 'wp_nav_menu_items', 'wti_loginout_menu_link', 10, 2 );

function wti_loginout_menu_link( $items, $args ) {
   if ($args->theme_location == 'primary_login')
   {
   		$items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_logout_url( home_url() ) .'">'. __("Logout") .'</a></li>';
   }
   else if ($args->theme_location == 'primary')
   {
   		$items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_login_url (get_bloginfo ('url') . "/dashboard") .'">'. __("Login") .'</a></li>';
   }
   else if ($args->theme_location == 'footer')
   {
   		if (is_user_logged_in())
   		{
   			$items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_logout_url( home_url() ) .'">'. __("Logout") .'</a></li>';
   		}
   		else
   		{
   			$items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_login_url (get_bloginfo ('url') . "/dashboard") .'">'. __("Login") .'</a></li>';
   		}
   }
   return $items;
}

//custom select options for upload resources form
function acf_load_video_choices( $field ) {
    
    // reset choices
    $field['choices'] = array();

    $master_modules = getModules(); // Get all the modules from the master LE course.

    // loop through array and add to field 'choices'
    foreach ($master_modules as $module) {
        if($module['component_type'] == 'page') // filter out only video types
        {
            $field['choices'][ $module['title'] ] = $module['title'];
        }
    }

    // return the field
    return $field;
}
add_filter('acf/load_field/name=video_name', 'acf_load_video_choices');


/**
 * Returns the message body for the password reset mail.
 * Called through the retrieve_password_message filter.
 *
 * @param string  $message    Default mail message.
 * @param string  $key        The activation key.
 * @param string  $user_login The username for the user.
 * @param WP_User $user_data  WP_User object.
 *
 * @return string   The mail message to send.
 */
function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    // Create new message
    $msg  = __( 'Hello!', 'personalize-login' ) . "<br><br>\r\n\r\n";
    $msg .= sprintf( __( 'Someone, hopefully you, asked us to reset your password for your Expert Online Training account using the email address: %s.', 'personalize-login' ), $user_login ) . "<br><br>\r\n\r\n";
    $msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'personalize-login' ) . "<br><br>\r\n\r\n";
    $msg .= __( 'To reset your password, visit the following address: ', 'personalize-login' ) . "\r\n\r\n";
    $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . " <br><br>\r\n\r\n";
    $msg .= __( 'Thanks!', 'personalize-login' ) . "<br><br>\r\n\r\n";
	$msg .= __( 'The Expert Online Training Team', 'personalize-login' ) . "<br>\r\n"; 
    return $msg;
}
add_filter( 'retrieve_password_message', 'replace_retrieve_password_message', 10, 4 );


function track_logins($user_login, $user) 
{
  // declare database
  global $wpdb;
  $user_id = $user->ID;
  $org_id = get_org_from_user ($user_id);

  //I'm assuming you want to check the user role and
  //check for user role
  foreach($user->roles as $role){
      //replace role with the role you assigned to your students
      if($role === 'student'){
          $wpdb->insert(TABLE_TRACK,array(
              'user_id' => $user_id,
              'org_id' => $org_id,
              'date' => date('Y-m-d H:i:s'),
              'type' => 'login'
              )
          );
      }
  }
}
add_action('wp_login', 'track_logins', 10, 2);