<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>         
    <?= CRUMB_SEPARATOR ?>
    <?= CRUMB_STATISTICS ?>          
    <?= CRUMB_SEPARATOR ?>  
    <span class="current">Course Stats</span>     
</div>
<?php
// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);

// verify this user has access to this portal/subscription/page/view
$true_subscription = verifyUserAccess();

// Check if the subscription ID is valid.
if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0) 
{
    if (isset($true_subscription['status']) && $true_subscription['status']) 
    {
        if (current_user_can("is_director"))
        {
            if(isset($_REQUEST['course_id']) && $_REQUEST['course_id'] > 0)
            {
              $user_id = isset($_REQUEST['user_id']) ? filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
              $fullname = get_user_meta($user_id, 'first_name', true)." ".get_user_meta($user_id, 'last_name', true);
              $track_login = getTrack($user_id, 0, 'login');
              //d($track_login);
              
?>
                <div class="smoothness">
                    <h1 class="article_page_title">Login Record for <?= $fullname ?></h1>
                    <p>
                        Times are shown in <b>Pacific Standard Time (PST)</b> <span class="small">(GMT - 8).</span><br />
                        It is currently <b><?=date('g:ia', time())?></b> on <b><?=date('F j, Y', time())?></b>.
                    </p>
                </div>                        
               
<?php                
                // Tables that will be displayed in the front end.
                $loginsTableObj = new stdClass();
                $loginsTableObj->rows = array();
                $loginsTableObj->headers = array(
                    '<div>No</div>' => 'left',
                    '<center><div>Date and Time</div></center>' => 'left'
                );
                $counter = 0;
                foreach ($track_login as $login) {
                    $counter++;
                    $loginsTableObj->rows[] = array(
                        $counter,
                        date('F j, Y g:i a', strtotime($login->date))
                        ); 
                }
                CreateDataTable($loginsTableObj,"100%",10,true,"Stats"); // Print the table in the page
            }
            else 
            {
                echo "You dont have a valid course ID";
            }
        } 
        else 
        {
            echo "Unauthorized!";
        }
    } 
    else 
    {
        echo "subscription ID does not belong to you";
    }
}
// Could not find the subscription ID
else
{
    echo "Could not find the subscription ID";
}
?>