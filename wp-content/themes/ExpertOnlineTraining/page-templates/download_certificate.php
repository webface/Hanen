<?php
	/**
	 * Template Name: Download Certificate
	 */
	if( current_user_can ('is_student') )
	{
		if( isset($_REQUEST['user_id']) && isset($_REQUEST['course_id']) )
		{
			// Variable declaration
			global $current_user;
			$user_id = $current_user->ID; // Wordpress user ID
	  		$org_id = get_org_from_user ($user_id); // Organization ID
	  		$portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
			$course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
	 		// Security check. Make sure it is the same person who submitted the request.
	 		if($user_id != $_REQUEST['user_id'])
	 		{
	 			wp_die('ERROR: This certificate does not belong to you.');
	 		}
	 		// Check if the type is syllabus. Generate a text file for syllabus.
	 		if( isset($_REQUEST['type']) && $_REQUEST['type'] == 'syllabus')
	 		{				
		 		$course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING);
			   	$certificate_syllabus = getCertificatesSyllabus($user_id, $course_id); // Get all syllabus certificates 
		   		// Generate the syllabus text file.
	   		   	if($certificate_syllabus)
	   		   	{
   		   			$modules = (isset($certificate_syllabus['modules'])) ? json_decode($certificate_syllabus['modules']) : array(); // All the modules of this course. 
	 			   	header("Content-type: text/plain");
		   			header("Content-Disposition: attachment; filename=syllabus_" . $user_id . "_" . $course_id . ".txt");
					echo "Syllabus for course: " . $certificate_syllabus['course_name'] . "\r\nConsists of the following modules: \r\n";
					// Print all the modules name.
					foreach ($modules as $module) 
					{
						echo "- " . $module . "\r\n";
					}
					exit;
	   		   	}
	   		   	else
	   		   	{
					wp_die('ERROR: Unable to find the syllabus in our database. Please contact the site administrator.');

	   		   	}
				
	 		}
	 		else if( isset($_REQUEST['type']) && $_REQUEST['type'] == "certificate")
	 		// Handles old enrollments that doesn't exsist in LU. We are assuming that image file exsist in our directory. Make a downloadable link.
	 		{
				$filename = "certificate_" . $user_id . "_" . $course_id . ".jpg"; // Certificate name.
				$fileLocation = realpath(get_template_directory() . "/images/certificates/" . $filename);
				if( file_exists( $fileLocation ) )
				{
					header('Content-Type: image/jpg');
					header('Content-Disposition: attachment;filename=' . basename($filename));
			    	readfile($fileLocation);
			    	exit;
				}
				else
				{
					wp_die('ERROR: Unable to find the certificate image for this course. Please contact the site administrator.');
				}

	 		}
	 		else
	 		{	// Invalid enrollment
	 			wp_die('ERROR: Invalid type.');
	 		}
		}
		else
		{	// Invalid request. No request for user ID or Course ID 
			wp_die('ERROR: Invalid request');
		}
	}
	else
	{	
		// Not a student?
		wp_die('ERROR: You do not have permisison to view this page.');
	}
?>