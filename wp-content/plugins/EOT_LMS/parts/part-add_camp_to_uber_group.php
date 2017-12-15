<?php
if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
{
	$admin_ajax_url = admin_url('admin-ajax.php');
	$camp_user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT);
	$camp_org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT);
	$camp_user = get_user_by('id', $camp_user_id);
	$camp_name = get_the_title($camp_org_id);
    // Variable declaration
    $args = array(
    	'role__in'=> array('uber_manager', 'umbrella_manager')
    );
    $users = get_users( $args); // Users in wordpress

    /*
     * Create table heading for users
     */
    $userTableObj = new stdClass(); 
    $userTableObj->rows = array();
    $userTableObj->headers = array(
    'Name' => 'left',
    'E-mail'=> 'left',
    'Org'=>'center',
    'Type' => 'center',
    'Actions'=> 'center'
    );
    foreach($users as $user)
    {
        if(user_can($user, 'is_uber_manager') || user_can($user, 'is_umbrella_manager')) // Check the user capability to filter only uber/umbrella managers
        {
        	$user_id = $user->ID; // Wordpress user ID
        	$name = get_user_meta ($user_id, 'first_name', true) . " " . get_user_meta ($user_id, 'last_name', true); // User's First and Last name in wordpress
        	$email = $user->user_email; // User's Wordpress Email
        	$type = (user_can($user, 'is_uber_manager')) ? "Uber Manager" : "Umbrella Manager"; 

            $org_id = get_user_meta( $user_id, 'org_id', true); // Get the user's org ID
            $org = get_the_title($org_id);
      		$action = '<a href="' . $admin_ajax_url . '?action=getCourseForm&form_name=add_director_to_uber_umbrella&org_id=' . $org_id. '&type='.$type.'&uber_id='.$user_id.'&camp_user_id='.$camp_user_id.'&camp_org_id='.$camp_org_id.'" rel="facebox"><i class="fa fa-plus" aria-hidden="true" '. hover_text_attr('View Stats',true) .' user-id="'. $user_id .'"></i></a>';
          // Create a table row.
      		$userTableObj->rows[] = array($name, $email, $org, $type, $action);
        }
    }

?>
    	<div class="breadcrumb">
    		<?= CRUMB_DASHBOARD ?>    
    		<?= CRUMB_SEPARATOR ?>     
        	<span class="current">Manage Uber/Umbrella Managers</span>     
    	</div>
    	<h1 class="article_page_title"><?= $camp_user->first_name." ".$camp_user->last_name ?></h1>
		<h3>Add <?= $camp_name?> to Uber/Umbrella</h3>
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
	
    CreateDataTable($userTableObj);

    ?>
		<script>
	      $ = jQuery;
          $(document).ready(function() {
            $('a[rel*=facebox]').facebox();

                        /******************************************************************************************
              * Handles  success
              *******************************************************************************************/        
            $(document).bind('success.add_director_to_uber_umbrella',
              function(event,data)
              {
                console.log(data);
                //close facebox and restart the page
                $('div.msgboxcontainer').show();
                $('div.msgbox').text('You have succesfully created the account. This page will restart in couple seconds...');
                $(document).trigger('close.facebox');
                window.location.href = "?part=manage_uber_managers";
              }
            ); 

          });
		</script>
    <?php
}
else
{
    echo "You do not have the privilege to view this page.";
}
?>