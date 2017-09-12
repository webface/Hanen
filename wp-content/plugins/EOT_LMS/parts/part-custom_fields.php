<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span class="current">Custom Fields</span>     
</div>
<h1 class="article_page_title">Custom Fields</h1>

<?php
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
        // Variable declaration
        $count = 0;
        $num_users_to_get = 1000;
        $args = array(
            'role__in' => array ('student', 'manager'),
            'role__not_in' => array('administrator', 'sales_manager', 'salesrep'),
            'number' => $num_users_to_get,
            'offset' => $count * $num_users_to_get,
            'fields' => array ('ID', 'display_name', 'user_email', 'user_registered')
        );
        $users = array();
        while ($wp_users = get_users( $args ))
        {
            $users = array_merge($users, $wp_users);
            $count++;
            $args = array(
                'role__in' => array ('student', 'manager'),
                'role__not_in' => array('administrator', 'sales_manager', 'salesrep'),
                'number' => $num_users_to_get,
                'offset' => $count * $num_users_to_get,
                'fields' => array ('ID', 'display_name', 'user_email', 'user_registered')
            );
        }

        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
    	$userTableObj->rows = array();
        $userTableObj->headers = array(
    		'Name' => 'left',
    		'E-mail'=> 'center',
    		'Camp' => 'center',
    		'<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'center',
    		'Actions'=> 'center'
    	);
        /* 
         * This add the users who are a student or a camp director.
         * Count the number of enrollments
         */
        foreach($users as $user)
        {
            $user_id = $user->ID; // Wordpress user ID
            $name = $user->display_name; // User's First and Last name in wordpress
            $email = $user->user_email; // User's Wordpress Email
            $date_registered = substr($user->user_registered, 0, 10); // User's date registration in wordpress
            $org_id = get_org_from_user ($user_id); // Organization ID
            $camp_name = get_the_title($org_id);

            // Create a table row.
    		$userTableObj->rows[] = array($name, $email, $camp_name, $date_registered, '<a href="?part=update_custom_fields&id=' . $user_id . '" onclick="load(\'load_loading\')">Edit</a>');
        }
?>
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