<?php
/*
	Plugin Name: EOT Subscriptions
	Plugin URI: http://www.expertonlinetraining.com
	Description: Creates / updates /deletes subscriptions for organizations
	Author: Expert Online Training
	Version: 1.0
	Author URI: http://www.expertonlinetraining.com
*/

global $subscription_db_version, $wpdb, $eot_dashboard_url;
$subscription_db_version = '1.0';

include_once ('subscription_variables.php');
include_once ('subscription_templates.php');
include_once ('subscribe_functions.php');
include_once ('stripe_functions.php');
include_once ('stripe/Stripe.php');
Stripe::setApiKey(STRIPE_SECRET);

register_activation_hook( __FILE__, 'eot_subscription_activate' );
register_deactivation_hook( __FILE__, 'eot_subscription_deactivate' );

ignore_user_abort(true);

/**
 * Create custom role on plugin activation
**/
function eot_subscription_activate() {
	global $wpdb;

	$charset_collate = '';
	
	if (!empty($wpdb->charset)) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if (!empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$sql = "
		CREATE TABLE IF NOT EXISTS `".TABLE_SUBSCRIPTIONS."` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`org_id` int(11) NOT NULL DEFAULT '0',
			`manager_id` int(11) NOT NULL DEFAULT '0',
			`library_id` int(11) NOT NULL DEFAULT '0',
			`start_date` date NOT NULL DEFAULT '0000-00-00',
			`end_date` date NOT NULL DEFAULT '0000-00-00',
			`method` enum('cardflex','stripe','check','paypal','free','pending') NOT NULL DEFAULT 'cardflex',
			`trans_id` text,
			`trans_date` date NOT NULL DEFAULT '0000-00-00',
			`price` decimal(9,2) NOT NULL DEFAULT '0.00',
			`dash_price` decimal(9,2) NOT NULL DEFAULT '0.00',
			`staff_price` decimal(9,2) NOT NULL DEFAULT '0.00',
			`dash_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
			`staff_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
			`staff_credits` int(11) NOT NULL DEFAULT '0',
			`status` enum('active','disabled','pending') NOT NULL DEFAULT 'active',
			`rep_id` int(11) NOT NULL DEFAULT '0',
			`notes` text,
			PRIMARY KEY (`id`)
		) $charset_collate;
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta ($sql);

	add_option( 'eot_db_version', $eot_db_version );
}

/**
 * Remove custom role on plugin deactivation
**/
function eot_subscription_deactivate() {
	
}

add_action('wp_enqueue_scripts', 'eot_subs_enqueue_scripts');

function eot_subs_enqueue_scripts () {
	wp_enqueue_style('eot-subscriptions-style', plugins_url( 'css/eot-subscriptions.css', __FILE__ ), '', '1.0.0');

	wp_enqueue_script('steps-js', plugins_url( 'js/jquery.steps.min.js', __FILE__ ), array ('jquery'), '1.1.0');
	wp_enqueue_script('validate-js', 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js', array ('jquery'), '1.13.0');
	wp_enqueue_script('eot-subscriptions-js', plugins_url( 'js/eot_subscriptions.js', __FILE__ ), array ('steps-js'), '1.0.0');

	wp_localize_script( 'eot-subscriptions-js', 'eot', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'dashboard' => get_bloginfo ('url') . "/dashboard/" ) );
}

add_action( 'wp_ajax_subscribe', 'handle_steps_callback' );
add_action( 'wp_ajax_nopriv_subscribe', 'handle_steps_not_logged_in' );

function handle_steps_not_logged_in () {
	echo json_encode(array('status' => 0, 'message' => 'You must be logged in.'));
	die ();
}

