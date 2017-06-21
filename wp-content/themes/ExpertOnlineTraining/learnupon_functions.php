<?php
/**
  * Functions related to Communication with LearnUpon
**/

/**
 * Sets the appropriate url, required to establish a connection to learnupon
 *
 * @param string $subdomain - The subdomain of the portal.
 * @param string $path - the path of the API that is requested.
 * @return string $url - The desired API url
 **/
function select_lrn_upon_url ($subdomain, $path) {

    $url = "https://$subdomain.". LRN_UPON_URL ."/api/v1/$path";
    
    return $url;
}

/**
 * Execute the communication to LearnUpon
 *
 * @param string $type - POST / GET / PUT
 * @param string $url - Url to call to
 * @param string $data_string - json string with data needed
 * @return array $response - an array of the decoded json object returned from the server
 **/
function execute_communication ($url, $data_string, $type = "GET", $lu_username = LU_USERNAME, $lu_password = LU_PASSWORD) 
{
    /*    global $lu_username, $lu_password;*/
    $credentials = $lu_username . ":" . $lu_password;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $credentials);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
    if ($type == "POST") {
        curl_setopt($curl, CURLOPT_POST, true);
    } else if ($type == "PUT") {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    } else if ($type == "DELETE") {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    if ($type == "POST" || $type == "PUT") {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    }

    date_default_timezone_set('America/Toronto');
/*
    $date = date("Y-m-d");
    $time_start = DateTime::createFromFormat('U.u', microtime(true))->format("H:i:s.u");

    $response = curl_exec($curl);

    date_default_timezone_set('America/Toronto');
    $time_end = DateTime::createFromFormat('U.u', microtime(true))->format("H:i:s.u");
*/
    // try using WPs current time
    $date = current_time('Y-m-d');
    $time_start = current_time('H:i:s');
    $response = curl_exec($curl);
    $time_end = current_time('H:i:s');

    curl_close($curl);

    // log the api call and response
    log_communication ( compact("url", "data_string", "type", "lu_username", "lu_password", "curl", "response", "date", "time_start", "time_end") );

    // return the response from LearnUpon as an array instead of an object
    return json_decode($response, true);
}

/**
 * log any API calls
 * @param array $data - a bunch of data to be loged
 */
function log_communication ( $data )
{
    extract($data);
   /********************************************************
   * Log the API call
   *
   * Variables needed for this action passed through as a 'compact' variable $data
   * url - the API url endpoint
   * data_string - the formatted payload
   * type - call type (GET/POST/PUT/etc..)
   * lu_username - the API username
   * lu_password - the API password
   * curl - the raw payload
   * response - the response back from the API endpoint
   ********************************************************/    

    // gather a few other required variables
    $user_id = get_current_user_id();
    $IP = (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    $web_url = (isset($_SERVER['HTTP_REFERER'])) ? filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL) : $_SERVER['SCRIPT_NAME'];

    // create the array structure to log
    $log_data = array (
        'user_id'   => $user_id,
        'date'  => $date,
        'time_start'  => $time_start,
        'time_end'  => $time_end,
        'endpoint'  => $url,
        'payload'  => $data_string,
        'type'  => $type,
        'username'  => $lu_username,
        'password'  => $lu_password,
        'raw'  => '',
        'response'  => $response,
        'URL'  => $web_url,
        'IP'  => $IP
    );

    // insert into db
    global $wpdb;
    if (!$wpdb->insert(TABLE_API_LOGS, $log_data))
    {
//      error_log("ERROR: wpdb last_error: " . $wpdb->last_error);
//      error_log("ERROR: wpdb last_query: " . $wpdb->last_query);
      error_log("Error logging api call: " . json_encode($log_data));
    }
}

/**
 * create a CNAME Record on cloudflare to point the subdomain.eot.com to subdomain.lu.com
 *
 * @param string $subdomainName - the subdomain submitted to LU. It includes a prefix.
 **/
function setCnameRecord($subdomainName = '')
{

// curl -X POST "https://api.cloudflare.com/client/v4/zones/090bf1cc1d85e6ff1d545c52c7b865b2/dns_records" -H "X-Auth-Email: hagai@targetdirectories.com" -H "X-Auth-Key: d0b867e584fe89824a58cfe3153d1fd1e6fe1" -H "Content-Type: application/json" --data '{"type":"CNAME","name":"udicamp","content":"eotudicamp.learnupon.com","ttl":1}'
    
    $url = CLOUDFLARE_ENDPOINT . "zones/" . CLOUDFLARE_ZONE . "/dns_records";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Auth-Email: hagai@targetdirectories.com',
        'X-Auth-Key: ' . CLOUDFLARE_API_KEY
    ));

    $eotSubdomain = getEOTSubdomain($subdomainName);
    $data_string = array (
        'type' => 'CNAME',
        'name' => $eotSubdomain,
        'content' => $subdomainName . '.learnupon.com',
        'ttl' => '1'
    );
    $data_string = json_encode($data_string);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
 
    $response = json_decode(curl_exec($curl), true);
    curl_close($curl);

    // check for response and return assoc array with status/message
    if(isset($response['success']) && $response['success'])
    {
        // all good.
        return array( 'status' => 1);
    }
    else
    {
        // something went wrong. CNAME was not created will need to do it manually.
        // send Hagai an email alert.
        $recepients = array(); // List of recepients
        // Recepient information
        $recepient = array (
            'email' => "hagai@expertonlinetraining.com",
            'message' => json_encode($response),
            'subject' => "Cloudflare error creating subdomain: " . $subdomainName
        );
        array_push($recepients, $recepient);
        sendMail('cloudflare_error', $recepients, $data);
        return array( 'status' => 1, 'message' => 'CLOUDFLARE ERROR: Couldnt create CNAME Record: ' . $response['errors'][0]['message']);
    }

}

//this function lists Cname Records for cloudflare. Can be filtered with $content and can return different pages (if more than one page) with $page
function listCnameRecords($content = '', $page = 1)
{

// curl -X GET "https://api.cloudflare.com/client/v4/zones/090bf1cc1d85e6ff1d545c52c7b865b2/dns_records?content=eottest" -H "X-Auth-Email: hagai@targetdirectories.com" -H "X-Auth-Key: d0b867e584fe89824a58cfe3153d1fd1e6fe1" -H "Content-Type: application/json"
    
    if($content)
    {
      $url = CLOUDFLARE_ENDPOINT . "zones/" . CLOUDFLARE_ZONE . "/dns_records?per_page=100&page=" . $page . "&content=" . $content.'.learnupon.com';
    }
    else
    {
      $url = CLOUDFLARE_ENDPOINT . "zones/" . CLOUDFLARE_ZONE . "/dns_records?per_page=100&page=" . $page;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Auth-Email: hagai@targetdirectories.com',
        'X-Auth-Key: ' . CLOUDFLARE_API_KEY
    ));

    $response = json_decode(curl_exec($curl), true);

    curl_close($curl);

    // check for response and return assoc array with status/message
    if(isset($response['success']) && $response['success'])
    {
        // all good.
        return $response['result'];
    }
    else
    {
        // something went wrong
        return array( 'status' => 0, 'message' => 'CLOUDFLARE ERROR: Couldnt grab CNAME Record: ' . $response['errors'][0]['message']);
    }

}

//This function lists all Cname Records available for cloudflare
function listCnameRecordsAll()
{
  $all_records = array();
  $counter = 1;

  while(true)
  {
    $records = listCnameRecords('', $counter);

    if(empty($records))
    {
      break;
    }
    else
    {
      $all_records = array_merge($all_records, $records);
    }

    $counter++;
  }
  return $all_records;
}

//this function updates Cname record name and content for cloudflare based on $subdomain_id
function updateCnameSubdomain($subdomain_id, $subdomainName)
{

// curl -X PUT "https://api.cloudflare.com/client/v4/zones/090bf1cc1d85e6ff1d545c52c7b865b2/dns_records/$subdomain_id" -H "X-Auth-Email: hagai@targetdirectories.com" -H "X-Auth-Key: d0b867e584fe89824a58cfe3153d1fd1e6fe1" -H "Content-Type: application/json" --data '{"type":"CNAME","name":"udicamp","content":"eotudicamp.learnupon.com"}'
    
    $url = CLOUDFLARE_ENDPOINT . "zones/" . CLOUDFLARE_ZONE . "/dns_records/" . $subdomain_id;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Auth-Email: hagai@targetdirectories.com',
        'X-Auth-Key: ' . CLOUDFLARE_API_KEY
    ));

    $eotSubdomain = getEOTSubdomain($subdomainName);
    $data_string = array (
        'type' => 'CNAME',
        'name' => $eotSubdomain,
        'content' => $subdomainName . '.learnupon.com'
    );
    $data_string = json_encode($data_string);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $response = json_decode(curl_exec($curl), true);

    curl_close($curl);

    // check for response and return assoc array with status/message
    if(isset($response['success']) && $response['success'])
    {
        // all good.
        return array( 'status' => 1);
    }
    else
    {
        // something went wrong
        return array( 'status' => 0, 'message' => 'CLOUDFLARE ERROR: Couldnt grab CNAME Record: ' . $response['errors'][0]['message']);
    }

}

function communicate_with_learnupon ($action, $data) 
{
  extract ($data);

  switch ($action) {
    case "create_account": {
            /********************************************************
             * Create new portal on LearnUpon for Camp
             *
             * Variables needed for this action passed through as a 'compact' variable $data
             * org_id - the organization ID from Wordpress
             * org_name - the organization name given
             * org_subdomain - the subdomain for the organization (lowercase alpha)
             * user_id - the director ID of the new user created in Wordpress
             * first_name - the director First Name
             * last_name - the director Last Name
             * email - the director Email Address
             * password - the password specified by the director
             *
             ********************************************************/

            if (!isset($number_of_licenses)) $number_of_licenses = 1;
            if (!isset($logo_image_url)) $logo_image_url = 'https://www.expertonlinetraining.com/wp-content/uploads/2016/09/EOT-Clear.png';
            if (!isset($favicon_image_url)) $favicon_image_url = 'https://www.expertonlinetraining.com/wp-content/themes/ExpertOnlineTraining/images/favicon.ico';
            if (!isset($header_color)) $header_color = '#ffffff';
            if (!isset($navigation_color)) $navigation_color = '#15B6E0';
            if (!isset($allow_course_authoring)) $allow_course_authoring = 1;
            
            return createPortal($org_name, $number_of_licenses, $logo_image_url, $org_subdomain, $data, $allow_course_authoring, $favicon_image_url, $header_color, $navigation_color);

//            return createPortal($org_name, $number_of_licenses, $logo_image_url, $org_subdomain, $data);
            break;
    }
    case "activate_account": {
            /********************************************************
             * Activate the User on the Portal
             *
             * Variables needed for this action passed through as a 'compact' variable $data
             * org_id - the organization ID from Wordpress
             * org_subdomain - the subdomain for the organization (lowercase alpha)
             * user_id - the director ID on Wordpress
             * usr_lrn_upon_id - the director ID of on LearnUpon
             * org_lrn_upon_id - the ID of the Portal on LearnUpon
             * number_of_licenses - the number of licenses the director purchased
             ********************************************************/

            return activatePortalAccount($data);
            break;
    }
        case "clone_courses": {
            /********************************************************
                Clones courses and adds them to each Camp Portal - STILL IN DEV MODE - DO NOT USE!
             ********************************************************/
            $portal_id = null;
            $course_id = null;
            $send_data = null;

            // Retrieve course ID's for all the courses
            $url = select_lrn_upon_url ("eot", "courses");
            $course_data = execute_communication($url, '');

            // Retrieve portal ID's for all the portals that have been created
            $url2 = select_lrn_upon_url("eot", "portals");
            $portal_data = execute_communication($url2, '');

            /*
             * Deals with cloning the courses to all the available portals.
             * When cloned, the courses are also published
             * */
            $url3 = select_lrn_upon_url("eot", "courses/clone");

            /**
             * This goes through all the portals,
             * It then retrieves all the courses present, based on their course ID's,
             * Then clones them into each portal.
             * When cloned, the courses are also published.
             *
             * NOTE: Only courses in draft mode can be cloned.
             **/
            for($i = 0; $i < count($portal_data['portals']); $i++) {
                $portal_id = $portal_data['portals'][$i]["id"] . " ";

                for($j = 0; $j < count($course_data['courses']); $j++) {
                    $course_id = $course_data['courses'][$j]["id"];
                    print "Course ID: $course_id<br>";

                    $clone_data = array (
                        'clone_to_portal_id' => $portal_id,
                        'course_id' => $course_id,
                        'publish_after_clone' => true
                    );
                    print "Cloned_data:<br>";
                    var_dump($clone_data);

                    $send_data = json_encode($clone_data);
                    $response = execute_communication($url3, $send_data, "POST");
                    if($response['message']) {
                        var_dump($response);
                        //echo "LUERROR: You cannot clone courses that are in draft mode" . $response['message'];
                        //return array('status' => 0, 'message' => "LUERROR: " . $response['message']);
                    }
                }
            }
            break;
        }

    default:
      return array('status' => 0, 'message' => "There was an error. Invalid LearnUpon Command.");
      break;
  }
}

/**
 * Creates a portal on learnupon via API
 *
 * @param string $title - The title of the new portal
 * @param int $number_of_licenses - If you are using the portal licensing models, you can specify the number of purchased licenses for a given portal.
 * @param string $subdomainName - The subdomain name of the new portal
 * @return array $response - The json response from learnUpon API
 **/
//function createPortal($title = '', $number_of_licenses = 0, $logo_image_url = '', $subdomainName = '', $data = array()) {
function createPortal($title = '', $number_of_licenses = 0, $logo_image_url = '', $subdomainName = '', $data = array(), $allow_course_authoring = 0, $favicon_image_url = '', $header_color = '#ffffff', $navigation_color = '#15B6E0') {    extract ($data);

    /**
     * Establishes a connection to eot subdomain's portals which are on the learnUpon API,
     * and creates a new portal with the mandatory data attributes being passed in.
     */
    $url = select_lrn_upon_url (DEFAULT_SUBDOMAIN, "portals");

    /*
     * First things first,
     * Perform necessary checks on the $subdomainName to ensure that its valid */
    if ($subdomainName == null || $subdomainName == " " || !$subdomainName) {
        return array('status' => 0, 'message' => "You must specify a subdomain for your Camp/Organization");
    }

    $new_portal = array (
        'subdomain' => $subdomainName,
        'title' => $title,
        'number_licenses_purchased' => $number_of_licenses,
        'logo_image_url' => $logo_image_url,
        'banner_image_url' => $logo_image_url,
        'store_banner_image_url' => $logo_image_url,
        'favicon_image_url' => $favicon_image_url,
        'header_color' => $header_color,
        'navigation_color' => $navigation_color,
        'allow_course_authoring' => $allow_course_authoring,
        'copy_from_parent' => true
    );

    $send_data = '{"Portal":' . json_encode($new_portal) . '}';
    $response = execute_communication($url, $send_data, "POST");

    //checks for errors when creating the portal
    if(isset($response['message'])) {
        return array('status' => 0, 'message' => "LUERROR in createPortal: " . $response['message'] . " Subdomain: $subdomainName");
    }
    else if($response['id']) {
        $org_lrn_upon_id = $response['id'];
        $sqsso_key = (isset($response['client_sqsso_secret_key'])) ? $response['client_sqsso_secret_key'] : 0;
        update_post_meta($org_id, 'lrn_upon_id', $org_lrn_upon_id);
        update_post_meta($org_id, 'lrn_upon_sqsso_key', $sqsso_key);
    }
    else {
        return array('status' => 0, 'message' => "LUERROR in createPortal: There was a problem. Please try again later.");
    }

    // Generate API Keys (username and password) for the newly created portal
    $url = select_lrn_upon_url($subdomainName, "portals/" . $org_lrn_upon_id . "/generate_keys");
    $response = execute_communication($url, '');

    //checks for errors when generating API keys
    if(isset($response['message'])) {
        return array('status' => 0, 'message' => "LUERROR in generate_keys: " . $response['message']);
    }
    else if ($response['portal'][0]['id']) {
        $portal_username = $response['portal'][0]['username'];
        $portal_password = $response['portal'][0]['password'];

        // save the keys to DB
        update_post_meta($org_id, 'lrn_upon_api_usr', $portal_username);
        update_post_meta($org_id, 'lrn_upon_api_pass', $portal_password);
    }
    else {
        return array('status' => 0, 'message' => "LUERROR in generate_keys: There was a problem. Please try again later. \n" . print_r($response, true));
    }

    // add CNAME record to cloudflare
    $cname = setCnameRecord($subdomainName);

    //checks for errors when adding the cname record
    if ($cname['status']) {
        // all good.
    }
    else {
        return array('status' => 0, 'message' => $cname['message']);
    }

    /********************************************************
     * Add director to Camp Portal
     */
    $url = select_lrn_upon_url ($subdomainName, "users");

    $new_user = array (
        'last_name' => $last_name,
        'first_name' => $first_name,
        'email' => $email,
        'password' => $password,
        'enabled' => 1,
        'user_type' => 'admin',
        'can_enroll' => 'true',
        'can_mark_complete' => 'true'
    );
    $send_data = '{"User":' . json_encode($new_user) . '}';
    $response = execute_communication ($url, $send_data, "POST", $portal_username, $portal_password);

    if (isset($response['message'])) {
        return array('status' => 0, 'message' => "LUERROR in add user to LU Portal: " . $response['message']);
    }
    else if ($response['id']) {
        $usr_lrn_upon_id = $response['id'];
        update_user_meta ($user_id, 'lrn_upon_id', $usr_lrn_upon_id);
    }
    else {
        return array('status' => 0, 'message' => "LUERROR in add user to LU Portal: There was a problem. Please try again later.\n");
    }

    /*****************************************************
     * Clone Library course into the new portal
     *****************************************************/

    // ################################################################### CHECK WHICH LIBRARY USE IS SUBSCRIBED TO. FOR NOW ASSUME LE
/*    
    // Retrieve course ID's for all the courses in the main portal
    $url = select_lrn_upon_url (DEFAULT_SUBDOMAIN, "courses?include_draft=false");
    $course_data = execute_communication($url, '');

    // Now clone courses into the new portal in draft mode.
    $response = cloneCourses($course_data, $org_lrn_upon_id);
*/

    $response = cloneCourse(lrn_upon_LE_Course_ID, $org_lrn_upon_id);

    if (isset($response['message'])) {
        return array('status' => 0, 'message' => "LUERROR in CloneCourses: " . $response['message']);
    }
    else if ($response['status']) {
        return array('status' => 1);
    }
    else {
        return array('status' => 0, 'message' => "LUERROR in cloneCourses: There was a problem. Please try again later.\n");
    }

}

//update learnupon subdomain name
//$portal_subdomain is the original subdomain nad $new_subdomain is the desired new subdomain name
function updatePortalSubdomain($portal_subdomain, $portal_id, $new_subdomain, $org_id)
{
  $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
  $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);

  $url = select_lrn_upon_url (DEFAULT_SUBDOMAIN, "portals/" . $portal_id);
  $send_data = '{"Portal":{"subdomain":' . $new_subdomain . '}}';

  $response = execute_communication($url, $send_data, "PUT");

  if(isset($response['message'])) 
  {
      return array('status' => 0, 'message' => "LUERROR in updatePortal:" . $response['message']);
  }
  else
  {
      return array( 'status' => 1);
  }
}

/**
 * Get the portals
 *  @param string $portal_subdomain - the subdomain of the portal
 *
 *  @return json encoded list of portals
 */
function getPortals($portal_subdomain = DEFAULT_SUBDOMAIN) {

    $url = select_lrn_upon_url ($portal_subdomain, "portals");
    $response = execute_communication($url, '', 'GET'); 

    //checks for errors when getting the portals
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in getPortals:" . $response['message']);
    }    
    else if (isset($response['portals'])) 
    {
        return $response['portals'];
    } 
    else
    {
        return null;
    }
}

/**
 * Get the portal by portal title
 *  @param string $portal_subdomain - the subdomain of the portal
 *  @param string $title - the subdomain title to look for
 *
 *  @return json encoded list of portals
 */
function getPortalByTitle($portal_subdomain = DEFAULT_SUBDOMAIN, $title) {

    $url = select_lrn_upon_url ($portal_subdomain, "portals?title=".rawurlencode($title));
    $response = execute_communication($url, '', 'GET'); 

    //checks for errors when getting the portals
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in getPortalByTitle:" . $response['message']);
    }    
    else if (isset($response['portals'])) 
    {
        return $response['portals'];
    } 
    else
    {
        return null;
    }
}

/**
 * Deals with Cloning/Copying each course from one portal to another portal or within the current
 * portal you are accessing via the API. It also clones all the modules in the course along with it.
 *
 * NOTE:
 * If you try to clone the same course to the same location more than once,
 * you will be required to specify a 'guid' in order to proceed with the clone.
 * See API documentation for more info.
 *
 * @param array $course_data - Stores the contents of the course
 * @param int $org_lrn_upon_id - The ID of the portal you are cloning into
 **/
function cloneCourses($course_data, $org_lrn_upon_id) {
    /**
     * This goes through all the courses,
     * It then retrieves all the courses present, based on their course ID's,
     * Then clones them into each portal.
     * When cloned, the courses are not published.
     *
     * NOTE: Only courses in draft mode can be cloned.
     **/
    for($i = 0; $i < count($course_data['courses']); $i++) {
        $course_id = $course_data['courses'][$i]["id"];
        $url = select_lrn_upon_url(DEFAULT_SUBDOMAIN, "courses/clone");

        $clone_data = array (
            'clone_to_portal_id' => $org_lrn_upon_id,
            'course_id' => $course_id,
            'publish_after_clone' => false
        );

        $send_data = json_encode($clone_data);
        $response = execute_communication($url, $send_data, "POST");
        if($response['message']) {
            return array('status' => 0, 'message' => "LUERROR in cloneAllCourses: " . $response['message']);
        }
    }
    return array('status' => 1);
}

