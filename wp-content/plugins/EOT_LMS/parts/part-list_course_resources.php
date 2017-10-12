<?php
	// make sure the user has permission to this page
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
?>
    	<div class="breadcrumb">
		<?= CRUMB_DASHBOARD ?>    
		<?= CRUMB_SEPARATOR ?>
		<span class="current"> List Course Resources</span> 
		<h1 class="article_page_title">List Course Resources</h1>
<?php
		// Tables for Course resources.
		$courseResourcesTableObj = new stdClass();
		$courseResourcesTableObj->rows = array();
	  	$courseResourcesTableObj->headers = array(
			'Course ID' => 'center',
			'Course Title' => 'center',
			'# Resources' => 'center',
			'Resources' => '',
		);
		$courses = getCourses(0, 0, 0); // Default courses
		foreach ($courses as $course) 
		{
			$course_id = $course->ID; // Course ID
			$types = array("exam", "video","doc", "link", "custom_video"); // Types of resources
			$string = "<ul>";
			$num_resources = 0; // Number of resources
			foreach ($types as $type) 
			{
				$resources = getResourcesInCourse($course_id, $type); // Resources in course
				// Get all resources
				foreach ($resources as $resource) 
				{
					$string .= "<li>" . $type . " - " . $resource['name'] . " - " . $resource['ID'];
					$num_resources++;
				}
			}
			$string .= "</ul>";
			// Populate courseResourcesTableObj table
		 	$courseResourcesTableObj->rows[] = array(
		    	$course_id,
		    	$course->course_name,
		    	$num_resources,
		    	$string,
			);	

		}
		CreateDataTable($courseResourcesTableObj); // Print the table in the page
	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>