<?php
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
        $admin_ajax_url = admin_url('admin-ajax.php');
        // Variable declaration
        $users = get_users( ); // Users in wordpress

        /*
         * Create table heading for users
         */
        $userTableObj = new stdClass(); 
        $userTableObj->rows = array();
        $userTableObj->headers = array(
        'Name' => 'left',
        'E-mail'=> 'left',
        'Type' => 'center',
        'Actions'=> 'center'
        );

        // create the director table
        $directorTableObj = new stdClass(); 
        $directorTableObj->rows = array();
        $directorTableObj->headers = array(
        'Name' => 'left',
        'E-mail'=> 'left',
        'Type' => 'center',
        'Actions'=> 'center'
        );

        /* 
         * This add the users who are a student or a camp director.
         * Count the number of enrollments
         */
        foreach($users as $user)
        {
            if(user_can($user, 'is_uber_manager') || user_can($user, 'is_umbrella_manager')) // Check the user capability to filter only uber/umbrella managers
            {
            	$user_id = $user->ID; // Wordpress user ID
            	$name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
            	$email = $user->user_email; // User's Wordpress Email
            	$type = (user_can($user, 'is_uber_manager')) ? "Uber Manager" : "Umbrella Manager"; 
  
              $org_id = get_user_meta( $user_id, 'org_id', true); // Get the user's org ID
              
              // Create a table row.
          		$userTableObj->rows[] = array($name, $email, $type, '<a href="' . $admin_ajax_url . '?action=getCourseForm&form_name=create_uber_camp_director&org_id=' . $org_id. '&type='.$type.'" rel="facebox"><i class="fa fa-user-plus add_camp_director" aria-hidden="true" '. hover_text_attr('Adding camp director',true) .' user-id="'. $user_id .'"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $admin_ajax_url . '?action=getCourseForm&form_name=view_uber_stats&org_id=' . $org_id. '&type='.$type.'&user_id='.$user_id.'" rel="facebox"><i class="fa fa-group" aria-hidden="true" '. hover_text_attr('View Stats',true) .' user-id="'. $user_id .'"></i></a>');
            }
            else if(user_can($user, 'is_director')) 
            {
              $user_id = $user->ID; // Wordpress user ID
              $org_id = get_org_from_user($user_id);
              $name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
              $email = $user->user_email; // User's Wordpress Email
              $type = 'Director';

              // create a director's row
              $directorTableObj->rows[] = array($name, $email, $type, '<a href="' . $admin_ajax_url . '?action=getCourseForm&form_name=upgrade_uber_manager&user_id=' . $user_id. '&type='.$type.'&org_id='.$org_id.'" rel="facebox"><i class="fa fa-user-plus " aria-hidden="true" '. hover_text_attr('Upgrade to Uber Manager',true) .' user-id="'. $user_id .'"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $admin_ajax_url . '?action=getCourseForm&form_name=upgrade_umbrella_manager&user_id=' . $user_id. '&type='.$type.'&org_id='.$org_id.'" rel="facebox"><i class="fa fa-umbrella " aria-hidden="true" ' . hover_text_attr('Upgrade to Umbrella Manager',true) . ' user-id="'. $user_id .'"></i></a>');
            }
        }
    ?>
    	<div class="breadcrumb">
    		<?= CRUMB_DASHBOARD ?>    
    		<?= CRUMB_SEPARATOR ?>     
        	<span class="current">Manage Uber/Umbrella Managers</span>     
    	</div>
    	<h1 class="article_page_title">Manage Uber/Umbrella Managers</h1>
      <div class="msgboxcontainer" style="display:none">  
        <div class="msg-tl">
          <div class="msg-tr"> 
            <div class="msg-bl">
              <div class="msg-br">
                <div class="msgbox">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
        // Display the user's table
    	CreateDataTable($userTableObj);
    ?>
    <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>

    <div class="main-content" data-user="<?= $user_id ?>">

      <div class="round_msgbox" id="loading_upgrade">
        <img id="loading" src="<?= get_template_directory_uri() . '/images/loading.gif'?>" /><br />
        <span id="message"></span>
      </div>

      <h1 class="article_page_title">Create New Uber/Umbrella Manager</h1>
<?php
      // Display the user's table
      CreateDataTable($directorTableObj);
?>
    </div>
        <script>
          $ = jQuery;
          $(document).ready(function() {
            $('a[rel*=facebox]').facebox();
            
            // upgrade the user to an uber manager
            $(".upgrade_uber_manager").bind('click',function(e) {
              $("#loading_upgrade").show();
              $("img#loading").show();
              $("span#message").text("");

              var user_id = $(this).attr('user-id');
              var data = "action=upgradeUberUmbrellaManager&user_id=" + user_id + "&type=uber";
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
                      $("img#loading").hide();
                      $("span#message").text(data['message']);
                    } 
                    else
                    {
                      alert("SUCCESS!");
                    }
                }
              });

          });


            $(".upgrade_umbrella_manager").click(function(e) {

              alert("umbrella user ID: " + $(this).attr('user-id'));

              $("#loading_upgrade").show();
            });

            /******************************************************************************************
              * Handles adding camp director on success
              *******************************************************************************************/        
            $(document).bind('success.create_uber_camp_director',
              function(event,data)
              {
                console.log(data);
                //close facebox and restart the page
                $('div.msgboxcontainer').show();
                $('div.msgbox').text('You have succesfully created the account. This page will restart in couple seconds...');
                $(document).trigger('close.facebox');
                location.reload();
              }
            ); 
          });
          /******************************************************************************************
              * Handles adding upgrade to uber manager on success
              *******************************************************************************************/        
            $(document).bind('success.upgrade_uber_manager',
              function(event,data)
              {
                console.log(data);
                //close facebox and restart the page
                $('div.msgboxcontainer').show();
                $('div.msgbox').text('You have succesfully created the account. This page will restart in couple seconds...');
                $(document).trigger('close.facebox');
                location.reload();
              }
            ); 
          });

/*

            //show loader gif and hide the content
            $('.main-content, .loader').toggle();

            //ajax call to update the fields
            $.ajax({
              type: "POST",
              url: url,
              dataType: 'json',
              data: $("#create_sales_rep").serialize(),
              success: function(data)
              {
                  if (!data['status'])
                  {
                    alert(data['message']);
                  }
                  else
                  {
                    alert("SUCCESS!");
                  }
                  location.reload();
              }
            });
            
            e.preventDefault(); // avoid to execute the actual submit of the form.
*/
        </script>
    <?php
    }
    else
    {
        echo "You do not have the privilege to view this page.";
    }
?>