/**
 * Clone/Copy a specific course from one portal to another portal or within the current
 * portal you are accessing via the API. It also clones all the modules in the course along with it.
 * The course to be cloned must be published.
 *
 * NOTE:
 * If you try to clone the same course to the same location more than once,
 * you will be required to specify a 'guid' in order to proceed with the clone.
 * See API documentation for more info.
 *
 * @param int $course_id - the LU ID of the course you want to clone
 * @param int $org_lrn_upon_id - The ID of the portal you are cloning into
 * @param boolean $publish - whether to publish the course after cloning
 **/
function cloneCourse($course_id = 0, $org_lrn_upon_id = 0, $publish = 'false', $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);

  /********************************************************
   * Variables needed for this action passed through as a 'compact' variable $data
   * org_id - the organization ID from Wordpress
   ********************************************************/

    /**
     * determines whether to delete courses from the default/main portal or the current portal that were in
     */
    if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
    {
      $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
      $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
      $url = select_lrn_upon_url($portal_subdomain, "courses/clone");
    }
    else {
      $portal_username = LU_USERNAME;
      $portal_password = LU_PASSWORD;
      $url = select_lrn_upon_url(DEFAULT_SUBDOMAIN, "courses/clone");
    }

    // check that we got a course ID and org ID
    if ($course_id && $org_lrn_upon_id)
    {
        $clone_data = array (
            'clone_to_portal_id' => $org_lrn_upon_id,
            'course_id' => $course_id,
            'publish_after_clone' => $publish
        );

        $send_data = json_encode($clone_data);
        $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);
        if(isset($response['message'])) {
            return array('status' => 0, 'message' => "LUERROR in cloneCourse: " . $response['message'] . " c_id: $course_id p_id: $org_lrn_upon_id ");
        }
    }
    return array('status' => 1);
}

/**
 * activates the user and sets the number of licenses for the portal after payment has been accepted.
 *
 * @param array $data - Stores the user/portal/license data. Array is compacted.
 * @return array - status with possible message
 **/
function activatePortalAccount($data = array())
{
  extract ($data);

  /********************************************************
   * Activate the User on the Portal
   *
   * Variables needed for this action passed through as a 'compact' variable $data
   * org_id - the organization ID from Wordpress
   * org_subdomain - the subdomain for the organization (lowercase alpha)
   * user_id - the director ID on Wordpress
   * usr_lrn_upon_id - the director ID of on LearnUpon
   * org_lrn_upon_id - the ID of the Portal on LearnUpon
   * number_of_licenses - the number of licenses the director purchased
   ********************************************************/

  $url = select_lrn_upon_url ($org_subdomain, "users/" . $usr_lrn_upon_id);

  $user = get_userdata ($user_id);

  $activate_user = array (
    'email' => $user->user_email,
    'enabled' => 1
  );
  $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
  $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);

  $send_data = '{"User":' . json_encode($activate_user) . '}';

  $response = execute_communication ($url, $send_data, "PUT", $portal_username, $portal_password);

  if (isset($response['message'])) {
    return array('status' => 0, 'message' => "LUERROR in activatePortalAccount: " . $response['message']);
  } else if ($response['id']) {
//    return array('status' => 1); //dont return yet because we need to add # licenses to portal
  } else {
    return array('status' => 0, 'message' => "LUERROR in activatePortalAccount: There was a problem. Please try again later.\n" . print_r ($response, true));
  }

  /********************************************************
   * Add # licenses to portal
   */
  $url = select_lrn_upon_url (DEFAULT_SUBDOMAIN, "portals/" . $org_lrn_upon_id); 

  $update_portal = array (
      'number_licenses_purchased' => $number_of_licenses
  );

  $send_data = '{"Portal":' . json_encode($update_portal) . '}';
  $response = execute_communication($url, $send_data, "PUT"); 

  //checks for errors when creating the portal
  if(isset($response['message'])) {
      return array('status' => 0, 'message' => "LUERROR in activatePortalAccount->update Licenses: " . $response['message']);
  }
  else if($response['id']) {
//    return array('status' => 1); // dont return yet because we need to clone the courses into the portal
  }
  else {
      return array('status' => 0, 'message' => "LUERROR in activatePortalAccount->update Licenses: There was a problem. Please try again later.");
  }

  /*********************************************************
   * check if the user has a LE subscription and if so, clone the courses into the portal
   */
  $subscriptions = get_current_subscriptions ($org_id);
  if (!empty ($subscriptions)) //has a subscription
  {
    $cloned = 0; // boolean to check if weve cloned already or not
    foreach($subscriptions as $subscription){
      if ($subscription->library_id == LE_ID && !$cloned)
      {
        // create 4 courses in LU for this portal
        // clone the base courses then modify them according to the answers
        global $base_courses;
        foreach ($base_courses as $course_name => $LU_course_ID)
        {
            //verify that we have both a course_ID and org_ID
            if ($LU_course_ID && $org_lrn_upon_id)
            {
                // Now clone courses into the new portal in draft mode.
                $response = cloneCourse($LU_course_ID, $org_lrn_upon_id, 'false');
                if (isset($response['status']) && !$response['status']) {
                    return array('status' => 0, 'message' => 'LUERROR in activatePortalAccount: Couldnt cloneCourse: $course_name -> $LU_course_ID ' . $response['message']);
                }
            }
        }
        $cloned = 1; // we cloned the courses. dont do it again for any reason.
      }
/* THIS IS MORE INVOLVED AS IT REQUIRES CHANGES TO CHECKING COMPLETED ENROLLMENTS AS WELL. FOR NOW LEAVE IT AS LE LIBRARY.
      else if ($subscription->library_id == LE_SP_DC && !$cloned)
      {
        // need to remove the leadership essentials library (course) and add the starter pack course
        $response = cloneCourse(lrn_upon_LE_SP_DC_Course_ID, $org_lrn_upon_id,'false');
        if (isset($response['status'] && !$response['status']) {
            return array('status' => 0, 'message' => 'LUERROR in activatePortalAccount: Couldnt cloneCourse: LE SP DC' . $response['message']);
        }
        // delete the Leadership Essentials Library
        $response = delete_course( , $portal_subdomain);
      } 
*/      
    }
  }
  return array('status' => 1); // success
}

/**
 *  Deals with deleting a course via the API.
 *  @param int $course_id - The ID of the course
 *  @param int $portal_subdomain - The subdomain of the portal
 */
function delete_course($course_id, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()){
  extract($data);

  /**
   * determines whether to delete courses from the default/main portal or the current portal that were in
   */
  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $url = select_lrn_upon_url($portal_subdomain, "courses/" . $course_id);
  }
  else {
    $portal_username = LU_USERNAME;
    $portal_password = LU_PASSWORD;
    $url = select_lrn_upon_url (DEFAULT_SUBDOMAIN, "courses/" . $course_id);
  }


  $deleted_course = array (
      'course_id' => $course_id,
  );

  $send_data = json_encode($deleted_course);

  $response = execute_communication($url,$send_data,'DELETE', $portal_username, $portal_password);

  //checks for errors when creating the portal
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in delete_course: " . $response['message']);
  }
  else if($response['id']) 
  {
    return array('status' => 1); // Check if LU actually returns an ID when deleting a course
  }
  else 
  {
    return array('status' => 0, 'message' => "LUERROR in delete_course: There was a problem. Please try again later.");
  }
}


/**
 * Get all the courses present in the portal
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param boolean draft - include courses with a status of draft?
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of courses
 */
function getCourses($portal_subdomain = DEFAULT_SUBDOMAIN, $draft = 0, $data = array()) {
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
  $inc_draft = ($draft) ? "?include_draft=true" : "";
  $url = select_lrn_upon_url ($portal_subdomain, "courses" . $inc_draft );
  
  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
  }
  else
  {
    $response = execute_communication($url, '', 'GET'); 
  }
  //checks for errors when creating the portal
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in getCourses:" . $response['message']);
  }    
  else if (isset($response['courses'][0]['id'])) 
  {
    return $response['courses'];
  } 
  else
  {
    return null;
  }
}

/**
 * Get all the modules inside a course
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *  @param string component_type - use this param to filter for videos only or exams only etc...
 *
 *  @return json encoded list of modules
 */
function getModules($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array(), $component_type = '') {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
  if ($course_id > 0) // check if course_id is set, otherwise list all modules in the portal
  {
    $url = select_lrn_upon_url ($portal_subdomain, "modules?course_id=" . $course_id);
  }
  else 
  {
    $url = select_lrn_upon_url ($portal_subdomain, "modules");
  }

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
  }
  else
  {
    $response = execute_communication($url, '', 'GET'); 
  }
  //checks for errors when creating the portal
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in getModules:" . $response['message']);
  }    
  else if (isset($response['modules'][0]['id'])) 
  {
    // check if we need to filter by component_type
    if ($component_type != '')
    {
      $filtered_modules = array();
      foreach ($response['modules'] as $key => $module)
      {
        if($module['component_type'] == $component_type)
        {
          array_push($filtered_modules, $module);
        }
      }
      return $filtered_modules;
    }
    else
    {
      return $response['modules'];
    }
  } 
  else
  {
    return null;
  }
}

/**
 * Delete the module in the course
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of modules
 */
function deleteModule($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID
   */
  if($course_id <= 0)
    return array('status' => 0, 'message' => "Error in deleteModule: Invalid course id");
  $url = select_lrn_upon_url ($portal_subdomain, "courses/remove_module");

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $send_data = '{"course_id":' . $course_id . ', "module_id": ' . $module_id . '}';
    $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);
  }
  else
  {
    return array('status' => 0, 'message' => "Error in deleteModule: No portal subdomain");
  }

  //checks for errors when deleting the module
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in deleteModule:" . $response['message']);
  }    
  else if (isset($response['id'])) 
  {
    return array('status' => 1, 'task' => 'deleted'); // Check if LU actually returns an ID when deleting a module

  } 
  else
  {
    return null;
  }
}

/**
 * Add the module in the course
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of modules
 */
function addModule($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID
   */
  if ($course_id > 0) // check if course_id is set, otherwise list all modules in the portal
  {
    $url = select_lrn_upon_url ($portal_subdomain, "courses/add_module");
  }
  else 
  {
    // No permission to add anything in any other portal subdomain.
  }

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $send_data = '{"course_id":' . $course_id . ', "module_id": ' . $module_id . '}';
    $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);
  }
  else
  {
    return array('status' => 0, 'message' => "Error in AddModule: No portal subdomain");
  }

  //checks for errors when adding the module
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in AddModule: " . $response['message']);
  }    
  else if (isset($response['id'])) 
  {
   return array('status' => 1, 'task' => 'added'); // Check if LU actually returns an ID when adding a module

  } 
  else
  {
    return null;
  }
}

/**
 *  Handles the add and remove of the module in a course
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of modules
 */
function toggleItemInAssignment($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID
   * module_id - the module ID in master library
   */

  $info_data = array("org_id" => $org_id, "module_id" => $module_id);
  $modules = getModules( $course_id, $portal_subdomain, $info_data ); // All the modules registered in the course.
  if (empty($modules))
  {
    $modules = array();
  }
  $course_module_ids = array_column($modules, 'id'); // Modules IDS

  // Check if the module id is in the course
  if(in_array($module_id, $course_module_ids))
  {
    return deleteModule($course_id, $portal_subdomain, $info_data);
  }
  else
  {
    return addModule($course_id, $portal_subdomain, $info_data);
  }

}

/**
 * Get a Course by name
 *  @param int $course_name - the course name
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of modules
 */
function getCourseByName($course_name, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
  $data = array( "org_id" => $org_id );
  if ($course_name == '' || $course_name == null) // check if course_id is set, otherwise list all modules in the portal
  {
    return array('status' => 0, 'message' => "LUERROR in getCourseByName: course name can't be empy or null.");
  }
  else 
  {
    $courses = getCourses($portal_subdomain, 1, $data); // All the courses that are in in draft mode.
    $key = array_search( $course_name, array_column( $courses, 'name') );
    if( $key >= 0 ) // This returns false if there is no course found.
    {
        return $courses[$key]; // Found module
    }
    else
    {
        return array('status' => 0, 'message' => "LUERROR in getCourseByName: couldn't find the course.");
    }
  }
}

/**
 * Get all the users enrolled in a course
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of users
 */
function getUsersInCourse($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()){
    extract($data);

   /*
    * Variables required in $data
    * org_id - the organization ID 
    */
    if ($course_id <= 0) // Check for invalid course
    {
        return array('status' => 0, 'message' => "ERROR in getUsersInCourse: Invalid course.");
    }
    else 
    {
        $url = select_lrn_upon_url ($portal_subdomain, "enrollments/search?course_id=" . $course_id);
    }
    if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
        $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
    }
    else
    {
        $response = execute_communication($url, '', 'GET'); 
    }
    //checks for errors when getting enrolled users
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in getUsersInCourse:" . $response['message']);
    }    
    else if (isset($response['enrollments'][0]['id'])) 
    {
        return $response['enrollments'];
    } 
    else
    {
        return null;
    }
}

/**
 * Update the course name
 *  @param int $course_id - the course id
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - Stores the contents of the course includes ($course_id, $course_name, $org_id)
 *  @return json encoded list of modules
 */
function updateCourse($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
  extract($data);

  /*
   * Optional variables in $data
   * course_name - the new name/title for the course 
   * due_date_after_enrollment - the due date for the course
   */

  // check if there is a course id, otherwise return error
  if ($course_id == 0) // check if course_id is set, otherwise list all modules in the portal
  {
    return array('status' => 0, 'message' => "ERROR in UpdateCourse: No course_id submitted.");
  }
  
  $url = select_lrn_upon_url ($portal_subdomain, "courses/" . $course_id);

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
  }
  else
  {
    $portal_username = LU_USERNAME;
    $portal_password = LU_PASSWORD;
  }

  if($course_name)
    $course_data['name'] = $course_name;

  if ($due_date_after_enrollment)
        $course_data['due_date_after_enrollment'] = $due_date_after_enrollment;

  $send_data = '{"Course":' . json_encode($course_data) . '}';
  $response = execute_communication($url, $send_data, 'PUT', $portal_username, $portal_password);

  //checks for errors when updating the course name
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in updateCourse:" . $response['message']);
  }    
  else if (isset($response['id'])) 
  {
    return array('status' => 1, 'id' => $response['id']);
  }
  else
  {
    return null;
  }
}

 /**
 * Creates a course on LearnUpon via the API
 *
 * @param string $course_name - the name of the course
 * @param string $portal_subdomain
 * @param array $data - user data
 */
function createCourse($course_name = '', $portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     * user_id - the wordpress/EOT userID 
     * course_due_date - OPTIONAL - the due date of the course
     */
   
    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else // if didn't receive the portal subdomain, return an error cause we will not be creating courses in the main EOT portal
    {
        return array('status' => 0, 'message' => "Error in createCourse: No portal subdomain");
    }
    $url = select_lrn_upon_url ($portal_subdomain, "courses");
    $lrn_upon_id = get_user_meta ( $user_id, 'lrn_upon_id', true );
    $create_course = array(
        'name' => trim($course_name),
        'owner_id' => $lrn_upon_id
    );
    $send_data = '{"Course":' . json_encode($create_course) . '}';
    $response = execute_communication($url, $send_data, 'POST', $portal_username, $portal_password);

    //checks for errors when updating the course name
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in createCourse:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
      return null;
    }
}

/**
 * Delete the course
 *  @param int $course_id - the course id
 *  @return json encoded status
 */
function deleteCourse($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization id
   */

  // check if there is a course id, otherwise return error
  if ($course_id == 0) // check if course_id is set, otherwise cant delete a course.
  {
    return array('status' => 0, 'message' => "ERROR in deleteCourse: No course_id submitted.");
  }
  
  $url = select_lrn_upon_url ($portal_subdomain, "courses/" . $course_id);

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
  }
  else
  {
    $portal_username = LU_USERNAME;
    $portal_password = LU_PASSWORD;
  }

  //Get all the enrolled users in this course
  $enrollments = getUsersInCourse($course_id, $portal_subdomain, $data);
  $unenroll_error = '';

  //unenroll users one by one before deleting the course
  foreach ($enrollments as $enrollment)
  {
    $response = deleteEnrolledUser($enrollment['id'], $portal_subdomain, $data);

    if(isset($response['message'])) 
    {
//      return array('status' => 0, 'message' => "LUERROR in deleteCourse while deleting enrollment:" . $response['message']);
      $unenroll_error .= "Couldn't unenroll user prior to deleting the course: " . $response['message'] . '<br>\n';
    }
  }

  // check if there is an unenroll error and if so prevent deletion of the course
  if ($unenroll_error)
  {
    return array('status' => 0, 'message' => "ERROR: Course was NOT deleted because: <br>\n" . $unenroll_error);
  }

  $response = execute_communication($url, '', 'DELETE', $portal_username, $portal_password);

  //checks for errors when updating the course name
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in deleteCourse:" . $response['message']);
  }    
  else 
  {
    return array('status' => 1);
  }
}

/**
 * Publish the course
 *  @param int $course_id - the course id
 *  @return json encoded status
 */
function publishCourse($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
    extract($data);
    /*
    * Variables required in $data
    * org_id - the organization id
    */

    // check if there is a course id, otherwise return error
    if ($course_id <= 0) // check if course_id is set, otherwise cant delete a course.
    {
        return array('status' => 0, 'message' => "ERROR in PublishCourse: invalid course id.");
    }

    $url = select_lrn_upon_url ($portal_subdomain, "courses/publish");

    // User information
    $publish_course = array (
        'course_id' => $course_id,  // Course ID
    );

    $send_data = json_encode($publish_course);

    if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
        $response = execute_communication($url, $send_data, 'POST', $portal_username, $portal_password); 
    }
    else
    {
        // User can't is forbidden to publish any courses using DEFAULT_SUBDOMAIN
        return array('status' => 0, 'message' => "ERROR in PublishCourse: can't find subdomain.");
    }

    //checks for errors when updating the publishing the course
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in PublishCourse:" . $response['message']);
    }
    else if (isset($response['id'])) 
    {
        return array('status' => 1, 'success' => true);
    }    
    else 
    {
        return null;
    }
}

/**
 * Get all the users info from the specified portal
 *
 * @param string $portal_subdomain - The subdomain of the portal
 * @param array string $data - Holds the value for $org_id
 */
function getUsers($portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */
   
    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else
    {
      // It should not used the master LU Username/Password
      return array('status' => 0, 'message' => "Error in getUsers: No portal subdomain supplied");
    }

    $url = select_lrn_upon_url ($portal_subdomain, "users");
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password);

    //checks for errors when getting the user data for this portal
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in getUsers:" . $response['message']);
    }    
    else if (isset($response['user'])) 
    {
      return array('status' => 1, 'users' => $response['user']);
    }
    else
    {
      return null;
    }
}

/**
 * Get the users info from LearnUpon
 *
 * @param string $portal_subdomain - The subdomain of the portal
 * @param int $user_id - The user's ID
 * @param array string $data - Holds the value for $org_id
 */
function getUser($portal_subdomain = DEFAULT_SUBDOMAIN, $user_id, $data) {
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else
    {
      // if subdomain was not passed in, return an error
      return array('status' => 0, 'message' => "Error in getUser: No portal subdomain supplied");
    }

    $url = select_lrn_upon_url ($portal_subdomain, "users/" . $user_id);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password);
   //checks for errors when getting the user data for this portal
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in getUser:" . $response['message']);
    }    
    else if (isset($response['user'])) 
    {
      return array('status' => 1, 'user' => $response['user'][0]);
    }
    else
    {
      return null;
    }
}

/**
 * Creating an account
 *
 * @param string $portal_subdomain - The subdomain of the portal
 * @param array string $data - Holds the value for $org_id and other info
 */
function createUser($portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
    extract($data);
    /**
     * first_name - first name
     * last_name - last name
     * email - email address
     * password - password
     * org_id - The org ID
     * user_type - the LU user type (learner, instructor, manager, admin)
     */

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else
    {
        // It should not used the master LU Username/Password
        $portal_username = LU_USERNAME;
        $portal_password = LU_PASSWORD;
    }
    $url = select_lrn_upon_url ($portal_subdomain, "users/");

    $new_user = array();

    //validation checks to ensure that empty form fields don't post
    if(isset($first_name) && $first_name != null && $first_name != "") 
    {
        $new_user['first_name'] = $first_name;
    }
    if(isset($last_name) && $last_name != null && $last_name != "") 
    {
        $new_user['last_name'] = $last_name;
    }
    if(isset($password) && $password != null && $password != "") 
    {
        $new_user['password'] = $password;
    }
    if(isset($email) && $email != null && $email != "") 
    {
        $new_user['email'] = $email;
    }
    if(isset($user_type) && $user_type != null && $user_type != "")
    {
      $new_user['user_type'] = $user_type;
      if ($user_type == 'manager')
      {
        $new_user['can_enroll'] = true;
      }
    }

    $send_data = '{"User":' . json_encode($new_user) . '}';
    $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);

    //checks for errors when updating the user 
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in createUser: " . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
      return null;
    }

    return $response;
}

/**
 * Worker function that sends the email to the specified receipients
 *
 * @param string $sender_email - The email of the sender
 * @param string $sender_name - The name of the sender
 * @param array $recipients - an array of associative arrays with each receipient and all the email info eg. name, email, subject, message
 */
