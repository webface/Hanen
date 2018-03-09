<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'fl_head', 'FLChildTheme::stylesheet' );

//add and remove menu items
add_filter( 'wp_nav_menu_items', 'wti_loginout_menu_link', 500, 2 );

function wti_loginout_menu_link( $items, $args ) {
	if ($args->menu == 'main-menu-for-beaver-builder' || is_object($args->menu) && $args->menu->slug == 'main-menu-for-beaver-builder')
	{
		
            preg_match_all('/<li[^>]*><a[^>]*>[^<]*<\/a><\/li>/', $items, $matches);
            if (is_user_logged_in())
            {
                //next 3 lines remove the features menu item
                $features = preg_grep('/<li[^>]*><a[^>]*>Features<\/a><\/li>/', $matches[0]);
                $features_key = array_search($features, $matches);
                unset($matches[0][$features_key]);
                //add dashboard menu item to the beginning
                array_unshift($matches[0], "<li id='menu-item-730' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-712'><a href='/dashboard'>Dashboard</a></li>");
                $items = implode($matches[0]);
                //add logout menu item to the end
                $items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_logout_url( home_url() ) .'">'. __("Logout") .'</a></li>';
            }
            else
            {
                //add login menu item to the end
                $items .= '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_login_url (get_bloginfo ('url') . "/dashboard") .'">'. __("Login") .'</a></li>';
            }
	}
	else if ($args->menu == 'footer-menu')
	{
		preg_match_all('/<li[^>]*><a[^>]*>[^<]*<\/a><\/li>/', $items, $matches);

   		if (is_user_logged_in())
   		{
                    //next 3 lines remove subscribe menu item
                    $subscribe = preg_grep('/<li[^>]*><a[^>]*>Subscribe<\/a><\/li>/', $matches[0]);
                    $subscribe_key = array_search($subscribe, $matches);
                    unset($matches[0][$subscribe_key]);
                    //add logout menu item to the beginning
                    array_unshift($matches[0], '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_logout_url( home_url() ) .'">'. __("Logout") .'</a></li>');
                    $items = implode($matches[0]);
   		}
   		else
   		{
                    //add login menu item to the beginning
                    array_unshift($matches[0], '<li id="menu-item-712" class="menu-item menu-item-type=post_type menu-item-object-page menu-item-712"><a href="'. wp_login_url (get_bloginfo ('url') . "/dashboard") .'">'. __("Login") .'</a></li>');
                    $items = implode($matches[0]);
   		}
	}
        
	return $items;
}

add_action( 'gform_user_registered', 'gravity_registration_autologin', 10, 4 );
/**
 * Auto login after registration.
 */
function gravity_registration_autologin( $user_id, $user_config, $entry, $password ) {
    $user = get_userdata( $user_id );
    $user_login = $user->user_login;
    $user_password = $password;

    $terror = wp_signon( array(
		'user_login' => $user_login,
		'user_password' =>  addslashes($user_password),
		'remember' => false
    ) );
}

// Filter wp_nav_menu() to add additional links and other output
function new_nav_menu_items($items,$args)
{
    //check if its the frigging main beaver builder menu
    if ($args->menu == 'main-menu-for-beaver-builder' || is_object($args->menu) && $args->menu->slug == 'main-menu-for-beaver-builder')
    {
        $languages = icl_get_languages('skip_missing=1');
        if(1 < count($languages))
        {
            foreach($languages as $l)
            {
            $items.= '<li class="menu-item wpml-ls-slot-19 wpml-ls-item"><a href="'.$l['url'].'"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /></a></li>';

            }
        }
    
    }
    
    return $items;
}
//add_filter( 'wp_list_pages', 'new_nav_menu_items' , 600, 2);
//add_filter( 'wp_nav_menu_items', 'new_nav_menu_items', 600, 2);

// Disabled video download for every page that is using beaver builder.
function disable_video_download_js() {
?>
    <script>
    $ = jQuery
    // Disable video from right clicking and download.
    $("video").on("contextmenu", function(e) 
    {
     e.preventDefault();
    });
    </script>;
<?php
};
// Add hook for admin <footer>
add_action('admin_footer', 'disable_video_download_js');
// Add hook for front-end <footer
add_action('wp_footer', 'disable_video_download_js');
 
