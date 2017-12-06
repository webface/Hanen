<?php
// Variable declaration
global $current_user;
$user_id = $current_user->ID; // Wordpress user ID
$email = $current_user->user_email; // Wordpres e-mail address
$org_id = get_org_from_user($user_id); // Organization ID
$org_name = get_the_title($org_id);
$portal_subdomain = ''; // Subdomain of the user REMOVE THIS LATER
$image = get_field('dashboard_logo', 'user_' . $org_id); // Advance custom fields. Field for the dashboard_logo
$page_title = __("Dashboard", "EOT_LMS");

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
        $page_title = __("Uber Manager Dashboard", "EOT_LMS");
    } 
    else if (current_user_can("is_umbrella_manager")) 
    {
        $page_title = __("Umbrella Manager Dashboard", "EOT_LMS");
    }
?>

    <h1 class="article_page_title"><?= $page_title ?></h1>
<?php

    // Check that this director accepted the terms of use
    $subscriptions = get_current_subscriptions ($org_id);
    if (!empty($subscriptions))
    {
        // user has at least 1 subscription so need to check if they accepted the terms
        $library = getLibrary($subscriptions[0]->library_id);
        $accepted_terms = accepted_terms($library); // boolean if user has accepted terms of use
        if (!$accepted_terms)
        {
            return; // dont disply the rest of the dashboard because user hasn't accepted the terms yet
        }
    }

    // Display errors if variable exsist
    if (isset($error_message)) 
    {
        echo "<div class='round_msgbox'>" . __("$error_message", "EOT_LMS") . "</div>";
    }

    // Display successful updgrade message.
    if (isset($_REQUEST['status']) && isset($_REQUEST['status']) == 'upgradeSubscription') 
    {
        echo "<div class='round_msgbox'><h2>" . __("Subscription Upgraded Successfully!", "EOT_LMS") . "</h2>" . __("You have succesfully upgraded your account.", "EOT_LMS") . "</div>";
    }

    // Display the dashboard banner
    if (!empty($image)) 
    {
        ?>
        <div class="dashboard_banner acf-image-image">
            <img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/>
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
        <li><a href="?part=user_list" onclick="load('load_manage_staff_accounts')"><b>Manage Users</b></a> | <a href="?part=user_list&ignore=students"><b>Manage Directors Only</b></a><span class="bs">
                <form class="form-inline pull-right" action="?part=user_list" method="POST">
            <div class="form-group">
              <label class="sr-only" for="search">Search by name, email</label>
              <div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-user"></i></div>
                  <input type="text" class="form-control" name="search" id="search" placeholder="Search Users" pattern=".{3,}"   required title="3 characters minimum">
                
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
          </form>
            </span>
        </li>
        <li><a href="?part=admin_create_account"><b>Create New User</b></a></li>
        <li><a href="?part=custom_fields"><b>Update Custom User Fields</b></a></li>
        <li><a href="?part=upload_resources"><b>Upload Resources</b></a></li>
        <li><a href="?part=manage_uber_managers"><b>Manage Uber/Umbrella Managers</b></a></li>

<?php
        if (current_user_can('sales_manager')) 
        {
?>
            <li><a href="?part=create_sales_report&start_date=<?= SUBSCRIPTION_START ?>&end_date=<?= SUBSCRIPTION_END ?>"><b>Create Sales Report</b></a></li>
            <li><a href="?part=manage_sales_rep"><b>Manage Sales Rep</b></a></li>
            <li><a href="?part=renewal_script"><b>Renewal Links</b></a></li>
            <li><a href="?part=list_module_resources"><b>Manage Module Resources</b></a></li>
            <li><a href="?part=list_course_resources"><b>Manage Course Resources</b></a></li>
            <li><a href="?part=view_users_org"><b>View Users Organization</b></a></li>
            <li><a href="?part=manage_quiz_eot"><b>Manage Quizzes</b></a></li>
<?php
        }
?>
    </ul>
<?php
    $years_include = array( 2017, 2018 ); // Years to include.

    foreach ($years_include as $year) 
    {
        echo '<button class="year_' . $year . '">Show ' . $year . ' sales</button><br><br>';
    }

    foreach ($years_include as $year) 
    {
        foreach ($libraries as $library) 
        {
            $library_name = $library->name; // The library name
            $revenue = 0; // The revenue for the this library
            $num_inactive = 0; // Nummber of inactive subscriptions.
            $subscriptions = getSubscriptions(0, $library->ID, 0, 1, 0, 0, $year);
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
            <table class="data" id="subscription<?=$year?>" style="display:none">
                <tbody>
                    <tr class="head">
                        <td colspan="2">
                            &nbsp;&nbsp;<b><?= $library_name ?></b>
                        </td>
                    </tr>
                    <tr class="head2">
                        <td class="label">
                            <?= $year ?>              
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <a href="?part=admin_view_subscriptions&library_id=<?= $library->ID ?>&sub_year=<?= $year ?>">
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
    ?>
    <script>
    $(document).ready(function(){
        /*
         * Toogle form base on year.
         */
        $(document).on('click', 'button[class^=year_]', function(){
            var year = $(this).text().substring(5,10);
            $('table').slideUp();
            $('table#subscription'+year).slideDown();                  
        }); 
    });
    </script>
    <?php
}
// Student
else if (current_user_can("is_student")) 
{
    $enrollments = getEnrollmentsByUserId($user_id); // All the enrollments of the user.
    if ( $enrollments && count($enrollments) > 0) 
    {
        $subscription_id = $enrollments[0]['subscription_id']; // the subscription ID for the first enrollment
        $subscription = getSubscriptions($subscription_id); // get all the data from subscription table
        if ($subscription)
        {
            // get the library object
            $library = getLibrary ($subscription->library_id);
            // make sure user accepted terms of use
            $accepted = accepted_terms($library); // Boolean if user has accepted terms
            if (!$accepted)
            {
               return; // do not continue to display the rest of the dashboard becuase user hasn't accepted the terms yet
            }

        }
    }

    //user just accepted terms and need to be presented with tutorial video
    if(isset($_REQUEST['tutorial']) && $_REQUEST['tutorial'] == 1)
    {
?>
        <h1 class="article_page_title"><?= __('Intro To Expert Online Training', 'EOT_LMS')?></h1>
        <div id='tutorial_video'>
            <video id="my-video" class="video-js vjs-default-skin" preload="auto" width="650" height="366" poster="https://www.expertonlinetraining.com/wp-content/uploads/2016/11/Chris-intro.png" data-setup='{"controls": true}'>
                <source src="https://eot-output.s3.amazonaws.com/tutorial_chris_course_intro.mp4" type='video/mp4'>
                <p class="vjs-no-js">
                    <?= __("To view this video please enable JavaScript, and consider upgrading to a web browser that", "EOT_LMS"); ?>
                    <a href="http://videojs.com/html5-video-support/" target="_blank"><?= __("supports HTML5 video", "EOT_LMS"); ?></a>
                </p>        
            </video>
        </div>
        <br><br><br><br>
        <a href="<?php bloginfo('url'); ?>/dashboard" class="statsbutton"><?= __("View Dashboard", "EOT_LMS"); ?></a>
<?php
    }
    else
    {
        $courses = getCourses(0, $org_id);

        if (!empty($image)) 
        {
?>
            <div class="dashboard_banner acf-image-image">
                <img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/>
                <br/>
            </div>
<?php
        }
        if ( $enrollments && count($enrollments) > 0) 
        { // Check if the user is enrolled to any course.
            // Display the enrollments information in the dashboard
            foreach ($enrollments as $enrollment) 
            {
                $subscription_id = isset($enrollment['subscription_id']) ? $enrollment['subscription_id'] : 0;
                $course_id = $enrollment['course_id']; // The course ID of the course this user is enrolled in
                $enrollment_id = isset($enrollment['ID']) ? $enrollment['ID'] : 0; // the enrollment ID
                // Get all the modules in this course
                $modules = getModulesInCourse($course_id);
                $course_name = ( array_key_exists($course_id, $courses) ) ? $courses[$course_id]->course_name : __("could not find the course name", "EOT_LMS"); // Check if the that course id is in $courses.
                $course = ( array_key_exists($course_id, $courses) ) ? $courses[$course_id] : "";
                if ($modules) 
                {
                    $status = formatStatus($enrollment['status']);
                    
                    $percentage_complete = ($status == 'Not Started') ? 0 : calc_course_completion($user_id, $course_id); // the percentage complete for this course
                    //d($status, $percentage_complete);
                    if ($status == "Failed") 
                    {
                        $status = __("In Progress", "EOT_LMS");
                    } 
                    if ($percentage_complete < 100) 
                    {
                        $status = __("In Progress", "EOT_LMS");
                    }
                    else
                    {
                        $status = __("Completed", "EOT_LMS");
                    }
?>
                    <div class="dashboard_border student">
                        <h1><?= $course_name ?>
                        </h1>
                        <div class="content_right">
                            <div class="clear"></div>
                            <div class="menu">
                                <a href="?part=my_library&course_id=<?= $course_id?>&enrollment_id=<?= $enrollment_id ?>" class="my_library">
                                    <div class="thumbnail">
                                        <i class="fa fa-youtube-play" alt="Content"></i>
                                    </div>
                                    <div class="para">
                                        <h1><?= __("Start Course", "EOT_LMS"); ?></h1>
                                        <br/>
                                        <?= __("Watch the videos, take quizzes, see resources", "EOT_LMS"); ?>
                                    </div>
                                </a>
                            </div> 
                        </div>
                        <div class="content_left student">
                            <table class="tb_border">
                                <tbody>
                                    <tr>
                                        <td class="s1 darklabel">
                                            <?= __("Modules", "EOT_LMS"); ?>
                                        </td>
                                        <td class="s2">
                                            <?= count($modules) ?>            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="s1 darklabel">
                                            <?= __("Status", "EOT_LMS"); ?>
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
                            <a href="?part=staff_lounge&subscription_id=<?= $subscription_id ?>" onclick="load('load_staff_lounge')">
                                <div class="title" style="padding-top: 5px;">
                                    <b><?= __("Virtual Staff Lounge", "EOT_LMS"); ?></b>
                                    <br><?= __("Manage your Forum", "EOT_LMS"); ?>
                                </div>
                            </a>
                        </div>
                        <div>
                            <b><?= __("Technical Support", "EOT_LMS"); ?></b>
                            <br>
                            <?= __("Toll-free", "EOT_LMS"); ?> 877-390-2267
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
                    echo '<b>' . $course_name . '</b>: ' . __("There are no modules in this course. Please contact your camp director.", "EOT_LMS");
                } 
            }
        } 
        else 
        { 
            // Display message if the user has no enrollments.
            echo "<p>" . __("You do not have any enrollments", "EOT_LMS") . ".</p>";
        }
    }
} 
else 
{
    if (!empty($image)) 
    {
?>
        <div class="dashboard_banner acf-image-image">
            <img src="<?php echo $image['sizes']['medium_large']; ?>" alt="<?php echo $image['alt']; ?>"/>
            <br/>
        </div>
<?php
    }
    wp_die(__("Please contact the administrator. I could not find your role.", "EOT_LMS"));
}
?>