function massMail ( $sender_email = '', $sender_name = '', $recipients = array()) {

    $status = 1; // status of the send, success or failure.

    // check that we have a name/email for the from field. ie. the sender.
    if( $sender_email != null && $sender_email != "" && $sender_name != null && $sender_name != "")
    {

        // using mandrill, can only send emails from our domain. So must set the from email manually.
        $headers = array(
            "From: $sender_name <".get_bloginfo('admin_email').">",
            "Reply-To: $sender_name <$sender_email>",
            "Content-Type: text/html; charset=UTF-8"
            );

        // we have the sender info now send them email(s) 
        foreach ($recipients as $recipient)
        {
            if(!wp_mail( $recipient['email'], $recipient['subject'], $recipient['message'], $headers ))
            {
              $status = 0;
              error_log('massmail error: to: ' . $recipient['email'] . ' num reciepients: ' . count($recipients));
            }
        }       
    }
    else
    {
        return array('status' => 0, 'message' => "massMail error: invalid sender name/email.");
    }


    // Check if the mesage sends.
    if ( $status )
    {
        return array('status' => 1, 'message' => 'Your message was sent succesfully.');
    } 
    else 
    {
        return array('status' => 0, 'message' => 'massMail error: Your messsage failed to send.');
    }
}

/**
 * Processed the sending of the message based on target
 *
 * @param string $target - The target action to be taken, eg. create_user, mass mail, etc..
 * @param array $receipients - an array of associative arrays which contain the receiptients information (name/email/message/subject)
 * @param array $data - any additional info we need such as org_id
 */
function sendMail ( $target = '', $recipients = array(), $data ) {
    extract($data);
    
    /*
     * Variables required in $data
     * org_id - The organization ID
     */
    
    global $current_user;
    $current_user = wp_get_current_user();
    $sender_email = $current_user->user_email;
    $sender_name = $current_user->user_firstname . " " . $current_user->user_lastname;

    // check that a target is defined.
    if( $target != null && $target != "" )
    {
        // check for at least 1 receipient
        if (count( $recipients ) > 0)
        {
            // we have at least 1 receipient. Check what to do next.
            if( $target == "create_account" )
            {  
                // can expect only 1 recipient, send email and return the response
                return massMail($sender_email, $sender_name, $recipients);
            }
            else if($target == "NewSubscription" || $target == "NewAccount")
            {
                return massMail(get_bloginfo( 'admin_email' ),'Expert Online Training', $recipients);
            }
            else if($target == "cloudflare_error")
            {
                return massMail(get_bloginfo( 'admin_email' ), 'Couldflare error', $recipients);
            }
            else if($target == "massmail" )
            {
              return massMail($sender_email, $sender_name, $recipients);
            }
        }
        else
        {
            // no receipients
            return array('status' => 0, 'message' => "sendMail error: no recipients.");
        }
    }
    else 
    {
        // No target defined. Return error.
        return array('status' => 0, 'message' => "sendMail error: invalid target.");
    }
}

/**
 * Update the details of an exsisting user based on user id
 *
 * @param string $portal_subdomain - The subdomain name of the portal
 * @param array $data - contains all the other data we need
 **/
function updateUser($portal_subdomain, $data){
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     * user_id -
     * first_name -
     * last_name -
     * email -
     * password - 
     */

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else
    {
        $portal_username = LU_USERNAME;
        $portal_password = LU_PASSWORD;
    }

    $url = select_lrn_upon_url ($portal_subdomain, "users/" . $user_id);

    $new_user = array();

    //validation checks to ensure that empty form fields don't post
    if(isset($first_name) && $first_name != null && $first_name != "") 
    {
        $new_user['first_name'] = $first_name;
    }
    if(isset($last_name) && $last_name != null && $last_name != "") 
    {
        $new_user['last_name'] = $last_name;
    }
    if(isset($password) && $password != null && $password != "") 
    {
        $new_user['password'] = $password;
    }
    if(isset($email) && $email != null && $email != "") 
    {
        $new_user['email'] = $email;
    }

    $new_user_data = '{"User":' . json_encode($new_user) . '}';
    $response = execute_communication($url, $new_user_data, "PUT", $portal_username, $portal_password);

    //checks for errors when updating the user 
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in updateUser:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);

/**
 * Need to update WP user as well.
 */

    }
    else
    {
      return null;
    }

}

/**
 * Delete the user based on user id
 * @param $user_id - The user id they wanted to be deleted
 * @param string $portal_subdomain - The subdomain name of the portal
 **/
function deleteUser($user_id = '', $portal_subdomain = DEFAULT_SUBDOMAIN, $data){
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else // if didn't receive the portal subdomain, return an error cause we will not be deleting users in the main EOT portal
    {
        return array('status' => 0, 'message' => "Error in deleteUser: No portal subdomain");
    }

    $url = select_lrn_upon_url ($portal_subdomain, "users/" . $user_id);
    $response = execute_communication($url, '', 'DELETE', $portal_username, $portal_password);

    //checks for errors when deleting the user
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in deleteUser:" . $response['message']);
    }    
    else
    {
      return array('status' => 1);
    }
}

/*
 *  Update the portal
 *  @param int $portal_id - the portal_id
 *  @param string $portal_subdomain - the subdomain of the portal you want to edit
 *  @param array $data - Stores the contents of the portal
 *  @return json encoded status
 */
function updatePortal($portal_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);
    /*
     * Variables required in $data
     */

    // check if there is a portal id, otherwise return error
    if ($portal_id <= 0) // check if portal_id is set, otherwise cant update the portal.
    {
        return array('status' => 0, 'message' => "ERROR in updatePortal: invalid portal id.");
    }

    // Updating portal requires LU Username and Password.
    $portal_username = LU_USERNAME;
    $portal_password = LU_PASSWORD;

    // It should really only use the EOT subdomain regardless of what is passed in.
    $portal_subdomain = DEFAULT_SUBDOMAIN;

    $url = select_lrn_upon_url ($portal_subdomain, "portals/" . $portal_id);

    // add the portal id to the $data array and pass it to LU
    $data['id'] = $portal_id;

    $send_data = '{"Portal":' . json_encode($data) . '}';

    $response = execute_communication($url, $send_data, 'PUT', $portal_username, $portal_password);
    
    //checks for errors when updating a portal
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in updatePortal:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
        return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
        return null;
    }
}

/**
 *  Get all the enrollments status by course ID
 *  @param int $course_ID - the course ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of enrollments
 */
function getEnrollment($course_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
  extract($data);

  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
  if ($course_id > 0) // check if course_id is set, otherwise return an error message
  {
    $url = select_lrn_upon_url ($portal_subdomain, "enrollments/search?course_id=" . $course_id);
  }
  else 
  {
    return array('status' => 0, 'message' => "ERROR in getEnrollment: Invalid course ID");
  }

  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
  }
  else
  {
    $response = execute_communication($url, '', 'GET'); 
  }

  //checks for errors when getting enrollments
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in getEnrollment:" . $response['message']);
  }    
  else if (isset($response['enrollments'][0]['id'])) 
  {
    return $response['enrollments'];
  } 
  else
  {
    return null;
  }
}

/**
 * Get the enrollment status based on the course name
 *  @param int $course_name - The name of the course
 *  @param string $portal_subdomain - the subdomain of the portal
 *  @return json encoded list of enrollment status
 */
function getEnrollmentStatusByCourseName($course_name = '', $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) {
    extract($data);
    /*
    * Variables required in $data
    * org_id - the organization ID 
    */
    if($course_name == '')
    {
        return array( 'status' => 0, 'message' => "ERROR in getEnrollmentStatusByCourseName: course name cannot be empty " );
    }
    else
    {
        $course_name = str_replace(' ', '%20', $course_name ); // replace the spaces with encoded value so it matches in LU API call
        $url = select_lrn_upon_url( $portal_subdomain, "enrollments/search?course_name=" . $course_name );
    }
    if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
        $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
    }
    else
    {
        $response = execute_communication($url, '', 'GET'); 
    }

    //checks for errors when getting the enrollment status
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in getEnrollmentStatusByCourseName: " . $response['message']);
    }  
    else if (isset($response['enrollments'])) 
    {
        return array('status' => 1, 'enrollments' => $response['enrollments']);
    } 
    else
    {
        return null;
    }
}

/********************************************************************************************************
 * Delete a staff account.
 *******************************************************************************************************/
add_action('wp_ajax_deleteStaffAccount', 'deleteStaffAccount_callback');
function deleteStaffAccount_callback () {
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['staff_id'] ) && isset ( $_REQUEST['email'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = change_course_status_form from this file.
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // The Org ID
        $staff_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_NUMBER_INT); // The staff account ID
        $email = sanitize_email( $_REQUEST['email'] ); // wordpress e-mail address
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $data = compact("org_id");

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'delete-staff_id-org_id_' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffAccount_callback error: Sorry, your nonce did not verify.';
        }
        else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteStaffAccount_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else
        {
            // Delete the staff account from LU
            $response = deleteUser($staff_id, $portal_subdomain, $data);
            if(isset($response['status']) && $response['status'])
            {
                $user = get_user_by( 'email', $email ); // The user in WP
                if($user)
                {
                    // Delete the account in WP
                    if (wp_delete_user( $user->ID ))
                    {
                        // Build the response if successful
                        $result['data'] = 'success';
                        $result['user_id'] = $staff_id;
                        $result['email'] = $email;
                        $result['success'] = true;
                    } 
                    else
                    {
                        $result['display_errors'] = 'failed';
                        $result['success'] = false;
                        $result['errors'] = 'deleteStaffAccount_callback ERROR: Could not delete the WP user account.';
                    }
                }
                else
                {   
                    $result['display_errors'] = 'failed';
                    $result['success'] = false;
                    $result['errors'] = 'deleteStaffAccount_callback ERROR: Could not find the WP user account.';
                }
            }
            else
            {
              $result['display_errors'] = 'failed';
              $result['success'] = false;
              $result['errors'] = 'deleteStaffAccount_callback ERROR: Could not delete the LU user account.';
            }
        }
    }
    else
    {
        $result['display_errors'] = 'failed';
        $result['success'] = false;
        $result['errors'] = 'deleteStaffAccount_callback ERROR: Missing some parameters.';
    }
    echo json_encode($result);
    wp_die();

}

/**
 *   Updating user info.
 */  
add_action('wp_ajax_updateUser', 'updateUser_callback'); //handles actions and triggered when the user is logged in
function updateUser_callback() {
    if( isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['lastname'] ) && isset($_REQUEST['email']) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['old_email'] ))
    {
        $first_name = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
        $last_name = filter_var($_REQUEST['lastname'],FILTER_SANITIZE_STRING);
        $email = sanitize_email( $_REQUEST['email'] );
        $old_email = sanitize_email( $_REQUEST['old_email'] );
        $user_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_STRING);
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $password = $_REQUEST['pw'];
        $data = compact("org_id", "first_name", "last_name", "email", "user_id", "password");
        $new_user = array();

        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'update-staff_' . $user_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'updateUser_callback error: Sorry, your nonce did not verify.';
        }
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'updateuser_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else 
        {
            $response = updateUser($portal_subdomain , $data);
            // Required to update the user information in the front end.
            $result['user_id'] = $user_id;
            $result['old_email'] = $old_email;
            $result['email'] = $email;
            $result['first_name'] = $first_name;
            $result['last_name'] = $last_name;
            $result['portal_subdomain'] = $portal_subdomain;
            $result['org_id'] = $org_id;
            if( $response['id'] ) // user updated in LU, now try WP
            {

                // update or insert new user in WP
                $WP_password = $password ? wp_hash_password($password) : wp_generate_password(); // make sure i have a password for the user
                $userdata = array (
                    'user_login' => $email,
                    'user_pass' => $WP_password,
                    'role' => 'student',
                    'user_email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                );
                // check if user exists
                $user_id = get_user_by( 'email', $old_email ); // The user in WP
                if ($user_id) 
                {
                    // check if email was updated because if it was, we need to update the user_login field.
                    if ($email != $old_email)
                    {
                        global $wpdb;
                        // cant use wp_insert_user because we need to updtate login as well and that function wont do it.
                        if ( $wpdb->update( $wpdb->users, array( 'user_login' => $email, 'user_email' => $email ), array( 'ID' => $user_id->ID ) ) )
                        {
                            // success
                            $result['success'] = true;
                            $result['message'] = 'User account information has been successfully updated.';
                        }
                        else
                        {
                            //failed
                            $result['display_errors'] = 'failed';
                            $result['success'] = false;
                            $result['errors'] = 'updateUser_callback Error: Could not update WP user.';
                        }
                    }

                    // set the userID to be updated
                    $userdata['ID'] = $user_id->ID;
                    // dont change their password unless they added a new password
                    if (!$password){
                        unset($userdata['user_pass']);
                    }

                    // update the user into WP
                    $WP_user_id = wp_insert_user ($userdata);
                    
                    // success
                    $result['success'] = true;
                    $result['message'] = 'User account information has been successfully updated.';

                }
                else
                {
                    // user doesnt exist to create WP User
                    // insert the user into WP
                    $WP_user_id = wp_insert_user ($userdata);

                    // Newly created WP user needs some meta data values added
                    update_user_meta ( $WP_user_id, 'lrn_upon_id', $response['id'] );
                    update_user_meta ( $WP_user_id, 'org_id', $org_id );
                    update_user_meta ( $WP_user_id, 'accepted_terms', '0');
                    update_user_meta ( $WP_user_id, 'portal', $portal_subdomain );

                    // assume we are successful for now... check later.
                    $result['success'] = true;
                    $result['message'] = 'User account information has been successfully updated.';


                }   
/*
                // check is successfully updated/created WP user
                if (!is_wp_error($user))
                {
                    $result['success'] = true;
                    $result['message'] = 'User account information has been successfully updated.';
                }
                else
                {
                    // failed to update WP user. Revert LU user email.
                    $result['display_errors'] = 'failed';
                    $result['success'] = false;
                    $result['errors'] = 'updateUser_callback Error: Could not create WP user.';

                    $email = $old_email;
                    $data = compact("org_id", "first_name", "last_name", "email", "user_id", "password");
                    $response = updateUser($portal_subdomain , $data);
                }
*/







/*
                // check if user exists
                $user_id = get_user_by( 'email', $old_email ); // The user in WP
                if ($user_id) {
                    // cant use wp_insert_user because we need to updtate login as well and that function wont do it.
                    if ( $wpdb->update( $wpdb->users, $userdata, array( 'ID' => $user_id->ID ) ) )
                    {
                        // success
                        $result['success'] = true;
                        $result['message'] = 'User account information has been successfully updated.';
                    }
                    else
                    {
                        //failed
                        $result['display_errors'] = 'failed';
                        $result['success'] = false;
                        $result['errors'] = 'updateUser_callback Error: Could not update WP user.';
                    }
                }
                else
                {
                    // insert the user into WP
                    $user = wp_insert_user ($userdata);

                    // check is successfully updated/created WP user
                    if (!is_wp_error($user))
                    {
                        $result['success'] = true;
                        $result['message'] = 'User account information has been successfully updated.';
                    }
                    else
                    {
                        // failed to update WP user. Revert LU user email.
                        $result['display_errors'] = 'failed';
                        $result['success'] = false;
                        $result['errors'] = 'updateUser_callback Error: Could not create WP user.';

                        $email = $old_email;
                        $data = compact("org_id", "first_name", "last_name", "email", "user_id", "password");
                        $response = updateUser($portal_subdomain , $data);
                    }
                }
*/       
            }
            else 
            {
                $result['display_errors'] = 'failed';
                $result['success'] = false;
                $result['errors'] = 'updateUser_callback Error: There is an error updating user: ' . $response['message'];
            }

            // check that previous attempts to update user did not fail. If not, check if we need to update portal name (camp name)
            if (isset($result['success']) && $result['success'])
            {
              // check if we need to update portal name
              if (isset($_REQUEST['portal_id']) && isset($_REQUEST['old_camp_name']) && isset($_REQUEST['camp_name']) && $_REQUEST['old_camp_name'] != $_REQUEST['camp_name'])
              {
                $camp_name = filter_var($_REQUEST['camp_name'],FILTER_SANITIZE_STRING);
                $portal_id = filter_var($_REQUEST['portal_id'],FILTER_SANITIZE_NUMBER_INT); // LU Portal ID
                // update the portal name on LU
                $portal_data = array('title' => $camp_name);
                $response = updatePortal($portal_id, $org_subdomain, $portal_data);
                if(isset($response['status']) && $response['status'])
                {
                  // Success in updating the portal on LU, now update camp name in WP
                  $post_data = array (
                    'ID'          => $org_id,
                    'post_title'  => $camp_name,
                    );
                  
                  $update_post = wp_update_post($post_data, true);

                  // check if there was an error updating WP camp name. If so, undo LU changes
                  if (is_wp_error($update_post))
                  {
                    $errors = $update_post->get_error_messages();
                    foreach ($errors as $error) {
                      write_log($error);
                    }

                    // undo changes in LU
                    $portal_data = array('title' => $_REQUEST['old_camp_name']);
                    $response = updatePortal($portal_id, $org_subdomain, $portal_data);
                  }
                }
                else
                {
                  // error in updating the portal
                  $result['display_errors'] = 'failed';
                  $result['success'] = false;
                  $result['errors'] = 'updateUser_callback Error: There is an error updating users portal: ' . $response['message'];
                }
              }
            }
        }
    }
    else
    {
        $result['display_errors'] = 'failed';
        $result['success'] = false;
        $result['errors'] = 'updateUser_callback Error: Missing some parameters.';
    }

    echo json_encode( $result );
    wp_die();
}

/**
 *   Manage the creation of a staff account 
 */  
add_action('wp_ajax_createUser', 'createUser_callback'); //handles actions and triggered when the user is logged in

function createUser_callback() {
    if( isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['lastname'] ) && isset ( $_REQUEST['email'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['pw'] ) && isset ( $_REQUEST['org_id'] ))
    {
        $first_name = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING); // User's first name
        $last_name = filter_var($_REQUEST['lastname'],FILTER_SANITIZE_STRING); // User's last name
        $email = sanitize_email( $_REQUEST['email']); // User's e-mail address
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $password = $_REQUEST['pw']; // User's password
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING); // The course name
        $data = compact("org_id", "first_name", "last_name", "email", "password");
        $send_mail = ( isset($_REQUEST['send_mail']) && filter_var($_REQUEST['send_mail'],FILTER_SANITIZE_NUMBER_INT) == 1 ) ? TRUE : FALSE;
        /*
        $data_info = array( "org_id" => $org_id );
        $lrn_upon_id = get_user_meta ( $user_id, 'lrn_upon_id', true );
        $response_getUser = getUser( $portal_subdomain, $lrn_upon_id, $data_info );
        */

        // Check permissions
        if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'create-staff_' . $org_id ) ) 
        {
            $result['display_errors'] = 'success';
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Sorry, your nonce did not verify.';
        }
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'create staff account error: Sorry, you do not have permisison to view this page. ';
        }
        /*
        else if(isset($response_getUser['status']) == 0)
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = $response_getUser['message'];
        }
        */
        else 
        {
            /*
            if(isset($response_getUser['status']) == 1)
            {
                $user = $response_getUser['user'];
                if( $user['number_of_enrollments'] > )
            }
            */

            // check that the user doesnt exist in WP
            if ( email_exists($email) == false )
            {
                $response = createUser($portal_subdomain, $data); // create the user in LU

                if(isset($response['id']))
                {
                    $result['success'] = true;
                    $result['msg_sent'] = $send_mail;
                    $result['name'] = $first_name;
                    $result['lastname'] = $last_name;
                    $result['org_id'] = $org_id;
                    $result['email'] = $email;
                    $result['password'] = $password;
                    $result['user_id'] = $response['id']; // LU User ID
                    $result['portal_subdomain'] = $portal_subdomain;

                    // create the user in WP (student)
                    $userdata = array (
                        'user_login' => $email,
                        'user_pass' => $password,
                        'role' => 'student',
                        'user_email' => $email,
                        'first_name' => $first_name,
                        'last_name' => $last_name
                    );
                    $WP_user_id = wp_insert_user ($userdata);

                    // Newly created WP user needs some meta data values added
                    update_user_meta ( $WP_user_id, 'lrn_upon_id', $response['id'] );
                    update_user_meta ( $WP_user_id, 'org_id', $org_id );
                    update_user_meta ( $WP_user_id, 'accepted_terms', '0');
                    update_user_meta ( $WP_user_id, 'portal', $portal_subdomain );

/*
                    // Create the user acount in wordpress
                    $user_id = wp_create_user( $email, $password, $email );
                    
                    // Update user META info
                    update_user_meta ($user_id, 'first_name', $first_name);
                    update_user_meta ($user_id, 'last_name', $last_name);
*/                    
                    // Create enrollment
                    if($course_name)
                    {
                        // Adding the course name in the $data
                        $data =  $data  + array("course_name" => $course_name);
                        $response = enrollUserInCourse($email, $portal_subdomain, $data);    
                        if($response['status'] == 1)
                        {
                            
                        }
                        else
                        {
                            $result['success'] = false;
                            $result['display_errors'] = true;
                            $result['errors'] = "CreateUser_callback Error: " . $response['message'];
                        }
                    }
                    else
                    {
                        $result['success'] = false;
                        $result['display_errors'] = true;
                        $result['errors'] = "createUser_callback Error: Created user but could not enroll in course because couldn't find the course name.";
                    }   
                }
                // Expected error
                else if($response['status'] == 0)
                {
                    $result['success'] = false;
                    $result['display_errors'] = true;
                    $result['errors'] = "createUser_callback Error: " . $response['message'];
                }
                else 
                {
                    $result['success'] = false;
                    $result['display_errors'] = 'Failed';
                    $result['message'] = "createUser_callback ERROR: could not create the staff account. Please contact the site administrator";
                }
            }
            else 
            {
                $result['success'] = false;
                $result['display_errors'] = true;
                $result['errors'] = 'Wordpress error: User already exsists.';
            } 

        }
        // This variable will return to part-manage_staff_accounts.php $(document).bind('success.create_staff_account). Line 865
        echo json_encode( $result );
    }
    wp_die();
}

