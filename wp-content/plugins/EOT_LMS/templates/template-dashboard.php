<?php
/**
 * Template Name: Template Dashboard
 * This is a Custom Page Template.
 * This is based on the theme page.php template.
 */

// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
get_header(); // Loads the header.php template. ?>
        <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

        <?php get_template_part( 'breadcrumbs' ); // Loads the loop-meta.php template. ?>
        <div id="main-content" class="s-c-x">
        <div id="colmask" class="ckl-color2">
            <div id="colmid" class="cdr-color1">
                <div id="colright" class="ctr-color1">
                    <?php get_sidebar (); ?>
                    <div id="col1wrap">
                        <div id="col1pad">
                            <div id="col1">
                                <div class="component-pad">
                                   <section class="hentry">
                                    <div class="entry-content">
                                   <?php
                                    // This will output the part.
                                    // It is wrapped in the conditional is_user_logged_in() to ensure only
                                    // users who are logged-in to the website can view the content.
                                    // https://codex.wordpress.org/Function_Reference/is_user_logged_in
                                    if ( is_user_logged_in() ) { 
                                        echo do_shortcode( '[load_template_part]' );
                                    } else {
                                        echo '<h4 class="red">Sorry, you must be logged in to your account to view this.<h4>';
                                    }
                                   
                                    /**
                                     * Here we are retrieving the 'EOT_LMS_settings' array from the options database table.
                                     * If the desired option does not exist, or no value is associated with it, FALSE will be returned.
                                     * See /wp-admin/options.php
                                     */
                                    $EOT_LMS_settings = get_option( 'EOT_LMS_settings', $default = false);
                                    if ($EOT_LMS_settings)
                                    {
                                        if (isset($EOT_LMS_settings['template_one_field']))
                                        {
                                            echo "<p>" . $EOT_LMS_settings['template_one_field'] . "</p>";
                                        }
                                        if (isset($EOT_LMS_settings['template_two_field']))
                                        {
                                        echo "<p>" . $EOT_LMS_settings['template_two_field'] . "</p>";
                                        }
                                    }
                                    else 
                                    {
                                        echo "Sorry, the key could not be found in the options database table.";
                                    }
                                    ?>
                                    </div>
                                </section>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
<?php get_footer(); // Loads the footer.php template. ?>

