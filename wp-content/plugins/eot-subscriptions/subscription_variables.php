<?php
/**
  * Define variables for the subscriptions
**/

// define variables for Leadership Essentials - Full Pack
define ('LE_PRICE', 399);
define ('LE_LVL_1_PRICE', 14);
define ('LE_LVL_1_MIN', 1);
define ('LE_LVL_1_MAX', 99);
define ('LE_LVL_2_PRICE', 13);
define ('LE_LVL_2_MIN', 100);
define ('LE_LVL_2_MAX', 249);
define ('LE_LVL_3_PRICE', 12);
define ('LE_LVL_3_MIN', 250);
define ('LE_MIN_ACC', 0);
define ('DATA_DRIVE', '49.00');

// define variables for Leadership Essentials - Starter Pack - Day Camps
define ('LE_SP_DC_PRICE', 199);
define ('LE_SP_DC_LVL_1_PRICE', 14);
define ('LE_SP_DC_LVL_1_MIN', 1);
define ('LE_SP_DC_LVL_1_MAX', 99);
define ('LE_SP_DC_LVL_2_PRICE', 13);
define ('LE_SP_DC_LVL_2_MIN', 100);
define ('LE_SP_DC_LVL_2_MAX', 249);
define ('LE_SP_DC_LVL_3_PRICE', 12);
define ('LE_SP_DC_LVL_3_MIN', 250);
define ('LE_SP_DC_MIN_ACC', 0);

// define variables for Leadership Essentials - Starter Pack - Overnight Camps
define ('LE_SP_OC_PRICE', 199);
define ('LE_SP_OC_LVL_1_PRICE', 14);
define ('LE_SP_OC_LVL_1_MIN', 1);
define ('LE_SP_OC_LVL_1_MAX', 99);
define ('LE_SP_OC_LVL_2_PRICE', 13);
define ('LE_SP_OC_LVL_2_MIN', 100);
define ('LE_SP_OC_LVL_2_MAX', 249);
define ('LE_SP_OC_LVL_3_PRICE', 12);
define ('LE_SP_OC_LVL_3_MIN', 250);
define ('LE_SP_OC_MIN_ACC', 0);

// define variables for Leadership Essentials - Starter Pack - Park & Rec Programs
define ('LE_SP_PRP_PRICE', 199);
define ('LE_SP_PRP_LVL_1_PRICE', 14);
define ('LE_SP_PRP_LVL_1_MIN', 1);
define ('LE_SP_PRP_LVL_1_MAX', 99);
define ('LE_SP_PRP_LVL_2_PRICE', 13);
define ('LE_SP_PRP_LVL_2_MIN', 100);
define ('LE_SP_PRP_LVL_2_MAX', 249);
define ('LE_SP_PRP_LVL_3_PRICE', 12);
define ('LE_SP_PRP_LVL_3_MIN', 250);
define ('LE_SP_PRP_MIN_ACC', 0);

// define variables for Leadership Essentials - Limited
define ('LEL_PRICE', 199);
define ('LEL_LVL_1_PRICE', 14);
define ('LEL_LVL_1_MIN', 1);
define ('LEL_LVL_1_MAX', 99);
define ('LEL_LVL_2_PRICE', 13);
define ('LEL_LVL_2_MIN', 100);
define ('LEL_LVL_2_MAX', 249);
define ('LEL_LVL_3_PRICE', 12);
define ('LEL_LVL_3_MIN', 250);
define ('LEL_MIN_ACC', 1);

// define variables for Clinical Essentials
define ('CE_PRICE', 49);
define ('CE_LVL_1_PRICE', 0);
define ('CE_LVL_1_MIN', 1);
define ('CE_LVL_1_MAX', 12);
define ('CE_LVL_2_PRICE', 10);
define ('CE_LVL_2_MIN', 13);
define ('CE_MIN_ACC', 12);

// define variables for Safety Essentials -> Child Welfare and Protection
define ('SE_PRICE', 99);
define ('SE_LVL_1_PRICE', 0);
define ('SE_LVL_1_MIN', 1);
define ('SE_LVL_1_MAX', 20);
define ('SE_LVL_2_PRICE', 10);
define ('SE_LVL_2_MIN', 21);
define ('SE_MIN_ACC', 21);

