<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>     
  <?= CRUMB_ADMINISTRATOR ?>    
  <?= CRUMB_SEPARATOR ?>
  <?= CRUMB_COURSES ?>    
  <?= CRUMB_SEPARATOR ?>        
    <span class="current"><?= __("Copy Previous Courses", "EOT_LMS"); ?></span>     
</div>
<?php
    $true_subscription = verifyUserAccess();
    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    global $current_user;
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID
    // Check if the subscription ID is valid.
    if (isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] != "") 
    {
        if (isset($true_subscription['status']) && $true_subscription['status']) 
        {
            if (current_user_can("is_director")) 
            {
                //copy courses begin
            $subscriptions =getSubscriptions(0, 0, TRUE, $org_id);
            $courses = getCoursesById($org_id, $subscription_id);//the courses in the current subscription
            $course_names = array_column($courses, 'course_name');// an array of current course names
                ?>
            <div class="bs">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                	<?php 
                	$count = 0;
                	$class = 'in';
                	foreach($subscriptions as $subscription){ ?>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingOne">
					      <h4 class="panel-title">
					        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $count?>" aria-expanded="true" aria-controls="collapse<?= $count?>">
					          <?= $subscription->library?>: From  <?= $subscription->start_date?> to  <?= $subscription->end_date?>
					        </a>
					      </h4>
					    </div>
					    <div id="collapse<?= $count?>" class="panel-collapse collapse <?= $class ?>" role="tabpanel" aria-labelledby="heading<?= $count?>">
					      <div class="panel-body">
					        <?php

					        $sub_courses = getCoursesById($org_id, $subscription->ID);
					       // d($sub_courses);

					        ?>
                  <div  id="staff_listing" display="staff_list" group_id="null" class="holder osX">
                                <div  id="staff_listing_pane" class="scroll-pane" style="width: 100%">  
                                    <div style="width:100%;">
                                        <div class="errorbox" style="display:none;"></div>
                                        <?php 
                                            foreach($sub_courses as $course)
                                            {
                                              $name = $course['course_name']; // the course name.
                                              $nonce = wp_create_nonce ('add-deleteCourse_' . $org_id);
                                              if(in_array($name, $course_names))
                                              {      
                                        ?>
                                                <div class="staff_and_assignment_list_row" style="background-color:#D7F3CA; width:100%" >  
                                                    <span class="staff_name" style="font-size:12px;"><?= $name ?></span> 
                                                    <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                        <a selected=1 class="add_remove_btn" collection="add_remove_from_group"  status="remove" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" nonce="<?= $nonce ?>" user_id="<?= $user_id ?>" >
                                                            <?=__('Remove', 'EOT_LMS')?>
                                                        </a>
                                                    </div>
                                                    <div style="clear:both;">
                                                    </div> 
                                                </div> 

                                            <?php
                                                }
                                                else 
                                                {
                                            ?>
                                                <div class="staff_and_assignment_list_row" style="width:100%;padding:7px 155px 7px 5px;" >  
                                                    <span class="staff_name" style="font-size:12px;"><?= $name ?></span>
                                                    <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                        <a selected=1 class="add_remove_btn" collection="add_remove_from_group"  status="add" org_id="<?= $org_id ?>" subscription_id="<?= $subscription_id ?>" nonce="<?= $nonce ?>" user_id="<?= $user_id ?>">
                                                             <?=__('Add', 'EOT_LMS')?>
                                                        </a>
                                                    </div>
                                                    <div style="clear:both;">
                                                    </div> 
                                                </div> 
                                        <?php
                                                }
                                            }                      
                                        ?>

                                    </div>
                                </div>
                            </div> 
<div style="clear:both;">


					      </div>
					    </div>
					  </div>
  			<?php 
  			$count ++;
  			$class = '';
  		}
  			?>
			</div>
		</div>

                <?php

                //d($subscriptions);
            } 
            else 
            {
                echo __("ERROR: This subscription does not match your user's access permissions. Please contact the administrator at info@expertonlinetraining.com for help with this issue.", "EOT_LMS");
            }
        } 
        else 
        {
            echo __("subscription ID does not belong to you", "EOT_LMS");
        }
    } 
    else 
    {
        echo __("Could not find the subscription ID", "EOT_LMS");
    }
?>