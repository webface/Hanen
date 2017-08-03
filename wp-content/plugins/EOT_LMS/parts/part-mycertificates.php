<div class="breadcrumb">
  <?= CRUMB_DASHBOARD ?>    
  <?= CRUMB_SEPARATOR ?>    
    <span class="current">My Certificates</span>     
</div>
<h1 class="article_page_title">My Certificates</h1>
<?php
  if( current_user_can ('is_student') || current_user_can ('is_director')  )
  {
  	// Variable declaration
  	global $current_user;
    $user_id = $current_user->ID; // Wordpress user ID
    $org_id = get_org_from_user ($user_id); // Organization ID
    $courses = getCourses(0, $org_id); // All the courses base on the organization ID.
    
    // If student.
    if( current_user_can ('is_student') )
    {
      $complete_enrollments = getEnrollments(0, $user_id, 0, "completed"); // All enrollments base on the user ID.
      $user_certificates = getCertificates($user_id); // All certificates for this user.
      $user_certificates_syllabus = getCertificatesSyllabus($user_id); // All syllabus for this user.
    }
    else if( current_user_can ('is_director') )
    {
      $complete_enrollments = getEnrollments(0, 0, $org_id, "completed"); // All completed enrollments base on the org ID.
      $student_user_ids = array_unique( array_column( $complete_enrollments, "user_id") ) ; // Ids of the users in the same organization
      $user_certificates = getCertificatesByUserIds($student_user_ids, "image"); // Certificates of all students
      $user_certificates_syllabus = getCertificatesByUserIds($student_user_ids, "syllabus"); // Certificate Syllabus for all students.
    }
d($complete_enrollments, $student_user_ids, $user_certificates, $user_certificates_syllabus);

    // Make an key associative array. User ID is the key, and the value is the array of courses they have completed.
    if($user_certificates_syllabus) 
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

    $all_certificate_filenames = ($user_certificates) ? array_column($user_certificates, 'filename') : array(); // Lists of certificates file names.
    
    // check if we have completed enrollments and if so display them in a table.
    if ( $complete_enrollments )
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
      foreach ($complete_enrollments as $complete_enrollment)
      {
        $user_id = $complete_enrollment['user_id']; // The enrollment user ID
        $course_id = $complete_enrollment['course_id']; // The enrollment course ID
        $filename = "certificate_" . $user_id . "_" . $course_id . ".jpg"; // Certificate name.
        $fileLocation = realpath(get_template_directory() . CERTIFICATE_PATH . $filename);
        $course_name = $courses[$course_id]->course_name; // The course name

        // check if certificate file exists, if not Generate certificate image
        if( !file_exists( $fileLocation ) )
        {
          $first_name = get_user_meta($user_id, "first_name", true);  // First name
          $last_name  = get_user_meta($user_id, "last_name", true); // Last name
          $name = $first_name . ' ' . $last_name;
          $im = new Imagick(get_template_directory() . CERTIFICATE_TEMPLATE); // The certificate template.
          $fileLocation = realpath(get_template_directory() . CERTIFICATE_PATH . $filename);
          $draw = new ImagickDraw();
          $maxwidth = 970;
          $font = 57;
          $draw->setFont( get_template_directory() . '/fonts/lucide_calligraphy_italic.TTF' );
          $draw->setFontSize( $font );
          $draw->setFillColor('#0882C5');
          $draw->setStrokeAntialias(true);
          $draw->setTextAntialias(true);

          /*** set gravity to the center ***/
          $draw->setGravity( Imagick::GRAVITY_CENTER );

          /* Dump the font metrics, autodetect multiline */
          $fontMetrics = $im->queryFontMetrics($draw, $name);

          while ($fontMetrics['textWidth'] > $maxwidth) {
            $font--;
            $draw->setFontSize( $font );
            $fontMetrics = $im->queryFontMetrics($draw, $name);
          }
            
          /*** writes the text onto the image: ***  text written: NAME ***/
          $im->annotateImage( $draw, 0, -95, 0, $name );

          $font = 24;
          $draw->setFont( get_template_directory() . '/fonts/MyriadPro-CondIt.otf' );
          $draw->setFontSize( $font );
          $draw->setFillColor('#000000');
          $draw->setStrokeAntialias(true);
          $draw->setTextAntialias(true);

          /*** set gravity to the center ***/
          $draw->setGravity( Imagick::GRAVITY_CENTER );

          $desc_text = "this Certificate of Continuing Education in Youth Leadership on this date: " . date("F j, Y");

          $fontMetrics = $im->queryFontMetrics($draw, $desc_text);

          while ($fontMetrics['textWidth'] > $maxwidth) {
            $font--;
            $draw->setFontSize( $font );
            $fontMetrics = $im->queryFontMetrics($draw, $desc_text );
          }

          /*** writes the text onto the image: ***  text written: BELOW NAME (and date) ***/
          $im->annotateImage( $draw, 0, -15, 0, $desc_text );

          $font = 24;
          $draw->setFontSize($font);
          $chars_per_line = calc_char_limit($maxwidth, $im, $draw);

          $detailed_text = "We congratulate you on having completed the videos and quizzes assigned for the course entitled " . $course_name . ". The syllabus for this course details the content and attests to your passing the required quizzes. ExpertOnlineTraining.com encourages you to complete additional coursework and to participate in educational experiences that further develop your leadership skills.";

          $words_array = explode(" ", $detailed_text);
          $lines_text = calc_line_text($words_array, $chars_per_line);

          while ( !($lines_text) ) 
          {
            $font--;
            $draw->setFontSize($font);
            $lines_text = calc_line_text($words_array, $chars_per_line);
          }
          $vertical_offset = 45;
          foreach ( $lines_text as $pos ) {
            /*** writes the text onto the image: ***  text written: 1 Line at a time ***/
            $im->annotateImage( $draw, 0, $vertical_offset, 0, $pos );
            $vertical_offset += 30;
          }

          /**** set to jpg ***/
          $im->setImageFormat ("jpeg");

          $im->writeImage( get_template_directory() . CERTIFICATE_PATH . $filename );

          /*** write image to disk ***/
          $file = realpath(get_template_directory() . CERTIFICATE_PATH . $filename);
        }
        
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
d($course_id, $course_modules, $module_titles, $syllabus_data);

        }
      }
    }
    
    // check if there are any certificates, and display them
    if (count($user_certificates) >= 1)
    {
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
                Start Date:(yy-mm-dd)
              </th>
              <th>
                &nbsp;<input type="text" name="start_date" id="start_date" class="date-picker" size="10" value="<?= $start_date?>">
              </th>
            </tr>
            <tr colspan="3">
              <td>
                End Date:(yy-mm-dd)
              </td>
              <td>
                &nbsp;<input type="text" name="end_date" id="end_date" class="date-picker" size="10" value="<?= $end_date ?>">
              </td>
              <td>
                &nbsp;&nbsp;<input type="submit" value="Generate Certificates" id="generateCertificates" style="">
              </td>
            </tr>
            <tr>
              <td>
                <select id="quickSelection">
                  <option value="" selected>Quick Selections</option>
                  <option value="this_month">This Month</option>
                  <option value="last_month">Last Month</option>
                  <option value="this_week">This Week</option>
                  <option value="last_week">Last Week</option>
                  <option value="last_7_days">Last 7 Days</option>
                  <option value="last_30_days">Last 30 Days</option>
                  <option value="this_season">This season</option>
                </select>
              </td>
            </tr>
          </table>
        </form>
<?php
      }
      
      // user has certificates in the DB. Display them.
      if($user_certificates)
      {
        foreach ($user_certificates as $certificate) 
        {
          // Sort by dates if requested.
          if( isset($_REQUEST['start_date']) && isset($_REQUEST['end_date']) )
          {
            $start_date = filter_var($_REQUEST['start_date'],FILTER_SANITIZE_STRING); // Start date
            $end_date = filter_var($_REQUEST['end_date'],FILTER_SANITIZE_STRING); // End date
            $date_created = $certificate['date_created']; // Date when the certificate was created.
            if ((($date_created >= $start_date) && ($date_created <= $end_date)) == false)
            {
              continue;
            }
          }
          $user_id = $certificate['user_id']; // The User ID from the certificate
          $course_id = $certificate['course_id']; // The course ID from the certificate
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
            '/download-certificate/?user_id=' .  $user_id . '&course_id=' . $course_id . '&type=syllabus">Syllabus</a>' // Download link
          );
        }
        CreateDataTable($certificateTableObj); // Print the table in the page
      }
      else
      {
        echo 'Could not find certificates.';
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
      echo "You don't have any certificates.";
    }
  }
  else
  {
    // Not a student
    echo 'ERROR: You do not have permisison to view this page.';
  }
?>