<div class="breadcrumb">
	<?= CRUMB_DASHBOARD ?>    
	<?= CRUMB_SEPARATOR ?>
	<span class="current">Manage Course Module Resources</span> 
	<h1 class="article_page_title">Manage Course Module Resources</h1>
<?php
	// make sure the user has permission to this page
    if(current_user_can("is_sales_manager"))
    {
    	global $wpdb;

    	$query = "
    		SELECT DISTINCT(cmr.course_id), c.course_name, c.org_id, p.post_title  
    		FROM wp_course_module_resources cmr 
    		LEFT JOIN wp_courses c ON cmr.course_id = c.ID
    		LEFT JOIN wp_posts p ON c.org_id = p.ID
    		WHERE cmr.course_id != 0 
    			AND c.org_id != 0
    	";
    	$all_courses = $wpdb->get_results( $query, ARRAY_A );
d($all_courses);
		
		foreach ($all_courses as $course)
		{
			$course_id = $course['course_id'];
			$course_name = $course['course_name'];
			$post_title = $course['post_title'];
			$org_id = $course['org_id'];
			echo "Course ID: $course_id Name: $course_name ORG: $post_title - $org_id<br>";

			// get modules assigned to this course
			$query = "SELECT module_id, type, resource_id FROM wp_course_module_resources WHERE course_id = $course_id"; 
			$course_resources = $wpdb->get_results( $query, ARRAY_A );
d($course_resources);			
			echo "<br>";
		}
/*
    	$query = "SELECT course_id, module_id, type, resource_id FROM wp_course_module_resources WHERE course_id > 0";
    	$all_resources = $wpdb->get_results( $query, OBJECT );
d($all_resources);
*/

/*
		// Tables for module resources.
		$moduleResourcesTableObj = new stdClass();
		$moduleResourcesTableObj->rows = array();
	  	$moduleResourcesTableObj->headers = array(
			'Module ID' => 'center',
			'Module Title' => 'center',
			'# Resources' => 'center',
			'Resources' => '',
			'Category' => 'center',

		);
		$modules = getModules(); // All modules
		$categories = getCategoriesByLibrary(1); // All categories
		foreach ($modules as $module) 
		{
			$module_id = $module['ID']; // Module ID
			$category_id = $module['category_id']; // Category ID
			$category_name = "Not Found"; // Category Name
			// Find the category name.
			foreach ($categories as $category) 
			{
				if($category_id == $category->ID)
				{
					$category_name = $category->name;
					break;
				}
			}
			$resources = getResourcesInModule($module_id); // Resources in module
			$string = "<ul>";
			// Get all resources
			foreach ($resources as $resource) 
			{
				$string .= "<li>" . $resource['type'] . " - " . $resource['name'] . " - " . $resource['ID'];
			}
			$string .= "</ul>";
			// Populate moduleResourcesTableObj table
		 	$moduleResourcesTableObj->rows[] = array(
		    	$module_id,
		    	$module['title'],
		    	count($resources),
		    	$string,
		    	$category_name . " - " . $category_id
			);	

		}
		CreateDataTable($moduleResourcesTableObj); // Print the table in the page
*/

	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>