function handle_steps_callback () {

	if (!isset($_REQUEST['currentIndex'])) {
		die ();
	}
	$step = $_REQUEST['currentIndex'];

	switch ($step) {
		case 0:
			if (!isset ($_REQUEST['le']) && !isset ($_REQUEST['lel']) && !isset ($_REQUEST['le_sp_dc']) && !isset ($_REQUEST['le_sp_oc']) && !isset ($_REQUEST['le_sp_prp']) && !isset ($_REQUEST['se'])) 
			{
				echo json_encode(array('status' => 0, 'message' => 'Please choose a library'));
			} 
			else if ( isset ($_REQUEST['le']) && isset ($_REQUEST['se']) ) 
			{
				echo json_encode(array('status' => 0, 'message' => 'Safety Essentials is included with Leadership Essentials. Please choose one or the other.'));
			} 
			else if (isset ($_REQUEST['le']) && (isset ($_REQUEST['le_sp_dc']) || isset ($_REQUEST['le_sp_oc']) || isset ($_REQUEST['le_sp_prp']))) 
			{
				echo json_encode(array('status' => 0, 'message' => 'Leadership Essentials - Starter Pack is a subset of Leadershipt Essentials - Full Pack. Please choose one or the other.'));
			} 
			else 
			{
				echo json_encode(array('status' => 1));
			}
			break;
		case 1:
			$data = array ();
			$total_price = 0;

			if (isset($_REQUEST['le'])) { // Leadership essentials - complete
				$le_staff = LE_MIN_ACC;
				$staff_price = 0;
				$subscription = array (
					"label" => "Leadership Essentials - Complete " . SUBSCRIPTION_YEAR . " Base Subscription",
					"price" => number_format (LE_PRICE, 2),
					"name" => "le_dash_price"
				);
				array_push ($data, $subscription);
				if (isset ($_REQUEST['le_staff']) && $_REQUEST['le_staff'] != "") {
					$le_staff = $_REQUEST['le_staff'];
					if ($le_staff >= LE_LVL_3_MIN)
					{
						$staff_price = $le_staff * LE_LVL_3_PRICE;
					}
					elseif ($le_staff >= LE_LVL_2_MIN) 
					{
						$staff_price = $le_staff * LE_LVL_2_PRICE;
					}
					elseif ($le_staff >= LE_LVL_1_MIN) 
					{
						$staff_price = $le_staff * LE_LVL_1_PRICE;
					}
				}
				$staff_accounts = array (
					"label" => $le_staff . " Staff Accounts",
					"price" => number_format ($staff_price, 2),
					"name" => "le_staff_price"
				);
				array_push ($data, $staff_accounts);
				$total_price += LE_PRICE + $staff_price;
			}

			if (isset($_REQUEST['le_sp_dc'])) { // Leadership essentials - Starter Pack - Day Camps
				$le_sp_dc_staff = LE_SP_DC_MIN_ACC;
				$staff_price = 0;
				$subscription = array (
					"label" => "Leadership Essentials - Starter Pack - Day Camps " . SUBSCRIPTION_YEAR . " Base Subscription",
					"price" => number_format (LE_SP_DC_PRICE, 2),
					"name" => "le_sp_dc_dash_price"
				);
				array_push ($data, $subscription);
				if (isset ($_REQUEST['le_sp_dc_staff']) && $_REQUEST['le_sp_dc_staff'] != "") {
					$le_sp_dc_staff = $_REQUEST['le_sp_dc_staff'];
					if ($le_sp_dc_staff >= LE_SP_DC_LVL_3_MIN)
					{
						$staff_price = $le_sp_dc_staff * LE_SP_DC_LVL_3_PRICE;
					}
					elseif ($le_sp_dc_staff >= LE_SP_DC_LVL_2_MIN) 
					{
						$staff_price = $le_sp_dc_staff * LE_SP_DC_LVL_2_PRICE;
					}
					elseif ($le_sp_dc_staff >= LE_SP_DC_LVL_1_MIN) 
					{
						$staff_price = $le_sp_dc_staff * LE_SP_DC_LVL_1_PRICE;
					}
				}
				$staff_accounts = array (
					"label" => $le_sp_dc_staff . " Staff Accounts",
					"price" => number_format ($staff_price, 2),
					"name" => "le_sp_dc_staff_price"
				);
				array_push ($data, $staff_accounts);
				$total_price += LE_SP_DC_PRICE + $staff_price;
			}

			if (isset($_REQUEST['le_sp_oc'])) { // Leadership essentials - Starter Pack - Overnight Camps
				$le_sp_oc_staff = LE_SP_OC_MIN_ACC;
				$staff_price = 0;
				$subscription = array (
					"label" => "Leadership Essentials - Starter Pack - Overnight Camps " . SUBSCRIPTION_YEAR . " Base Subscription",
					"price" => number_format (LE_SP_OC_PRICE, 2),
					"name" => "le_sp_oc_dash_price"
				);
				array_push ($data, $subscription);
				if (isset ($_REQUEST['le_sp_oc_staff']) && $_REQUEST['le_sp_oc_staff'] != "") {
					$le_sp_oc_staff = $_REQUEST['le_sp_oc_staff'];
					if ($le_sp_oc_staff >= LE_SP_OC_LVL_3_MIN)
					{
						$staff_price = $le_sp_oc_staff * LE_SP_OC_LVL_3_PRICE;
					}
					elseif ($le_sp_oc_staff >= LE_SP_OC_LVL_2_MIN) 
					{
						$staff_price = $le_sp_oc_staff * LE_SP_OC_LVL_2_PRICE;
					}
					elseif ($le_sp_oc_staff >= LE_SP_OC_LVL_1_MIN) 
					{
						$staff_price = $le_sp_oc_staff * LE_SP_OC_LVL_1_PRICE;
					}
				}
				$staff_accounts = array (
					"label" => $le_sp_oc_staff . " Staff Accounts",
					"price" => number_format ($staff_price, 2),
					"name" => "le_sp_oc_staff_price"
				);
				array_push ($data, $staff_accounts);
				$total_price += LE_SP_OC_PRICE + $staff_price;
			}

			if (isset($_REQUEST['le_sp_prp'])) { // Leadership essentials - Starter Pack - Parks & Rec Programs
				$le_sp_prp_staff = LE_SP_PRP_MIN_ACC;
				$staff_price = 0;
				$subscription = array (
					"label" => "Leadership Essentials - Starter Pack - Parks & Rec Programs " . SUBSCRIPTION_YEAR . " Base Subscription",
					"price" => number_format (LE_SP_PRP_PRICE, 2),
					"name" => "le_sp_prp_dash_price"
				);
				array_push ($data, $subscription);
				if (isset ($_REQUEST['le_sp_prp_staff']) && $_REQUEST['le_sp_prp_staff'] != "") {
					$le_sp_prp_staff = $_REQUEST['le_sp_prp_staff'];
					if ($le_sp_prp_staff >= LE_SP_PRP_LVL_3_MIN)
					{
						$staff_price = $le_sp_prp_staff * LE_SP_PRP_LVL_3_PRICE;
					}
					elseif ($le_sp_prp_staff >= LE_SP_PRP_LVL_2_MIN) 
					{
						$staff_price = $le_sp_prp_staff * LE_SP_PRP_LVL_2_PRICE;
					}
					elseif ($le_sp_prp_staff >= LE_SP_PRP_LVL_1_MIN) 
					{
						$staff_price = $le_sp_prp_staff * LE_SP_PRP_LVL_1_PRICE;
					}
				}
				$staff_accounts = array (
					"label" => $le_sp_prp_staff . " Staff Accounts",
					"price" => number_format ($staff_price, 2),
					"name" => "le_sp_prp_staff_price"
				);
				array_push ($data, $staff_accounts);
				$total_price += LE_SP_PRP_PRICE + $staff_price;
			}

			if (isset($_REQUEST['se'])) { // Child Welfare & Protection (formarly Safety essentials)
				$se_staff = SE_MIN_ACC;
				$staff_price = 0;
				$subscription = array (
					"label" => "Safety Essentials " . SUBSCRIPTION_YEAR . " Base Subscription",
					"price" => number_format (SE_PRICE, 2),
					"name" => "se_dash_price"
				);
				array_push ($data, $subscription);

				if (isset ($_REQUEST['se_staff']) && $_REQUEST['se_staff'] != "") {
					$se_staff = $_REQUEST['se_staff'];
					if ($se_staff >= SE_LVL_1_MIN && $se_staff <= SE_LVL_1_MAX) {
						$staff_price = $se_staff * SE_LVL_1_PRICE;
					}
					if ($se_staff >= SE_LVL_2_MIN) {
						$num = $se_staff - SE_LVL_2_MIN + 1;
						$staff_price = (SE_LVL_1_PRICE * SE_LVL_1_MAX) + ($num * SE_LVL_2_PRICE);
					}
				}
				$staff_accounts = array (
					"label" => $se_staff . " Staff Accounts",
					"price" => number_format ($staff_price, 2),
					"name" => "se_staff_price"
				);
				array_push ($data, $staff_accounts);

				$total_price += SE_PRICE + $staff_price;
			}
			echo json_encode(array('status' => 1, 'message' => $data, 'total_price' => number_format ($total_price, 2)));
			break;
		case 2:
			echo json_encode(array('status' => 1));
			break;
		case 3:
			echo json_encode(array('status' => 1));
			break;
		case 4:
			include_once ('stripe/Stripe.php');
			Stripe::setApiKey(STRIPE_SECRET);
			$error = '';
			$success = '';
			$org_id = intval ($_REQUEST['org_id']);
			$subscriptions = getSubscriptions(0, 0, true, $org_id); // All subscriptions for this organization
			$skip_activate_account = 0;
			$processPayment = true; //variable to enable/disable processing of payment
			$statement_description = "Expert Online Training - Full Pack " . SUBSCRIPTION_YEAR . " Subscription";
			$number_of_licenses = LE_MIN_ACC; // at least 1 license for the admin user.
			$library_id = 0; // initalize the variable.

			//  Get the library ID
			if( isset($_REQUEST['le']) )
			{
				$library_id = LE_ID;
			}
			else if( isset($_REQUEST['le_sp_dc']) )
			{
				$library_id = LE_SP_DC_ID;
				$statement_description = "Expert Online Training - Starter Pack - Day Camps " . SUBSCRIPTION_YEAR . " Subscription";
				$number_of_licenses = LE_SP_DC_MIN_ACC; // at least 1 license for the admin user.
			}
			else if( isset($_REQUEST['le_sp_oc']) )
			{
				$library_id = LE_SP_OC_ID;
				$statement_description = "Expert Online Training - Starter Pack - Overnight Camps " . SUBSCRIPTION_YEAR . " Subscription";
				$number_of_licenses = LE_SP_OC_MIN_ACC; // at least 1 license for the admin user.
			}
			else if( isset($_REQUEST['le_sp_prp']) )
			{
				$library_id = LE_SP_PRP_ID;
				$statement_description = "Expert Online Training - Starter Pack - Parks & Rec Programs " . SUBSCRIPTION_YEAR . " Subscription";
				$number_of_licenses = LE_SP_PRP_MIN_ACC; // at least 1 license for the admin user.
			}
			else if( isset($_REQUEST['se']) )
			{
				$library_id = SE_ID;
				$statement_description = "Expert Online Training - Child Welfare & Protection " . SUBSCRIPTION_YEAR . " Subscription";
				$number_of_licenses = SE_MIN_ACC; // at least 20 
			}

			// check if there's already an active subscirption for this user and library.
			if(isset($subscriptions))
			{
				foreach ($subscriptions as $subscription)
				{
					if($subscription->library_id == $library_id )
					{
						// dont process payment again
						$processPayment = 0;
						// dont activate the account.
						$skip_activate_account = 1;
					}
				}
			}

			try 
			{
				if (!isset($_REQUEST['total_price']) || (floatval ($_REQUEST['total_price'] <= 0) && isset($_REQUEST['method']) && $_REQUEST['method'] != 'free') ) 
				{
					throw new Exception("The price is invalid");
				}

				$org_id = intval ($_REQUEST['org_id']);
				$user_id = $_REQUEST['user_id'];
				$price = floatval ($_REQUEST['total_price']);

				if ($processPayment) 
				{ //process payment based on status of variable defined above
					
					// check if paying by credit card
					if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'Stripe')
					{

						if (!isset($_REQUEST['cc_card']) && ($_REQUEST['cc_num'] == '' || $_REQUEST['cc_cvc'] == '')) 
						{
							throw new Exception("You must choose a credit card or add a new credit card.");
						}
						
						$cc_card = array (
							"object" => "card",
							"number" => $_REQUEST['cc_num'],
							"exp_month" => $_REQUEST['cc_mon'],
							"exp_year" => $_REQUEST['cc_yr'],
							"cvc" => $_REQUEST['cc_cvc'],
							"name" => $_REQUEST['full_name'],
							"address_line1" => $_REQUEST['address'],
							"address_city" => $_REQUEST['city'],
							"address_state" => $_REQUEST['state'],
							"address_zip" => $_REQUEST['zip'],
							"address_country" => $_REQUEST['country']
						);

						if (isset($_REQUEST['customer_id'])) 
						{
							$customer_id = $_REQUEST['customer_id'];
						} 
						else 
						{
							$customer = create_new_customer ($cc_card, $_REQUEST['email'], $_REQUEST['org_name']); //$customer->{'id'};	
							$customer_id = $customer['customer_id'];
							$card_id = $customer['cc_card'];
						}

						update_post_meta ($org_id, 'stripe_id', $customer_id);
						
						if (isset($_REQUEST['cc_card'])) 
						{
							$card_id = $_REQUEST['cc_card'];
						} 
						else 
						{
							$card_id = $cc_card;
						}
						
						$trans_id = charge_customer ($price, $customer_id, $card_id, $statement_description); //$charge->{'id'};
					}

					$break = "\n";
					$message = "";
					
					if (isset($_REQUEST['le'])) { // Leadership essentials - complete
						$dash_price = $_REQUEST['le_dash_price'];
						$staff_price = $_REQUEST['le_staff_price'];
						$data_disk_price = $_REQUEST['subtotal_dd'];
						// if free, set price to 0
						if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
						{
							$dash_price = 0;
							$staff_price = 0;
						}
						$number_of_licenses += (isset($_REQUEST['le_staff'])) ? $_REQUEST['le_staff'] : 0;
						$subscription_data = array (
							'org_id' => $org_id, 												// org id
							'manager_id' => $user_id,	 										// manager id
							'lib_id' => LE_ID, 													// library id (1=Leadership,2=Clinical,3=Safety)
							'start' => SUBSCRIPTION_START,										// subscription start date
							'end' => SUBSCRIPTION_END,											// subscription end date
							'method' => (isset($_REQUEST['method'])) ? $_REQUEST['method'] : '',	// transaction method
							'trans_id' => $trans_id,											// transaction id
							'date' => date ('Y-m-d'),											// current date
							'total' => number_format ($dash_price+$staff_price+$data_disk_price, 2, '.', ''),	// total price paid
							'data_disk_price' => number_format ($data_disk_price, 2, '.', ''),	// Data Disk price for library
							'dash_price' => number_format ($dash_price, 2, '.', ''),			// dashboard price for library
							'staff_price' => number_format ($staff_price, 2, '.', ''),			// staff price for library
							'dash_dis' => (isset($_REQUEST['le_dash_disc'])) ? $_REQUEST['le_dash_disc'] : 0.00,	// discount for dashboard
							'staff_dis' => (isset($_REQUEST['le_staff_disc'])) ? $_REQUEST['le_staff_disc'] : 0.00,	// discount for staff accounts
							'count' => (isset($_REQUEST['le_staff'])) && (!empty($_REQUEST['le_staff'])) ? $_REQUEST['le_staff'] : LE_MIN_ACC,	// number of staff accounts for subscription
							'status' => 'active',												// subscription status
							'rep_id' => (isset($_REQUEST['rep_id'])) ? $_REQUEST['rep_id'] : 0,	// ID of the rep for the sale
							'notes' => (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] : ''	// any notes
						);

						if (!add_new_subscription($subscription_data)) {
							$message .= "Unable to add subscription for Leadership Essentials" . $break;
						}
					}
					if (isset($_REQUEST['le_sp_dc'])) { // Leadership essentials - Starter Pack - Day Camps
						$dash_price = $_REQUEST['le_sp_dc_dash_price'];
						$staff_price = $_REQUEST['le_sp_dc_staff_price'];
						$data_disk_price = $_REQUEST['subtotal_dd'];
						// if free, set price to 0
						if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
						{
							$dash_price = 0;
							$staff_price = 0;
						}
						$number_of_licenses += (isset($_REQUEST['le_sp_dc_staff'])) ? $_REQUEST['le_sp_dc_staff'] : 0;
						$subscription_data = array (
							'org_id' => $org_id, 												// org id
							'manager_id' => $user_id,	 										// manager id
							'lib_id' => LE_SP_DC_ID, 												// library id (1=Leadership,2=Clinical,3=Safety)
							'start' => SUBSCRIPTION_START,										// subscription start date
							'end' => SUBSCRIPTION_END,											// subscription end date
							'method' => (isset($_REQUEST['method'])) ? $_REQUEST['method'] : '',	// transaction method
							'trans_id' => $trans_id,											// transaction id
							'date' => date ('Y-m-d'),											// current date
							'total' => number_format ($dash_price+$staff_price+$data_disk_price, 2, '.', ''),	// total price paid
							'data_disk_price' => number_format ($data_disk_price, 2, '.', ''),	// Data Disk price for library
							'dash_price' => number_format ($dash_price, 2, '.', ''),			// dashboard price for library
							'staff_price' => number_format ($staff_price, 2, '.', ''),			// staff price for library
							'dash_dis' => (isset($_REQUEST['le_sp_dc_dash_disc'])) ? $_REQUEST['le_sp_dc_dash_disc'] : 0.00,	// discount for dashboard
							'staff_dis' => (isset($_REQUEST['le_sp_dc_staff_disc'])) ? $_REQUEST['le_sp_dc_staff_disc'] : 0.00,	// discount for staff accounts
							'count' => (isset($_REQUEST['le_sp_dc_staff']) && (!empty($_REQUEST['le_sp_dc_staff'])) ? $_REQUEST['le_sp_dc_staff'] : LE_SP_DC_MIN_ACC),	// number of staff accounts for subscription
							'status' => 'active',												// subscription status
							'rep_id' => (isset($_REQUEST['rep_id'])) ? $_REQUEST['rep_id'] : 0,	// ID of the rep for the sale
							'notes' => (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] : ''	// any notes
						);

						if (!add_new_subscription($subscription_data)) {
							$message .= "Unable to add subscription for Leadership Essentials - Starter Pack - Day Camps" . $break;
						}
					}
					if (isset($_REQUEST['le_sp_oc'])) { // Leadership essentials - Starter Pack - Overnight Camps
						$dash_price = $_REQUEST['le_sp_oc_dash_price'];
						$staff_price = $_REQUEST['le_sp_oc_staff_price'];
						$data_disk_price = $_REQUEST['subtotal_dd'];
						// if free, set price to 0
						if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
						{
							$dash_price = 0;
							$staff_price = 0;
						}
						$number_of_licenses += (isset($_REQUEST['le_sp_oc_staff'])) ? $_REQUEST['le_sp_oc_staff'] : 0;
						$subscription_data = array (
							'org_id' => $org_id, 												// org id
							'manager_id' => $user_id,	 										// manager id
							'lib_id' => LE_SP_OC_ID, 												// library id (1=Leadership,2=Clinical,3=Safety)
							'start' => SUBSCRIPTION_START,										// subscription start date
							'end' => SUBSCRIPTION_END,											// subscription end date
							'method' => (isset($_REQUEST['method'])) ? $_REQUEST['method'] : '',	// transaction method
							'trans_id' => $trans_id,											// transaction id
							'date' => date ('Y-m-d'),											// current date
							'total' => number_format ($dash_price+$staff_price+$data_disk_price, 2, '.', ''),	// total price paid
							'data_disk_price' => number_format ($data_disk_price, 2, '.', ''),	// Data Disk price for library
							'dash_price' => number_format ($dash_price, 2, '.', ''),			// dashboard price for library
							'staff_price' => number_format ($staff_price, 2, '.', ''),			// staff price for library
							'dash_dis' => (isset($_REQUEST['le_sp_oc_dash_disc'])) ? $_REQUEST['le_sp_oc_dash_disc'] : 0.00,	// discount for dashboard
							'staff_dis' => (isset($_REQUEST['le_sp_oc_staff_disc'])) ? $_REQUEST['le_sp_oc_staff_disc'] : 0.00,	// discount for staff accounts
							'count' => (isset($_REQUEST['le_sp_oc_staff']) && (!empty($_REQUEST['le_sp_oc_staff'])) ? c : LE_SP_OC_MIN_ACC),	// number of staff accounts for subscription
							'status' => 'active',												// subscription status
							'rep_id' => (isset($_REQUEST['rep_id'])) ? $_REQUEST['rep_id'] : 0,	// ID of the rep for the sale
							'notes' => (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] : ''	// any notes
						);

						if (!add_new_subscription($subscription_data)) {
							$message .= "Unable to add subscription for Leadership Essentials - Starter Pack - Overnight Camps" . $break;
						}
					}
					if (isset($_REQUEST['le_sp_prp'])) { // Leadership essentials - Starter Pack - Parks & Rec Programs
						$dash_price = $_REQUEST['le_sp_prp_dash_price'];
						$staff_price = $_REQUEST['le_sp_prp_staff_price'];
						$data_disk_price = $_REQUEST['subtotal_dd'];
						// if free, set price to 0
						if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
						{
							$dash_price = 0;
							$staff_price = 0;
						}
						$number_of_licenses += (isset($_REQUEST['le_sp_prp_staff'])) ? $_REQUEST['le_sp_prp_staff'] : 0;
						$subscription_data = array (
							'org_id' => $org_id, 												// org id
							'manager_id' => $user_id,	 										// manager id
							'lib_id' => LE_SP_PRP_ID, 												// library id (1=Leadership,2=Clinical,3=Safety)
							'start' => SUBSCRIPTION_START,										// subscription start date
							'end' => SUBSCRIPTION_END,											// subscription end date
							'method' => (isset($_REQUEST['method'])) ? $_REQUEST['method'] : '',	// transaction method
							'trans_id' => $trans_id,											// transaction id
							'date' => date ('Y-m-d'),											// current date
							'total' => number_format ($dash_price+$staff_price+$data_disk_price, 2, '.', ''),	// total price paid
							'data_disk_price' => number_format ($data_disk_price, 2, '.', ''),	// Data Disk price for library
							'dash_price' => number_format ($dash_price, 2, '.', ''),			// dashboard price for library
							'staff_price' => number_format ($staff_price, 2, '.', ''),			// staff price for library
							'dash_dis' => (isset($_REQUEST['le_sp_prp_dash_disc'])) ? $_REQUEST['le_sp_prp_dash_disc'] : 0.00,	// discount for dashboard
							'staff_dis' => (isset($_REQUEST['le_sp_prp_staff_disc'])) ? $_REQUEST['le_sp_prp_staff_disc'] : 0.00,	// discount for staff accounts
							'count' => (isset($_REQUEST['le_sp_prp_staff']) && (!empty($_REQUEST['le_sp_prp_staff'])) ? $_REQUEST['le_sp_prp_staff'] : LE_SP_PRP_MIN_ACC),	// number of staff accounts for subscription
							'status' => 'active',												// subscription status
							'rep_id' => (isset($_REQUEST['rep_id'])) ? $_REQUEST['rep_id'] : 0,	// ID of the rep for the sale
							'notes' => (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] : ''	// any notes
						);

						if (!add_new_subscription($subscription_data)) {
							$message .= "Unable to add subscription for Leadership Essentials - Starter Pack - Parks & Rec Programs" . $break;
						}
					}

					if (isset($_REQUEST['se'])) { // Child Care & Protection (formerly Safety essentials)
						$dash_price = $_REQUEST['se_dash_price'];
						$staff_price = $_REQUEST['se_staff_price'];
						$data_disk_price = $_REQUEST['subtotal_dd'];
						// if free, set price to 0
						if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'free')
						{
							$dash_price = 0;
							$staff_price = 0;
						}
						$number_of_licenses += (isset($_REQUEST['se_staff'])) ? $_REQUEST['se_staff'] : 0;
						$subscription_data = array (
							'org_id' => $org_id, 												// org id
							'manager_id' => $user_id,	 										// manager id
							'lib_id' => SE_ID, 													// library id (1=Leadership,2=Clinical,3=Safety)
							'start' => SUBSCRIPTION_START,										// subscription start date
							'end' => SUBSCRIPTION_END,											// subscription end date
							'method' => (isset($_REQUEST['method'])) ? $_REQUEST['method'] : '',	// transaction method
							'trans_id' => $trans_id,											// transaction id
							'date' => date ('Y-m-d'),											// current date
							'total' => number_format ($dash_price+$staff_price+$data_disk_price, 2, '.', ''),	// total price paid
							'data_disk_price' => number_format ($data_disk_price, 2, '.', ''),	// Data Disk price for library
							'dash_price' => number_format ($dash_price, 2, '.', ''),			// dashboard price for library
							'staff_price' => number_format ($staff_price, 2, '.', ''),			// staff price for library
							'dash_dis' => (isset($_REQUEST['se_dash_disc'])) ? $_REQUEST['se_dash_disc'] : 0.00,	// discount for dashboard
							'staff_dis' => (isset($_REQUEST['se_staff_disc'])) ? $_REQUEST['se_staff_disc'] : 0.00,	// discount for staff accounts
							'count' => (isset($_REQUEST['se_staff']) && (!empty($_REQUEST['se_staff'])) ? $_REQUEST['se_staff'] : SE_MIN_ACC),	// number of staff accounts for subscription
							'status' => 'active',												// subscription status
							'rep_id' => (isset($_REQUEST['rep_id'])) ? $_REQUEST['rep_id'] : 0,	// ID of the rep for the sale
							'notes' => (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] : ''	// any notes
						);

						if (!add_new_subscription($subscription_data)) {
							$message .= "Unable to add subscription for Safety Essentials" . $break;
						}
					}
				}

				if ($message != "") 
				{
					throw new Exception ($message);
				}
				else if (!$skip_activate_account) // if they already have the subscription, skip creating the account.
				{

					// if part of an umbrella group, add a user meta field and post meta field
					if (isset($_REQUEST['ugroup_id']) && $_REQUEST['ugroup_id'])
					{
						$ugroup_id = filter_var($_REQUEST['ugroup_id'],FILTER_SANITIZE_NUMBER_INT);
						if(!update_user_meta($user_id, 'umbrella_group_id', $ugroup_id))
						{
							add_user_meta($user_id, 'umbrella_group_id', $ugroup_id);
						}

						if(!update_post_meta($org_id, 'umbrella_group_id', $ugroup_id))
						{
							add_post_meta($org_id, 'umbrella_group_id', $ugroup_id);
						}
					}

					$data = compact ("org_id", "user_id", "number_of_licenses");

					// Send e-mail message
					$first_name = get_user_meta ( $user_id, 'first_name', true ); // Director's First Name
					$last_name = get_user_meta ( $user_id, 'last_name', true ); // Director's Last Name

					$vars = array(
						'eot_link' => get_site_url(),
						'username' => $_REQUEST['email'],
						'first_name' => $first_name,
						'lost_password' => wp_lostpassword_url(),
						'camp_name' => $_REQUEST['org_name'] // The name of the camp
					);
					$fileLocation = get_template_directory_uri() . '/emailTemplates/NewSubscription.txt'; // Template message
					$body = wp_remote_fopen($fileLocation, "r");
					$subject = 'Your new Subscription Purchase on EOT';
					/* Replace %%VARIABLE%% using vars*/
					foreach($vars as $key => $value)
					{
						$body = preg_replace('/%%' . $key . '%%/', $value, $body);
					}
					$recepients = array(); // List of recepients
					// Recepient information
			        $recepient = array (
			            'name' => $first_name . " " . $last_name,
			            'email' => $_REQUEST['email'],
			            'message' => $body,
			            'subject' => $subject
			        );
			        array_push($recepients, $recepient);
			        // send the e-mail
					$response = sendMail( 'NewSubscription', $recepients, $data );
                    echo json_encode($response);
				}
				else
				{
					echo json_encode(array('status' => 0, 'message' => 'Error: Looks like you are already subscribed to this library so we will not charge you again... :)'));
				}
			} catch (Exception $e) {
				echo json_encode(array('status' => 0, 'message' => $e->getMessage()));
			}
			break;
		default:
			echo json_encode(array('status' => 0, 'message' => 'Invalid steps value'));
	}

	wp_die ();
}

