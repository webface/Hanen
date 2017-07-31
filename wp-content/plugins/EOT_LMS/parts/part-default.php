<?php
// Variable declaration
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$email = $current_user->user_email; // Wordpres e-mail address
$org_id = get_org_from_user($user_id); // Organization ID
$org_name = get_the_title($org_id);
$portal_subdomain = ''; // Subdomain of the user REMOVE THIS LATER
$data = compact("org_id");
$image = get_field('dashboard_logo', 'user_' . $org_id); // Advance custom fields. Field for the dashboard_logo
$page_title = "Dashboard";

// Camp Director
if (current_user_can("is_director")) 
{
//Org name is the same as portal in LU
//   // This creates the portal, if the portal does not exsist.
//    $isPortalFound = getPortalByTitle(DEFAULT_SUBDOMAIN, $org_name);
//    if (!isset($isPortalFound[0]['id'])) 
//    {
//        // no portal found so create it
//        $org_subdomain = $portal_subdomain;
//        $first_name = $current_user->user_firstname;
//        $last_name = $current_user->user_lastname;
//        $password = wp_generate_password('8', false);
//        $portal_data = compact("org_id", "org_name", "org_subdomain", "user_id", "first_name", "last_name", "email", "password");
//        // Create the Portal
//        $result = communicate_with_learnupon('create_account', $portal_data);
//
//        // Check for errors.
//        if (isset($result['status']) && !$result['status']) 
//        {
//            $error_message = $result['message'];
//        }
//    }
    
    if (current_user_can("is_uber_manager")) 
    {
        $page_title = "Uber Manager Dashboard";
    } 
    else if (current_user_can("is_umbrella_manager")) 
    {
        $page_title = "Umbrella Manager Dashboard";
    }
    ?>

    <h1 class="article_page_title"><?= $page_title ?></h1>
    <?php
    // Display errors if variable exsist
    if (isset($error_message)) 
    {
        echo "<div class='round_msgbox'>$error_message</div>";
    }

    // Display successful updgrade message.
    if (isset($_REQUEST['status']) && isset($_REQUEST['status']) == 'upgradeSubscription') 
    {
        echo "<div class='round_msgbox'><h2>Subscription Upgraded Successfully!</h2>You have succesfully upgraded your account.</div>";
    }

    // Display the dashboard banner
    if (!empty($image)) 
    {
        ?>
        <div class="dashboard_banner acf-image-image">
            <center><img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/></center>
            <br/>
        </div>
        <?php
    }

    // if its an uber manager, display the uber manager dashboard
    if (current_user_can("is_uber_manager") && function_exists('display_uber_manager_dashboard')) 
    {
        display_uber_manager_dashboard();
        ?>
        <script>
            $ = jQuery;
            $(document).ready(function ()
            {
                $('.dataTable').addClass('td-ubermanager');
            });
        </script>
        <?php
    }
    // if its an uber manager, display the uber manager dashboard
    else if (current_user_can("is_umbrella_manager") && function_exists('display_umbrella_manager_dashboard')) 
    {
        display_umbrella_manager_dashboard();
        ?>
        <script>
            $ = jQuery;
            $(document).ready(function ()
            {
                $('.dataTable').addClass('td-ubermanager');
            });
        </script>
        <?php
    }

    // Display info for the subscription page.
    if (function_exists('display_subscriptions')) 
    {
        display_subscriptions();
    }
}
// Sales representative and the Sales manager.
else if (current_user_can("is_sales_rep") || current_user_can("is_sales_manager")) 
{
    $libraries = getLibraries(); // All the libraries in wp_library table
    ?> 
    <h1 class="article_page_title"> Sales Rep Administration Panel</h1>
    <ul>
        <li><a href="?part=user_list" onclick="load('load_manage_staff_accounts')"><b>Manage Users</b></a> | <a href="?part=user_list&ignore=students"><b>Manage Directors Only</b></a></li>
        <li><a href="?part=admin_create_account"><b>Create New User</b></a></li>
        <li><a href="?part=custom_fields"><b>Update Custom User Fields</b></a></li>
        <li><a href="?part=upload_resources"><b>Upload Resources</b></a></li>
        <li><a href="?part=manage_uber_managers2"><b>Manage Uber/Umbrella Managers</b></a></li>

        <?php
        if (current_user_can('sales_manager')) 
        {
            ?>
            <li><a href="?part=create_sales_report&start_date=<?= SUBSCRIPTION_START ?>&end_date=<?= SUBSCRIPTION_END ?>"><b>Create Sales Report</b></a></li>
            <li><a href="?part=manage_sales_rep"><b>Manage Sales Rep</b></a></li>
            <?php
        }
        ?>
    </ul>
    <?php
    foreach ($libraries as $library) 
    {
        $library_name = $library->name; // The library name
        $revenue = 0; // The revenue for the this library
        $num_inactive = 0; // Nummber of inactive subscriptions.
        $subscriptions = getSubscriptions(0, $library->ID, 0, 1);
        /**
         * This calculates the total amount of all subscriptions in this library.
         * also counts the subscriptions that are not active.
         */
        foreach ($subscriptions as $subscription) 
        {
            $price = $subscription->price; // Sold Price for this subscription
            $status = $subscription->status; // Subscription Status
            $revenue += $price;
            if ($status != "active") 
            {
                $num_inactive++;
            }
        }
        ?>
        <table class="data">
            <tbody>
                <tr class="head">
                    <td colspan="2">
                        &nbsp;&nbsp;<b><?= $library_name ?></b>
                    </td>
                </tr>
                <tr class="head2">
                    <td class="label">
                        <?= SUBSCRIPTION_YEAR ?>              
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        <a href="./?part=admin_view_subscriptions&library_id=<?= $library->ID ?>">
                            Subscribers
                        </a>
                    </td>
                    <td class="value right">
                        <?= count($subscriptions); ?>           
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        Revenue
                    </td>
                    <td class="value right">
                        $ <?= number_format($revenue, 2, '.', ',') ?>            
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        Inactive Subscriptions
                    </td>
                    <td class="value right"> 
                        <?= $num_inactive ?>        
                    </td>
                </tr>
                <tr>
                    <td class="label" colspan="2">
                        <a href="?part=questionnaire&library_id=<?= $library->ID; ?>">Questionnaire Dashboard</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }
}
// Student
else if (current_user_can("is_student")) 
{
    $enrollments = getEnrollmentsByUserId($user_id); // All the enrollments of the user.
    $courses = getCourses(0, $org_id);

    if (!empty($image)) 
    {
?>
        <div class="dashboard_banner acf-image-image">
            <center><img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/></center>
            <br/>
        </div>
<?php
    }
    if ( $enrollments && count($enrollments) > 0) 
    { // Check if the user is enrolled to any course.
        // Display the enrollments information in the dashboard
        foreach ($enrollments as $enrollment) 
        {
            $course_id = $enrollment['course_id']; // The course ID of the course this user is enrolled in
            // Get all the modules in this course
            $modules = getModulesInCourse($course_id);
            $course_name = ( array_key_exists($course_id, $courses) ) ? $courses[$course_id]->course_name : "could not find the course name"; // Check if the that course id is in $courses.
            $course = ( array_key_exists($course_id, $courses) ) ? $courses[$course_id] : "";
            if ($modules) 
            {
                $status = formatStatus($enrollment['status']);
                $percentage_complete = ($status == 'Not Started') ? 0 : calc_course_completion($user_id, $course_id); // the percentage complete for this course
                if ($status == "Failed") 
                {
                    $status = 'In Progress';
                } 
                else if ($status == "Completed" || $status == "Passed") 
                {
                    $percentage_complete = 100;
                }
?>
                <div class="dashboard_border student">
                    <h1><?= $course_name ?>
                    </h1>
                    <div class="content_right">
                        <div class="clear"></div>
                        <div class="menu">
                            <a href="?part=my_library&course_id=<?= $course_id?>" class="my_library">
                                <div class="thumbnail">
                                    <i class="fa fa-youtube-play" alt="Content"></i>
                                </div>
                                <div class="para">
                                    <h1>Start Course</h1>
                                    <br/>
                                    Watch the videos, take quizzes, see resources
                                </div>
                            </a>
                        </div> 
                    </div>
                    <div class="content_left student">
                        <table class="tb_border">
                            <tbody>
                                <tr>
                                    <td class="s1 darklabel">
                                        Modules
                                    </td>
                                    <td class="s2">
                                        <?= count($modules) ?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td class="s1 darklabel">
                                        Status
                                    </td>
                                    <td class="s2">
                                        <?= $status ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>

                    </div>
                    <div class="dashboard_button">
                        <a href="?part=staff_lounge&subscription_id=" onclick="load('load_staff_lounge')">
                            <div class="title" style="padding-top: 5px;">
                                <b>Virtual Staff Lounge</b>
                                <br>Manage your Forum
                            </div>
                        </a>
                    </div>
                    <div>
                        <b>Technical Support</b>
                        <br>
                        Toll-free 877-237-3931
                    </div>
                </div>
                <script>
                    $ = jQuery;
                // Create HTML with the enrollments and append it to the sidebar
                    $("#listOfCourses").append('\
                        <div id="bannerArea">\
                                <img id="menu-banner" src="' + ajax_object.template_url + '/images/menu-banner.png">\
                                <h2><?= $course_name ?></h2>\
                        </div>\
                        <center><h3><?= $status ?></h3></center>' + '<?php echo eotprogressbar('99%', $percentage_complete, false); ?>');
                </script>
<?php
            } 
            else 
            { 
                // User has no modules
                echo '<b>' . $course_name . '</b>: There are no modules in this course. Please contact your camp director.';
            } 
        }
    } 
    else 
    { 
        // Display message if the user has no enrollments.
        echo "<p>You do not have any enrollments.</p>";
    }
} 
else 
{
    if (!empty($image)) 
    {
?>
        <div class="dashboard_banner acf-image-image">
            <center><img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/></center>
            <br/>
        </div>
<?php
    }
    wp_die("Please contact the administrator. I could not find your role.");
}
?>