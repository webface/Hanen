<?php
/**
 * Plugin Name: SW Breadcrumbs
 * Plugin URI: http://www.beaverlodgehq.com
 * Description: Add breadcrumbs in Beaver Builder.
 * Version: 1.0.0
 * Author: Jon Mather
 * Author URI: http://www.simplewebsiteinaday.com.au
 */

define( 'SW_BREADCRUMBS_MODULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SW_BREADCRUMBS_MODULE_URL', plugins_url( '/', __FILE__ ) );

function sw_breadcrumbs_module() {
    if ( class_exists( 'FLBuilder' ) ) {
        require_once 'includes/bb-sw-breadcrumbs-module.php';
    }
}
add_action( 'init', 'sw_breadcrumbs_module' );

// Breadcrumbs
//function sw_breadcrumbs_old() {
//		echo '<ul class="breadcrumbs">';
//	if (!is_home()) {
//		echo '<li><a href="';
//		echo get_option('home');
//		echo '">';
//		echo 'Home';
//		echo "</a></li>";
//		if (is_category() || is_single()) {
//			echo '<li>';
//			the_category(' </li><li> ');
//			if (is_single()) {
//				echo "</li><li>";
//				the_title();
//				echo '</li>';
//			}
//		} elseif (is_page()) {
//			echo '<li>';
//			echo the_title();
//			echo '</li>';
//		}
//	}
//	elseif (is_tag()) {single_tag_title();}
//	elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
//	elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
//	elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
//	elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
//	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
//	elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
//	echo '</ul>';
//}

// Breadcrumbs
function sw_breadcrumbs() {
    global $post;
    echo '<ul id="breadcrumbs">';
    if (!is_home()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo '</a></li><li class="separator"> / </li>';
        if (is_category() || is_single()) {
            echo '<li>';
            the_category(' </li><li class="separator"> / </li><li> ');
            if (is_single()) {
                echo '</li><li class="separator"> / </li><li>';
                the_title();
                echo '</li>';
            }
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {
                    $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">/</li>';
                }
                echo $output;
                echo '<li><strong title="'.$title.'"> '.$title.'</strong></li>';
            } else {
                echo '<li><strong> '.get_the_title().'</strong></li>';
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
    elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
    elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
    elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
    elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
    echo '</ul>';
}