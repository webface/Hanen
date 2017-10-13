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
		$courses = getCourses(0, 0, 0); // Default courses
		foreach ($courses as $course) 
		{
			// Tables for Course resources.
			$courseResourcesTableObj = new stdClass();
			$courseResourcesTableObj->rows = array();
		  	$courseResourcesTableObj->headers = array(
		  		'Module ID' => 'center',
				'Module Name' => 'center',
				'# Resources' => 'center',
				'Resources' => '',
			);
			$course_id = $course->ID; // Course ID
			$string = "<ul>";
			$course_module = getCourseModules($course_id); // Coourse Modules
			foreach ($course_module as $module) 
			{
				$module = getModule($module['module_id']); // Module information
				$resources = getResourcesInModule($module['ID']); // Module resources
				$string = "<ul>";
				foreach ($resources as $resource) 
				{
				
					$string .= "<li>" . $resource['type'] . " - " . $resource['name'] . " - " . $resource['ID'];
				}
				$string .= "</ul>";
				// Populate courseResourcesTableObj table
		 		$courseResourcesTableObj->rows[] = array(
		 			$module['ID'],
		 			$module['title'],
		 			count($resources),
		 			$string
				);
			}
			echo "<h2>" . $course->course_name . " - $course_id</h2>";
			CreateDataTable($courseResourcesTableObj); // Print the table in the page	
		}
	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>