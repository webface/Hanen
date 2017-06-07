<?php
/**
 * Template Name: Template Register
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

        <div class="container">

            <div id="content" <?php if ( !is_single() && !is_page() && !is_attachment() ) echo 'class="hfeed"'; ?>>

                <?php get_template_part( 'loop' ); // Loads the loop.php template. ?>

                <?php if ( ! maybe_bbpress() ) get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

                <section class="hentry">
                    <div class="entry-content">

                    <h4>Retrieving info from the database options table.</h4>
                    
                    <?php
                    /**
                     * Here we are retrieving the 'EOT_LMS_settings' array from the options database table.
                     * If the desired option does not exist, or no value is associated with it, FALSE will be returned.
                     * See /wp-admin/options.php
                     */
                    $EOT_LMS_settings = get_option( 'EOT_LMS_settings', $default = false);
                    if ($EOT_LMS_settings):
                        echo "<p>" . $EOT_LMS_settings['template_one_field'] . "</p>";
                        echo "<p>" . $EOT_LMS_settings['template_two_field'] . "</p>";
                    else:
                        echo "Sorry, the key could not be found in the options database table.";
                    endif;
                    ?>
                    </div>
                </section>

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
                ?>

            </div><!-- #content -->

        <?php 

        if ( maybe_woocommerce() ) {
            get_sidebar( 'woocommerce' ); // Loads the sidebar-woocommerce.php template.
        } elseif ( maybe_bbpress() ) {
            get_sidebar( 'bbpress' ); // Loads the sidebar-bbpress.php template.
        } else {
            get_sidebar( 'primary' ); // Loads the sidebar-primary.php template. 
        }

        ?>

        </div><!-- .container -->

<?php get_footer(); // Loads the footer.php template. ?>