/**
 *   Ajax call for a deleting a course.
 */  
add_action('wp_ajax_deleteCourse', 'deleteCourse_callback'); //handles actions and triggered when the user is logged in

function deleteCourse_callback() {
    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['org_id'] ) )
    {

      // Get the Post ID from the URL
      $course_id          = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
      $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
      $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);

      $data = compact("org_id");

      // Check permissions
      if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'delete-course_' . $course_id ) ) 
      {
          $result['display_errors'] = 'failed';
          $result['success'] = false;
          $result['errors'] = 'deleteCourse_callback error: Sorry, your nonce did not verify.';
      }
      if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
      {
          $result['display_errors'] = 'failed';
          $result['success'] = false;
          $result['errors'] = 'deleteCourse_callback Error: Sorry, you do not have permisison to view this page.';
      }
      else 
      {
        $response = deleteCourse($course_id, $portal_subdomain, $data);
        if ($response['status'] == 0)
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Response Message: " . $response['message'];

        }
        else if($response['status'] == 1)
        {
            // Build the response if successful
            $result['data'] = 'success';
            $result['message'] = 'Course has been deleted';
            $result['group_id'] = $course_id;
            $result['success'] = true;
        }
        else
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "ERROR: Could not delete the course.";
        }
      }
      echo json_encode( $result );
    }
    wp_die();
}


add_action('wp_ajax_getModules', 'getModules_callback'); // Executes Courses_Modules functions actions only for log in users

function getModules_callback() {
    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['subscription_id'] ) && isset( $_REQUEST['course_status'] )  )
    {

        // Get the Post ID from the URL
        $course_id          = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $subscription_id    = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
        $course_status      = filter_var($_REQUEST['course_status'],FILTER_SANITIZE_STRING); //  The course status


        $info_data = array("org_id" => $org_id);


        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can ('is_sales_manager') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'LU Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            // Build the response if successful
            // get modules of the course
            $modules = getModules( $course_id, $portal_subdomain, $info_data );

            /*********************************************************************************************
            * Create HTML template and return it back as message. this will return an HTML div set to the 
            * javascript and the javascript will inject it into the HTML page.
            **********************************************************************************************/
            $html = '<div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "width: 350px">';
            $html.= '  <div style = "width:100%;">';

            $num_modules_type_page = 0;
            if( $modules && count($modules) > 0 ) 
            {
                foreach( $modules as $module )
                {
                    /* 
                     * Include only the courses modules
                     */
                    if($module['component_type'] == "page")
                    {
                        $module_description_text = $module['description_text']; 
                        $module_title     = $module['title'];
                        //$html.= ' <div class = "staff_and_assignment_list_row" onmouseover="Tip(\''.str_replace('"','&quot;',addslashes($module_description_text)).'\', WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\',TITLE,\'Description\')" onmouseout="UnTip()">';
                        $html.= ' <div class = "staff_and_assignment_list_row">';
                        $html.= '  <span class="staff_name" >'.$module_title.'</span>';
                        $html.= ' </div>';
                        $num_modules_type_page++;
                    }
                }
                $html.= '   </div>'; 
                $html.= '</div>';  
                $result['video_count'] = $num_modules_type_page;
                $result['data'] = 'success';
                $result['message'] = $html;
                $result['group_id'] = $course_id; // if not included, when clicking on manage assignment/course, it will not open the dialog box.
                $result['course_status'] = $course_status; // The course status, to determine if the user is allowed to use manage module button.
            }
            else if( count($modules) == 0 )
            {
                $result['video_count'] = 0; // No videos.
                $result['group_id'] = $course_id; // course ID. This is still required on the template so that the user will be able to click the "Manage Courses" button.
                $result['data'] = 'failed';
                $result['message'] = '<p>There are no modules available in this course.</p>';
                $result['course_status'] = $course_status; // The course status, to determine if the user is allowed to use manage module button.
            }
            else 
            {
                $result['data'] = 'failed';
                $result['message'] = 'Error in getting modules for course id: ('. $course_id .')';
            }
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }

    echo json_encode($result);
    wp_die();

}


/********************************************************************************************************
 * Create HTML form and return it back as message. this will return an HTML div set to the 
 * javascript and the javascript will inject it into the HTML page.
 * The submit and cancel buttons are handled by javascript in this HTML (part-manage_courses.php for now)  
 *******************************************************************************************************/
