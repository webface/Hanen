<?php
	/**
	 * Template Name: Download Certificate
	 */
	if( isset($_REQUEST['user_id']) && isset($_REQUEST['course_id']) )
	{
		// Variable declaration
		global $current_user;
		$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID

 		// Make sure it is the same person who is submitting the request.
		if( current_user_can ('is_student') )
		{
			$user_id = $current_user->ID; // Wordpress user ID
	 		if($user_id != filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT))
	 		{
	 			wp_die('ERROR: This certificate does not belong to you.');
	 		}
		}
		else if( current_user_can ('is_director') ) 
		{	
			$user_id = filter_var($_REQUEST['user_id'], FILTER_SANITIZE_NUMBER_INT); // The student user ID from a director's view.
  			$org_id = get_org_from_user ($current_user->ID); // Director's Organization ID
			$student_org_id = get_org_from_user ($user_id); // The students org id
	 		
	 		// Make sure both student and director are in the same organization. Potentially student moved to a new camp but we want to be able to download the old certificate still...
	 		if($org_id != $student_org_id)
	 		{
	 			$certificate = verifyCertificate($user_id, $org_id);
	 			if(!$certificate)
	 			{
					wp_die('ERROR: You are not permitted to download the certificate for this student.');
	 			}
	 		}
		}
		else
		{
			// Not a student?
			wp_die('ERROR: You do not have permisison to view this page.');
		}

		// Check if the type is syllabus. Generate a text file for syllabus.
 		if( isset($_REQUEST['type']) && $_REQUEST['type'] == 'syllabus')
 		{				
		   	$certificate_syllabus = getCertificatesSyllabus($user_id, $course_id); // Get all syllabus certificates 
	   		// Generate the syllabus text file.
   		   	if(!empty($certificate_syllabus))
   		   	{
 			   	header("Content-type: text/plain");
	   			header("Content-Disposition: attachment; filename=syllabus_" . $user_id . "_" . $course_id . ".txt");
				echo "Syllabus for course: " . $certificate_syllabus['course_name'] . "\r\nConsists of the following modules: \r\n";
				if( isset( $certificate_syllabus['modules'] ) )
				{
					// All the modules of this course. 
					$modules = json_decode(str_replace('\\', '\\\\', $certificate_syllabus['modules']));
					if (!empty($modules))
					{
						// Print all the modules name.
						foreach ($modules as $module) 
						{
							echo "- " . $module . "\r\n";
						}
					}
					else
					{
						// there was an error decoding the josn (module titles)
						error_log("Couldn't decode json course sylabus: " . json_last_error());
					}
				}
				else
				{
					echo "There are no modules in the course.";
				}
				exit;
   		   	}
   		   	else
   		   	{
				wp_die('ERROR: Unable to find the syllabus in our database. Please contact the site administrator.');

   		   	}
			
 		}
 		else if( isset($_REQUEST['type']) && $_REQUEST['type'] == "certificate")
 		// We are assuming that image file exsist in our directory. Make a downloadable link.
 		{
 			// Check if records exsist in wp_certificate

			$filename = "certificate_" . $user_id . "_" . $course_id . ".jpg"; // Certificate name.
			$fileLocation = realpath(get_template_directory() . CERTIFICATE_PATH . $filename);
			// Check in the certificate table if the record exsist.
			$certificate = getCertificates($user_id, 0, $course_id);
			if(!$certificate)
			{
				wp_die('ERROR: Could not find the certificate. Please contact the administrator.');
			}
			else if( file_exists( $fileLocation ) )
			{
				header_remove();
				header('Content-Type: image/jpg');
				header("Cache-Control: no-store, no-cache");
				header('Content-Disposition: attachment;filename=' . basename($filename));
		    	readfile($fileLocation);
		    	exit;
			}
			else
			{
				$course = getCourse($course_id);
				$course_name = ( isset($course) ) ? $course['course_name'] : "Invalid Course";
	        	$first_name = get_user_meta($user_id, "first_name", true);  // First name
				$last_name  = get_user_meta($user_id, "last_name", true); // Last name
				$name = $first_name . ' ' . $last_name;

				$im = new Imagick(get_template_directory() . CERTIFICATE_TEMPLATE); // The certificate template.
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

				// This process the instant download if the file is created.
				if (file_exists($file))
				{
					header_remove();
					header('Content-Type: image/jpg');
					header("Cache-Control: no-store, no-cache");
					header('Content-Disposition: attachment;filename=' . basename($filename));
			    	readfile(realpath(get_template_directory() . CERTIFICATE_PATH . $filename));
			    	exit;
				}
				else
				{
					// Could not find the created file.
					echo 'File does not exist. Please contact the administrator.';
				}
			}

 		}
 		else
 		{	// Invalid enrollment
 			wp_die('ERROR: Invalid type.');
 		}
	}
	else
	{
		// Invalid request. No request for user ID or Course ID 
		wp_die('ERROR: user_id and course_id');
	}


?>