<?php
/**
  * Define variables for the subscriptions
**/

$subscription_id = isset( $_REQUEST['subscription_id'] ) ? filter_var($_REQUEST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$library_id = isset( $_REQUEST['library_id'] ) ? filter_var($_REQUEST['library_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$course_id = isset( $_REQUEST['course_id'] ) ? filter_var($_REQUEST['course_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$org_id = isset( $_REQUEST['org_id'] ) ? filter_var($_REQUEST['org_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$quiz_id = isset($_REQUEST['quiz_id']) ? filter_var($_REQUEST['quiz_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
$enrollment_id = isset($_REQUEST['enrollment_id'])? filter_var($_REQUEST['enrollment_id'], FILTER_SANITIZE_NUMBER_INT) : 0;


// define default name for Leadership Essentials - ie. the name of the course in LU in case we need to reference it.
define ('LE_LIBRARY_TITLE', 'Leadership Essentials'); // @TODO remove this later on

// define default length of each video/module
define ('DEFAULT_MODULE_VIDEO_LENGTH', 10);

// define default length of each quiz
define ('DEFAULT_QUIZ_LENGTH', 7);

// define breadcrumb to dashboard page
define ('CRUMB_DASHBOARD', '<a href="'. get_home_url() .'/dashboard/" onclick="load(\'load_dashboard\')">My Dashboard</a>');

// define breadcrumb to modules page
define ('CRUMB_MODULES', '<a href="/dashboard?part=manage_custom_modules&subscription_id='.$subscription_id.'">Manage Your Custom Modules</a>');

// define breadcrumb to administrator page
define ('CRUMB_ADMINISTRATOR', '<a href="'. get_home_url() .'/dashboard/?part=administration&subscription_id='.$subscription_id.'" onclick="load(\'load_administration\')">Administration</a>');

// define breadcrumb to subscription details page
define ('CRUMB_SUBSCRIPTION_DETAILS', '<a href="'. get_home_url() .'/dashboard/?part=admin_subscription_details&subscription_id='.$subscription_id.'&library_id='.$library_id.'" onclick="load(\'load_loading\')">Subscription Details</a>');

if($subscription_id == 0)
{
// define breadcrumb to the quiz page
define('CRUMB_QUIZ','<span><a href="?part=manage_quiz_eot&subscription_id='.$subscription_id.'" onclick="load(\'load_quiz\')">Manage Quizzes</a></span>');
}
else 
{
  // define breadcrumb to the quiz page
define('CRUMB_QUIZ','<span><a href="?part=manage_quiz&subscription_id='.$subscription_id.'" onclick="load(\'load_quiz\')">Manage Quizzes</a></span>');  
}
// define breadcrumb to view subscription page. This requires the subscription ID.
define ('CRUMB_VIEW_SUBSCRIPTIONS', '<a href="'. get_home_url() .'/dashboard/?part=admin_view_subscriptions&library_id='.$library_id.'" onclick="load(\'load_manage_staff_accounts\')">View Subscriptions</a>');

// define breadcrumb to the library page
define ('CRUMB_VIEW_LIBRARY', '<a href="'. get_home_url() .'/dashboard/?part=view_library&subscription_id='.$subscription_id.'" onclick="load(\'load_view_library\')">View Library</a>');

// define breadcrumb separator
define ('CRUMB_SEPARATOR', '<span class="crumb">&gt;</span>');

// define breadcrumb to the statistics page
define ('CRUMB_STATISTICS', '<a href="'. get_home_url() .'/dashboard/?part=statistics&subscription_id='.$subscription_id.'" onclick="load(\'load_statistics\')">View Statistics</a>');

// define breadcrumb to user lists page
define ('CRUMB_USERSLISTS', '<a href="'. get_home_url() .'/dashboard/?part=user_list" onclick="load(\'load_manage_staff_accounts\')">Users Lists</a>');

// define breadcrumb to the manage staff accounts
define ('CRUMB_MANAGE_STAFF_ACCOUNTS', '<a href="'. get_home_url() .'/dashboard/?part=manage_staff_accounts&subscription_id='.$subscription_id.'&org_id='.$org_id.'">Manage Staff Accounts</a>');

// define breadcrumb to custom fields page
define ('CRUMB_CUSTOMFIELDS', '<a href="'. get_home_url() .'/dashboard/?part=custom_fields"">Custom Fields</a>');

// define breadcrumb to directors development page
define ('CRUMB_DEVELOPMENT', '<a href="'. get_home_url() .'/dashboard/?part=directors_development&subscription_id='.$subscription_id.'">Directors\' Professional Development</a>');

// define breadcrumb to directors corner page
define ('CRUMB_CORNER', '<a href="'. get_home_url() .'/dashboard/?part=directors_corner&subscription_id='.$subscription_id.'">Director\'s Corner</a>');

// define breadcrumb to my library page
define ('CRUMB_MY_LIBRARY', '<a href="'. get_home_url() .'/dashboard/?part=my_library&course_id='.$course_id.'&enrollment_id='.$enrollment_id.'" onclick="load(\'load_loading\')">My Library</a>');

// define breadcrumb to manage sales rep page
define ('CRUMB_MANAGESALESREP', '<a href="'. get_home_url() .'/dashboard/?part=manage_sales_rep"">Manage Sales Rep</a>');

// define breadcrumb to manage subdomains page
define ('CRUMB_SUBDOMAINS', '<a href="'. get_home_url() .'/dashboard/?part=manage_subdomains"">Manage Subdomains</a>');

// define breadcrumb to email your staff page
define ('CRUMB_EMAIL_YOUR_STAFF', '<a href="'. get_home_url() .'/dashboard/?part=email_staff&subscription_id='.$subscription_id.'">E-mail Your Staff</a>');

// define the part name of manage logo dashboard
define ('FILE_MANAGE_LOGO', 'manage_logo');

// define the part name of uploadspreadsheet
define ('FILE_UPLOADSPREADSHEET', 'uploadspreadsheet');

// define the part name of upload_resources
define ('FILE_UPLOADRESOURCES', 'upload_resources');

// define the part name of improved email staff
define ('FILE_IMPROVED_EMAIL_STAFF', 'improved_email_staff');

// an array of page names that require the ACF_FORM_HEAD() because they have acf forms on it.
$GLOBALS['pages_with_acf_form'] = array (
	FILE_UPLOADSPREADSHEET,
	FILE_UPLOADRESOURCES,
	FILE_IMPROVED_EMAIL_STAFF,
	FILE_MANAGE_LOGO
);

/**
 * NEED UNIQUE POST IDS FOR EACH ENVIRONMENT
 */
global $DEV_ENV;
if ($DEV_ENV == 'hagai')
{
	// The ID of the ACF upload spreadsheet form
	define ('ACF_UPLOAD_SPREADSHEET', 11842);

	// The ID of the ACF upload resources form
	define ('ACF_UPLOAD_RESOURCE', 11835);

	// The POST ID for ACF Staff Members form
	define ('ACF_COMPOSE_MESSAGE_STAFF_MEMBERS', 11823);

	// The POST ID for ACF Incomplete and Complete form
	define ('ACF_COMPOSE_MESSAGE_INCOMPLETE_COMPLETE', 11820);

	// The POST ID for ACF Yet to login form
	define ('ACF_COMPOSE_MESSAGE_YET_TO_LOGIN', 11829);

	// The POST ID for ACF Message Staff Password form
	define ('ACF_COMPOSE_MESSAGE_STAFF_PASSWORD', 11826);

	// The POST ID for ACF Message Group Password form
	define ('ACF_COMPOSE_MESSAGE_COURSE_PASSWORD', 11817);

	// The POST ID for ACF Manage Logo form
	define ('ACF_MANAGE_LOGO', 11813);
}
else if ($DEV_ENV == 'tommy')
{
	// The ID of the ACF upload spreadsheet form
	define ('ACF_UPLOAD_SPREADSHEET', 11820);

	// The ID of the ACF upload resources form
	define ('ACF_UPLOAD_RESOURCE', 1439);

	// The POST ID for ACF Staff Members form
	define ('ACF_COMPOSE_MESSAGE_STAFF_MEMBERS', 2739);

	// The POST ID for ACF Incomplete and Complete form
	define ('ACF_COMPOSE_MESSAGE_INCOMPLETE_COMPLETE', 2740);

	// The POST ID for ACF Yet to login form
	define ('ACF_COMPOSE_MESSAGE_YET_TO_LOGIN', 2741);

	// The POST ID for ACF Message Staff Password form
	define ('ACF_COMPOSE_MESSAGE_STAFF_PASSWORD', 2737);

	// The POST ID for ACF Message Group Password form
	define ('ACF_COMPOSE_MESSAGE_COURSE_PASSWORD', 2738);

	// The POST ID for ACF Manage Logo form
	define ('ACF_MANAGE_LOGO', 11813);
}
else if ($DEV_ENV == 'adrian')
{
	// The ID of the ACF upload spreadsheet form
	define ('ACF_UPLOAD_SPREADSHEET', 11842);

	// The ID of the ACF upload resources form
	define ('ACF_UPLOAD_RESOURCE', 1439);

	// The POST ID for ACF Staff Members form
	define ('ACF_COMPOSE_MESSAGE_STAFF_MEMBERS', 2739);

	// The POST ID for ACF Incomplete and Complete form
	define ('ACF_COMPOSE_MESSAGE_INCOMPLETE_COMPLETE', 2740);

	// The POST ID for ACF Yet to login form
	define ('ACF_COMPOSE_MESSAGE_YET_TO_LOGIN', 2741);

	// The POST ID for ACF Message Staff Password form
	define ('ACF_COMPOSE_MESSAGE_STAFF_PASSWORD', 2737);

	// The POST ID for ACF Message Group Password form
	define ('ACF_COMPOSE_MESSAGE_COURSE_PASSWORD', 2738);

	// The POST ID for ACF Manage Logo form
	define ('ACF_MANAGE_LOGO', 11813);
}
else
{
	// live server
	// The ID of the ACF upload spreadsheet form
	define ('ACF_UPLOAD_SPREADSHEET', 12697);

	// The ID of the ACF upload resources form
	define ('ACF_UPLOAD_RESOURCE', 12690);

	// The POST ID for ACF Staff Members form
	define ('ACF_COMPOSE_MESSAGE_STAFF_MEMBERS', 12676);

	// The POST ID for ACF Incomplete and Complete form
	define ('ACF_COMPOSE_MESSAGE_INCOMPLETE_COMPLETE', 12673);

	// The POST ID for ACF Yet to login form
	define ('ACF_COMPOSE_MESSAGE_YET_TO_LOGIN', 12682);

	// The POST ID for ACF Message Staff Password form
	define ('ACF_COMPOSE_MESSAGE_STAFF_PASSWORD', 12679);

	// The POST ID for ACF Message Group Password form
	define ('ACF_COMPOSE_MESSAGE_COURSE_PASSWORD', 12670);

	// The POST ID for ACF Manage Logo form
	define ('ACF_MANAGE_LOGO', 12685);
}