add_action('wp_ajax_getCourseForm', 'getCourseForm_callback'); 
function getCourseForm_callback ( ) {
    if(isset($_REQUEST['org_id']) && isset($_REQUEST['form_name']) )
    {
        global $current_user;
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $form_name = filter_var($_REQUEST['form_name'],FILTER_SANITIZE_STRING);
        $user_id = $current_user->ID;

        if($form_name == "create_course_group")
        {
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Create a Course</div>
            </div>
            <div class="middle">
                <form id= "create_staff_group" frm_name="create_staff_group" frm_action="createCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr> 
                            <td class="label"> 
                                <label for="field_name">Name:</label> 
                            <td class="value"> 
                                <input  type="text" name="name" id="field_name" size="35" /><span class="asterisk">*</span> 
                            </td>
                        </tr> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                                <span class="fyi">eg. First-Year Staff, Returning Staff, etc.</span>
                            </td>
                        </tr> 
                        <!--
                        <tr> 
                            <td class="label"> 
                                <label for="field_desc">Description:</label> 
                            </td> 
                            <td class="value"> 
                                <input type="text" name="desc" id="field_desc" size="35" />  
                            </td> 
                        </tr>
                        <tr >
                        <TD></TD>
                            <td class="value">
                                <span class="fyi">(for your own information)</span>
                            </td>
                        </tr>              
                        <tr> 
                        <td class="label"> 
                        </td> 
                        -->
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="user_id" value="<?= $user_id ?>" /> 
                                <input type="hidden" name="subscription_id" value="<?= $subscription_id ?>" /> 
                                <?php wp_nonce_field( 'create-course_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="save_create" style="display:none"></i>
                    <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                        <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt="Close"/>
                        Cancel
                    </a>
                        <a active = '0' acton = "create_staff_group" rel = "submit_button" class="positive" onclick="jQuery('#save_create').show();">
                        <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt="Save"/> 
                        Save
                    </a>
                </div>
            </div>

            <?php
            $html = ob_get_clean();
        }

        else if($form_name == "create_uber_camp_director")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $data = compact("org_id");
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Add a Camp Director</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_group" frm_name="create_uber_camp_director" frm_action="createUberCampDirector" rel="submit_form" hasError=0> 
                    <table style="border-spacing: 10px; border-collapse: separate;">   
                        <tr> 
                          <td class="label"> 
                            <label for="field_camp_name">Camp Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="camp_name" id="field_camp_name" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_first_name">First Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="first_name" id="field_first_name" size="20" value=""/>
                          </td>
                        </tr>
                        <tr> 
                          <td class="label"> 
                            <label for="field_last_name">Last Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="last_name" id="field_last_name" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_password">Email:</label> 
                          <td class="value"> 
                            <input  type="email" name="email" id="field_email" size="20" value=""/>
                          </td>
                        </tr>  
                        <tr> 
                          <td class="label"> 
                            <label for="field_password">Password:</label> 
                          <td class="value"> 
                            <input  type="password" name="password" id="field_password" size="20" value=""/>
                          </td>
                        </tr>
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                 <?php wp_nonce_field( 'create-uber_camp_director_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="save_uber" style="display:none"></i>
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "create_uber_camp_director" rel = "submit_button" class="positive" onclick="jQuery('#save_uber').show();">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }
        else if($form_name == "edit_course_group")
        {
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID
            $data = compact("org_id");
            $course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Edit Course</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_group" frm_name="edit_staff_group" frm_action="updateCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr> 
                          <td class="label"> 
                            <label for="field_name">Name:</label> 
                          <td class="value"> 
                            <input  type="text" name="name" id="field_name" size="35" value="<?= $course_data['name'] ?>"/><span class="asterisk">*</span> 
                          </td>
                        </tr> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                            <span class="fyi">eg. First-Year Staff, Returning Staff, etc.</span>
                            </td>
                        </tr> 
                        <tr> 
                        <!--
                          <td class="label"> 
                            <label for="field_desc">Description:</label> 
                          </td>
                          <td class="value"> 
                            <input type="text" name="desc" id="field_desc" size="35" value=""/>  
                          </td> 
                        </tr>
                        <tr >
                            <TD></TD>
                            <td class="value">
                                <span class="fyi">(for your own information)</span>
                            </td>
                        </tr>   
                        -->           
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                                <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                                <?php wp_nonce_field( 'edit-course_' . $org_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="save_group" style="display:none"></i>
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "edit_staff_group" rel = "submit_button" class="positive" onclick="jQuery('#save_group').show();">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }

        else if($form_name == "delete_course")
        {
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Delete <?=$course_name?> Course</div>
            </div>
            <div class="middle">
                <form id= "delete_staff_group" frm_name="delete_staff_group" frm_action="deleteCourse" rel="submit_form" hasError=0> 
                    <table padding=0 class="form"> 
                        <tr >
                            <TD></TD>
                            <td class="value">
                            <span class="fyi">Staff accounts enrolled in this course will NOT be deleted, but staff will no longer be able to access this course or its contents and any progress they made so far will be lost.</span>
                            </td>
                        </tr> 
                        <tr> 
                            <td class="label"> 
                            </td> 
                            <td class="value"> 
                                <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                                <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                                <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                                <?php wp_nonce_field( 'delete-course_' . $course_id ); ?>
                            </td> 
                        </tr> 
                    </table> 
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="deleting_course" style="display:none"></i>
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "delete_staff_group" rel = "submit_button" class="positive" onclick="jQuery('#deleting_course').show();">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Delete
                  </a>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
        }
        else if($form_name == "manage_camp_course")
        {
          $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
          $org_id = filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT); // The organization ID
          $portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
          $umbrellaCamps = (current_user_can('is_umbrella_manager')) ? getUmbrellaCamps($org_id, 'regional_umbrella_group_id') : getUmbrellaCamps($org_id); // Lists of umbrella camps
          $camps = array(); // Lists of camps
          $data = compact("org_id");
          $course_name = filter_var($_REQUEST['course_name'], FILTER_SANITIZE_STRING); // the name of the course
          
          if ($umbrellaCamps->have_posts())
          { 
            // Get all umbrella camps, and add them into camps array.
            while ( $umbrellaCamps->have_posts() ) 
            {
              $umbrellaCamps->the_post(); 
              $camp['id'] = get_the_ID(); // The org ID
              $camp['name'] = get_the_title(); // The camp name
              array_push($camps, $camp);
            }
          }


        ?>

             <div style="position:absolute;right:-290px;top:0px;">
              <div class="popup"> 
                <table> 
                  <tbody>
                    <tr> 
                      <td class="tl"/><td class="b"/><td class="tr"/> 
                    </tr>
                    <tr> 
                      <td class="b"/> 
                      <td class="body"> 
                        <div class="content">  
                          <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                            <tr  class="head">
                              <td style ="padding:5px;" colspan="2">
                                Publish Option
                              </td>
                            </tr>
                            <tr>
                              <td style ="padding:2px 5px 2px 5px;">Do you want to Publish this course after the copy?<br>(Published courses can not be modified by the individual camp. Draft courses will need to be published by each individual camp.)</td>
                              <td style="padding:2px 5px 2px 5px;font-size:16px;font-weight:bold;text-align:center;"  id="timeToComplete" >
                                <input type="checkbox" name="chkbox_is_publish_course_after_copy" id="chkbox_is_publish_course_after_copy" value='' /> 
                              </td>
                            </tr>
                          </table>         
                        </div> 
                      </td> 
                      <td class="b"/> 
                    </tr>
                    <tr> 
                      <td class="bl"/>
                      <td class="b"/>
                      <td class="br"/>
                    </tr>
                  </tbody> 
                </table> 
              </div> 
            </div>
            <div class="title">
              <div class="title_h2"><?= $course_name; ?></div>
            </div>
            <div class="middle" style ="padding:0px;clear:both;">  
              <div id="video_listing" display="video_list" group_id="null" class="holder osX">
                <div id="video_listing_pane" class="scroll-pane" style="padding:0px 0px 0px 10px;width: 600px">
                  <form name = "add_video_group" id = "add_video_group">
                    <ul class="tree organizeassignment">
                      <h3 class="library_topic">Select which camps to copy this course into:</h3>
              <?php
                      // Display all uber camps and a clone checkbox beside it for cloning functionality.
                      foreach($camps as $camp)
                      {
              ?>
                        <li class="video_item" camp_id="<?= $camp['id']?>">
                          <input collection="add_remove_from_group" org_id="<?= $org_id ?>" portal_subdomain="<?= DEFAULT_SUBDOMAIN ?>" id="chk_video_<?= $camp['id']?>" name="chk_video_<?= $camp['id']?>" type="checkbox" value="1" camp_id="<?= $camp['id']?>" course_id="<?= $course_id ?>" "/> 
                          <label for="<?= $camp['id'] ?>">
                              <span name="video_title">
                                <b>Camp</b> - <span class="vtitle"><?= $camp['name'] ?></span>
                              </span>
                          </label>
                          <img style="margin-right: 0; display:none" class="loader" id="img_loading" src="<?= get_template_directory_uri() . "/images/loading.gif"?>">
                          <img style="margin-right: 0; display:none" class="loader" id="img_check" src="<?= get_template_directory_uri() . "/images/checkmark.gif"?>">
                          <img style="margin-right: 0; display:none" class="loader" id="img_delete" src="<?= get_template_directory_uri() . "/images/delete.gif"?>">
                          <span id="clone_error_message" style="margin-right: 0; display:none"></span>
                        </li> 
              <?php
                      }
              ?> 
                    </ul>
                  </form>
                </div>
              </div>
            </div>      
            <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
              <div class="buttons" >
                <a active='0' acton="add_video_group" rel="done_button" >
                  Done
                </a>
      <!--
                <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="unselect_all_button" >
                  Remove All
                </a> 
                <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="select_all_button" >
                  Add All
                </a>
      -->
                <div style="clear:both"></div>                      
              </div>
              <div style="margin-top:5px;margin-bottom: -10px;">
              </div>
            </div>

        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "add_video_group")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $data = array( "org_id" => $org_id ); // to pass to our functions above
            $course_modules = getModules($course_id, $portal_subdomain, $data); // all the modules in the specified course
if (empty($course_modules))
{
  $course_modules = array();
}
            $course_modules_titles = array_column($course_modules, 'title'); // only the titles of the modules in the specified course
            $modules_in_portal = getModules(0,$portal_subdomain,$data); // all the modules in this portal
            $user_modules_titles = array_column($modules_in_portal, 'title'); // only the titles of the modules from the user library (course).
            $master_course = getCourseByName(lrn_upon_LE_Course_TITLE, $portal_subdomain, $data); // Get the master course. Cloned LE
            $master_course_id = $master_course['id']; // Master library ID
            $master_modules = getModules($master_course_id, $portal_subdomain, $data); // Get all the modules from the master library (course).
            $master_modules_titles = array_column($master_modules, 'title'); // only the titles of the modules from the master library (course).
            $course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
            $due_date = $course_data['due_date_after_enrollment']; // the due date of the specified course
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); //  The subscription ID
            $videoCount = count($course_modules_titles);
            $quizCount = count($course_modules_titles);
            ob_start();
        ?>

       <div style="position:absolute;right:-290px;top:0px;">
        <div class="popup"> 
          <table> 
            <tbody>
              <tr> 
                <td class="tl"/><td class="b"/><td class="tr"/> 
              </tr>
              <tr> 
                <td class="b"/> 
                <td class="body"> 
                  <div class="content">  
                    <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                      <tr  class="head">
                        <td style ="padding:5px;" colspan="2">
                          Assignment Summary
                        </td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Videos</td>
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="videoCount"><?= count($course_modules_titles) ?></td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Quizzes</td>
                        <td style ="text-align:center;padding:2px 5px 2px 5px;" id="quizCount"><?= count($course_modules_titles) ?></td>
                      </tr>
                      <tr>
                        <td style ="padding:2px 5px 2px 5px;">Estimated time to complete&nbsp;</td>
                        <td style="padding:2px 5px 2px 5px;font-size:16px;font-weight:bold;text-align:center;"  id="timeToComplete" ><?= round((MINUTES_PER_VIDEO*$videoCount + MINUTES_PER_QUIZ*$quizCount)/60,1) ?> hours</td>
                      </tr>
 
                    </table>         
                  </div> 
                </td> 
                <td class="b"/> 
              </tr>
              <tr> 
                <td class="bl"/>
                <td class="b"/>
                <td class="br"/>
              </tr>
            </tbody> 
          </table> 
        </div> 
      </div>
    <div style="position:absolute;right:-290px;top:150px;">
        <div class="popup"> 
          <table> 
            <tbody>
              <tr> 
                <td class="tl"/><td class="b"/><td class="tr"/> 
              </tr>
              <tr> 
                <td class="b"/> 
                <td class="body"> 
                  <div class="content">  
                    <table class="assign_summary data" style = "width:260px;margin:0px;padding:5px;">
                      <tr  class="head">
                        <td style ="padding:5px;" colspan="2">
                          Assignment Due Date
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:12px;font-size:12px;">
                          <center>
                            <?php
                              if ($due_date == "") {
                            ?>
                                <p class='curr_duedate'><strong>Due Date:</strong> No due date set.</p>
                                <div id="datepicker"></div>
                                <div id="remove_date" class="buttons" style="display:none;padding-top:10px;">
                                  <a class="negative" style="margin-right:50px;">
                                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                                      Remove Due Date
                                  </a>
                                </div>
                            <?php
                              } 
                              else 
                              {
                                echo "<p class='curr_duedate'><strong>Due Date:</strong> " . date('j F, Y', strtotime($due_date));
                            ?>
                                <div id="datepicker"></div>
                                <div id="remove_date" class="buttons" style="padding-top:10px;">
                                  <a class="negative" style="margin-right:50px;">
                                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                                    Remove Due Date
                                  </a>
                                </div>
                            <?php
                              }
                            ?>
                          </center>
                        </td>
                      </tr>
                    </table>         
                  </div> 
                </td> 
                <td class="b"/> 
              </tr>
              <tr> 
                <td class="bl"/>
                <td class="b"/>
                <td class="br"/>
              </tr>
            </tbody> 
          </table> 
        </div> 
      </div>
      <div class="title">
        <div class="title_h2"><?= $course_name ?></div>
      </div>
      <div>
        <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
      </div>
      <div class="middle" style ="padding:0px;clear:both;">  
        <div id="video_listing" display="video_list" group_id="null" class="holder osX">
          <div id="video_listing_pane" class="scroll-pane" style="padding:0px 0px 0px 10px;width: 600px">
            <form name = "add_video_group" id = "add_video_group">
              <ul class="tree organizeassignment">
                <?php 
                    $subscription = getSubscriptions($subscription_id,0,1); // get the current subscription
                    $library = getLibrary ($subscription->library_id); // The library information base on the user current subscription
                    // Check if the library exsist
                    if( isset($library) )
                    {
                        $library_tag = $library->tag; // The library Tag for this subscription
                        $modules = array(); // Array of Module objects
                        $categories = array(); // Array of the name of the categories
                        foreach($master_modules as $key => $module)
                        {
                            /* 
                             * This populates the modules array.
                             */
                            $pieces = explode(" ", $module['tags']); // Expected result is [String category Library Codes] : The library code could be more than one.
                            $category_name = $pieces[0]; // The category for this module
                            /*
                             * Create a module object and category if the module belongs to this subscription.
                             * Otherwise, skip them.
                             */
                            if($category_name) // Check if there are tags set in LU
                            {
                               /*
                                * Include only the modules that are in the same library tag
                                */
                            array_shift($pieces); // Remove the category in this array and left with the library codes.
                                if (in_array($library_tag, $pieces)) 
                                {
                                    $new_module = new module( $module['id'], $module['title'], $category_name, $module['component_type']); // Make a new module.
                                    array_push($modules, $new_module); // Add the new module to the modules array.
                                    /*
                                     * Populate the category name array if the category name is not yet in the array.
                                     */
                                    if(!in_array($category_name, $categories))
                                    {
                                        array_push($categories, $category_name);
                                    }
                                }
                            }
                        }
                        usort($categories, "category_sort"); // Sort the categories based on the function below.
                        /*  
                         * Display the category and display its modules
                         */
                        foreach($categories as $category)
                        {
                            $category_name = str_replace("_", " ", $category);// The category name. Replace Coma with spaces.
                        ?>
                            <h3 class="library_topic"><?= $category_name ?></h3>
                        <?php
                            /************************************************************************************************
                            * Print modules that are in the same category its in.
                            *************************************************************************************************/
                            foreach( $modules as $key => $module )
                            {
                                if($module->type == "page") 
                                { // Print the course modules.
                                    if ( $module->category == $category )
                                    {
                                        $video_active = 0; // variable to indicate whether module is currently in the portal course
                                        $video_class = 'disabled'; // variable to indicate whther module is currently in the portal course
                                        $module_id = $module->id; // The module ID 
                                        echo '<li class="video_item" video_id="' . $module_id . '">';
                                            if(in_array($module->title, $course_modules_titles))
                                            {   
                                                $video_active = '1';
                                                $video_class = 'enabled';
                                                // if module is in course, need to get it's module id from course, not master course.
                                                foreach ($course_modules as $key2 => $value)
                                                {
                                                  if ($value['title'] == $module->title)
                                                  {
                                                    $module_id =  $value['id']; // overwrite the module id originally from master modules with the module id from this specific course
                                                    break;
                                                  }
                                                }
                                            }
                                    ?>
                                            <input collection="add_remove_from_group" video_length="<?= lrn_upon_Module_Video_Length ?>" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> assignment_id="<?= $course_id ?>" video_id="<?= $module_id ?>" id="chk_video_<?= $module_id ?>" name="chk_video_<?= $module_id ?>" type="checkbox" value="1" <?=($video_active)?' checked="checked"':'';?> /> 
                                            <label for="chk_video_<?= $module_id ?>">
                                                <span name="video_title" class="<?=$video_class?> video_title">
                                                  <b>Video</b> - <span class="vtitle"><?= $module->title ?></span>
                                                </span>
                                            </label>
                                            <?php
                                                /* 
                                                 * Check if there is a an exam for this module
                                                 * The exam checkbox input will not be shown, if there are no exam uploaded in LU.
                                                 * Find the ID of this exam in the modules array.
                                                 */
                                                $exam_name = $module->title . " - Exam"; // Name of the exam
                                                if(in_array($exam_name, $user_modules_titles))
                                                {
                                                ?>
                                                  <div video_id=<?= $module_id ?> class="<?=$video_class?> item" <?=((in_array($exam_name, $course_modules_titles) && in_array($exam_name, $user_modules_titles)) || (in_array($exam_name, $user_modules_titles) && in_array($module->title, $course_modules_titles)) )?' org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>"':'style="display:none"';?> >
                                                <?php
                                                    foreach($modules_in_portal as $exam)
                                                    {
                                                        if($exam['title'] == $exam_name)
                                                        {
                                                            $exam_id = $exam['id']; 
                                                            continue;
                                                        }
                                                    }
                                            ?>
                                                    <input item="quiz" quiz_length="<?= lrn_upon_Quiz_Length ?>" group_id="<?= $course_id ?>" <?= $exam_id ? ' item_id="' . $exam_id . '" name="chk_defaultquiz_'.$exam_id.'" id="chk_defaultquiz_' .$exam_id . ' "':'';?> type="checkbox"   assignment_id="<?= $course_id ?>" value="1" owner="" org_id="<?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" <?= in_array($exam_name, $course_modules_titles) ? ' checked="checked"':''; $exam_id = 0; // Reset Exam ID?> /> 
                                                    <label for="chk_defaultquiz_<?= $module_id ?>">
                                                      <i>Exam</i> (<?= $module->title ?>) 
                                                    </label>
                                                  </div>
                                            <?php
                                                }
                                            ?>
                                        </li> 
                                    <?php
                                        unset( $modules[$key] ); // remove this module in the modules array
                                    }
                                }
                            }
                            // End of Modules foreach
                        }
                        // End of Category foreach
                    }
                    else
                    {
                        // Error in getting the library for this subscription ID
                        echo "Invalid library ID.";
                    }
                ?>
              </ul>

              <div id="custom_quizzes_and_resources">
                <h2 class="library_topic">Your Custom Modules</h2>
                <ul class="tree organizeassignment">
                  <?php
                    foreach($modules_in_portal as $key => $module) // go thourh all the modules in our portal
                    {
//                        if($module['component_type'] == "page" || $module['component_type'] == 'exam')
//                        {
                            $module_active = 0; // variable to indicate whether module is currently in the portal course
                            $module_class = 'disabled'; // variable to indicate whther module is currently in the portal course
                            // check if portal module exists in master course
                            // if it does, do not display it as a custom module
                            if(!in_array($module['title'], $master_modules_titles)) 
                            {
                                // check if the module is in this specific course. if it is, then enable it, otherwise its default disabled.
                                if(in_array($module['title'], $course_modules_titles))
                                {
                                    $module_active = '1';
                                    $module_class = 'enabled';
                                }

                                if ($module['component_type'] == "exam")
                                {
                                  // displpay the module title but disable the checkbox and if clicked alert a message
?>
                                  <li class="video_item" video_id="<?= $module['id'] ?>" >
                                  <input item="custom_quiz" disabled readonly collection="add_remove_from_group" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> video_length="<?= lrn_upon_Module_Video_Length ?>" assignment_id="<?= $course_id ?>" video_id="<?= $module['id'] ?>" id="chk_video_<?= $module['id'] ?>" name="chk_video_<?= $module['id'] ?>" type="checkbox" value="1" <?=($module_active)?' checked="checked"':'';?> 
class="tooltip" onmouseover="Tip('NOTE: You CAN NOT add quizzes to a course using this interface. Please go to <b>Administration -> Manage Your Custom Content</b> to add a custom quiz to this course.', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()"
                                  /> 
                                  <label for="chk_video_<?= $module['id'] ?>">
                                  <span name="video_title" class="<?=$module_class?> video_title">
<?php
                                }
                                else
                                {
                                  // show the input checkbox as ususal
?>
                                  <li class="video_item" video_id="<?= $module['id'] ?>" >
                                  <input collection="add_remove_from_group" org_id=" <?= $org_id ?>" portal_subdomain="<?= $portal_subdomain ?>" group_id=<?= $course_id ?> video_length="<?= lrn_upon_Module_Video_Length ?>" assignment_id="<?= $course_id ?>" video_id="<?= $module['id'] ?>" id="chk_video_<?= $module['id'] ?>" name="chk_video_<?= $module['id'] ?>" type="checkbox" value="1" <?=($module_active)?' checked="checked"':'';?> /> 
                                  <label for="chk_video_<?= $module['id'] ?>">
                                  <span name="video_title" class="<?=$module_class?> video_title">
<?php                                  
                                }
?>

<?php
                                if ($module['component_type'] == "page")
                                {
                                  echo "<b>Page</b> - ";
                                }
                                else if ($module['component_type'] == "exam")
                                {
                                  echo "<b>Quiz</b> - ";
                                }
                                else if ($module['component_type'] == "scorm")
                                {
                                  echo "<b>SCORM</b> - ";
                                }
?>
                                  <span class="vtitle"><?= $module['title'] ?></span>
                                </span>
                                </label>
                                </li>
                      <?php
                            }
//                        }
                    }
                  ?>
                </ul>
              </div>
            </form>
          </div>
        </div>
      </div>      
      <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
        <div class="buttons" >
          <a active='0' acton="add_video_group" rel="done_button" >
            Done
          </a>
          <!--
          <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="unselect_all_button" >
            Remove All
          </a> 
          <a active='0' acton="add_video_group" collection="add_remove_from_group" rel="select_all_button" >
            Add All
          </a>
          -->
<!--
          <a href="my-dashboard.html?view=managedocuments&" active='0' acton="add_video_group" rel="loading" >
            Upload Resource
          </a>
          <a href ="my-dashboard.html?view=managequizzes&org_id=" active='0' acton="add_video_group" rel="loading" >
            Create Custom Quiz
          </a> 
-->                              
          <div style="clear:both"></div>                      
        </div>
        <div style="margin-top:5px;margin-bottom: -10px;">
        </div>
      </div>

        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "edit_staff_account")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $staff_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_NUMBER_INT);
            $data = array( "org_id" => $org_id );
            $response = getUser($portal_subdomain, $staff_id, $data);
            if ($response['status'] == 1)
            {
              $user = $response['user'];
            }
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Edit Staff Account</div>
            </div>
            <div class="middle">
                <form id= "edit_staff_account" frm_name="edit_staff_account" frm_action="updateUser" rel="submit_form" hasError=0> 
                Change your Staffs user details. Once they log in, you will not be able <br /> to edit nor delete their accounts.<br />
                <span class="asterisk">*</span> Required fields        
                <br /><br />
                <table class="Tstandard">
                  <tr>
                    <td class="label">
                      First Name:<span class="asterisk">*</span>     
                    </td>
                    <td class="field">
                      <input type="text" name="name" value="<?= $user['first_name'] ?>" size="30" />
                    </td>
                  </tr>
                  <tr>
                    <td class="label">
                      Last Name:<span class="asterisk">*</span>     
                    </td>
                    <td class="field">
                      <input type="text" name="lastname" value="<?= $user['last_name'] ?>" size="30" />
                    </td>
                  </tr>
                  <tr>
                    <td class="label">
                      E-mail:<span class="asterisk">*</span>        
                    </td>
                    <td class="field">
                      <input type="text" name="email" value="<?= $user['email'] ?>" size="35" />
                    </td>
                  </tr>
                  <tr class="spacer">
                  </tr>
                  <tr>
                    <td class="label">
                      Change Password:<span class="asterisk"></span>      
                    </td>
                    <td class="field">
                      <input type="text" name="pw" value="" size="18" /> <span class="small">(not hidden)</span>
                    </td>
                  </tr>
                  <tr class="spacer">
                  </tr>
                  <tr>
                    <td></td>
                    <td class="field">
                      <input type="hidden" name="subscription_id" value="" />
                      <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                      <input type="hidden" name="staff_id" value="<?= $staff_id ?>" />
                      <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                      <input type="hidden" name="old_email" value="<?= $user['email'] ?>" />
                      <?php wp_nonce_field( 'update-staff_' . $staff_id ); ?>
                    </td>
                  </tr>
                </table>        
              </form>
              <br /><br />    
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="update_staff" style="display:none"></i>
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                  <img src="<?php bloginfo('template_directory'); ?>/images/cross.png" alt=""/>
                    Cancel
                </a>
                <a active = "0" acton = "edit_staff_account" rel = "submit_button" class="positive" onclick="jQuery('#update_staff').show();">
                  <img src="<?php bloginfo('template_directory'); ?>/images/tick.png" alt=""/> 
                  Update
                </a>        
              </div>
            </div>
        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "create_staff_account")
        {
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);            
            $data = array( "org_id" => $org_id );
            $courses_in_portal = getCourses($portal_subdomain, '0', $data); // get all the published courses in the portal
            $course_id = 0; // The course ID
            if(isset($_REQUEST['group_id']))
            {
                $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_STRING);
            }
        ?>
            <div class="title">
              <div class="title_h2">Create Staff Account</div>
            </div>
            <div class="middle">
              <div class="fixed_fb_width">
                <form id="create_staff_account" frm_name="create_staff_account" frm_action="createUser" rel="submit_form" hasError=0> 
                  <br />
                  <br />
                  <span class="asterisk">*</span> Required fields            
                  <br />
                  <br />
                  <table id="create_staff_table" class="Tstandard" style="font-size:12px;">
                    <tr>
                      <td class="label">
                        Camp, School or Youth Program:
                      </td>
                      <td class="field">
                        <?= get_the_title($org_id); ?>                
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Enroll in course:
                      </td>
                      <td class="field">
                        <select name="course_name">                          
                          <?php
                            foreach ($courses_in_portal as $key => $course)
                            {
                                // If course ID is found, set the dropdown selection to this course.
                                if($course_id > 0)
                                {
                                    if($course['id'] == $course_id)
                                    {
                                        echo "<option name='course_name' value='" . $course['name'] . "' selected>" . $course['name'] . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option name='course_name' value='" . $course['name'] . "'>" . $course['name'] . "</option>";
                                    }
                                }
                                // There's no course to be selected.
                                else
                                {
                                    echo "<option name='course_name' value='" . $course['name'] . "'>" . $course['name'] . "</option>";
                                }               
                            }
                          ?>
                        </select>
                        <img src="<?= get_template_directory_uri() . "/images/info.gif" ?>" title="You must enroll a user directly into a <b>course</b>. All users must be in enrolled in at least 1 course to gain access to the content.<br /><br />To create or edit courses, go to:<br><i>Administration &gt; Manage Courses</i>" class="tooltip" style="margin-bottom: -9px" onmouseover="Tip('You must enroll a user directly into a <b>course</b>. All users must be in enrolled in at least 1 course to gain access to the content.<br /><br />To create or edit courses, go to:<br><i>Administration &gt; Manage Courses</i>', FIX, [this, 45, -70], WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, '#E5E9ED', BORDERCOLOR, '#A1B0C7', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, '#F1F3F5')" onmouseout="UnTip()" />                
                      </td>
                    </tr>
                    <tr class="spacer">
                    </tr>
                    <tr>
                      <td class="label">
                        First Name: <span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                            <div id="autofill_suggest">
                                <input type="text" name="name" value="" size="30" required />
                            </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Last Name:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                        <div id="autofill_suggest">
                            <input type="text" name="lastname" value="" size="30" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        E-mail:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                          <div id="autofill_suggest">
                             <input class="email_large" type="text" name="email" value="" size="40" />
                          </div>            
                      </td>
                    </tr>
                    <tr>
                      <td class="label">
                        Password:<span class="asterisk">*</span>                
                      </td>
                      <td class="field">
                        <input type="text" name="pw" value="" size="18" />
                      </td>
                    </tr>
                    <tr class="spacer">
                    </tr>
                    <tr>
                      <td></td>
                      <td>
                            <span class="asterisk">*</span> 
                            <input id="send" type="checkbox" name="send_mail" value="1" checked="checked" /> 
                            <label for="send">Yes, send an e-mail with their login password.</label>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td class="field">
                        <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                        <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                        <?php wp_nonce_field( 'create-staff_' . $org_id ); ?>
                      </td>
                    </tr>
                  </table>
                </form>
                <br /><br />    
              </div>   
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="creating_staff" style="display:none"></i>
                <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                  <img src="<?php bloginfo('template_directory'); ?>/images/cross.png" alt=""/>
                    Cancel
                </a>
                <a active="0" acton="create_staff_account" rel="submit_button" class="positive" onclick="jQuery('#creating_staff').show();">
                  <img src="<?php bloginfo('template_directory'); ?>/images/tick.png" alt=""/> 
                  Create
                </a>        
              </div>
            </div>
        <?php
            $html = ob_get_clean();
        }
        else if($form_name == "send_message")
        {        
            $name = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
            $password = $_REQUEST['password'];    
            $email = sanitize_email( $_REQUEST['email'] );
            $target = filter_var($_REQUEST['target'],FILTER_SANITIZE_STRING);
            $data = array( "org_id" => $org_id );
            global $current_user;
            wp_get_current_user();
        ?>
            <div class="title">
              <div class="title_h2">
                Edit Email Message
              </div>
            </div>
            <div class="middle" style ="font-size:12px;width:600px;margin:10px;padding:0px;clear:both;">
                <form frm_name="send_message" id="send_message" frm_action="sendMail" rel="submit_form" hasError=0>
                    <table>
                      <tr>
                        <td class="label" width="100px">
                          From
                        </td>
                        <td class="value">
                          <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?><span class="small">(<?= $current_user->user_email ?>)</span>
                        </td>
                      </tr>
                      <tr>
                        <td class="label">
                          Subject
                        </td>
                        <td class="value">
                          <input type="text" name="subject" value="Your account on ExpertOnlineTraining.com (Leadership Training)" size="60" />
                        </td>
                      </tr>
                      <tr>
                        <td class="label vtop">Message</td>
                        <td class="value">
                          <textarea class="tinymce" id="composed_message" name="message" style="margin-left:1px;width: 525px; height: 300px">
                            <b>Welcome</b>, <?= $name ?>!<br><br>
                            <b>Congratulations!</b> You are now a member of Expert Online Training (EOT), the world’s best virtual classroom for youth development professionals. 
                            By using EOT now, before your job starts at <?= get_the_title($org_id); ?>, you will turbocharge your leadership skills, boost your self-confidence, 
                            and get even more out of <?= get_the_title($org_id); ?>’s on-site training.<br><br>

                            <p><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" alt="EOT Logo" style="width: 125px; height: 94px; float: left;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image1.png" data-mce-style="width: 150px; height: 113px; float: left;"> 
                            <br><b>Take EOT with you.</b> We know you are busy, so our new website is mobile-friendly. You can now watch EOT videos and take your quizzes on any smartphone, tablet, or laptop with a WiFi connection. 
                            Imagine learning more about behavior management, leadership, supervision, games, and safety while you sit in a café, library, or student lounge!</p><br><br>

                            <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> just created an account for you with these login credentials:<br><br>

                            E-mail / username: <?= $email ?><br>
                            Password: <?= $password ?><br><br>

                            To watch EOT’s intro video and log in, <a href="https://www.expertonlinetraining.com" target="_blank" data-mce-href="https://www.expertonlinetraining.com">click here</a>.<br><br>

                            <b>When is it due?</b> Directors usually require staff to complete their online learning assignment before arriving on-site. 
                            If you have not yet received a due-date for your assignment, check with <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> to get one. 
                            As you move through your course, <?= $current_user->user_firstname; ?> <?= $current_user->user_lastname; ?> will have access to an electronic dashboard that allows them to track your progress and quiz scores.<br><br>

                            <b>Got Questions?</b> If you get stuck, watch our online help videos or call us at <b>877-237-3931</b>! The EOT Customer Success team is on duty M-F from 9-5 ET. As Director of Content, I also welcome your comments and suggestions for new features and video topics.<br><br>

                            Enjoy your training!<br><img src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" alt="Chris's signature" style="width: 100px; height: 55px;" data-mce-src="https://www.expertonlinetraining.com/wp-content/uploads/2017/02/image2.jpeg" data-mce-style="width: 100px; height: 55px;"><br>
                            Dr. Chris Thurber<br> 
                            EOT Co-Founder &amp;<br> 
                            Director of Content
                          </textarea>
                          <br /><br />
                            <input type="hidden" name="email" value="<?= $email ?>" />
                            <input type="hidden" name="org_id" value="<?= $org_id ?>" />
                            <input type="hidden" name="target" value="<?= $target ?>" />
                            <input type="hidden" name="name" value="<?= $name ?>" />
                        </td>
                      </tr>
                    </table>
                </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="sending_message" style="display:none"></i>
                <a onclick="jQuery(document).trigger('close.facebox');"  class="negative">
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a active='0' acton="send_message" rel="submit_button" class="positive" onclick="jQuery('#sending_message').show();">
                  <div style="height:15px;padding-top:2px;"> Send Message</div>
                </a>
              </div>
            </div>    
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "add_staff_to_group")
        {        
            $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['group_name'],FILTER_SANITIZE_STRING);
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);  
            $data = array( "org_id" => $org_id );
            $org_subdomain = filter_var($_REQUEST['org_subdomain'],FILTER_SANITIZE_STRING);
            $response = getUsers($org_subdomain, $data);
            if($response['status'] == 1)
            {
                $users = $response['users'];  // All users available base on portal subdomain
                usort($users, "sort_first_name"); // sort the users by first name.
                $enrollments = getEnrollment( $course_id, $org_subdomain, $data ); // The lists of enrollment status including the info of the users registered in the course.
                if (!$enrollments['status'])
                {
                  // create an associative array for userID => enroolement
                  $enrollments_associative_array = array();
                  foreach ($enrollments as $enrollment)
                  {
                    $enrollments_associative_array[$enrollment['user_id']] = $enrollment;
                  }
                    $user_ids_in_course = array();
                    $user_ids_in_course = array_column($enrollments, 'user_id'); // Lists of enrollments with the info of users that are registered in this course.
                    ?>
                    <div class="title">
                        <div class="title_h2"><?= $course_name ?></div>
                    </div>
                    <div class="middle" style="padding:0px;clear:both;">
                        <div class="fixed_fb_width">
                            <form id="add_staff_to_group" frm_name="add_staff_to_group" rel="submit_form" hasError=0> 
                                <div  id="staff_listing" display="staff_list" group_id="null" class="holder osX">
                                    <div  id="staff_listing_pane" class="scroll-pane" style="width: 600px">  
                                        <div style="width:100%;">
                                            <div class="errorbox" style="display:none;"></div>
                                            <?php 
                                                foreach($users as $user)
                                                {
                                                    // we dont want to display super admins in the user list so skip the loop if its an admin
                                                    if ($user['user_type'] == 'admin') 
                                                    {
                                                        continue;
                                                    }
                                                    $name = $user['first_name'] . " " . $user['last_name'];
                                                    $email = $user['email'];
                                                    $user_id = $user['id'];
                                                    $enrollment_id = $enrollments_associative_array[$user['id']]['id'];
                                                    $nonce = wp_create_nonce ('add/deleteEnrollment-userEmail_' . $email);
                                                    if(in_array($user_id, $user_ids_in_course))
                                                    {      
                                            ?>
                                                    <div class="staff_and_assignment_list_row" style="width:600px;padding:7px 155px 7px 5px;background-color:#D7F3CA" >  
                                                        <span class="staff_name" style="font-size:12px;"><?= $name ?></span> / 
                                                        <span class="staff_name" style="font-size:12px;"><?= $email ?></span>
                                                        <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                            <a selected=1 class="add_remove_btn" collection="add_remove_from_group" group_id="<?= $course_id ?>" email="<?= $email ?>" status="remove" org_id="<?= $org_id ?>" enrollment_id="<?= $enrollment_id ?>" course_name="<?= $course_name ?>" portal_subdomain="<?= $org_subdomain ?>" nonce="<?= $nonce ?>" >
                                                                Remove from course
                                                            </a>
                                                        </div>
                                                        <div style="clear:both;">
                                                        </div> 
                                                    </div> 

                                                <?php
                                                    }
                                                    else 
                                                    {
                                                ?>
                                                    <div class="staff_and_assignment_list_row" style="width:600px;padding:7px 155px 7px 5px;" >  
                                                        <span class="staff_name" style="font-size:12px;"><?= $name ?></span> / 
                                                        <span class="staff_emai" style="font-size:12px;"><?= $email ?></span>
                                                        <div style="width:140px;text-align:center;float:right;padding-right:35px;">
                                                            <a selected=1 class="add_remove_btn" collection="add_remove_from_group" group_id="<?= $course_id ?>" email="<?= $email ?>" status="add" org_id="<?= $org_id ?>" course_name="<?= $course_name ?>" portal_subdomain="<?= $org_subdomain ?>" nonce="<?= $nonce ?>">
                                                                Add to course
                                                            </a>
                                                        </div>
                                                        <div style="clear:both;">
                                                        </div> 
                                                    </div> 
                                            <?php
                                                    }
                                                }                      
                                            ?>

                                        </div>
                                    </div>
                                </div>     
                            </form>
                        </div>
                    </div>      
                    <div class="popup_footer" style="background-color:#FFF; padding:15px 15px 5px 15px;">
                        <div class="buttons" style="padding-right:20px;">
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="edit_staff" style="display:none"></i>
                            <a active='0' acton="add_staff_to_group" rel="done_button" onclick="jQuery('#edit_staff').show();">
                                <!--<img src="/images/tick.png" alt=""/>--> 
                                Done
                            </a>
                            <a active='0' acton="add_staff_to_group" collection="add_remove_from_group" rel="unselect_all_button" >
                                <!--<img src="/images/selectall.png" alt=""/>-->  
                                Remove All
                            </a> 
                            <a active='0' acton="add_staff_to_group" collection="add_remove_from_group" rel="select_all_button" >
                                <!--<img src="/images/icon-user.gif" alt=""/> --> 
                                Add All
                            </a>
<!--
                            <a active='0' acton="add_staff_to_group" href="#" id="invite_staff_fb">
                                Invite To Register
                            </a>          
                            <a active='0' acton="add_staff_to_group" id="upload_spreadsheet_fb">
                                Upload Spreadsheet
                            </a>
-->
                            <a active='0' acton="add_staff_to_group" href='#' id="create_staff_fb">
                                <!--<img src="/images/icon-user.gif" alt=""/> --> 
                                Create Staff
                            </a>
                            <div style="clear:both"></div>                      
                        </div>
                    </div>
                <?php
                }
                else
                {
                     echo "getCourseForm_callback error: add_staff_to_group form: " . $enrollments['message'];
                }
            }
            else if($enrollments['status'] == 0)
            {
                echo "getCourseForm_callback error: add_staff_to_group form: " . $enrollments['message'];
            }
        ?>

        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "change_course_status_form")
        {        
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Publish Course?</div>
            </div>
            <div class="middle publish_modules">
                <p><b>IMPORTANT:</b> Once a course is published, you can no longer add/remove modules from this course. If you still need to finalize your modules, please do so prior to publishing this course.</p>

                <p>Please confirm that this course has the modules you intended it to have. If you notice any extra or missing modules, please contact us for assistance <b>BEFORE</b> publishing the course:</p>

                <p>
                  <ol>
<?php
                    $modules = getModules($course_id, $portal_subdomain, compact("org_id"));
                    foreach ($modules as $module)
                    {
                      echo "<li>" . $module['title'] . "</li>";
                    }
?>                  
                  </ol>
                </p>

                <p><b>Publish <?= $course_name ?> Course?</b></p>
                <form id= "change_course_status" frm_name="change_course_status" frm_action="changeCourseStatus" rel="submit_form" hasError=0> 
                    <input type="radio" name="status" value="draft" <?php echo ( $status == "draft" ) ? 'checked' : ' '; ?> >No<br>
                    <input type="radio" name="status" value="published" <?php echo ( $status == "published" ) ? 'checked' : ' '; ?> >Yes<br>


                    <input type="hidden" name="org_id" value="<?= $org_id ?>" /> 
                    <input type="hidden" name="group_id" value="<?= $course_id ?>" />
                    <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                    <?php wp_nonce_field( 'change-status-org_id_' . $org_id ); ?>
                </form>
            </div>      
            <div class="popup_footer">
                <div class="buttons">
                  <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="change_status" style="display:none"></i>
                  <a onclick="jQuery(document).trigger('close.facebox');" class="negative">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/cross.png" alt=""/>
                      Cancel
                  </a>
                  <a active = '0' acton = "change_course_status" rel = "submit_button" class="positive" onclick="jQuery('#change_status').show();">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/> 
                    Save
                  </a>
                </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "invite_staff_register")
        {        
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
        <div class="title">
            <div class="title_h2">Invite Staff to Register</div>
        </div>
        <div class="middle" style ="font-size:11px;clear:both;">
            <div class="fixed_fb_width">
                <div class="msgboxcontainer_no_width">
                    <div class="msg-tl">
                        <div class="msg-tr"> 
                            <div class="msg-bl">
                            <div class="msg-br">
                                <div class="msgbox">
                                    <p>Staff will receive an e-mail with a hyperlink (containing a unique code) that lets them register
                            and be automatically placed in the <b>Group 1</b> Group.</p>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h2>Choose an Invitation Method:</h2>
                <ol>
                    <li>
                        <h3>Use our Invitation Sender</h3>
                        <ul>
                            <li>
                                Just copy-and-paste the staff e-mail addresses and we'll send out the invitation e-mail.
                            </li>
                            <li>
                                You can <b>Track</b> those who haven't registered yet by viewing the <b>Invite List Report</b>.
                            </li>
                        </ul>
                    </li>
                    <li>
                        <h3>Use your own E-mail (Hotmail, GMail, etc.)</h3>
                        <ul>
                            <li>
                                We give you the hyperlink to copy-and-paste into an e-mail and you send it yourself.
                            </li>
                            <li>
                                You <b><u>cannot</u> Track</b> those who haven't registered yet.
                            </li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>
        <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
            <div class="buttons" >        
                <a class="back_fb" data-curr_view="invite_staff" data-prev_view="main">
                    <div style="height:15px;padding-top:2px;"> 
                        Back
                    </div>
                </a>
                <a class = "use_own_email" >
                    <div style="height:15px;padding-top:2px;"> 
                        Use your own Email
                    </div>
                </a>
                <a class = "use_invitation_email" >
                    <div style="height:15px;padding-top:2px;"> 
                        Use our Invitation Sender
                    </div>
                </a>            
                <div style="clear:both">
                </div>                      
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "use_invitation_email")
        {        
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $status = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">
                    Email Addresses
                </div>
            </div>
            <div class="middle" style ="font-size:11px;clear:both;">
                <div class="fixed_fb_width">
                    <div class="msgboxcontainer_no_width">
                        <div class="msg-tl">
                            <div class="msg-tr"> 
                                <div class="msg-bl">
                                    <div class="msg-br">
                                        <div class="msgbox">
                                            <p>
                                                Paste a list of e-mail addresses in the field below.
                                            </p>
                                            <div class="small">
                                                <b>Format of E-mail Addresses:</b>
                                                 The format should be one of the following:
                                                <ul>
                                                    <li>
                                                        comma-separated list, or
                                                    </li>
                                                    <li>
                                                        one e-mail address per line (no comma), or
                                                    </li>
                                                    <li>
                                                        a combination of commas and one-per-line
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="" method="post" id="invite_emails">
                        <textarea name="emails" style="width: 600px; height: 300px;">
john@sample.com, jane@email.com
OR
john@sample.com
jane@email.com
                        </textarea>
                    </form>
                </div>
            </div>
            <div class="popup_footer" style = "background-color:#FFF; padding:15px 15px 5px 15px;">
                <div class="buttons" >        
                    <a class="back_fb update_email_textbox" data-curr_view="invite_send_email" data-prev_view="invite_staff">
                        <div style="height:15px;padding-top:2px;"> 
                            Back
                        </div>
                    </a>
                    <a class = "use_invitation_msg" >
                        <div style="height:15px;padding-top:2px;"> 
                            Next
                        </div>
                    </a>
                    <div style="clear:both">
                    </div>                      
                </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "delete_staff_account")
        {        
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
            $staff_id = filter_var($_REQUEST['staff_id'],FILTER_SANITIZE_NUMBER_INT);
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $email =  sanitize_email( $_REQUEST['email'] );
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Delete Staff Account</div>
            </div>
            <div class="middle">
              <form id= "delete_staff_account" frm_name="delete_staff_account" frm_action="deleteStaffAccount" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Are you sure that you want to delete this staff account? 
                      <input type="hidden" name="org_id" id="org_id" value="<?= $org_id ?>" /> 
                      <input type="hidden" name="email" id="email" value="<?= $email ?>" /> 
                      <input type="hidden" name="staff_id" id="staff_id" value=" <?= $staff_id ?>" />
                      <input type="hidden" name="portal_subdomain" value="<?= $portal_subdomain ?>" />
                        <?php wp_nonce_field( 'delete-staff_id-org_id_' . $org_id ); ?>
                    </td>
                  </tr> 
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="delete_staff" style="display:none"></i>
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a active = '0' acton = "delete_staff_account" rel = "submit_button" onclick="jQuery('#delete_staff').show();">
                  <div style="height:15px;padding-top:2px;"> Delete</div>
                </a>
              </div>
            </div>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "edit_questionnaire_conditions")
        {
            $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT); //question id
            $answer_number = filter_var($_REQUEST['answer_number'],FILTER_SANITIZE_NUMBER_INT);     //answer number
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            $all_videos = grabVideos();                                                             //list of all exisiting videos

            //List of all courses
            $courses = getCourses(DEFAULT_SUBDOMAIN, 1);

            //This variable will contain all course ids based on their name
            $course_ids = array();

            //this loop will put the course ids based on name into course_ids
            foreach ($courses as $course)
            {
                $course_ids[$course['name']] = $course['id'];
            }
            
            ob_start();
        ?>
            <div class="title">
                <div class="title_h2">Conditions</div>
            </div>
            <div>
            <img style="margin-right:60em;" class="loader" src="<?= get_template_directory_uri() . "/images/loading.gif"?>" hidden>
            </div>
            <!-- question id, answer number and library id are stored here using a data structure for consistency -->
            <div class="middle" data-question="<?= $question_number ?>" data-answer="<?= $answer_number ?>" data-library="<?= $library_id ?>">
                <!-- Following 6 lines set up the tabs -->
                <ul class="tab">
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'new-staff')">New Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'returning-staff')">Returning Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'program-staff')">Program Staff</a></li>
                    <li><a href="#" class="tablinks" onclick="openCity(event, 'supervisory-staff')">Supervisory Staff</a></li>
                </ul>
                <div id= "new-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="1">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 1);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of New Staff course
                                $new_staff_modules = getModules($course_ids['New Staff'], DEFAULT_SUBDOMAIN);

                                //this variable will contain the modules for New Staff course in a desired format
                                $new_staff_videos = array();

                                //this loop helps create new_staff_videos
                                foreach ($new_staff_modules as $module) {
                                    array_push($new_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $new_staff_videos), array_diff($new_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "returning-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="2">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 2);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Returning Staff course
                                $returning_staff_modules = getModules($course_ids['Returning Staff'], DEFAULT_SUBDOMAIN);

                                //this variable will contain the modules for Returning Staff course in a desired format
                                $returning_staff_videos = array();

                                //this loop helps create returning_staff_videos
                                foreach ($returning_staff_modules as $module) {
                                    array_push($returning_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $returning_staff_videos), array_diff($returning_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "program-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="3">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 3);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Program Staff course
                                $program_staff_modules = getModules($course_ids['Program Staff'], DEFAULT_SUBDOMAIN);

                                //this variable will contain the modules for Program Staff course in a desired format
                                $program_staff_videos = array();

                                //this loop helps create program_staff_videos
                                foreach ($program_staff_modules as $module) {
                                    array_push($program_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $program_staff_videos), array_diff($program_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
                <div id= "supervisory-staff" class="tabcontent">
                    <form id= "edit_questionnaire_conditions" frm_name="edit_questionnaire_conditions" frm_action="editQuestionnaireConditions" rel="submit_form" hasError=0>
                        <table padding=0 class="condition-form" data-course-name-id="4">
                            <tr>
                                <th>
                                    Video Name
                                </th>
                                <th>
                                    Included
                                </th>
                                <th>
                                    Not Included
                                </th>
                            </tr>
                            <?php

                                //grab all conditions
                                $all_conditions = grabConditions($question_number, $answer_number, 4);

                                //this variable will contain the conditions in a desired format 
                                $all_conditions_fixed = array();

                                //this loop helps create all_conditions_fixed
                                foreach ($all_conditions as $condition)
                                {
                                    array_push($all_conditions_fixed, $condition[0]);
                                }

                                //Getting all modules of Supervisory Staff course
                                $supervisory_staff_modules = getModules($course_ids['Supervisory Staff'], DEFAULT_SUBDOMAIN);

                                //this variable will contain the modules for Supervisory Staff course in a desired format
                                $supervisory_staff_videos = array();

                                //this loop helps create supervisory_staff_videos
                                foreach ($supervisory_staff_modules as $module) {
                                    array_push($supervisory_staff_videos, $module['title']);
                                }

                                //this variable includes the Union of two arrays without the intersection
                                $included_videos = array_merge(array_diff($all_conditions_fixed, $supervisory_staff_videos), array_diff($supervisory_staff_videos, $all_conditions_fixed));

                                //this loop will print the video titles and their radio buttons
                                foreach ($all_videos as $video) {

                                    //video id
                                    $id = $video->id;

                                    //video name
                                    $name = $video->name;

                                    //if the name is in the included_videos array, then the 'included' radio button will be checked
                                    if(in_array($name, $included_videos))
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included" checked><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included"><br></td></tr>';
                                    }

                                    //if the name is not in the included_videos array, then the 'not included' radio button will be checked
                                    else
                                    {
                                        echo '<tr><td>' . $name . '</td><td data-id="' . $id . '" data-change="Add"><input type="radio" name="inclusion' . $id . '" value="included"><br></td><td data-id="' . $id . '" data-change="Remove"><input type="radio" name="inclusion' . $id . '" value="not included" checked><br></td></tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
            </div>
            </img>
            <div class="popup_footer">
                <div class="buttons">
                  <a onclick="jQuery(document).trigger('close.facebox');" class="positive">
                    <img src="<?php bloginfo ('stylesheet_directory'); ?>/images/tick.png" alt=""/>
                      Done
                  </a>
                </div>
            </div>
            <script>

                //This function deals with the tab functionality
                function openCity(evt, cityName) {
                    // Declare all variables
                    var i, tabcontent, tablinks;

                    // Get all elements with class="tabcontent" and hide them
                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }

                    // Get all elements with class="tablinks" and remove the class "active"
                    tablinks = document.getElementsByClassName("tablinks");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(" active", "");
                    }

                    // Show the current tab, and add an "active" class to the link that opened the tab
                    document.getElementById(cityName).style.display = "block";
                    evt.currentTarget.className += " active";
                }

                //This function will trigger when a radio button is checked
                $('input:radio').change(function(){
                    var video_id = $(this).parent().attr('data-id');                                                //video id
                    var change = $(this).parent().attr('data-change');                                              //this variable contains "Add" or "Remove"
                    var course_name_id = $(this).parent().parent().parent().parent().attr('data-course-name-id');   //course_name_id
                    var question_number = $('.middle').attr('data-question');                                       //question id
                    var answer_number = $('.middle').attr('data-answer');                                           //answer number
                    var library_id = $('.middle').attr('data-library');                                             //library id

                    //data is used for the ajax call
                    //contains variable needed by the PHP function to make changes to the database
                    var data = {
                        action: 'changeCondition',
                        video_id: video_id,
                        question_number: question_number,
                        answer_number: answer_number,
                        course_name_id: course_name_id,
                        library_id: library_id,
                        change: change
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //hide radio buttons and show loading gif
                    var tempScrollTop = $(window).scrollTop();
                    $('.middle, .loader').toggle();

                    //ajax call to change conditions in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            //show radio buttons and hide loading gif
                            $('.middle, .loader').toggle();
                            $(window).scrollTop(tempScrollTop);
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });
            </script>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "delete_question_questionnaire")
        {
            $question_number = filter_var($_REQUEST['question_number'],FILTER_SANITIZE_NUMBER_INT); //question id
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Delete Question</div>
            </div>
            <div class="middle">
              <form id= "delete_question" frm_name="delete_question" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Are you sure that you want to delete this question? 
                    </td>
                  </tr> 
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a rel = "submit_button" >
                  <div class = "delete-question" data-question="<?= $question_number ?>" data-library="<?= $library_id ?>" style="height:15px;padding-top:2px;"> Delete</div>
                </a>
              </div>
            </div>
            <script>
                $('div.delete-question').click(function()
                {
                    var question_id = $(this).attr('data-question');        //question id
                    var library_id = $(this).attr('data-library');          //library id

                    var data = {
                        action: 'deleteQuestion',
                        question_id: question_id,
                        library_id: library_id
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //ajax call to delete question in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            jQuery(document).trigger('close.facebox');
                            location.reload();
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });
            </script>
        <?php
        $html = ob_get_clean();
        }
        else if($form_name == "add_question_questionnaire")
        {
            $library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);           //library id
            ob_start();
        ?>
            <div class="title">
              <div class="title_h2">Add Question</div>
            </div>
            <div class="middle">
              <form id= "add_question" frm_name="add_question" rel="submit_form" hasError=0> 
                <table padding=0 class="form"> 
                  <tr> 
                    <td class="value"> 
                      Question: <input id="question" type="text">
                    </td>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                    <td>
                        Answers:
                    </td>
                  </tr>
                  </tr>
                    <td>
                        <br>
                    </td>
                  <tr>
                  <tr>
                    <td>
                        <div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                        <div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div>
                    </td>
                  </tr>
                </table> 
              </form>
            </div>      
            <div class="popup_footer">
              <div class="buttons">
                <a onclick="jQuery(document).trigger('close.facebox');" >
                  <div style="height:15px;padding-top:2px;"> Cancel</div>
                </a>
                <a rel = "submit_button" >
                  <div class = "add-question" data-library="<?= $library_id ?>" style="height:15px;padding-top:2px;"> Add</div>
                </a>
              </div>
            </div>
            <script>

                $('div.add-question').click(function()
                {
                    var library_id = $(this).attr('data-library');      //library id
                    var question = $('input#question').val();           //question text

                    //gather all answers
                    var all_answers_array = $('table.form').find('.answer').map(function() { return $(this).val(); }).get();
                    var all_answers_object = {};

                    //This loop will make an object so that all answers can be converted to JSON
                    for(var i = 1; i<=all_answers_array.length;i++)
                    {
                        all_answers_object[i.toString()] = all_answers_array[i-1];
                    }

                    //this will convert the object to JSON string
                    var answer = JSON.stringify(all_answers_object);

                    var data = {
                        action: 'addQuestion',
                        library_id: library_id,
                        question: question,
                        answer: answer
                    };

                    //url for ajax call
                    var url =  ajax_object.ajax_url;

                    //ajax call to add question in database
                    $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success:
                        function(data)
                        {
                            jQuery(document).trigger('close.facebox');
                            location.reload();
                        },
                        error:
                        function(data)
                        {
                            alert("Something went wrong");
                        }
                    });
                });

                $('i.plus').live("click", function()
                {
                    $(this).parent().parent().parent().after('<tr><td><div><i class="fa fa-minus-square minus" aria-hidden="true"></i>   <i class="fa fa-plus-square plus" aria-hidden="true"></i>   <input class="answer" type="text"></div></td></tr>');
                });

                $('i.minus').live("click", function()
                {
                    $(this).parent().parent().parent().remove();
                });
            </script>
        <?php
        $html = ob_get_clean();
        }
        else
        {
            $html = "<p>Invalid form.</p>";
        }
    }
    else
    {   
        $error_path = get_site_url() . "/subscribe/dashboard/?part=error";
        // Someone is fooling our system. Redirect to error page.
        header("Location: " . $error_path . "");
        die();
    }

    echo $html;

    wp_die();
}

/********************************************************************************************************
 * This processed the creation of a course.
 * @param int $org_id - Organization ID
 * @param int $user_id - User ID from wordpress
 * @param string $org_subdomain - Subdomain name
 * @param string $course_name - Name of the course
 * @param string $course_description - description of the course
 *******************************************************************************************************/
add_action('wp_ajax_createCourse', 'createCourse_callback'); 
function createCourse_callback ( ) {
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['name'] ) && isset ( $_REQUEST['user_id'] ) && isset ( $_REQUEST['subscription_id'] ) )
    {
        // This form is generated in getCourseForm function from this file.
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $user_id = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);
        $org_subdomain =  get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user
        $subscription_id    = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); // The subscription ID

        $course_name  = trim(filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING));
        $course_description  = filter_var($_REQUEST['desc'],FILTER_SANITIZE_STRING);

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'create-course_' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'createCourse_callback error: Sorry, your nonce did not verify.';
        }
        else if(empty($_REQUEST['name']))
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'createCourse_callback error: Please Enter the <b>Name</b> of the course.';
        }
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['display_errors'] = 'success';
            $result['errors'] = 'createCourse_callback error: Sorry, you do not have permisison to view this page. ';
        }
        else 
        {
            $course_due_date = ""; //for future use
            $data = compact( "org_id", "user_id", "course_due_date");
            // Add the course
            $response = createCourse($course_name, $org_subdomain, $data );
            if($response['status'] == 0)
            {
                // Build the response if create course failed to execute.
                $result['display_errors'] = true;
                $result['data'] = 'failed';
                $result['errors'] = "Response Message: " . $response['message'];
            }
            elseif ($response['status'] == 1) 
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'Course has been created';
                $result['success'] = true;
                $result['group_name'] = $course_name;
                $result['group_id'] = $response['id'];
                $result['group_desc'] = $course_description;
                $result['subscription_id'] = $subscription_id;
                $result['portal_subdomain'] = $org_subdomain;
            }
            else
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR: Could not create the course name.";
            }

        }
        echo json_encode($result);

    }
    wp_die();
}