// define variables for Prep 4 Camp
define ('P4C_PRICE', 499); // price if not subscribed to LE
define ('P4C_LVL_1_PRICE', 7); // price for 1 account for parent
define ('P4C_LVL_1_MIN', 1);
define ('P4C_LVL_1_MAX', 1);
define ('P4C_LVL_2_PRICE', 99); // price for camp thats already subscribed to LE
define ('P4C_LVL_2_MIN', 2);
define ('P4C_LVL_2_MAX', 249);
define ('P4C_LVL_3_PRICE', 499);
define ('P4C_LVL_3_MIN', 250);
define ('P4C_MIN_ACC', 500);

// define subscription variables
$today = time();
$current_year = date('Y');
$next_year = date('Y', strtotime('+1 year'));
$last_year = date('Y', strtotime('-1 year'));

if($today >= strtotime("$current_year-08-01"))
{
    define ('SUBSCRIPTION_YEAR', $next_year);
    define ('SUBSCRIPTION_START', "$current_year-08-01");
    define ('SUBSCRIPTION_END', "$next_year-10-15");
}
else 
{
    define ('SUBSCRIPTION_YEAR', $current_year);
    define ('SUBSCRIPTION_START', "$last_year-08-01");
    define ('SUBSCRIPTION_END', "$current_year-10-15");
}

// define video variables
define ('NUM_VIDEOS', 120);
define ('NUM_VIDEOS_LEL', 12); // number of videos they get access to
define ('NUM_VIDEOS_MAX_LEL', 30); // number of videos they get to choose from
define ('NUM_VIDEOS_LE_SP_DC', 9); // number of videos they get access to for le starter pack day camps
define ('NUM_VIDEOS_LE_SP_OC', 9); // number of videos they get access to for le starter pack overnight camps
define ('NUM_VIDEOS_LE_SP_PRP', 9); // number of videos they get access to for le starter pack parks & rec programs

// define time variables
define ('AVG_VIDEO_LENGTH', 10); // the number of minutes it takes to watch a video
define ('AVG_EXAM_LENGTH', 10);  // the number of minutes it takes to complete an exam

// define table names
define ('TABLE_AUDIO', $wpdb->prefix."audio");
define ('TABLE_SUBSCRIPTIONS', $wpdb->prefix."subscriptions");
define ('TABLE_LIBRARY', $wpdb->prefix."library");
define ('TABLE_LIBRARY_MODULES', $wpdb->prefix."library_modules");
define ('TABLE_VIDEOS', $wpdb->prefix."video");
define ('TABLE_QUESTIONS', $wpdb->prefix."questionnaire_questions");
define ('TABLE_UPGRADE_SUBSCRIPTION', $wpdb->prefix."upgrade");
define ('TABLE_QUESTION_MODIFICATIONS', $wpdb->prefix."questionnaire_modifications");
define ('TABLE_API_LOGS', $wpdb->prefix."api_logs");
define ('TABLE_POSTMETA', $wpdb->prefix."postmeta");
define ('TABLE_RESOURCES', $wpdb->prefix."resources");
define ('TABLE_ENROLLMENTS',$wpdb->prefix.'enrollments');
define ('TABLE_CERTIFICATES', $wpdb->prefix."certificates");
define ('TABLE_CERTIFICATES_SYLLABUS', $wpdb->prefix."certificate_syllabus");
define ('TABLE_PENDING_USERS', $wpdb->prefix."pending_users");
define ('TABLE_PENDING_EMAILS', $wpdb->prefix."pending_emails");
define ('TABLE_INVITATIONS', $wpdb->prefix."invitations");
define ('TABLE_PENDING_SUBSCRIPTIONS', $wpdb->prefix."pending_subscriptions");
define ('TABLE_USERS', $wpdb->prefix."users");
define ('TABLE_USERMETA', $wpdb->prefix."usermeta");
define ('TABLE_COURSES', $wpdb->prefix.'courses');
define ('TABLE_COURSES_QUIZZES',$wpdb->prefix.'course_quizzes');
define ('TABLE_COURSES_RESOURCES',$wpdb->prefix.'course_resources');
define ('TABLE_COURSES_MODULES', $wpdb->prefix.'course_module');
define ('TABLE_MODULES', $wpdb->prefix.'modules');
define ('TABLE_MODULE_RESOURCES',$wpdb->prefix.'module_resources');
define ('TABLE_COURSE_MODULE_RESOURCES',$wpdb->prefix.'course_module_resources');
define ('TABLE_QUIZ', $wpdb->prefix.'quiz');
define ('TABLE_QUIZ_QUESTION', $wpdb->prefix . 'quiz_question');
define ('TABLE_QUIZ_ANSWER', $wpdb->prefix . 'quiz_answer');
define ('TABLE_QUIZ_RESULT', $wpdb->prefix . 'quiz_results');
define ('TABLE_QUIZ_QUESTION_RESULT', $wpdb->prefix . 'quiz_question_results');
define ('TABLE_QUIZ_IN_COURSE', $wpdb->prefix . 'quiz_in_course');
define ('TABLE_QUIZ_ATTEMPTS', $wpdb->prefix . 'quiz_attempts');
define ('TABLE_CATEGORIES', $wpdb->prefix.'category');
define ('TABLE_HELP_TOPICS', $wpdb->prefix.'help_topics');
define ('TABLE_HELP_TOPICS_FOR_VIEW', $wpdb->prefix . 'help_topics_for_view');
define ('TABLE_TRACK', $wpdb->prefix . 'track');
define ('TABLE_REFUNDS', $wpdb->prefix . 'refunds');
define ('TABLE_USERS_IN_SUBSCRIPTION', $wpdb->prefix . 'user_in_subscription'); 
define ('TABLE_UNSUBSCRIBE', $wpdb->prefix . 'unsubscribe');


