<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current"><?= __("My Certificates", "EOT_LMS"); ?></span>     
</div>
<h1 class="article_page_title"><?= __("My Certificates", "EOT_LMS"); ?></h1>
<?php
  if( current_user_can ('is_student') || current_user_can ('is_director')  )
  {
  	// Variable declaration
  	global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user ($user_id); // Organization ID
    $courses = getCourses(0, $org_id); // All the courses base on the organization ID.
    $completed_enrollments = array(); // will store the completed enrollments
    $modules_in_portal = array(); // all of the modules in the portal. Will be used in calc_course_completion so to speed it up we only use it once.
    if ( $org_id )
    {
      $modules_in_portal = getModules($org_id); // populate the modules in portal
    }

    // If student.
    if( current_user_can ('is_student') )
    {
      $enrollments = getEnrollments(0, $user_id, 0); // All enrollments base on the user ID.
      $user_certificates = getCertificates($user_id); // All certificates for this user.
      $user_certificates_syllabus = getCertificatesSyllabus($user_id); // All syllabus for this user.
    }
    else if( current_user_can ('is_director') )
    {
      // make sure we aren't looking for all enrollments all time.
      $start_date = (isset( $_REQUEST['start_date']) ) ? filter_var($_REQUEST['start_date'],FILTER_SANITIZE_STRING) : SUBSCRIPTION_START;
      $end_date = (isset( $_REQUEST['end_date']) ) ? filter_var($_REQUEST['end_date'],FILTER_SANITIZE_STRING) : SUBSCRIPTION_END;
      $enrollments = getEnrollments(0, 0, $org_id, "", 0, $start_date, $end_date); // All completed enrollments base on the org ID.
      $student_user_ids = array_unique( array_column( $enrollments, "user_id") ) ; // Unique IDs of the users who have completed enrollments in this org
      $user_certificates = getCertificatesByUserIds($student_user_ids, "image", $start_date, $end_date);
      $user_certificates_syllabus = getCertificatesByUserIds($student_user_ids, "syllabus"); // Certificate Syllabus for all students.
    }
//d($enrollments, $user_certificates, $user_certificates_syllabus);

    // Make an key associative array. User ID is the key, and the value is the array of courses they have completed.
    if(!empty($user_certificates_syllabus)) 
    {
      $user_course_completed = array();
      foreach ($user_certificates_syllabus as $student_syllabus) 
      {
        $student_user_id = $student_syllabus['user_id']; // The learner user ID
        $student_course_id = $student_syllabus['course_id']; // The learner course ID
        if( !isset($user_course_completed[$student_user_id]) )
        {
          $user_course_completed[$student_user_id] = array($student_course_id);
        }
        else
        {
          array_push($user_course_completed[$student_user_id], $student_course_id);
        }
      }
    }
//d($user_course_completed);

    // go through all enrolled courses for this user and calculate which ones they completed.
    // don't need to calculate if they already have a certificate
    $count = 0;
    foreach ( $enrollments as $enrollment )
    {
      $student_user_id = $enrollment['user_id'];
      $student_course_id = $enrollment['course_id'];
      $student_org_id = $enrollment['org_id'];
      if ( isset( $user_course_completed[$student_user_id] ) && in_array( $student_course_id, $user_course_completed[$student_user_id] ) )
      {
        // this student does have some completed courses. check if this course is part of the completed ones.
//        // remove this row from the enrollments table so we dont have to calculate whether or not its completed in the future.
//        unset( $enrollments[$count] );

        // add it to the completed_enrollments array to show download link.
        array_push($completed_enrollments, $enrollment);
      }
      else
      {
        // before we calculate we must make sure we have the right modules in the portal
        if ( $student_org_id != $org_id )
        {
          // get modules in this portal
          $modules_in_portal = getModules($org_id);
          $org_id = $student_org_id;
        }
        // this student does not have a certificate entry for this course yet. Check if they are complete.
        $student_progress = calc_course_completion( $student_user_id, $student_course_id, $student_org_id, $modules_in_portal );
        if ( $student_progress == 100 )
        {
          // student completed this course. add certificate info into table + add to completed enrollments
          array_push($completed_enrollments, $enrollment);
        }
//d($student_user_id, $student_course_id, $student_progress);
      }
      $count++;
    }
//d($completed_enrollments);

    $all_certificate_filenames = ($user_certificates) ? array_column($user_certificates, 'filename') : array(); // Lists of certificates file names.
    // check if we have completed enrollments and if so display them in a table.
    if ( $completed_enrollments )
    {
      // Tables that will be displayed in the front end.
      $certificateTableObj = new stdClass();
      $certificateTableObj->rows = array();
      $certificateTableObj->headers = array(
        'Date' => 'center',
        'Course Name' => 'center',
        'Staff Name' => 'center',
        'Download' => 'center',
      );
      
      // go through each completed enrollment and make sure we have DB entries for the certificate and create the cert. image if it doesnt exist.
      foreach ($completed_enrollments as $complete_enrollment)
      {
        $user_id = $complete_enrollment['user_id']; // The enrollment user ID
        $course_id = $complete_enrollment['course_id']; // The enrollment course ID
        $org_id = $complete_enrollment['org_id']; // The enrollment org ID
        $filename = "certificate_" . $user_id . "_" . $course_id . ".jpg"; // Certificate name.
        $course_name = isset($courses[$course_id]) ? $courses[$course_id]->course_name : ''; // The course name
        
        // make sure the director is not setting a start/end date
        if(!isset($_REQUEST['start_date']) && !isset($_REQUEST['end_date']))
        {
          // check if the certificate file name is already in our DB, if not add it
          if( !in_array($filename, $all_certificate_filenames) )
          {
            $status = 'conferred';
            $certificate_data = compact("org_id", "course_id", "course_name", "filename", "status");
            setCertificate($user_id, $certificate_data); // create the certificate record
            $certificate = array
              (
                "user_id" => $user_id,
                "org_id" => $org_id,
                "course_id" => $course_id,
                "course_name" => $course_name,
                "filename" => $filename,
                "date_created" => date("Y-m-d H:i:s"),
                "status" => $status
              );
            // because entry didn't exist in DB, add it to the user_certificates array so we can display it in the data table  
            array_push($user_certificates, $certificate); 
          }
        }
        
        // check if syllabus exists for this user/course_id, if not Generate syllabus.
        if(!isset($user_course_completed[$user_id]) || !in_array($course_id, $user_course_completed[$user_id]))
        {
          $course_modules = getModulesInCourse($course_id); // get all the modules in the course
          // make sure we have some modules in this course
          if ($course_modules)
          {
            $module_titles = json_encode( array_column($course_modules, 'title') ); // Get all the titles of the modules.
            $syllabus_data = compact('course_id', 'course_name', 'module_titles');         
            setCertificateSyllabus($user_id, $syllabus_data); // Save the certificate syllabus into our database.
          }
          else
          {
            $module_titles = json_encode( array ('There are no modules in this course') );
            $syllabus_data = compact('course_id', 'course_name', 'module_titles');         
            setCertificateSyllabus($user_id, $syllabus_data); // Save the certificate syllabus into our database.
          }
        }
      }
    }

    // the director gets a date sort/filter option
    if( current_user_can('is_director') )
    {
      $start_date = (isset( $_REQUEST['start_date']) ) ? filter_var($_REQUEST['start_date'],FILTER_SANITIZE_STRING) : SUBSCRIPTION_START;
      $end_date = (isset( $_REQUEST['end_date']) ) ? filter_var($_REQUEST['end_date'],FILTER_SANITIZE_STRING) : SUBSCRIPTION_END;
?>
      <form>
        <table>
          <input type="hidden" name="part" value="mycertificates">
          <tr>
            <th>
              <?= __("Start Date:(yy-mm-dd)", "EOT_LMS"); ?>
            </th>
            <th>
              &nbsp;<input type="text" name="start_date" id="start_date" class="date-picker" size="10" value="<?= $start_date?>">
            </th>
          </tr>
          <tr colspan="3">
            <td>
              <?= __("End Date:(yy-mm-dd)", "EOT_LMS"); ?>
            </td>
            <td>
              &nbsp;<input type="text" name="end_date" id="end_date" class="date-picker" size="10" value="<?= $end_date ?>">
            </td>
            <td>
              &nbsp;&nbsp;<input type="submit" value="<?= __("Generate Certificates", "EOT_LMS"); ?>" id="generateCertificates" style="">
            </td>
          </tr>
          <tr>
            <td>
              <select id="quickSelection">
                <option value="" selected><?= __("Quick Selections", "EOT_LMS"); ?></option>
                <option value="this_month"><?= __("This Month", "EOT_LMS"); ?></option>
                <option value="last_month"><?= __("Last Month", "EOT_LMS"); ?></option>
                <option value="this_week"><?= __("This Week", "EOT_LMS"); ?></option>
                <option value="last_week"><?= __("Last Week", "EOT_LMS"); ?></option>
                <option value="last_7_days"><?= __("Last 7 Days", "EOT_LMS"); ?></option>
                <option value="last_30_days"><?= __("Last 30 Days", "EOT_LMS"); ?></option>
                <option value="this_season"><?= __("This season", "EOT_LMS"); ?></option>
              </select>
            </td>
          </tr>
        </table>
      </form>
<?php
    }

    // check if there are any certificates, and display them
    if (count($user_certificates) >= 1)
    {
      // user has certificates in the DB. Display them.
      if($user_certificates)
      {
        foreach ($user_certificates as $certificate) 
        {
          $user_id = $certificate['user_id']; // The User ID from the certificate
          $course_id = $certificate['course_id']; // The course ID from the certificate
          $org_id = $certificate['org_id']; // The org ID from the certificate
          $first_name = get_user_meta($user_id, "first_name", true);  // First name
          $last_name  = get_user_meta($user_id, "last_name", true); // Last name
          $date_created = new DateTime($certificate['date_created']); 
          $date_created = $date_created->format('Y-m-d');
          $certificateTableObj->rows[] = array
          (
            $date_created, // Fix date format 
            $certificate['course_name'], // The course name.
            $first_name . ' ' . $last_name,
            '<a href="'.get_site_url().'/download-certificate/?user_id='. $user_id .'&course_id=' . $course_id . '&org_id='.$org_id.'&type=certificate">Certificate</a> | <a href="' .  get_site_url() . 
            '/download-certificate/?user_id=' .  $user_id . '&course_id=' . $course_id . '&type=syllabus">' . __("Syllabus", "EOT_LMS") . '</a>' // Download link
          );
        }
        CreateDataTable($certificateTableObj); // Print the table in the page
      }
      else
      {
        echo __("Could not find certificates.", "EOT_LMS");
      }
?>
      <script>
      // Quick selections
      $( document ).on('change', '#quickSelection', function() 
      {
        var dateObj = new Date();
        // Quick date Selections.
        switch( $(this).val() )
        {
          case "this_month":
            var lastDay = new Date(dateObj.getFullYear(), dateObj.getMonth() +1, 0);
            var last_day_of_month = lastDay.getUTCDate();
            first_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-01";
            last_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + last_day_of_month;
            break;
          case "last_month":
            dateObj.setDate(1);
            dateObj.setMonth(dateObj.getMonth()-1);
            first_date = dateObj.getUTCFullYear()  + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-01";
            var lastDay = new Date(dateObj.getFullYear(), dateObj.getMonth() +1, 0);
            last_date = dateObj.getUTCFullYear()  + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + lastDay.getUTCDate();
            break;
          case "this_week":
            first_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate() - dateObj.getDay() +1);
            last_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate() - dateObj.getDay()+7);
            break;
          case "last_week":
            dateObj.setDate(dateObj.getDate() - 7);
            first_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate() - dateObj.getDay() +1);
            last_date = dateObj.getUTCFullYear()  + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate() - dateObj.getDay()+7);
            break;
          case "last_7_days":
            last_date = dateObj.getUTCFullYear()  + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + dateObj.getDate();
            dateObj.setDate(dateObj.getDate() - 7);
            first_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate());
            break;
          case "last_30_days":
            last_date = dateObj.getUTCFullYear()  + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + dateObj.getDate();
            dateObj.setDate(dateObj.getDate() - 30);
            first_date = dateObj.getUTCFullYear() + "-" + ("0" + (dateObj.getUTCMonth() + 1)).slice(-2) + "-" + (dateObj.getDate());
            break;
          case "this_season":
            last_date = '<?= SUBSCRIPTION_END ?>'
            first_date = '<?= SUBSCRIPTION_START ?>';
            break;
          default:
            break;
        }
        $('#start_date').val(first_date); // Inject the selected date in the form.
        $('#end_date').val(last_date); // Inject the selected date in the form.
        $('#generateCertificates').click(); // Click the Generate Certificates.
      })
      $ = jQuery;
      $('.date-picker').datepicker(
      { 
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
      });
      </script>
<?php
    }
    else
    {
      // No certificate
      echo __("You don't have any certificates.", "EOT_LMS");
    }
  }
  else
  {
    // Not a student
    echo __("ERROR: You do not have permisison to view this page.", "EOT_LMS");
  }
?>