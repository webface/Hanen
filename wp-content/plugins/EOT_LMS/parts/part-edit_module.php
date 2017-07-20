<div class="breadcrumb">
    <?= CRUMB_DASHBOARD ?>    
    <?= CRUMB_SEPARATOR ?>     
    <?= CRUMB_ADMINISTRATOR ?>    
    <?= CRUMB_SEPARATOR ?>    
    <?= CRUMB_MODULES ?>
    <?= CRUMB_SEPARATOR ?>
    <span class="current">Edit Module</span>
</div>
<?php
    // verify this user has access to this portal/subscription/page/view
    $true_subscription = verifyUserAccess();
    $path = WP_PLUGIN_DIR . '/eot_quiz/';
    //require $path . 'public/class-eot_quiz_data.php';
    //$eot_quiz = new EotQuizData();
    // Variable declaration
    global $current_user;
    global $wpdb;
    $user_id = $current_user->ID; // Wordpress user ID
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
                if (isset($_REQUEST['submit'])) 
                {
                    //parse and update the resource order
                    foreach ($_REQUEST as $k => $v) 
                    {
                        if (strpos($k, 'order_') == 0) 
                        {
                            $parts = explode('order_', $k);
                            $id = $parts[1];
                            $upd = $wpdb->update(TABLE_MODULE_RESOURCES, array('order' => $v), array('resource_id' => $id, 'module_id' => $module_id, 'org_id' => $org_id));
                        }
                    }
                }
                $module = getModule($module_id); // gets all the table info for this module id.
                $resources = getUserUploads($org_id, $user_id); // get all the user's uploaded resources
                //$quizzes = $eot_quiz->getQuizzes($org_id, $user_id);
                $quizzes= array();
                $module_resources = getResourcesInModule($module_id); // gets all the current resources in the module
?>
            <h1 class="article_page_title" id="moduleTitle"><?= $module['title'] ?></h1>
            <a class="btn btn-primary float-l" href="#" onclick="rename_module();">Rename Module</a>
            <h3>Your Modules Content</h3>
            <div class="bs">
                <form method="POST" action="/dashboard?part=edit_module&subscription_id=<?= $subscription_id ?>&module_id=<?= $module_id ?>">
                    <table class="table table-hover table-bordered table-striped files">
                        <thead>
                            <tr>
                                <td>Order</td>
                                <td>Title</td>
                                <td>Type</td>
                                <td>Actions</td>
                            </tr>
                        <tbody>
                            <?php
                            foreach ($module_resources as $module_resource) 
                            {
                                echo "<tr>";
                                echo"<td><input name='order_" . $module_resource['ID'] . "' class='form_control form-group-sm' type='text' value='" . $module_resource['order'] . "'/></td>";
                                echo"<td>" . $module_resource['name'] . "</td>";
                                echo"<td>" . $module_resource['type'] . "</td>";
                                echo'<td><a href="' . $admin_ajax_url . '?action=get_module_form&form_name=delete_resource&resource_id=' . $module_resource['resource_id'] . '&module_id=' . $module_resource['ID'] . '&org_id=' . $org_id . '" class="delete" rel="facebox"><span class="fa fa-minus-circle"></span></a></td>';
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                        </thead>
                    </table>
                    <input type="submit" class="btn btn-primary" name="submit" value="Update Order"/>
                </form>
                <div class="bs">
                    <h3>Add Resources To This Module</h3>
                    <h4>Select the type of resource to add to this module:</h4>


                    <label class="radio-inline">
                        <input type="radio" name="radioGroup" id="quizr" value="quiz"> Quiz
                    </label><br>
                    <label class="radio-inline">
                        <input type="radio" name="radioGroup" id="resourcer" value="resource"> Resource
                    </label>

                    <br>&nbsp;<br>
                    <select class="form-control" name="" id="quiz" style="display:none">
                        <option value="">Select Quiz</option>
                        <?php
                        foreach ($quizzes as $quiz) {
                            echo "<option value=" . $quiz['ID'] . ">" . $quiz['name'] . "</option>";
                        }
                        ?>
                    </select>
                    <select class="form-control" name="" id="resource" style="display:none">
                        <option value="">Select Resource</option>
                        <?php
                        foreach ($resources as $resource) {
                            echo "<option value=" . $resource['ID'] . ">" . $resource['name'] . " ---> " . $resource['type'] . "</option>";
                        }
                        ?>
                    </select>
                    <div class="btns" style="display:none">
                        <label for="order">Order
                            <input id="order" name="order" value="50" class="form-control form-group-sm" type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/><br>(50 is the default, if you want a resource to appear first, set it to a value less than 50)
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
                                alert('Please select from the drop down');
                                return false;
                            }
                            if ($.trim($("#order").val()) === "") 
                            {
                                alert('Please specify the order');
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
                                        alert("There was an error saving your data: " + data.message);
                                    }
                                },
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                    alert("There was an error saving your data");
                                }
                            });
                        });
                        $(document).bind('success.delete_resource',
                                function (event, data)
                                {
                                    if (data.success === 'true') 
                                    {
                                        window.location.href="/dashboard?part=edit_module&module_id=<?= $module_id?>&subscription_id=<?= $subscription_id?>";
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
                                    $.facebox('There was an error loading the title. Please try again shortly.');
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