function add_new_subscription ($data) {
	global $wpdb;
	extract ($data);
	$sql = "INSERT INTO ".TABLE_SUBSCRIPTIONS." (org_id, manager_id, library_id, start_date, end_date, method, trans_id, trans_date, price, dash_price, staff_price, data_disk_price, dash_discount, staff_discount, staff_credits, status, rep_id, notes) VALUES ($org_id, $manager_id, $lib_id, '$start', '$end', '$method', '$trans_id', '$date', $total, $dash_price, $staff_price, $data_disk_price, $dash_dis, $staff_dis, $count, '$status', '$rep_id', '$notes')";

	if (!$wpdb->query($sql)) {
		return false;
	} else {
		return true;
	}
}


// just a function to write errors to the error log for debugging purposes.
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

/*******************************************************************************
* Process the payment for the customer via stripe
* int $customer_id - The customer stripe ID
* string $method - Type of payment. Free or Stripe
* float $price - The price given to the customer.
* @param array $data - Subscription upgrade information 
*********************************************************************************/
function chargeCustomer($customer_id = 0, $method = '', $price = 0, $data = array()) 
{
	extract($data);
	 /**
    * Variables required in $data
    * string $statement_description - The statement description
    * int $cc_card
    * int $cc_cvc
    * int $cc_mon
    * int $cc_yr
    * int $full_name
    * int $address
    * int $city
    * int $state
    * int $zip
    * int $country
    * string $director_email
    * string $org_name
   	**/
   	include_once ('stripe/Stripe.php');
	Stripe::setApiKey(STRIPE_SECRET);
	if($method == 'stripe')
	{
		// Check for variables
		if ( $cc_card == '' || $cc_cvc== '' || $cc_mon == '' || $statement_description == '' || $cc_yr == '' || $full_name == ''
			|| $address == '' || $city == '' || $state == '' || $zip == '' || $country == '' || $director_email == '' || $org_name == '') 
		{
			echo json_encode(array('success' => false, 'errors' => 'chargeCustomer error: Invalid input'));
		}
		else
		{
			$cc_card = array (
				"object" => "card",
				"number" => $cc_card,
				"exp_month" => $cc_mon,
				"exp_year" => $cc_yr,
				"cvc" => $cc_cvc,
				"name" => $full_name,
				"address_line1" => $address,
				"address_city" => $city,
				"address_state" => $state,
				"address_zip" => $zip,
				"address_country" => $country
			);
			if($customer_id <= 0)
			{
				try {
					$customer = create_new_customer ($cc_card, $director_email, $org_name);
					$trans_id = charge_customer ($price, $customer_id, $card_id, $statement_description);
					update_post_meta ($org_id, 'stripe_id', $customer_id);
					return $trans_id;
				} catch (Exception $e) {
					echo json_encode(
						array('success' => false, 'errors' => $e->getMessage())
				    );
    				return;
				}	
			}
		}
	}
	else if($method == 'free')
	{
		return 1;
	}
	else
	{
		return null;
    }
}

