<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>  
  <span class="current"><?= __("Administration", "EOT_LMS"); ?></span>     
</div>
<?php

  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess();
  // Variable declaration
  global $current_user;
  
  $page_title = __("Administration", "EOT_LMS");

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  { 

    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $user_id =  (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) ? filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT):$current_user->ID; // The // Wordpress user ID
    $org_id = (isset($_REQUEST['org_id']) && !empty($_REQUEST['org_id'])) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : get_org_from_user ($user_id); // Organization ID

    if(isset($true_subscription['status']) && $true_subscription['status'])
    {
      if(current_user_can( "is_director" ))
      {
        $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
        $data = compact ("org_id");

        if (current_user_can("is_uber_manager") || current_user_can("is_umbrella_manager"))
        {
          $page_title .= " - " . get_the_title($org_id);
        }
        echo '<h1 class="article_page_title">'.$page_title.'</h1>';       

?>
        <p><?= __("Choose an option below to harness the power of online learning for all your staff.", "EOT_LMS"); ?></p>

        <div class="row">
          <div class="col">
            <a href="?part=manage_courses&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?= $subscription_id ?>&amp;user_id=<?=  $user_id ?>" onclick="load('load_courses')">
              <i class="fa fa-graduation-cap fa-3x" aria-hidden="true"></i>
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_courses&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?= $subscription_id ?>&amp;user_id=<?=  $user_id ?>" onclick="load('load_courses')"><?= __("Manage Courses", "EOT_LMS"); ?></a>
            <br>
            <?= __("Select, create, and publish courses, then assign staff to a course.", "EOT_LMS"); ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <a href="?part=manage_staff_accounts&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>&amp;user_id=<?=  $user_id ?>" onclick="load('load_manage_staff_accounts')">
                <i class="fa fa-users fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
              </a>
            </div>
            <div class="col">
              <a href="?part=manage_staff_accounts&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>&amp;user_id=<?=  $user_id ?>" onclick="load('load_manage_staff_accounts')"><?= __("Manage Staff Accounts", "EOT_LMS"); ?></a>
              <br>
              <?= __("Enter or upload staff names, email addresses, and passwords.", "EOT_LMS"); ?>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <a href="?part=upload_file&subscription_id=<?=  $subscription_id ?>&user_id=<?= $user_id ?>" target="_blank">
              <i class="fa fa-file-text fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=upload_file&subscription_id=<?=  $subscription_id ?>&user_id=<?= $user_id ?>" onclick="load('load_manage_custom_content')"><?= __("Manage Your Custom Content", "EOT_LMS"); ?></a>
            <br>
            <?= __("Upload your home-made videos, documents, links, and quizzes.", "EOT_LMS"); ?>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <a href="?part=manage_quiz&amp;subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>" onclick="load('load_quiz')">
              <i class="fa fa-question-circle fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_quiz&amp;subscription_id=<?= $subscription_id ?>&user_id=<?= $user_id ?>" onclick="load('load_quiz')"><?= __("Manage Your Custom Quizzes", "EOT_LMS"); ?></a>
            <br>
            <?= __("Create custom quizzes for your staff.", "EOT_LMS"); ?>
          </div>
        </div>
          <div class="row">
            <div class="col">
              <a href="?part=manage_custom_modules&subscription_id=<?=  $subscription_id ?>&user_id=<?=  $user_id ?>" target="_blank">
              <i class="fa fa-list-alt fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_custom_modules&subscription_id=<?=  $subscription_id ?>&user_id=<?=  $user_id ?>" onclick="load('load_manage_custom_content')"><?= __("Manage Your Custom Modules", "EOT_LMS"); ?></a>
            <br>
            <?= __("Create and manage your own modules with your videos and quizzes.", "EOT_LMS"); ?>
          </div>
        </div>
        <?php if($current_user->ID == $user_id)//show below buttons if its not an uber or umbrella managing this page
                {
            ?>
        <div class="row">
          <div class="col">
            <a href="?part=email_staff&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_email')">
              <i class="fa fa-envelope fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=email_staff&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_email')"><?= __("Send Staff Mass Mail", "EOT_LMS"); ?></a>
            <br>
            <?= __("Select staff by several options and send them customized emails.", "EOT_LMS"); ?>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <a href="?part=staff_lounge&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_staff_lounge')">
              <i class="fa fa-comments fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=staff_lounge&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_staff_lounge')"><?= __("Virtual Staff Lounge", "EOT_LMS"); ?></a>
            <br>
            <?= __("Manage your forum.", "EOT_LMS"); ?>
          </div>
        </div>
<!--
        <div class="row">
          <div class="col">
            <a href="#manage_forum">
              <img alt="" src="<?php echo bloginfo('template_directory'); ?>/images/anav-forum.gif">
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_forum&amp;org_id=<?= $org_id ?>">Manage Virtual Staff Lounge</a>
            <br>
            Manage the online discussion forum where your staff can chat
          </div>
        </div>
-->
        <div class="row">
          <div class="col">
            <a href="?part=manage_logo&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>">
              <i class="fa fa-picture-o fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_logo&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>"><?= __("Customize Dashboard Logo", "EOT_LMS"); ?></a>
            <br>
            <?= __("Personalize what your staff see by uploading your own logo.", "EOT_LMS"); ?>
          </div>
        </div>
      <?php
                } //close if is an uber or umbrella editing
      }
      else
      {
        echo __("Unauthorized", "EOT_LMS");
      }
    }
    else
    {
      echo __("subscription ID does not belong to you", "EOT_LMS");
    }
  }
  else
  {
    echo __("Invalid subscription ID.", "EOT_LMS");
  }
?>
