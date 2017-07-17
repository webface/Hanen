<?php
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
        // Variable declaration
        global $current_user;
        $user_id = $current_user->ID;                  // Wordpress user ID
        $org_id = get_org_from_user ($user_id);              // Organization ID
        $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
        $data = compact ("org_id", "org_subdomain", "user_id");
        $users = get_users( ); // Users in wordpress

        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
    	$userTableObj->rows = array();
        $userTableObj->headers = array(
    		'Name' => 'quiz-title',
    		'E-mail'=> 'center',
    		'Camp' => 'center',
    		'<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'staff-progress',
    		'Actions'=> 'actions'
    	);
        /* 
         * This add the users who are a student or a camp director.
         * Count the number of enrollments
         */
        foreach($users as $user)
        {
            if(isset($user->allcaps['is_student'])  || isset($user->allcaps['is_director'])) // Check the user capability to filter only students and directors
            {
            	$user_id = $user->ID; // Wordpress user ID
            	$name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
            	$email = $user->user_email; // User's Wordpress Email
            	$date_registered = substr($user->user_registered, 0, 10); // User's date registration in wordpress
                $org_id = get_org_from_user ($user_id); // Organization ID
                $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
                $data = compact ("org_id");
                $camp_name = get_the_title($org_id);

                // Create a table row.
        		$userTableObj->rows[] = array($name, $email, $camp_name, $date_registered, '<a href="?part=update_custom_fields&id=' . $user_id . '" onclick="load(\'load_loading\')">Edit</a>');
            }
        }
    ?>
    	<div class="breadcrumb">
    		<?= CRUMB_DASHBOARD ?>    
    		<?= CRUMB_SEPARATOR ?>     
        	<span class="current">Custom Fields</span>     
    	</div>
    	<h1 class="article_page_title">Custom Fields</h1>
    <?php
        // Display the user's table
    	CreateDataTable($userTableObj);
    }
    else
    {
        echo "You do not have the privilege to view this page.";
    }
?>
<script>
    $ = jQuery;
    $(document).ready(function()
    {
        $('.dataTable').addClass('user-custom');
    });
</script>