/*******************************************************************************
* Calculates the total cost by library.
* int $library_id - The Library ID
* int $num_staff_current - The current number of staff.
* int $num_staff_upgrade - The number of staff they wanted to upgrade to
*********************************************************************************/
function calculateCost($library_id, $num_staff_current, $num_staff_upgrade)
{
	$total_number_staff = $num_staff_current + $num_staff_upgrade;
	//Leadership Essentials
	if($library_id == LE_ID)
	{
		if ($total_number_staff >= LE_LVL_3_MIN)
		{
			$staff_price = $num_staff_upgrade * LE_LVL_3_PRICE;
		}
		elseif ($total_number_staff >= LE_LVL_2_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_LVL_2_PRICE;
		}
		elseif ($total_number_staff >= LE_LVL_1_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_LVL_1_PRICE;
		}

	}
	//Clinical Essentials
	else if($library_id == CE_ID)
	{
		if ($total_number_staff >= CE_LVL_1_MIN && $total_number_staff <= CE_LVL_1_MAX) 
		{
			$staff_price = $num_staff_upgrade * CE_LVL_1_PRICE;
		}
		if ($total_number_staff >= CE_LVL_2_MIN) 
		{
			$num = $num_staff_upgrade - CE_LVL_2_MIN + 1;
			$staff_price = (CE_LVL_1_PRICE * CE_LVL_1_MAX) + ($num_staff_upgrade * CE_LVL_2_PRICE);
		}

	}
	//Safety Essentials
	else if($library_id == SE_ID)
	{
		if ($total_number_staff >= SE_LVL_1_MIN && $total_number_staff <= SE_LVL_1_MAX) 
		{
			$staff_price = $num_staff_upgrade * SE_LVL_1_PRICE;
		}
		if ($total_number_staff >= SE_LVL_2_MIN) 
		{
			//$num_staff_upgrade = $num_staff_upgrade - SE_LVL_2_MIN + 1;
			//$staff_price = (SE_LVL_1_PRICE * SE_LVL_1_MAX) + ($num_staff_upgrade * SE_LVL_2_PRICE);
			$staff_price = ($num_staff_upgrade * SE_LVL_2_PRICE);
		}

	}
	//Burn Camps Bundle
	else if($library_id == BCB_ID)
	{
		return null;
	}
	//Leadership Essentials - Limited
	else if($library_id == LEL_ID)
	{
		if ($total_number_staff >= LEL_LVL_3_MIN)
		{
			$staff_price = $num_staff_upgrade * LEL_LVL_3_PRICE;
		}
		elseif ($total_number_staff >= LEL_LVL_2_MIN) 
		{
			$staff_price = $num_staff_upgrade * LEL_LVL_2_PRICE;
		}
		elseif ($total_number_staff >= LEL_LVL_1_MIN) 
		{
			$staff_price = $num_staff_upgrade * LEL_LVL_1_PRICE;
		}
	}
	// Leadership Essentials - Starter Pack - Day Camps
	else if($library_id == LE_SP_DC_ID)
	{
		if ($total_number_staff >= LE_SP_DC_LVL_3_MIN)
		{
			$staff_price = $num_staff_upgrade * LE_SP_DC_LVL_3_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_DC_LVL_2_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_DC_LVL_2_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_DC_LVL_1_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_DC_LVL_1_PRICE;
		}
	}
	// Leadership Essentials - Starter Pack - Overnight Camps
	else if($library_id == LE_SP_OC_ID)
	{
		if ($total_number_staff >= LE_SP_OC_LVL_3_MIN)
		{
			$staff_price = $num_staff_upgrade * LE_SP_OC_LVL_3_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_OC_LVL_2_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_OC_LVL_2_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_OC_LVL_1_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_OC_LVL_1_PRICE;
		}
	}
	// Leadership Essentials - Starter Pack - Park & Rec Programs
	else if($library_id == LE_SP_PRP_ID)
	{
		if ($total_number_staff >= LE_SP_PRP_LVL_3_MIN)
		{
			$staff_price = $num_staff_upgrade * LE_SP_PRP_LVL_3_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_PRP_LVL_2_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_PRP_LVL_2_PRICE;
		}
		elseif ($total_number_staff >= LE_SP_PRP_LVL_1_MIN) 
		{
			$staff_price = $num_staff_upgrade * LE_SP_PRP_LVL_1_PRICE;
		}
	}
	else
	{
		return null;
	}
	return $staff_price;
}