/********************************************************************************************************
 * Updating a course name/description
 *******************************************************************************************************/
add_action('wp_ajax_updateCourse', 'updateCourse_callback'); 
function updateCourse_callback ( ) {
    if( isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['name'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = edit_course_group from this file.
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $course_name  = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
        $course_description  = filter_var($_REQUEST['desc'],FILTER_SANITIZE_STRING);
        $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'edit-course_' . $org_id ) ) 
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'edit course error: Sorry, your nonce did not verify.';
        }
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['display_errors'] = 'Failed';
            $result['success'] = false;
            $result['errors'] = 'edit course error: Sorry, you do not have permisison to view this page. ';
        }
        else 
        {
            $data = compact( "org_id", "course_name");
            // Edit the course
            $response = updateCourse($course_id, $portal_subdomain, $data);
            if ($response['status'] == 0)
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Response Message: " . $response['message'];

            }
            else if($response['status'] == 1)
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'Course has been updated';
                $result['success'] = true;
                $result['group_name'] = $course_name;
                $result['group_id'] = $course_id;
                $result['group_desc'] = $course_description;
            }
            else
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR: Could not update the course name.";
            }

        }
        echo json_encode($result);
    }
    wp_die();
}

/********************************************************************************************************
 * send e-mail base on type
 *******************************************************************************************************/
