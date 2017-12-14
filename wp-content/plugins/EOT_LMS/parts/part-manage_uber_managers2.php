<?php

  /* ABANDONED */

  if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
  {
    $admin_ajax_url = admin_url('admin-ajax.php');

    ?>
        <div class="breadcrumb">
          <?= CRUMB_DASHBOARD ?>    
          <?= CRUMB_SEPARATOR ?>     
          <span class="current">Manage Uber/Umbrella Managers</span>     
        </div>
    <?php

    // check which step were on
    if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'step2' && isset($_REQUEST['umbrella_group_id']))
    {
      // let the user select the umbrella manager
      // display the umbrella managers for this org
      $umbrella_group_id = filter_var($_REQUEST['umbrella_group_id'], FILTER_SANITIZE_NUMBER_INT);
      $camp_name = get_the_title($umbrella_group_id);

      $args = array(
        'role__in' => array(
          'umbrella_manager'
        ),
        'meta_key' => 'umbrella_group_id',
        'meta_value' => $umbrella_group_id
      );
      
      $umbrella_managers = new WP_User_Query($args);
      d($umbrella_managers);
      if (!empty($umbrella_managers->results))
      {

        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
        $userTableObj->rows = array();
        $userTableObj->headers = array(
        'Name' => 'left',
        'E-mail' => 'left',
        'Type' => 'center',
        'Actions'=> 'center'
        );

        echo '<h1 class="article_page_title">Select an Umbrella Manager from '.$camp_name.' to add camps to:</h1>';     

        foreach($umbrella_managers->results as $umbrella_manager)
        {
          $user_id = $umbrella_manager->ID; // Wordpress user ID
          $name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
          $email = $umbrella_manager->user_email; // User's Wordpress Email
          $org_id = get_user_meta( $user_id, 'org_id', true); // Get the user's org ID

          // Create a table row.
          $userTableObj->rows[] = array($umbrella_manager->display_name, $umbrella_manager->user_email, 'Umbrella Manager' ,'<a href="?part=manage_uber_managers2&step=step3&umbrella_group_id=' . $umbrella_group_id . '&org_id='.$org_id.'&umbrella_manager_id='.$user_id.'" rel="facebox"><i class="fa fa-user-plus" aria-hidden="true" '. hover_text_attr('Link Camps to this Umbrella Manager',true) .' user_id="'. $user_id .'"></i></a>');
        }

        // Display the user's table
        CreateDataTable($userTableObj);
      }
    }
    else if (isset($_REQUEST['step']) && $_REQUEST['step'] == 'step3' && isset($_REQUEST['umbrella_group_id']) && isset($_REQUEST['org_id']) && isset($_REQUEST['umbrella_manager_id']))
    {
      $umbrella_group_id = filter_var($_REQUEST['umbrella_group_id'], FILTER_SANITIZE_NUMBER_INT);
      $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
      $umbrella_manager_id = filter_var($_REQUEST['umbrella_manager_id'], FILTER_SANITIZE_NUMBER_INT);
      $regional_umbrella_group_id = get_user_meta($umbrella_manager_id, 'org_id', true); // get the org ID for the umbrella manager
      $camp_name = get_the_title($umbrella_group_id);
      $umbrella_manager_name = get_user_meta ($umbrella_manager_id, 'first_name', true) . " " . get_user_meta ($umbrella_manager_id, 'last_name', true); // Umbrella Manager's First and Last name in wordpress

      // display all the camps in this umbrella group that do not have a regional_umbrella_group_id.
      $args = array(
        'role' => 'manager', // this is the camp director role
        'role__not_in' => array(
          'uber_manager',
          'umbrella_manager'
        ),
        'meta_key' => 'umbrella_group_id',
        'meta_value' => $umbrella_group_id,
        'meta_query' => array(
          array(
            'key' => 'regional_umbrella_group_id',
            'value' => '',
            'compare' => 'NOT EXISTS'
          )
        )
      );
      $camp_directors = new WP_User_Query($args);
      if (!empty($camp_directors->results))
      {
        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
        $userTableObj->rows = array();
        $userTableObj->headers = array(
        'Name' => 'left',
        'E-mail' => 'left',
        'Type' => 'center',
        'Actions'=> 'center'
        );

        echo '<h1 class="article_page_title">Select the camps from '.$camp_name.' that you want '.$umbrella_manager_name.' to be in charge of:</h1>';       

        foreach($camp_directors->results as $camp_director)
        {
          $user_id = $camp_director->ID; // Wordpress user ID
          $name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
          $email = $camp_director->user_email; // User's Wordpress Email
          $camp_org_id = get_user_meta( $user_id, 'org_id', true); // Get the user's org ID

          // Create a table row.
          $userTableObj->rows[] = array($camp_director->display_name, $camp_director->user_email, 'Director' ,'<i class="fa fa-plus assign_umbrella_manager" aria-hidden="true" '. hover_text_attr('Add this camp to ' .$umbrella_manager_name .'s camp' ,true) .' user_id="'. $user_id .'" regional_umbrella_group_id="'. $regional_umbrella_group_id .'" org_id="'.$camp_org_id.'"></i><i class="fa fa-check assigned_umbrella_manager" aria-hidden="true"></i><i class="fa fa-times failed_assign_umbrella_manager" aria-hidden="true"></i><i class="fa fa-spinner fa-spin assigning_umbrella_manager" aria-hidden="true"></i>');
        }
?>
        <div class="round_msgbox" id="loading_upgrade">
          <span id="message"></span>
        </div>
<?php

        // Display the user's table
        CreateDataTable($userTableObj, "100%", 100);

?>
  <script>
    $ = jQuery;
    $(document).ready(function() {

      // hide the loading, check, and x icons in the actions tab
      $(".assigned_umbrella_manager").hide(); 
      $(".assigning_umbrella_manager").hide();
      $(".failed_assign_umbrella_manager").hide();

      $(".assign_umbrella_manager").click(function(e) { // when a user clicks the plus icon to assign this camp to this umbrella manager
        $("div.round_msgbox").hide(); // hide the message box if it was previously displayed
        $("span#message").text(""); // clear the error text
        $(this).hide(); // hide the plus sign
        var loading = $(this).siblings(".assigning_umbrella_manager"); // assign a variable to the loading icon
        loading.show(); // show the loading sign
        var x = $(this).siblings(".failed_assign_umbrella_manager"); // assign a variable to the x icon
        var check = $(this).siblings(".assigned_umbrella_manager"); // assign a variable to the check icon


        var user_id = $(this).attr('user_id');
        var org_id = $(this).attr('org_id');
        var regional_umbrella_group_id = $(this).attr('regional_umbrella_group_id');
        var data = "action=assignCampUmbrellaManager&user_id=" + user_id + "&regional_umbrella_group_id=" + regional_umbrella_group_id + "&org_id=" + org_id;

        //ajax call to update the user
        $.ajax({
          type: "POST",
          url: eot.ajax_url,
          dataType: 'json',
          data: data,
          success: function(data)
          {
              if (!data['status'])
              {
                // failed so show the error message
                $("div.round_msgbox").show();
                $("span#message").text(data['message']);
                // hide the loading sign, show the x icon
                loading.hide();
                x.show();
              } 
              else
              {
                // success so hide the loading and show the check
                loading.hide();
                check.show();
              }
          }
        });

      });

    });
  </script>
<?php
      }      
    }
    else
    {
      // display the uber managers
      $args = array(
        'role__in' => array(
          'uber_manager'
        ),
      );
      $uber_admins = new WP_User_Query($args);
      if (!empty($uber_admins->results))
      {
        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
        $userTableObj->rows = array();
        $userTableObj->headers = array(
        'Name' => 'left',
        'E-mail' => 'left',
        'Type' => 'center',
        'Actions'=> 'center'
        );

        echo '<h1 class="article_page_title">Manage Uber Admins</h1>';       

        foreach($uber_admins->results as $uber_admin)
        {
          $user_id = $uber_admin->ID; // Wordpress user ID
          $name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
          $email = $uber_admin->user_email; // User's Wordpress Email
          $umbrella_group_id = get_user_meta( $user_id, 'org_id', true); // Get the user's org ID

          // Create a table row.
          $userTableObj->rows[] = array($uber_admin->display_name, $uber_admin->user_email, 'Uber Admin' ,'<a href="?part=manage_uber_managers2&step=step2&umbrella_group_id=' . $umbrella_group_id. '" rel="facebox"><i class="fa fa-umbrella" aria-hidden="true" '. hover_text_attr('Assign Umbrella Managers',true) .' user_id="'. $user_id .'"></i></a>');
        }
        // Display the user's table
        CreateDataTable($userTableObj);
      }
    }
  }
  else
  {
      echo "You do not have the privilege to view this page.";
  }
?>
