<?php
global $current_user;
$user_id = $current_user->ID; // WP User ID
$module_id = filter_var($_REQUEST['module_id'],FILTER_SANITIZE_NUMBER_INT); // Module ID
$resource_id = filter_var($_REQUEST['resource_id'],FILTER_SANITIZE_NUMBER_INT); // Video ID
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
      
      //echo "Filepath: $file_path <br>";
      $fname = $resource['name'];
      
      $allowed_ext = array (

        // archives
        'zip' => 'application/zip',

        // documents
        'pdf' => 'application/pdf',
        'csv' => 'text/csv',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'application/txt',
        'rtf' => 'application/txt',
        
        // executables
        //'exe' => 'application/octet-stream',

        // images
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'bmp' => 'image/bmp'
        );
      
      // Make sure program execution doesn't time out
      set_time_limit(0);
      
//      if (!is_file($file_path)) {
//        die("$file_path File does not exist. Please try again. If the problem persists, please contact system administrators with the details."); 
//      }
//      
//
//      // file size in bytes
      $head = array_change_key_case(get_headers($file_path, TRUE));
      $fsize = $head['content-length'];
      $fext = strtolower(substr(strrchr($file_path,"."),1));
      error_log($fext);
      // check if allowed extension
      if (!array_key_exists($fext, $allowed_ext)) {
        die("Not allowed file type."); 
      }
      
      // get mime type
      if ($allowed_ext[$fext] == '') {
        $mtype = '';
        // mime type is not set, get from server settings
        if (function_exists('mime_content_type')) {
          $mtype = mime_content_type($file_path);
        }
        else if (function_exists('finfo_file')) {
          $finfo = finfo_open(FILEINFO_MIME); // return mime type
          $mtype = finfo_file($finfo, $file_path);
          finfo_close($finfo);  
        }
        if ($mtype == '') {
          $mtype = "application/force-download";
        }
      }
      else {
        // get mime type defined by admin
        $mtype = $allowed_ext[$fext];
      }
      
      // Browser will try to save file with this filename, regardless original filename.
      // You can override it if needed.
      
      if (!isset($_GET['fc']) || empty($_GET['fc'])) {
        $asfname = $fname;
      }
      else {
        // remove some bad chars
        $asfname = str_replace(array('"',"'",'\\','/'), '', $_GET['fc']);
        if ($asfname === '') $asfname = 'untitled';
      }
      // set headers
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Type: $mtype");
      header("Content-Disposition: attachment; filename=\"$asfname\"");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: " . $fsize);
      readfile("$file_path");

?>