add_action('wp_ajax_sendMail', 'sendMail_callback'); 
function sendMail_callback() {
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
    $target = filter_var($_REQUEST['target'],FILTER_SANITIZE_STRING);

    // Check permissions
    if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'wp_ajax_sendMail error: Sorry, you do not have permisison to view this page. ';
        return $result;
    }

    else if( $target == "create_account" )
    {
        $message = stripslashes($_REQUEST['composed_message']); // Remove backward slash from GET. This fixed the problem for sending message with colored fonts.
        $subject = filter_var($_REQUEST['subject'],FILTER_SANITIZE_STRING);
        $name = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
        $email = sanitize_email($_REQUEST['email']);

        $recepients = array(); // List of recepients

        $recepient = array (
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'subject' => $subject
        );

//        $recepients = array_merge($recepients, array($recepient['email'] => $recepient));
        array_push($recepients, $recepient);
        $data = compact( "org_id" );

        $response = sendMail( $target, $recepients, $data );

        if($response['status'] == "0")
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "Error in sendMail_callback: " . $response['message'];
        }
        else if($response['status'] == "1")
        {
            $result['display_errors'] = false;
            $result['success'] = true;
        }
        else
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "ERROR in sendMail_callback: Could not email the user";
        }
    echo json_encode($result);
    }
    wp_die();
}

/********************************************************************************************************
 * get a list of users enrolled in a course
 *******************************************************************************************************/
add_action('wp_ajax_getUsersInCourse', 'getUsersInCourse_callback'); 
function getUsersInCourse_callback() {
    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) )
    {

        // Get the Post ID from the URL
        $course_id          = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);

        $info_data = array("org_id" => $org_id);

        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'LU Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            // Build the response if successful
            // get the users who are enrolled in the course
            $users = getUsersInCourse($course_id, $portal_subdomain, $info_data);

            /*********************************************************************************************
            * Create HTML template and return it back as message. this will return an HTML div set to the 
            * javascript and the javascript will inject it into the HTML page.
            **********************************************************************************************/
            $html = '<div  id="staff_and_assignment_list_pane" class="scroll-pane" style = "width: 350px">';
            $html .= '  <div style = "width:100%;">';

            if( $users && count($users) > 0 ) 
            {
                usort($users, "sort_first_name"); // sort the users by first name
                foreach( $users as $user )
                {
                    $html .= '<div class ="staff_and_assignment_list_row">';
                    $html .= '<span class="staff_name">' . $user['first_name'] . ' ' . $user['last_name'] . '</span>';
                    $html .= '</div>';
                }
                $html .= '   </div>'; 
                $html .= '</div>';  

                $result['staff_count'] = count($users);
                $result['data'] = 'success';
                $result['message'] = $html;
                $result['group_id'] = $course_id; // if not included, when clicking on manage assignment/course, it will not open the dialog box.
            }
            else if( count($users) == 0 )
            {
                $result['staff_count'] = 0;
                $result['data'] = 'failed';
                $result['message'] = '<p>There are no users enrolled in this course.</p>';
            }
            else 
            {
                $result['data'] = 'failed';
                $result['message'] = 'Error in getting enrolled users for course id: ('. $course_id .')';
            }
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Change the course status to draft or published
 *******************************************************************************************************/
add_action('wp_ajax_changeCourseStatus', 'changeCourseStatus_callback');
function changeCourseStatus_callback () {
    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['status'] ) )
    {
        // This form is generated in getCourseForm function with $form_name = change_course_status_form from this file.
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $status  = filter_var($_REQUEST['status'],FILTER_SANITIZE_STRING);
        $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $info_data = array("org_id" => $org_id);

        // Check permissions
        if( ! wp_verify_nonce( $_POST['_wpnonce'] ,  'change-status-org_id_' . $org_id ) ) 
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'deleteCourse_callback error: Sorry, your nonce did not verify.';
        }
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'updateuser_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else
        {
            // Change the status of the course to public of draft.
            if($status == 'published')
            {
                $response = publishCourse($course_id, $portal_subdomain, $info_data);
                if($response['status'] == 1)
                {
                    // Build the response if successful
                    $result['data'] = 'success';
                    $result['course_id'] = $course_id;
                    $result['status'] = 'published';
                    $result['success'] = true;
                }
                else
                {
                    return $response;
                }
            }
            else if($status == 'draft')
            {
                // Ran DRAFT 
                $result['data'] = 'success';
                $result['course_id'] = $course_id;
                $result['status'] = 'draft';
                $result['success'] = true;
            }
            $result['status'] = $status;
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }
    echo json_encode($result);
    wp_die();

}

/********************************************************************************************************
 * Update the portal
 *******************************************************************************************************/
add_action('wp_ajax_updatePortal', 'updatePortal_callback');
function updatePortal_callback () {
 if( isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['org_id'] ))
    {
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
        {
            $result['display_errors'] = 'failed';
            $result['success'] = false;
            $result['errors'] = 'updatePortal_callback Error: Sorry, you do not have permisison to view this page.';
        }
        else
        {
//            $link = esc_url($_REQUEST['logo_image_url']); // logo image URL
            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING); 
            $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // the org ID for the user in WP postmeta table
            $portal_id = filter_var(get_post_meta ( $org_id, 'lrn_upon_id', true ),FILTER_SANITIZE_NUMBER_INT); // the portal ID from WP Postmeta table
            // remove the 3 mandatory parameters from $_REQUEST and pass it into updatePortal
            unset($_REQUEST['portal_subdomain']);
            unset($_REQUEST['org_id']);
            unset($_REQUEST['action']);

//            $info_data = array("logo_image_url" => $link);
            
            $response = updatePortal($portal_id, $portal_subdomain, $_REQUEST);
            
            if($response['status'] == "0")
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Error in updatePortal_callback: " . $response['message'];
            }
            else if($response['status'] == "1")
            {
                $result['display_errors'] = false;
                $result['success'] = true;
            }
            else
            {
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR in updatePortal_callback: Could not update the portal";
            }
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Toogle on and off module in a course
 *******************************************************************************************************/
add_action('wp_ajax_toggleItemInAssignment', 'toggleItemInAssignment_callback'); 
function toggleItemInAssignment_callback() {

    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['item'] ) && isset ( $_REQUEST['item_id'] ) )
    {
        $course_id          = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
        $item               = filter_var($_REQUEST['item'],FILTER_SANITIZE_STRING);;
        $item_id            = filter_var($_REQUEST['item_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $info_data          = array("org_id" => $org_id, "module_id" => $item_id);
        // get modules of the course
        //deleteModule($course_id, $portal_subdomain, $info_data);
        //$result['message'] = AddModule($course_id, $portal_subdomain, $info_data);
        $response = toggleItemInAssignment($course_id, $portal_subdomain, $info_data);

        echo json_encode($response);
    }
    wp_die();
}

/**
 * Get all the enrollments of the user
 *  @param int $user_id - the user ID
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of enrollments
 */
function getEnrollmentsByUserId($user_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()){
    extract($data);

   /*
    * Variables required in $data
    * org_id - the organization ID 
    */
    if ($user_id <= 0) // Check for invalid course
    {
        return array('status' => 0, 'message' => "ERROR in getEnrollmentsByUserId: Invalid user id.");
    }

    $url = select_lrn_upon_url ($portal_subdomain, "enrollments/search?user_id=" . $user_id);

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
        $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
    }
    else
    {
        $response = execute_communication($url, '', 'GET'); 
    }

    //checks for errors when getting enrollments
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "LUERROR in getEnrollmentsByUserId:" . $response['message']);
    }    
    else if (isset($response['enrollments'][0]['id'])) 
    {
        return array('status' => 1, 'enrollments' => $response['enrollments']);
    } 
    else
    {
        return null;
    }
}

/**
 * Delete the user from the enrollment in the course based on enrollment id
 *
 * @param string $enrollment_id - The enrollment id 
 * @param string $portal_subdomain - The subdomain name of the portal
 **/
function deleteEnrolledUser($enrollment_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data) {
    extract($data);
    /*
    * Variables required in $data
    * org_id - the organization ID
    */

    if($enrollment_id == 0)
        return array('status' => 0, 'message' => "ERROR in deleteEnrolledUser: invalid enrollment id");

    $url = select_lrn_upon_url ($portal_subdomain, "enrollments/" . $enrollment_id);

    if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
        $response = execute_communication($url, '', 'DELETE', $portal_username, $portal_password); 
    }
    else
    {
        $response = execute_communication($url, '', 'DELETE'); 
    }

    //checks for errors when deleting enrollment
    if(isset($response['message'])) 
    {
        return array('status' => 0, 'message' => "Error in deleteEnrolledUser: " . $response['message']);
    }    
    else 
    {
        return array('status' => 1);
    } 
}

/********************************************************************************************************
 * Create enrollment for the user
 *******************************************************************************************************/
add_action('wp_ajax_enrollUserInCourse', 'enrollUserInCourse_callback'); 
function enrollUserInCourse_callback () {

    // This form is generated in getCourseForm function with $form_name = add_staff_to_group from this file.
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // The organization ID
    $email  = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING); // The Email Address of the user
    $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT); // The Course ID
    $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING); // The course Name
    $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);

    // Check permissions
    if(wp_verify_nonce( $_REQUEST['nonce'], 'process-userEmail_' . $email ) )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'enrollUserInCourse_callback error: Sorry, your nonce did not verify.';
    }
    else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'enrollUserInCourse_callback error: Sorry, you do not have permisison to view this page.';
    }
    else 
    {
        $data = compact( "org_id", "course_name");
        // Enroll the user to the course
        $response = enrollUserInCourse($email, $portal_subdomain, $data);
        if ($response['status'] == 0)
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "enrollUserInCourse_callback error: " . $response['message'];

        }
        else if($response['status'] == 1)
        {
            // Build the response if successful
            $result['message'] = 'User has been enrolled.';
            $result['success'] = true;
            $result['enrollment_id'] = $response['enrollment_id'];
        }
        else
        {
            // return an error message
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "enrollUserInCourse_callback : Could not enroll user in course";
        }
    }
    echo json_encode($result);
    wp_die();
}


/********************************************************************************************************
 * Toogle on and off module in a course
 *******************************************************************************************************/
add_action('wp_ajax_getVideosInGroup', 'getVideosInGroup_callback'); 
function getVideosInGroup_callback() {
    if( isset ( $_REQUEST['group_id'] ) && isset ( $_REQUEST['org_id'] ) && isset ( $_REQUEST['portal_subdomain'] ) )
    {
        $course_id          = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain   = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
        $org_id             = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $info_data          = array("org_id" => $org_id);

        // check if user has admin/manager permissions
        if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
        {
            $result['data'] = 'failed';
            $result['message'] = 'LU Error: Sorry, you do not have permisison to view this page. ';
        }
        else
        {
            $modules = getModules( $course_id, $portal_subdomain, $info_data ); // All the modules in the course.

            /*********************************************************************************************
            * Create HTML template and return it back as message. this will return an HTML div set to the 
            * javascript and the javascript will inject it into the HTML page.
            **********************************************************************************************/
            $html = '<div  id="staff_and_assignment_list_pane" class="scroll-pane" style ="width: 350px"><div style="width:100%;">';
            if(count($modules > 0))
            {
                foreach ($modules as $module)
                {
                    $html .= '
                        <div class ="staff_and_assignment_list_row" onmouseover="Tip(\'tiptip\', WIDTH, 240, DELAY, 5, FADEIN, 300, FADEOUT, 300, BGCOLOR, \'#E5E9ED\', BORDERCOLOR, \'#A1B0C7\', PADDING, 9, OPACITY, 90, SHADOW, true, SHADOWWIDTH, 5, SHADOWCOLOR, \'#F1F3F5\',TITLE,\'Description\'\')" onmouseout="UnTip()">
                            <span class="staff_name">' . $module['title'] . '
                            </span>
                        </div>
                    ';
                }
                $html .= '</div></div>';
                $result['message'] = $html;
                $result['group_id'] = $course_id;
                $result['video_count'] = count($modules);
            }
            else if( count($modules) == 0 )
            {
                $result['data'] = 'failed';
                $result['message'] = '<p>There are no modules in this course.</p>';
            }
            else 
            {
                $result['data'] = 'failed';
                $result['message'] = 'Error in getting modules for course id: ('. $course_id .')';
            }
        }
    }
    else
    {
        $result['data'] = 'failed';
        $result['message'] = '<p>Unable to process your request</p>';
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Delete the enrollment for the user
 *******************************************************************************************************/
add_action('wp_ajax_deleteEnrolledUser', 'deleteEnrolledUser_callback'); 
function deleteEnrolledUser_callback () {

    // This form is generated in getCourseForm function with $form_name = add_staff_to_group from this file.
    $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
    $email  = filter_var($_REQUEST['email'],FILTER_SANITIZE_STRING);
    $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
    $enrollment_id = filter_var($_REQUEST['enrollment_id'],FILTER_SANITIZE_NUMBER_INT);
    $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);

    // Check permissions
    if(wp_verify_nonce( $_REQUEST['nonce'], 'process-userEmail_' . $email ) )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'deleteEnrolledUser_callback Error: Sorry, your nonce did not verify.';
    }
    else if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
    {
        $result['display_errors'] = 'Failed';
        $result['success'] = false;
        $result['errors'] = 'deleteEnrolledUser_callback Error: Sorry, you do not have permisison to view this page.';
    }
    else 
    {
      $data = compact("org_id");

      $response = deleteEnrolledUser($enrollment_id, $portal_subdomain, $data);
      if ($response['status'] == 0)
      {
          // return an error message
          $result['display_errors'] = true;
          $result['success'] = false;
          $result['errors'] = "Response Message: " . $response['message'];

      }
      else if($response['status'] == 1)
      {
          // Build the response if successful
          $result['message'] = 'User has been deleted from the course.';
          $result['success'] = true;
      }
      else
      {
          // return an error message
          $result['display_errors'] = true;
          $result['success'] = false;
          $result['errors'] = "deleteEnrolledUser_callback ERROR: Could not delete the user from the course.";
      }
    }
    echo json_encode($result);
    wp_die();
}

/********************************************************************************************************
 * Update due date for the course.
 *******************************************************************************************************/
add_action('wp_ajax_updateDueDate', 'updateDueDate_callback'); 
function updateDueDate_callback() {
    if( isset ( $_REQUEST['action'] ) && isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['task'] ) && isset ( $_REQUEST['portal_subdomain'] ) && isset ( $_REQUEST['org_id'] ) )
    {
        if($_REQUEST['task'] == "remove" || $_REQUEST['task'] == "add")
        {
            $course_id                   = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID
            $portal_subdomain            = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING); // the portal subdomain
            $org_id                      = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT); // The org id
            $task                        = filter_var($_REQUEST['task'],FILTER_SANITIZE_STRING); // The task
            
            // check if user has admin/manager permissions
            if( !current_user_can ('is_director') && !current_user_can ('is_sales_rep') )
            {
                $result['display_errors'] = 'Failed';
                $result['success'] = false;
                $result['errors'] = 'updateDueDate_callback Error: Sorry, you do not have permisison to view this page.';
            }
            else
            {
                // This sets the due date
                if( $task == "remove" )
                {
                    $due_date_after_enrollment = "null"; // Remove the date.
                }
                else if( $task == "add" )
                {
                    $due_date_after_enrollment = filter_var($_REQUEST['date'],FILTER_SANITIZE_STRING); // The due date 
                }

                $data = compact( "org_id", "due_date_after_enrollment");
                // Edit the course
                $response = updateCourse($course_id, $portal_subdomain, $data);
                if ($response['status'] == 0)
                {
                    $result['display_errors'] = true;
                    $result['success'] = false;
                    $result['errors'] = "updateDueDate_callback ERROR: ". $response['message'];

                }
                else if($response['status'] == 1)
                {
                    // Build the response if successful
                    $result['success'] = true;
                }
                else
                {
                    // return an error message
                    $result['display_errors'] = true;
                    $result['success'] = false;
                    $result['errors'] = "updateDueDate_callback ERROR: Could not update the course.";
                }
            }
        }
        else
        {
            $result['display_errors'] = true;
            $result['success'] = false;
            $result['errors'] = "updateDueDate_callback ERROR: invalid task.";
        }
    }
    else
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = "updateDueDate_callback ERROR: Missing parameters";
    }
    echo json_encode($result);
    wp_die();
}


/***************************************************************************
 *
 * TEST AJAX CALLS
 * URL: http://eot.dev/wp-admin/admin-ajax.php?action=TESTAJAX&portal_subdomain=winbaum&course_name=Expert Online Training Tutorial&course_id=66810&org_id=199
 *
 **************************************************************************/
add_action('wp_ajax_TESTAJAX', 'TESTAJAX_callback'); 
function TESTAJAX_callback ( ) {


// testing manage module section
        global $current_user;
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $form_name = filter_var($_REQUEST['form_name'],FILTER_SANITIZE_STRING);
        $user_id = $current_user->ID;

            $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
            $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
            $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);

            $data = array( "org_id" => $org_id ); // to pass to our functions above

            $course_modules = getModules($course_id, $portal_subdomain, $data); // all the modules in the specified course
            $course_modules_titles = array_column($course_modules, 'title'); // only the titles of the modules in the specified course
var_dump($course_modules);
            $modules_in_portal = getModules(0,$portal_subdomain,$data); // all the modules in this portal
var_dump($modules_in_portal);
wp_die();

            $master_course = getCourseByName(lrn_upon_LE_Course_TITLE, $portal_subdomain, $data); // Get the master course. Cloned LE
            $master_course_id = $master_course['id']; // Master library ID
            $master_modules = getModules($master_course_id, $portal_subdomain, $data); // Get all the modules from the master library (course).
            $master_modules_titles = array_column($master_modules, 'title'); // only the titles of the modules from the master library (course).
            $user_modules_titles = array_column($modules_in_portal, 'title'); // only the titles of the modules from the user library (course).
            $course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
            $due_date = $course_data['due_date_after_enrollment']; // the due date of the specified course
            $subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT); //  The subscription ID


var_dump($master_modules);

  wp_die();
/*
// TEST creating portal on LU

        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $org_name  = filter_var($_REQUEST['org_name'],FILTER_SANITIZE_STRING);
        $org_subdomain  = filter_var($_REQUEST['org_subdomain'],FILTER_SANITIZE_STRING);
        $user_id  = filter_var($_REQUEST['user_id'],FILTER_SANITIZE_NUMBER_INT);
        $first_name = filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING);
        $last_name = filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING);
        $email = filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL);
        $password = $_REQUEST['password'];

    $data = compact ("org_id", "org_name", "org_subdomain", "user_id", "first_name", "last_name", "email", "password");
//var_dump($data);
//echo json_encode($data);
//wp_die();
    $response = communicate_with_learnupon ('create_account', $data);

var_dump($response);

            if ($response['status'] == 0)
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Response Message: " . $response['message'];

            }
            else if($response['status'] == 1)
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'portal created';
                $result['success'] = true;
                $result['group_name'] = $course_name;
            }
            else
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR: Could not create portal";
            }

        echo json_encode($result);
    wp_die();
*/
/* 
        // This form is generated in getCourseForm function with $form_name = edit_course_group from this file.
        $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
        $course_name  = filter_var($_REQUEST['name'],FILTER_SANITIZE_STRING);
        $course_description  = filter_var($_REQUEST['desc'],FILTER_SANITIZE_STRING);
        $course_id = filter_var($_REQUEST['group_id'],FILTER_SANITIZE_NUMBER_INT);
        $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);

            $data = compact( "org_id", "course_name", course_description, course_id, portal_subdomain);
            // Edit the course
            $response = updateCourse($course_id, $portal_subdomain, $data);

var_dump($response);

            if ($response['status'] == 0)
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "Response Message: " . $response['message'];

            }
            else if($response['status'] == 1)
            {
                // Build the response if successful
                $result['data'] = 'success';
                $result['org_id'] = $org_id;
                $result['message'] = 'Course has been updated';
                $result['success'] = true;
                $result['group_name'] = $course_name;
            }
            else
            {
                // return an error message
                $result['display_errors'] = true;
                $result['success'] = false;
                $result['errors'] = "ERROR: Could not update the course name.";
            }

        echo json_encode($result);
    wp_die();
*/
/*
  $portal_subdomain = filter_var($_REQUEST['portal_subdomain'],FILTER_SANITIZE_STRING);
  $course_name = filter_var($_REQUEST['course_name'],FILTER_SANITIZE_STRING);
  $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT);
  $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
  $data = array( "org_id" => $org_id );

  $course_modules = getModules($course_id, $portal_subdomain, $data); // all the modules in the specified course
  $course_modules_titles = array_column($course_modules, 'title'); // only the titles of the modules in the specified course
  $modules_in_portal = getModules('0',$portal_subdomain,$data); // all the modules in this portal
  $master_modules = getModules(lrn_upon_LE_Course_ID, DEFAULT_SUBDOMAIN, array()); // Get all the modules from the master library (course).
  $master_modules_titles = array_column($master_modules, 'title'); // only the titles of the modules from the master library (course).
  $course_data = getCourse($portal_subdomain, $course_id, $data); // all the settings for the specified course
  $due_date = $course_data[0]['due_date_after_enrollment']; // the due date of the specified course


  foreach($modules_in_portal as $key => $module) // go thourh all the modules in our portal
  {
    echo "Testing if module in portal: " . $module['title'] . " is in the master modules titles --->";
    if(!in_array($module['title'], $master_modules_titles)) 
    {
      echo "NO <br>";
    } 
    else 
    {
      echo "Yes <br>";
    }
  }
*/                       
/*
  echo "Modules in portal";
  var_dump($modules_in_portal);


  echo "Course Modules";
  var_dump($course_modules);
/*
  echo "Course Modules Title";
  var_dump($course_modules_titles);

  echo "master modules";
  var_dump($master_modules);
*/

