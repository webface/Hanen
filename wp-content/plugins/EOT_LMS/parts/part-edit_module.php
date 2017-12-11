<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>    
    <?= CRUMB_MODULES ?>
    <?= CRUMB_SEPARATOR ?>
    <span class="current"><?= __("Edit Module", "EOT_LMS"); ?></span>
</div>
<?php
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess();
    $path = WP_PLUGIN_DIR . '/eot_quiz/';
    require $path . 'public/class-eot_quiz_data.php';
    $eot_quiz = new EotQuizData();
    // Variable declaration
    global $current_user;
    global $wpdb;
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    $email = $current_user->user_email; // Wordpres e-mail address
    $org_id = get_org_from_user($user_id); // Organization ID
    $admin_ajax_url = admin_url('admin-ajax.php');

    // Check if the subscription ID is valid.
    if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") 
    {
        $subscription_id = filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT); // The subscription ID

        if (isset($true_subscription['status']) && $true_subscription['status']) 
        {
            if (current_user_can("is_director")) 
            {
                $module_id = filter_var($_REQUEST['module_id'], FILTER_SANITIZE_NUMBER_INT);
                // looking for resource order form submission
                if (isset($_POST['submit'])) 
                {
                    //parse and update the resource order
                    foreach ($_POST as $k => $v) 
                    {
                        if (strpos($k, 'order_') == 0) 
                        {
                            $parts = explode('order_', $k);
                            if(isset($parts[1]))
                            {
                                $id = filter_var($parts[1], FILTER_SANITIZE_NUMBER_INT);
                                $upd = $wpdb->update(TABLE_MODULE_RESOURCES, array('order' => filter_var($v, FILTER_SANITIZE_NUMBER_INT)), array('resource_id' => $id, 'module_id' => $module_id));
                            }
                        }
                    }
                }
                $module = getModule($module_id); // gets all the table info for this module id.
                $resources = getUserUploads($org_id, $user_id); // get all the user's uploaded resources
                $quizzes = $eot_quiz->getQuizzes($org_id, $user_id);
                $module_resources = getResourcesInModule($module_id); // gets all the current resources in the module
                //d($module_resources);
?>              
            <h1 class="article_page_title" id="moduleTitle"><?= $module['title'] ?></h1>
            <a class="btn btn-primary float-l" href="#" onclick="rename_module();"><?= __("Rename Module", "EOT_LMS"); ?></a>
            <h3><?= __("Your Modules Content", "EOT_LMS"); ?></h3>
            <div class="bs">
                <form method="POST" action="/dashboard?part=edit_module&subscription_id=<?= $subscription_id ?>&module_id=<?= $module_id ?>">
                    <table class="table table-hover table-bordered table-striped files">
                        <thead>
                            <tr>
                                <td><?= __("Order", "EOT_LMS"); ?></td>
                                <td><?= __("Title", "EOT_LMS"); ?></td>
                                <td><?= __("Type", "EOT_LMS"); ?></td>
                                <td><?= __("Actions", "EOT_LMS"); ?></td>
                            </tr>
                        <tbody>
                            <?php
                            foreach ($module_resources as $module_resource) 
                            {
                                echo "<tr>";
                                echo"<td><input name='order_" . $module_resource['ID'] . "' class='form_control form-group-sm' type='text' value='" . $module_resource['order'] . "'/></td>";
                                echo"<td>" . $module_resource['name'] . "</td>";
                                echo"<td>" . $module_resource['type'] . "</td>";
                                echo'<td><a href="' . $admin_ajax_url . '?action=get_module_form&form_name=delete_resource&resource_id=' . $module_resource['ID'] . '&module_id=' . $module_id . '&org_id=' . $org_id . '" class="delete" rel="facebox"><span class="fa fa-minus-circle"></span></a></td>';
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        </thead>
                    </table>
                    <input type="submit" class="btn btn-primary" name="submit" value="<?= __("Update Order", "EOT_LMS"); ?>"/>
                </form>
                <div class="bs">
                    <h3><?= __("Add Resources To This Module", "EOT_LMS"); ?></h3>
                    <h4><?= __("Select the type of resource to add to this module", "EOT_LMS"); ?>:</h4>


                    <label class="radio-inline">
                        <input type="radio" name="radioGroup" id="quizr" value="quiz"> <?= __("Quiz", "EOT_LMS"); ?>
                    </label><br>
                    <label class="radio-inline">
                        <input type="radio" name="radioGroup" id="resourcer" value="resource"> <?= __("Resource", "EOT_LMS"); ?>
                    </label>

                    <br>&nbsp;<br>
                    <select class="form-control" name="" id="quiz" style="display:none">
                        <option value=""><?= __("Select Quiz", "EOT_LMS"); ?></option>
                        <?php
                        foreach ($quizzes as $quiz) {
                            echo "<option value=" . $quiz['ID'] . ">" . $quiz['name'] . "</option>";
                        }
                        ?>
                    </select>
                    <select class="form-control" name="" id="resource" style="display:none">
                        <option value=""><?= __("Select Resource", "EOT_LMS"); ?></option>
                        <?php
                        foreach ($resources as $resource) {
                            echo "<option value=" . $resource['ID'] . ">" . $resource['name'] . " ---> " . $resource['type'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="btns" style="display:none">
                        <label for="order">Order
                            <input id="order" name="order" value="50" class="form-control form-group-sm" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/><br><?= __("(50 is the default, if you want a resource to appear first, set it to a value less than 50)", "EOT_LMS"); ?>
                        </label>
                        <a href="#" class="btn btn-primary addBtn">Add</a>
                    </div>
                </div>
                <script>
                    $(document).ready(function () {
                        $('a[rel*=facebox]').facebox();
                        $('input[type=radio][name=radioGroup]').change(function () {
                            if (this.value == 'quiz') 
                            {
                                $('#quiz').show();
                                $('#quiz').attr('name', 'add')
                                $('#resource').hide();
                                $('#resource').attr('name', '')
                                $('.btns').show();
                            }
                            else if (this.value == 'resource') 
                            {
                                $('#quiz').hide();
                                $('#quiz').attr('name', '')
                                $('#resource').show();
                                $('#resource').attr('name', 'add')
                                $('.btns').show();
                            }

                        });
                        $('.addBtn').click(function (e) {
                            e.preventDefault();
                            //alert($('input[type=radio][name=radioGroup]:checked').val());
                            if ($("select[name='add']").val() === "") 
                            {
                                alert('<?= __("Please select from the drop down", "EOT_LMS"); ?>');
                                return false;
                            }
                            if ($.trim($("#order").val()) === "") 
                            {
                                alert('<?= __("Please specify the order", "EOT_LMS"); ?>');
                                return false;
                            }
                            var type = $('input[type=radio][name=radioGroup]:checked').val();
                            var selection = $("select[name='add']").val()
                            //alert($("select[name='add']").val());
                            var data = {
                                'action': 'add_resource_to_module',
                                'user_id': <?= $user_id; ?>,
                                'org_id': <?= $org_id; ?>,
                                'id': selection,
                                'type': type,
                                'module_id':<?= $module_id ?>,
                                'order': $("#order").val()
                            };
                            console.log(data);
                            //return;
                            $.ajax({
                                url: '<?= $admin_ajax_url ?>',
                                type: 'POST',
                                dataType: 'json',
                                data: data,
                                success: function (data) {
                                    console.log(data);
                                    if (data.message == "success") 
                                    {
                                        load('load_processing');
                                        location.reload();
                                    } 
                                    else 
                                    {
                                        alert("<?= __("There was an error saving your data", "EOT_LMS"); ?>: " + data.message);
                                    }
                                },
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                    alert("<?= __("There was an error saving your data", "EOT_LMS"); ?>");
                                }
                            });
                        });
                        $(document).bind('success.delete_resource',
                                function (event, data)
                                {
                                    if (data.success === 'true') 
                                    {
                                        window.location.href="/dashboard?part=edit_module&module_id=<?= $module_id?>&subscription_id=<?= $subscription_id?>&user_id=<?= $user_id?>";
                                    }else{
                                        alert(data.errors);
                                    }
                                }
                        )
                        $(document).bind('success.update_module_title',
                                function (event, data)
                                {
                                    if (data.success === true) {
                                        location.reload();
                                    }else{
                                        alert(data.errors);
                                    }
                                }
                        )

                    });
                    function rename_module() {
                        //alert($("#moduleTitle").html())
                        $.facebox(function () {

                            $.ajax({
                                data: {'title': $('#moduleTitle').html(), 'module_id':<?= $module_id; ?>, 'org_id':<?= $org_id; ?>},
                                error: function () {
                                    $.facebox('<?= __("There was an error loading the title. Please try again shortly.", "EOT_LMS"); ?>');
                                },
                                success: function (data) {
                                    $.facebox(data);
                                },
                                type: 'post',
                                url: '<?= $admin_ajax_url; ?>?action=get_module_form&form_name=update_title'
                            });

                        });
                    }
                </script>
                <?php
            } 
            else 
            {
                echo __("Unauthorized!", "EOT_LMS");
            }
        } 
        else 
        {
            echo __("subscription ID does not belong to you", "EOT_LMS");
        }
    }
    // Could not find the subscription ID
    else 
    {
        echo __("Could not find the subscription ID", "EOT_LMS");
    }
    ?>