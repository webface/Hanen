<?php
	// make sure the user has permission to this page
    if(current_user_can("is_sales_rep") || current_user_can("is_sales_manager"))
    {
?>
    	<div class="breadcrumb">
		<?= CRUMB_DASHBOARD ?>    
		<?= CRUMB_SEPARATOR ?>
		<span class="current"> List Module Resources</span> 
		<h1 class="article_page_title">List Module Resources</h1>
<?php
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
				if($category_id == $category->id)
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
	}
	else
	{
		wp_die('You do not have access to this display.');
	}
?>