/*
  echo "due date: " . $due_date;
  echo "course data";
  var_dump($course_data);
*/
  wp_die();

}

/* Add automatic image sizes
*/
if ( function_exists( 'add_image_size' ) ) { 
    add_image_size( 'presenter-headshot', 210, 350, true );
}

/**
 *   Manage the creation of a staff account in LU and WP 
 *  @param string $portal_subdomain - the subdomain of the portal you want to create a user for
 *  @param array $data - an array of user data to create a new user
 *  @param boolean $wp - to create a WP user or not
 *  @param boolean $wp - to create a LU user or not
 *  @param strong $role - the role of the WP user
 *
 *  @return an array of success or not and messages
 */  
function createWpLuUser($portal_subdomain = DEFAULT_SUBDOMAIN, $data = array(), $wp = true, $lu = true, $role = 'student')
{
   /********************************************************
   * Create Wordpress and LU User
   * org_id - The organization ID
   * first_name - User First Name
   * last_name - User Last Name
   * email - User Email
   * password - User Password
   * @return json encoded list of completion or errors
   ********************************************************/    
  extract($data);
  // Check if all parameters are available.
  if( $org_id != "" && $first_name != "" &&  $last_name != "" &&  $email != "" && $password != "" )
  {
    // need to create LU user
    if ( $lu )
    {
      $response = createUser($portal_subdomain, $data); // create the user in LU
      if(isset($response['id']))
      {
          $result['success'] = true;
          $result['name'] = $first_name;
          $result['lastname'] = $last_name;
          $result['org_id'] = $org_id;
          $result['email'] = $email;
          $result['password'] = $password;
          $result['user_id'] = $response['id']; // LU User ID
          $result['portal_subdomain'] = $portal_subdomain;

          // create the user in WP (student)
          $userdata = array (
              'user_login' => $email,
//              'user_pass' => wp_hash_password($password),
              'user_pass' => $password,
              'role' => $role,
              'user_email' => $email,
              'first_name' => $first_name,
              'last_name' => $last_name
          );
          
          // check if we need to create a WP user as well
          if ( $wp )
          {
            $WP_user_id = wp_insert_user ($userdata);
          }
          else
          {
            // user already existed so get his id
            $WP_user_id = get_user_by('email', $email)->ID;
          }

          // Newly created WP user needs some meta data values added
          update_user_meta ( $WP_user_id, 'lrn_upon_id', $response['id'] );
          update_user_meta ( $WP_user_id, 'org_id', $org_id );
          update_user_meta ( $WP_user_id, 'accepted_terms', '0');
          update_user_meta ( $WP_user_id, 'portal', $portal_subdomain );
      }
      // Expected error
      else if($response['status'] == 0)
      {
          $result['success'] = false;
          $result['display_errors'] = true;
          $result['errors'] = "createWpLuUser Error: " . $response['message'];
          $result['message'] = "createWpLuUser Error: " . $response['message'];
      }
      else 
      {
          $result['success'] = false;
          $result['display_errors'] = 'Failed';
          $result['message'] = "createWpLuUser ERROR: could not create the staff account. Please contact the site administrator";
      }
    }
    else if ( $wp ) 
    {
      // create the WP user but not an LU user
      $userdata = array (
          'user_login' => $email,
//          'user_pass' => wp_hash_password($password),
          'user_pass' => $password,
          'role' => $role,
          'user_email' => $email,
          'first_name' => $first_name,
          'last_name' => $last_name
      );
      $WP_user_id = wp_insert_user ($userdata);
    } 
  }
  else
  {
    $result['success'] = false;
    $result['display_errors'] = 'Failed';
    $result['message'] = "createWpLuUser ERROR: invalid arguments.";
  }
  return $result;
}

/**
 *  enroll a user in courses
 *  @param string $portal_subdomain - the subdomain of the portal you want to create a user for
 *  @param array $courses - an array of course names to enroll the user in
 *
 *  @return result array with succes/failiure
 */  
function enrollUserInCourses($portal_subdomain = DEFAULT_SUBDOMAIN, $courses = array(), $org_id = 0, $email = '')
{
  // make sure we have the data we need.
  if ($email == '' || !$org_id)
  {
    return array('status' => 0, 'message' => 'Error: Invalid arguments supplied to enrollUserInCourses');
  }

  // go through each course and enroll the user
  foreach ($courses as $course_name)
  {
    $data = compact('org_id', 'course_name');
    $response = enrollUserInCourse($email, $portal_subdomain, $data);
    if(isset($response['status']) && !$response['status']) // failed to enroll staff in course
    {
      $result['status'] = 0;
      $result['message'] = "enrollUserInCourses Error: " . $response['message'];
      return $result;
    }
  }
  return array('status' => 1);
}

/********************************************************************************************************
 * Upgrade the user to uber/umbrella manager
 *******************************************************************************************************/
add_action('wp_ajax_upgradeUberUmbrellaManager', 'upgradeUberUmbrellaManager_callback'); 
function upgradeUberUmbrellaManager_callback() 
{
    if( isset ( $_REQUEST['type'] ) && isset ( $_REQUEST['user_id'] ) )
    {
      echo json_encode(array('status' => 0, 'message' => 'Failed to Upgrade '. $_REQUEST['type'] .' user ' . $_REQUEST['user_id']));

    }
    else
    {
      echo json_encode(array('status' => 0, 'message' => 'Failed to Upgrade user...'));
    }
  wp_die();
}

/**
 * Get all the groups present in the portal
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param string $title - if supplied, will search for a group with this specific title
 *  @param array $data - user/camp data to access portal keys etc...
 *
 *  @return json encoded list of groups
 */
function getGroups($portal_subdomain = DEFAULT_SUBDOMAIN, $title = '', $data = array()) 
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
 
  $search_title = (!empty($title)) ? "?title=" . $title : "";
  $url = select_lrn_upon_url ($portal_subdomain, "groups" . $search_title );
  
  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
  }
  else
  {
    $response = execute_communication($url, '', 'GET'); 
  }
  
  //checks for errors when listing the groups
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in getGroups:" . $response['message']);
  }    
  else if (isset($response['groups'][0]['id'])) 
  {
    return $response['groups'];
  } 
  else
  {
    return null;
  }
}

/**
 * Get all the groups and their managers present in the portal
 *  @param string $portal_subdomain - the subdomain of the portal you want the courses from
 *  @param array $data - user/camp data to access portal keys etc...
 *  @param int $group_id - if supplied, will search for a group with this specific group id
 *  @param int $user_id - if supplied, will search for a group with this specific user id
 *  @param int $username - if supplied, will search for a group with this specific username
 *  @param int $email - if supplied, will search for a group with this specific email
 *
 *  @return json encoded list of groups
 */
function getGroupManagers($portal_subdomain = DEFAULT_SUBDOMAIN, $data = array(), $group_id = 0, $user_id = 0, $username = '', $email = '') 
{
  extract($data);
  /*
   * Variables required in $data
   * org_id - the organization ID 
   */
  
  if (!empty($group_id))
  {
    $search = "?group_id=" . $group_id;
  }
  else if (!empty($user_id))
  {
    $search = "?user_id=" . $user_id;
  }
  else if (!empty($username))
  {
    $search = "?username=" . $username;
  }
  else if (!empty($email))
  {
    $search = "?email=" . $email;
  }
  else
  {
    $search = "";
  }  

  $url = select_lrn_upon_url ($portal_subdomain, "group_managers" . $search);
  
  if ($portal_subdomain != DEFAULT_SUBDOMAIN) 
  {
    $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
    $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password); 
  }
  else
  {
    $response = execute_communication($url, '', 'GET'); 
  }
  
  //checks for errors when listing group managers
  if(isset($response['message'])) 
  {
    return array('status' => 0, 'message' => "LUERROR in getGroupManagers:" . $response['message']);
  }    
  else if (isset($response['group_manager'][0]['id'])) 
  {
    return $response['group_manager'];
  } 
  else
  {
    return null;
  }
}

 /**
 * Creates a group on LearnUpon in the specific org via the API
 *
 * @param string $group_name - the name of the group
 * @param string $portal_subdomain
 * @param array $data - user data
 */
function createGroup($group_name = '', $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array()) 
{
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */
   
    $url = select_lrn_upon_url ($portal_subdomain, "groups");

    $new_group = array(
        'title' => $group_name
    );

    $send_data = '{"Group":' . json_encode($new_group) . '}';

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
      $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
      $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
      $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);
    }
    else 
    {
      $response = execute_communication($url, $send_data, "POST");
    }

    //checks for errors when updating the course name
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in createGroup:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
      return null;
    }
}

 /**
 * Links a LU user ID to a LU group
 *
 * @param int $user_id - the ID of the user
 * @param int $group_id - the ID of the group
 * @param string $portal_subdomain
 * @param array $data - user data
 */
function createGroupManager($user_id = 0, $group_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data = array())
{
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */

    // check that user ID and group ID were provided.
    if (!user_id)
    {
      return array('status' => 0, 'message' => 'Error in createGroupManager: user ID not provided');
    }
    if (!group_id)
    {
      return array('status' => 0, 'message' => 'Error in createGroupManager: group ID not provided');
    }

    $url = select_lrn_upon_url ($portal_subdomain, "group_managers");

    $group_manager = array(
        'group_id' => $group_id,
        'user_id' => $user_id,
        'can_create_users' => 'true'
    );

    $send_data = '{"GroupManager":' . json_encode($group_manager) . '}';

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
      $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
      $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
      $response = execute_communication($url, $send_data, "POST", $portal_username, $portal_password);
    }
    else 
    {
      $response = execute_communication($url, $send_data, "POST");
    }

    //checks for errors when updating the course name
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in createGroup:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
      return null;
    }
}

/**
 * Deletes a group on LearnUpon in the specific org via the API
 *
 * @param int $group_id - the id of the group
 * @param string $portal_subdomain
 * @param array $data - user data
 * @param int $delete_enrollements = boolean whether or not to delete enrollemets when deleting a group.
 */
function DeleteGroup($group_id = 0, $portal_subdomain = DEFAULT_SUBDOMAIN, $data, $delete_enrollments = 1) 
{
    extract($data);
    /*
     * Variables required in $data
     * org_id - the organization ID
     */
   
    // check that ID was supplied
    if (!$group_id)
    {
      return null;
    }

    $url = select_lrn_upon_url ($portal_subdomain, "groups/$group_id");

    if (!$delete_entrollments)
    {
      $delete_enrollments = 'false';
    }
    else
    {
      $delete_enrollments = 'true';
    }

    $delete_data = array(
        'delete_enrollments' => $delete_enrollments
    );

    $send_data = json_encode($delete_data);

    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
      $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
      $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
      $response = execute_communication($url, $send_data, "DELETE", $portal_username, $portal_password);
    }
    else 
    {
      $response = execute_communication($url, $send_data, "DELETE");
    }

    //checks for errors when updating the course name
    if(isset($response['message'])) 
    {
      return array('status' => 0, 'message' => "LUERROR in deleteGroup:" . $response['message']);
    }    
    else if (isset($response['id'])) 
    {
      return array('status' => 1, 'id' => $response['id']);
    }
    else
    {
      return null;
    }
}

/*
 *   Manage the creation of an Uber Camp Director account 
 */  
add_action('wp_ajax_createUberCampDirector', 'createUberCampDirector_callback'); //handles actions and triggered when the user is logged in
function createUberCampDirector_callback() 
{
  if( isset ( $_REQUEST['org_id'] ))
  {
    // Check permissions
    if( ! wp_verify_nonce( $_REQUEST['_wpnonce'] ,  'create-uber_camp_director_' . $_REQUEST['org_id']  )) 
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = 'create staff account error: Sorry, your nonce did not verify.';
    }
    else if( !current_user_can ('is_sales_rep') && !current_user_can('is_sales_manager') )
    {
        $result['display_errors'] = true;
        $result['success'] = false;
        $result['errors'] = 'create uber camp director account error: Sorry, you do not have permisison to view this page. ';
    }
    else
    {
      $camp_name = filter_var($_REQUEST['camp_name'],FILTER_SANITIZE_STRING); // The camp name
      $first_name = filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING); // User's first name
      $last_name = filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING); // User's last name
      $email = sanitize_email( $_REQUEST['email']); // User's e-mail address
      $password = $_REQUEST['password']; // User's password
      $org_id = filter_var($_REQUEST['org_id'],FILTER_SANITIZE_NUMBER_INT);
      $org_subdomain =  get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the org
      $user_type = 'manager'; // The LU user type for uber/umbrella manager
      $data = compact("org_id", "first_name", "last_name", "email", "password", "camp_name", "user_type");

      // create a group in this org
      $group = createGroup($camp_name, $org_subdomain, $data);

      if (isset($group['status']) && !$group['status'])
      {
        // eror creating the group
        $result['success'] = false;
        $result['display_errors'] = true;
        $result['errors'] = "Couldn't create the group: " . $group['message'];
      }
      else if (isset($group['status']) && $group['status'])
      {
        // successfully created the group now create the user
        $group_id = $group['id'];

        // create user 
        // check that the user doesnt exist in WP
        if ( email_exists($email) == false )
        {

          // create LU user
          // Create WP User
          // Link this user to this group

          $response = createUser($org_subdomain, $data); // create the user in LU

          if(isset($response['status']) && !$response['status'])
          {
            // got an error from createUser
            $result['success'] = false;
            $result['display_errors'] = true;
            $result['errors'] = "createUberCampDirector_callback Error: " . $response['message'];
          }
          else if(isset($response['status']) && $response['status'])
          {
            $result['success'] = true;
            $result['data'] = 'success';
            $result['name'] = $first_name;
            $result['lastname'] = $last_name;
            $result['org_id'] = $org_id;
            $result['email'] = $email;
            $result['password'] = $password;
            $result['user_id'] = $response['id']; // LU User ID
            $result['portal_subdomain'] = $org_subdomain;

            // create the user in WP (student)
            $userdata = array (
                'user_login' => $email,
                'user_pass' => $password,
                'role' => 'director',
                'user_email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name
            );
            $WP_user_id = wp_insert_user ($userdata);
  //              $WP_user_id->add_role ('umbrella_manager'); // give this new WP user an umbrella manager user role as well

            // Newly created WP user needs some meta data values added
            update_user_meta ( $WP_user_id, 'lrn_upon_id', $response['id'] );
            update_user_meta ( $WP_user_id, 'org_id', $org_id );
            update_user_meta ( $WP_user_id, 'accepted_terms', '0');
            update_user_meta ( $WP_user_id, 'portal', $org_subdomain );
            update_user_meta ( $WP_user_id, 'lrn_upon_group_id', $group_id ); // set the group ID for this user.

            // make this user a manager of the group in LU
            $groupManager = createGroupManager($response['id'], $group_id, $org_subdomain, $data);

            // check that it worked.
            if (isset($groupManager['status']) && !$groupManager['status'])
            {
              //error creating group manager
              $result['success'] = false;
              $result['display_errors'] = true;
              $result['errors'] = "createUberCampDirector_callback Error: " . $response['message'];
            }
          }
          else 
          {
            $result['success'] = false;
            $result['display_errors'] = true;
            $result['message'] = "createUberCampDirector_callback ERROR: could not create the staff account. Please contact the site administrator";
          }
        }
        else 
        {
          // user already exists so delete the group since we already created it.      
          // delete the group
          $delete_group = deleteGroup($group_id, $org_subdomain, $data, 1); // delete the newly created group.

          $result['success'] = false;
          $result['display_errors'] = true;
          $result['errors'] = 'Wordpress error: User already exsists. Couldn\'t create teh group.';
        } 
      }
    }
  }
  else
  {
    // no orgID so send error
    $result['success'] = false;
    $result['display_errors'] = true;
    $result['errors'] = 'Parameter Error: no org id supplied.';

  }
  // This variable will return to part-manage_staff_accounts.php $(document).bind('success.create_staff_account). Line 865
  echo json_encode( $result );
  wp_die();
}

/********************************************************************************************************
 * Clone a course to umbrella camps.
 *******************************************************************************************************/
add_action('wp_ajax_cloneCourse', 'cloneCourse_callback'); 
function cloneCourse_callback() 
{

    if( isset ( $_REQUEST['course_id'] ) && isset ( $_REQUEST['camp_id'] ) )
    {
        $course_id = filter_var($_REQUEST['course_id'],FILTER_SANITIZE_NUMBER_INT); // The course ID to clone
        $camp_id = filter_var($_REQUEST['camp_id'],FILTER_SANITIZE_NUMBER_INT); // The WP camp (POST) id
        $publish_course_after_copy = filter_var($_REQUEST['publish_course_after_copy'],FILTER_SANITIZE_STRING); // Boolean. True if chkbox "is publish course" is checked.
        $portal_id = get_post_meta($camp_id, 'lrn_upon_id', true); // gets the LU portal ID for this camp
        $portal_subdomain = (isset($_REQUEST['portal_subdomain'])) ? filter_var($_REQUEST['portal_subdomain'], FILTER_SANITIZE_STRING) : get_post_meta($camp_id, 'org_subdomain', true); // the subdomain name
        $org_id = $camp_id; // the org id
        $data = compact("org_id");

        // make sure publish course is true/false
        if ($publish_course_after_copy != 'true' && $publish_course_after_copy != 'false')
        {
          $publish_course_after_copy = 'false';
        }

        $response = cloneCourse($course_id, $portal_id, $publish_course_after_copy, $portal_subdomain, $data);

        // Check for error message.
        if (isset($response['message'])) 
        {
          $result['success'] = false;
          $result['data'] = 'failed';
          $result['message'] = "LUERROR in cloneCourse_callback: " . $response['message'] . " " . $course_id . " " . $portal_id . " " . $portal_subdomain . " " . $org_id;
        }
        else if (isset($response['status']) && $response['status']) 
        {
          $result['success'] = true;
          $result['data'] = 'success';
        }
        else 
        {
          $result['success'] = false;
          $result['data'] = 'failed';
          $result['message'] = "LUERROR in cloneCourse_callback: There was a problem. Please try again later.\n";
        }

    }
    else // Invalid request.
    {
        $result['success'] = false;
        $result['data'] = 'failed';
        $result['message'] = 'cloneCourse_callback: missing parameters.';
    }

    echo json_encode($result);
    wp_die();
}

/*******************************************************************************************************
 * Get staff in course used in improved_email_staff.php
 *******************************************************************************************************/
add_action('wp_ajax_getStaffInCourse', 'getStaffInCourse_callback'); 
function getStaffInCourse_callback () 
{
  // Variable declaration
  global $current_user;
  $user_id = $current_user->ID; // Wordpress user ID
  $org_id = get_org_from_user ($user_id); // Organization ID
  $data = array( "org_id" => $org_id );
  $org_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Subdomain of the user

  if( isset($_REQUEST['ids']) )
  {
    $course_ids = $_REQUEST['ids']; // The course ID's FILTER THIS LATER.
    $users = array(); // Lists of users
    // Get users from course enrollments.
    foreach($course_ids as $course_id)
    {
      $enrollments = getEnrollment($course_id, $org_subdomain, $data); // All enrollments in the courses 
      if( $enrollments )
      {
        foreach ($enrollments as $enrollment) 
        {
          $user = array (
            'id' => $enrollment['user_id'],
            'name' => $enrollment['first_name'],
            'lastname' => $enrollment['last_name'],
            'email' => $enrollment['email']
          );
          array_push($users, $user);
        }
      }
    }
    $result = $users;
  }
  else
  {
      $result['data'] = 'failed';
      $result['message'] = '<p>Unable to process your request</p>';
  }
  echo json_encode($result);
  wp_die();
}

/**
 * Get all users info
 *
 * @param int $portal_id - The portal ID
 * @param string $portal_subdomain - The subdomain of the portal
 * @param array string $data - Holds the value for $org_id
 */
function showUsers($portal_subdomain = DEFAULT_SUBDOMAIN, $data) 
{
    extract($data);
    $url = select_lrn_upon_url ($portal_subdomain, "users");
    if ($portal_subdomain != DEFAULT_SUBDOMAIN)
    {
        $portal_username = get_post_meta ($org_id, 'lrn_upon_api_usr', true);
        $portal_password = get_post_meta ($org_id, 'lrn_upon_api_pass', true);
    }
    else
    {
        // It should not used the master LU Username/Password
        //$portal_username = LU_USERNAME;
        //$portal_password = LU_PASSWORD;
    }

    $response = execute_communication($url, '', 'GET', $portal_username, $portal_password);

    return $response['user'];

}