// base courses (course name => course description)
$base_courses = array (
	"New Staff" => 1,
	"Returning Staff" => 2,
	"Program Staff" => 3,
	"Supervisory Staff" => 4
);

$base_courses_prep4camp = array (
	"Prep4Camp" => 5249
);

// define LearnUpon Vars
define ('DEFAULT_SUBDOMAIN', 'eot');

//define Library IDs
define ('LE_ID', 1); // Leadership Essentials - Full Pack
define ('LEL_ID', 5); // Leadership Essentials - Limited
define ('SE_ID', 3); // Safety Essentials -> Child Welfare and Protection
define ('CE_ID', 2); // Clinical Essentials
define ('BCB_ID', 4); // Burn Camp Bundle
define ('LE_SP_DC_ID', 6); // Leadership Essentials - Starter Pack - Day Camps
define ('LE_SP_OC_ID', 7); // Leadership Essentials - Starter Pack - Overnight Camps
define ('LE_SP_PRP_ID', 8); // Leadership Essentials - Starter Pack - Park & Rec Programs
define ('P4C_ID', 9); // Prep 4 Camp

// define specific course IDs
define ('CHILD_WELFARE_COURSE_ID', 32);
define ('PREP4CAMP_COURSE_ID', 5249);


// questionnaire base course IDs
$questionnaire_base_course_id = array (
	"New Staff" => '1',
	"Returning Staff" => '2',
	"Program Staff" => '3',
	"Supervisory Staff" => '4'
);

// the prefix for all portal subdomains
define ('SUBDOMAIN_PREFIX', 'eot');

// Commision Percentage
define ('COMMISION_PERCENT_NEW', '.10');
define ('COMMISION_PERCENT_RENEWAL', '.05');

define ('SALES_REPORT_FILE_NAME', 'EOTSalesReport');

define ('MINUTES_PER_VIDEO', 10);
define ('MINUTES_PER_QUIZ', 10);

define ('CERTIFICATE_TEMPLATE', '/images/eot_certificate.jpg'); // The certificate image template path filename.
define ('CERTIFICATE_PATH', '/images/certificates/'); // The path to store certificate images

define ('PENDING_USERS_CRON_LIMIT', 15); // The number of pending users to process via cron job
define ('PENDING_USERS_CRON_TIME_LIMIT', 2); // The number of hours to wait before processing orphaned users
define ('PENDING_USERS_LIMIT', 1); // The number of pending users to process via ajax

define ('PENDING_EMAILS_LIMIT', 1); // the number of pending emails to send via ajax
define ('PENDING_EMAILS_CRON_LIMIT', 100); // The number of pending users to process via cron job
define ('PENDING_EMAILS_CRON_TIME_LIMIT', 1); // The number of hours to wait before processing orphaned users

// AWS Elastic Transcoder pipeline and presets
define ('AWS_PIPELINE','1509985159167-m5ro0u');
define ('AWS_PRESET_HD_HIGH','1351620000001-000001');
define ('AWS_PRESET_HD_MED','1351620000001-000010');
define ('AWS_PRESET_HD_LOW','1351620000001-000020');
define ('AWS_PRESET_SD_HIGH','1351620000001-000030');
define ('AWS_PRESET_SD_MED','1351620000001-000050');
define ('AWS_PRESET_SD_LOW','1351620000001-000061');

// AWS S3 bucket
define ('AWS_S3_BUCKET','eot-videos');

// DEFAULT EMAIL ADDRESS IF ERRORS HAPPEN
define ('DEFAULT_EMAIL', 'hagai@expertonlinetraining.com');
