<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>  
    <span class="current">Administration</span>     
</div>
<?php
  // verify this user has access to this portal/subscription/page/view
  $true_subscription = verifyUserAccess(); 

  // Variable declaration
  global $current_user;
  $page_title = 'Administration';

  // Check if the subscription ID is valid.
  if(isset($_REQUEST['subscription_id']) && $_REQUEST['subscription_id'] > 0)
  { 

    $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
    $user_id = $current_user->ID; // Wordpress user ID
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
        <p>Choose an option below to harness the power of online learning for all your staff.</p>

        <div class="row">
          <div class="col">
            <a href="?part=manage_courses&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>" onclick="load('load_courses')">
              <i class="fa fa-graduation-cap fa-3x" aria-hidden="true"></i>
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_courses&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>" onclick="load('load_courses')">Manage Courses</a>
            <br>
            Select, create, and publish courses, then assign staff to a course.
          </div>
        </div>
        <div class="row">
          <div class="col">
            <a href="?part=manage_staff_accounts&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>" onclick="load('load_manage_staff_accounts')">
                <i class="fa fa-users fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
              </a>
            </div>
            <div class="col">
              <a href="?part=manage_staff_accounts&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>" onclick="load('load_manage_staff_accounts')">Manage Staff Accounts</a>
              <br>
              Enter or upload staff names, email addresses, and passwords.
            </div>
          </div>
          <div class="row">
            <div class="col">
              <a href="?part=upload_file&subscription_id=<?=  $subscription_id ?>" target="_blank">
              <i class="fa fa-file-text fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=upload_file&subscription_id=<?=  $subscription_id ?>" onclick="load('load_manage_custom_content')">Manage Your Custom Content</a>
            <br>
            Upload your home-made videos, documents, links, and quizzes.
          </div>
        </div>

        <div class="row">
          <div class="col">
            <a href="?part=manage_quiz&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_quiz')">
              <i class="fa fa-question-circle fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=manage_quiz&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_quiz')">Manage Your Custom Quizzes</a>
            <br>
            Create custom quizzes for your staff.
          </div>
        </div>

        <div class="row">
          <div class="col">
            <a href="?part=email_staff&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_email')">
              <i class="fa fa-envelope fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=email_staff&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_email')">Send Staff Mass Mail</a>
            <br>
            Select staff by several options and send them customized emails.
          </div>
        </div>

        <div class="row">
          <div class="col">
            <a href="?part=staff_lounge&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_staff_lounge')">
              <i class="fa fa-comments fa-3x" aria-hidden="true"></i>&nbsp;&nbsp;
            </a>
          </div>
          <div class="col">
            <a href="?part=staff_lounge&amp;subscription_id=<?= $subscription_id ?>" onclick="load('load_staff_lounge')">Virtual Staff Lounge</a>
            <br>
            Manage your forum.
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
            <a href="?part=manage_logo&amp;org_id=<?= $org_id ?>&amp;subscription_id=<?=  $subscription_id ?>">Customize Dashboard Logo</a>
            <br>
            Personalize what your staff see by uploading your own logo.
          </div>
        </div>
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
  else
  {
    echo "Invalid subscription ID.";
  }
?>