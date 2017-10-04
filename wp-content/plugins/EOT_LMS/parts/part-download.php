<?php
  global $current_user;
  $user_id = $current_user->ID; // WP User ID
  $module_id = isset($_REQUEST['module_id']) ? filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT) : 0; // Module ID
  $resource_id = isset($_REQUEST['resource_id']) ? filter_var($_REQUEST['resource_id'],FILTER_SANITIZE_NUMBER_INT) : 0; // Video ID
  $org_id = get_org_from_user($user_id);
  
  global $wpdb;
  $data = array(
      'user_id' => $user_id,
      'module_id' => $module_id,
      'org_id' => $org_id,
      'resource_id' => $resource_id,
      'date' => date('Y-m-d H:i:s'),
      'type' => 'download_resource'
      
  );
  

  $existing = $wpdb->get_row("SELECT * FROM ".TABLE_TRACK." WHERE user_id = $user_id AND module_id = $module_id AND org_id = $org_id AND resource_id = $resource_id AND type = 'download_resource'");
  if(!$existing)
  {
    $insert = $wpdb->insert(TABLE_TRACK,$data);
  }
  
  $resource = $wpdb->get_row("SELECT * FROM ". TABLE_RESOURCES . " WHERE ID = $resource_id",ARRAY_A);
  
  $file_path = $resource['url'];

  
  // Make sure program execution doesn't time out
  set_time_limit(0);



	// required for IE
	if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

	// get the file mime type using the file extension
	switch(strtolower(substr(strrchr($file_path, '.'), 1))) {
		case 'pdf': $mime = 'application/pdf'; break;
		case 'zip': $mime = 'application/zip'; break;
    case 'doc': $mime = 'application/msword'; break;
    case 'docx': $mime = 'application/msword'; break;
    case 'csv': $mime = 'text/csv'; break;
    case 'txt': $mime = 'application/txt'; break;
		case 'jpeg': $mime = 'image/jpg'; break;
		case 'jpg': $mime = 'image/jpg'; break;
    case 'xls': $mime = 'application/vnd.ms-excel'; break;
    case 'xlsx': $mime = 'application/vnd.ms-excel'; break;
    case 'ppt': $mime = 'application/vnd.ms-powerpoint'; break;
    case 'pptx': $mime = 'application/vnd.openxmlformats-officedocument.presentationml.presentation'; break;
    case 'rtf': $mime = 'application/txt'; break;
    case 'exe': $mime = 'application/octet-stream'; break;
    case 'gif': $mime = 'image/gif'; break;
    case 'png': $mime = 'image/png'; break;
    case 'bmp': $mime = 'image/bmp'; break;
		default: $mime = 'application/force-download';
	}
        
  $file_name = str_replace(array('"',"'",'\\','/'), '', basename($file_path));

	header('Pragma: public'); 	// required
	header('Expires: 0');		// no cache
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private',false);
	header('Content-Type: '.$mime);
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($file_path));	// provide file size
	header('Connection: close');
	readfile($file_path);		// push it out
	exit();


?>