<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(is_plugin_active('EOT_LMS/EOT_LMS.php'))
{
	echo "plugin active";
}
else
{
	echo "plugin inactive";
}
?>
<h1 class="article_page_title"><?php the_title(); ?></h1>

<div class="entry-content">
	<?php the_content(); ?> 
</div>