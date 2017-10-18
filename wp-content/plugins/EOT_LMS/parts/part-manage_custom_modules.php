<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>    
    <span class="current">Manage Your Custom Modules</span>     
</div>
<?php
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess();

    // Variable declaration
    global $current_user;
    global $wpdb;
    $user_id = $current_user->ID; // Wordpress user ID
    $email = $current_user->user_email; // Wordpres e-mail address
    $org_id = get_org_from_user($user_id); // Organization ID
    $admin_ajax_url = admin_url('admin-ajax.php');

    if (isset($_REQUEST['submit'])) // user is trying to add a new module
    {
        if (!isset($_REQUEST['submit_module']) || !wp_verify_nonce($_REQUEST['submit_module'], 'submit_module')) 
        {
            echo 'Sorry, your nonce did not verify.';
            wp_die();
        }
        // the module data
        $data = array(
            'title' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['moduleName'])),
            'description_text' => preg_replace("/[^a-zA-Z0-9'?_\. !&-]+/","",sanitize_text_field($_REQUEST['moduleDescription'])),
            'created_at' => current_time('Y-m-d'),
            'org_id' => filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT),
            'user_id' => filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT)
        );
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT);
        $module_added = $wpdb->insert(TABLE_MODULES, $data);
        $module_id = $wpdb->insert_id;
        $url ='/dashboard?part=edit_module&module_id=' . $module_id . '&subscription_id=' . $subscription_id; 
        ob_start();
        header('Location: '.$url);
        ob_end_flush();
        die();
    }

    // Check if the subscription ID is valid.
    if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") 
    {
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID

        if (isset($true_subscription['status']) && $true_subscription['status']) 
        {
            if (current_user_can("is_director")) 
            {
?>
                <h1 class="article_page_title">Manage Your Custom Modules</h1>
                <h3>Your Modules</h3>
<?php
                $modules = getModules($org_id); // get all the custom modules belonging to this org
                if ($modules)// then display datatable 
                {
                    // Tables that will be displayed in the front end.
                    $moduleTableObj = new stdClass();
                    $moduleTableObj->rows = array();
                    $moduleTableObj->headers = array(
                        'Title' => 'left',
                        'Num Resources' => 'center',
                        'Actions' => 'center'
                    );
                    foreach ($modules as $module) 
                    {
                        $num_resources = $wpdb->get_var("SELECT COUNT(*) FROM " . TABLE_MODULE_RESOURCES . " WHERE module_id = " . $module['ID']);
                        $moduleTableObj->rows[] = array(
                            '<span>' . stripslashes($module['title']) . '</span>',
                            $num_resources,
                            '<a href="/dashboard?part=edit_module&module_id=' . $module['ID'] . '&subscription_id=' . $subscription_id . '" onclick="load(\'load_edit_module\')"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp; 
                            <a href="' . $admin_ajax_url . '?action=get_module_form&form_name=delete_module&module_id=' . $module['ID'] . '&org_id=' . $org_id . '" class="delete" rel="facebox"><i class="fa fa-trash" aria-hidden="true"></i></a>'
                        );
                    }
                    CreateDataTable($moduleTableObj); // Print the table in the page
                } 
                else 
                {
                    echo "<div class='bs'><div class='well well-lg'>No Custom Modules yet!</div></div>";
                }
?>
                <span><em>Create a new module below</em></span><br>
                <div class="bs">
                    <div class="well" style="padding:10px">
                        <form action="/dashboard?part=manage_custom_modules&subscription_id=<?= $subscription_id ?>" method="POST">
                            <?php wp_nonce_field('submit_module', 'submit_module'); ?>
                            <div class="form-group">
                                <label for="moduleName">Module Name*</label>
                                <input type="text" class="form-control" id="moduleName" name="moduleName" placeholder="Module Name">
                            </div>
                            <div class="bs form-group">
                                <label for="moduleDescription">Module Description</label>
                                <textarea class="form-control" rows="3" id="moduleDescription" name="moduleDescription"></textarea>
                            </div>

                            <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>">
                            <input type="hidden" name="org_id" value="<?= $org_id; ?>">
                            <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                            <input type="submit" class="btn btn-primary pull-right" name="submit" value="Submit">
                            <span class="clearfix"></span>
                        </form>
                    </div>
                </div>
                <script>
                    $(document).ready(function () {
                        $('a[rel*=facebox]').facebox();
                        $('form').submit(function () {
                            var errors = false;
                            if ($.trim($('#moduleName').val()) == '') 
                            {
                                errors = true;
                            }
//                            if ($.trim($('#moduleDescription').val()) == '') 
//                            {
//                                errors = true;
//                            }
                            if (!errors) 
                            {
                                return true;
                            } 
                            else 
                            {
                                alert("Name and Description fields are mandatory");
                                return false;
                            }
                        })

                        $(document).bind('success.delete_module',
                                function (event, data)
                                {
                                    if (data.success === 'true') 
                                    {
                                        window.location.href="/dashboard?part=manage_custom_modules&subscription_id=<?=$subscription_id?>";
                                    }
                                    else
                                    {
                                        alert("There was an error deleting your module");
                                    }
                                }
                        )
                    });
                </script>
                <?php
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