<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <span class="current">Users Lists</span>     
</div>
<h1 class="article_page_title">Users List</h1>
<?php
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
        $search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
?>
    <ul>
        <li>
            <a href="?part=user_list" onclick="load('load_manage_staff_accounts')"><b>Manage Users</b></a> | <a href="?part=user_list&ignore=students"><b>Manage Directors Only</b></a><span class="bs">
                <form class="form-inline pull-right" action="?part=user_list" method="POST">
                    <div class="form-group">
                      <label class="sr-only" for="search">Search by name, email</label>
                      <div class="input-group">
                          <div class="input-group-addon"><i class="fa fa-user"></i></div>
                          <input type="text" class="form-control" value="<?= $search ?>" name="search" id="search" placeholder="Search Users" pattern=".{3,}"   required title="3 characters minimum">
                        
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </span>
        </li>
    </ul>
<?php
        if($search != '')
        {
            // disply the search term in the search box.
            $search_string = esc_attr( $search );
            $users = new WP_User_Query( array(//search columns
                'search'         => "*{$search_string}*",
                'search_columns' => array(
                    'user_login',
                    'user_nicename',
                    'user_email',
                    'user_url',
                )
            ) );

            $users2 = new WP_User_Query( array(//search meta fields
                'search'         => "*{$search_string}*",
                'search_columns' => array(
                'user_login',
                'user_nicename',
                'user_email',
                'user_url',
                ),
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'first_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'last_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    )
                )
            ) );

            $users_found =  array_merge(  $users->get_results(),  $users2->get_results() ) ;
            $users_found = array_unique($users_found, SORT_REGULAR);
            /*
             * Create table heading for users
             */
            $userTableObj = new stdClass(); 
            $userTableObj->rows = array();
            $userTableObj->headers = array(
                'Name' => 'center',
                'E-mail'=> 'center',
                'Camp' => 'center',
                '<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'staff-progress',
                'Role' => 'center',
                'Actions'=> 'actions'
            );
            /* 
                 * This add the users who are a student or a camp director.
                 * Count the number of enrollments
                 */

            foreach($users_found as $user)
            {
                $user_id = $user->ID; // Wordpress user ID
                $name = $user->display_name;
                //$email = str_replace("@", "\@", $user->user_email); // User's Wordpress Email
                $email = $user->user_email;
                $date_registered = substr($user->user_registered, 0, 10); // User's date registration in wordpress
                $org_id = get_org_from_user ($user_id); // Organization ID
                $camp_name = get_the_title($org_id);
    //            $roles = str_replace('/bbp_participant', '', implode("/", $user->roles));
                $roles = 'subscriber';
                if(in_array('student', $user->roles))
                {
                    $roles = 'Student';
                    $actions = '<a id="switch-user" href="#" data-id="' . $user_id . '"' . hover_text_attr('Switch to User',true) . '><i class="fa fa-share" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=update_user&id=' . $user_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Update User',true) . '><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=confirm_delete_user&user_id='.$user_id.'" onclick="load(\'load_loading\')"' . hover_text_attr('Delete User',true) . '><i class="fa fa-trash" aria-hidden="true"></i></a>';
                }
                if(in_array('manager', $user->roles))
                {
                    $roles = 'Director';
                    $actions = '<a href="?part=admin_create_account&user_id=' . $user_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Create a new subscription',true) . '><i class="fa fa-cart-plus" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a id="switch-user" href="#" data-id="' . $user_id . '"' . hover_text_attr('Switch to User',true) . '><i class="fa fa-share" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=update_user&id=' . $user_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Update User',true) . '><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=confirm_delete_user&user_id='.$user_id.'" onclick="load(\'load_loading\')"' . hover_text_attr('Delete User',true) . '><i class="fa fa-trash" aria-hidden="true"></i></a>';
                }
                if($roles != "subscriber")
                {
                    // Create a table row.
                    $userTableObj->rows[] = array($name, $email, $camp_name, $date_registered, /*str_replace('manager', 'Director', $roles)*/ $roles, $actions);
                }
            }

            echo "<h2>Search Results</h2>";
            CreateDataTable($userTableObj, "100%", 25);
        }
        else 
        {
        
            $ignore = (isset($_GET['ignore'])) ? $_GET['ignore'] : '';
            if ($ignore != 'students')
            {
                // Variable declaration
                $count = 0;
                $num_users_to_get = 5000;
                $args = array(
                    'role__in' => array ('student'),
                    'role__not_in' => array('administrator', 'sales_manager', 'salesrep'),
                    'number' => $num_users_to_get,
                    'offset' => $count * $num_users_to_get,
                    'fields' => array ('ID', 'display_name', 'user_email', 'user_registered')
                );
                $users = array();
                while ($students = get_users( $args ))
                {
                    $users = array_merge($users, $students);
                    $count++;
                    $args = array(
                        'role__in' => array ('student'),
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
                    'Name' => 'center',
                    'E-mail'=> 'center',
                    'Camp' => 'center',
                    '<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'staff-progress',
                    'Role' => 'center',
                    'Actions'=> 'actions'
                );

                /* 
                 * This add the users who are a student or a camp director.
                 * Count the number of enrollments
                 */

                foreach($users as $user)
                {
                    $user_id = $user->ID; // Wordpress user ID
                    $name = $user->display_name;
                    //$email = str_replace("@", "\@", $user->user_email); // User's Wordpress Email
                    $email = $user->user_email;
                    $date_registered = substr($user->user_registered, 0, 10); // User's date registration in wordpress
                    $org_id = get_org_from_user ($user_id); // Organization ID
                    $camp_name = get_the_title($org_id);
        //            $roles = str_replace('/bbp_participant', '', implode("/", $user->roles));
                    $roles = 'Student';
                    // Create a table row.
                    $userTableObj->rows[] = array($name, $email, $camp_name, $date_registered, /*str_replace('manager', 'Director', $roles)*/ $roles,'<a id="switch-user" href="#" data-id="' . $user_id . '"' . hover_text_attr('Switch to User',true) . '><i class="fa fa-share" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=update_user&id=' . $user_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Update User',true) . '><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=confirm_delete_user&user_id='.$user_id.'" onclick="load(\'load_loading\')"' . hover_text_attr('Delete User',true) . '><i class="fa fa-trash" aria-hidden="true"></i></a>');

                }

                echo "<h2>Students</h2>";
                CreateDataTable($userTableObj, "100%", 10);
            } // end if ignore students

            // get the directors
            $args2 = array(
                'role__in' => array ('manager'),
                'role__not_in' => array('administrator', 'sales_manager', 'salesrep'),
                'number' => -1,
                'fields' => array ('ID', 'display_name', 'user_email', 'user_registered')
            );
            $directors = get_users( $args2 ); // Users in wordpress

            $directorTableObj = new stdClass(); 
            $directorTableObj->rows = array();
            $directorTableObj->headers = array(
                'Name' => 'center',
                'E-mail'=> 'center',
                'Camp' => 'center',
                '<div ' . hover_text_attr('The date when user first registered.',true) .'>Registered</div>' => 'staff-progress',
                'Role' => 'center',
                'Actions'=> 'actions'
            );

            foreach($directors as $director)
            {
                $director_id = $director->ID; // Wordpress user ID
                $name = $director->display_name;
                //$email = str_replace("@", "\@", $director->user_email); // User's Wordpress Email
                $email = $director->user_email;
                $date_registered = substr($director->user_registered, 0, 10); // User's date registration in wordpress
                $org_id = get_org_from_user ($director_id); // Organization ID
                $camp_name = get_the_title($org_id);
    //            $roles = str_replace('/bbp_participant', '', implode("/", $director->roles));
                $roles = 'Director';
                // Create a table row.
                $directorTableObj->rows[] = array($name, $email, $camp_name, $date_registered, /*str_replace('manager', 'Director', $roles)*/ $roles,'<a href="?part=admin_create_account&user_id=' . $director_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Create a new subscription',true) . '><i class="fa fa-cart-plus" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a id="switch-user" href="#" data-id="' . $director_id . '"' . hover_text_attr('Switch to User',true) . '><i class="fa fa-share" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=update_user&id=' . $director_id . '" onclick="load(\'load_loading\')"' . hover_text_attr('Update User',true) . '><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="?part=confirm_delete_user&user_id='.$director_id.'" onclick="load(\'load_loading\')"' . hover_text_attr('Delete User',true) . '><i class="fa fa-trash" aria-hidden="true"></i></a>');
            }    

            // Display the user's table
            echo "<h2>Directors</h2>";
            CreateDataTable($directorTableObj, "100%", 10);
        }
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
        $('.dataTable').addClass('user-list');
    });

    $("#switch-user").live('click', function() {
        load('load_loading');

        var user_id = $(this).attr('data-id');
        var data = { action: 'switchUser', user_id: user_id};
        var url =  ajax_object.ajax_url;

        //ajax call to switch user
        $.ajax({
          type: "POST",
          url: url,
          dataType: 'json',
          data: data,
          success:
          function(data)
          {
            window.location.href = eot.dashboard; // Redirect to dashboard
          }
        });
        return false;
    });
</script>