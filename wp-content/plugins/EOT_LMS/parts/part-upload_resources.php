<?php
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
    ?>
    	<div class="breadcrumb">
    		<?= CRUMB_DASHBOARD ?>    
    		<?= CRUMB_SEPARATOR ?>     
        	<span class="current">Upload Resources</span>     
    	</div>
    	<h1 class="article_page_title">Upload Resources</h1>
        <?php

        //Show success or error message after uploading
        if(isset($_REQUEST['updated']) && $_REQUEST['updated'] == true)
        {  
            if(get_field('file_or_url') == 'File')
            {
                $file = get_field('resource');
                $url = $file['url'];
            }
            else
            {
                $url = get_field('url');
            }
            $result = uploadResource(get_field('video_name'), get_field('name'), get_field('order'), $url);
            echo $result;   
        }

        //Display the custom form
        $options = array(
                'field_groups' => array(ACF_UPLOAD_RESOURCE),
                'return' => '?part=upload_resources&updated=true',
                'submit_value' => __("Upload Resource", 'acf'),
        );
        acf_form( $options );
    }
    else
    {
        echo "You do not have the privilege to view this page.";
    }
?>
<script>
</script>