/********************************************************************************************************
 * Calculate subscription upgrade cost
 *******************************************************************************************************/
add_action( 'wp_ajax_calculateCost', 'calculateCost_callback' );
function calculateCost_callback()
{
	if(isset($_REQUEST['library_id']) || isset($_REQUEST['num_staff_current']) || isset($_REQUEST['num_staff_upgrade']))
	{
		$library_id = filter_var($_REQUEST['library_id'],FILTER_SANITIZE_NUMBER_INT);
		$num_staff_current = filter_var($_REQUEST['num_staff_current'],FILTER_SANITIZE_NUMBER_INT);
		$num_staff_upgrade = filter_var($_REQUEST['num_staff_upgrade'],FILTER_SANITIZE_NUMBER_INT);
		$total = calculateCost($library_id, $num_staff_current, $num_staff_upgrade);
		if($total)
		{
			$result['staff_price'] = number_format($total,2);
    		$result['success'] = true;
		}
		else
		{
		    $result['display_errors'] = 'failed';
    		$result['success'] = false;
    		$result['errors'] = 'calculateCost_callback ERROR: Unable to calculate cost. Please contact the administrator.';	
		}
	}
	else
	{
	    $result['display_errors'] = 'failed';
    	$result['success'] = false;
    	$result['errors'] = 'calculateCost_callback ERROR: Invalid Input.';
	}
	echo json_encode( $result );
	wp_die();
}