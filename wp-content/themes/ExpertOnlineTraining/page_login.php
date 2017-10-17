<?php
/**
 * Template Name:Login
 * 
 * @package bootstrap-basic
 */
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb">
    <head>
        <?php wp_head(); ?>
        <style>
            body{
                background: #f1f1f1;
                min-width: 0;
                color: #444;
                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                font-size: 13px;
                line-height: 1.4em;
            }
            .login{
                width:100%;
                text-align: center;
            }
            .login h1{
                color:transparent;
            }
            .login h1 a {
                width: 300px !important;
                height: 67px !important;
            }
            .login label {
                color: #ffffff;
            }
        </style>
    </head>
    <body>
        <div class="login">
            <h1><a href="https://www.expertonlinetraining.com" title="Online Training for Summer Camp Staff" tabindex="-1"><img src="https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear-300x67-1.png"/></a></h1>
        <?php
        // Start the loop.
        while (have_posts()) : the_post();

            // Include the page content template.

            the_content();


        // End of the loop.
        endwhile;
        ?>
        </div>
        <?php wp_footer(); ?>
        
    </body>
</html> 