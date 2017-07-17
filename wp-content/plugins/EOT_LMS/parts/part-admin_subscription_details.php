<h1 class="article_page_title">Subscription Details</h1>
<?php 
	// check user's role for permission to access this display
	if (!current_user_can('is_sales_rep') && !current_user_can('is_sales_manager'))
	{
		wp_die('You do not have access to this display.');
	}
	if( isset($_REQUEST['subscription_id']) )
	{
		$subscription_id = filter_var($_REQUEST['subscription_id'],FILTER_SANITIZE_NUMBER_INT);
	  	$admin_ajax_url = admin_url('admin-ajax.php');

	 	// Variable declaration
		$subscription = getSubscriptions($subscription_id); // get the subscription info for this subscription
		if (!isset($subscription->ID)) // make sure that there is a subscription
		{
			wp_die("I couldn't find that subscription ID");
		}
		$user_id = $subscription->manager_id; // Director's User ID
		$user = get_user_by('id', $user_id); // Director's information in wordpress
		$org_id =  isset($user->ID) ? get_org_from_user ($user->ID) : 0; // Director's Organization ID
		$camp_name = $org_id > 0 ? get_post($org_id)->post_title : "N/a"; // The name of the camp
		$portal_subdomain = get_post_meta ($org_id, 'org_subdomain', true); // Director's Subdomain of the user
		$data = compact ("org_id");
		$price = $subscription->price; // Subscription price q
		$library = getLibraries($subscription->library_id); // The library of this subscription
		$first_name = get_user_meta ( $user_id, 'first_name', true ); // Director's First Name
		$last_name = get_user_meta ( $user_id, 'last_name', true ); // Director's Last Name
		$rep_id = $subscription->rep_id; // Representative's ID
		$rep_name = get_user_meta ( $rep_id, 'first_name', true );// Name of te representative
	  	$url = get_post_meta ($org_id, 'org_url', true); // Organization URL
  		$courses = getCourses($org_id); // All the courses in the subdomain.

	  	$users = getEotUsers($org_id, 'student'); // gets the users for the org
		if ($users['status'] == 1)
		{
			$learners = $users['users'];
		}
		else
		{
			$learners = 0;
		}
		if($library)
		{
		    // Add upgrade number of staff
		    $upgrades = getUpgrades ($subscription_id);
		    $num_staff = $subscription->staff_credits; // Number of accounts.
		    // Sum all of the upgrades staff accounts and add them to the current subscription staff credits.
		    if($upgrades)
		    {
		        foreach($upgrades as $upgrade)
		        {
		            $num_staff += $upgrade->accounts;
		        }
		    }
	?>
			<table class="data sm">
				<tbody>
					<tr>
						<td class="label">
							Library
						</td>
						<td class="value">
							<?= $library->name?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Subscribe Date
						</td>
						<td class="value">
							<?= dateTimeFormat($subscription->trans_date) ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Start Date
						</td>
						<td class="value">
							<?= dateTimeFormat($subscription->start_date) ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Expire Date
						</td>
						<td class="value">
							<?= dateTimeFormat($subscription->end_date) ?>              
						</td>
					</tr>
					<tr>
						<td class="label">
							Director
						</td>
						<td class="value">

							<?php
							/* 
							 * Display the director info if they exsist, otherwise display N/a 
							 */
							if($user)
							{
								echo $first_name . " " . $last_name . '/ <a href="mailto: ' . $user->user_email .'"><' . $user->user_email . '</a>';
							}
							else
							{
								echo "N/a.";
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="label">
							Camp Name
						</td>
						<td class="value">
							<?= $camp_name ?>        
						</td>
					</tr>
					<tr>
						<td class="label">
							Staff
						</td>
						<td class="value">
							<?= ($learners) ? count($learners) : 0 ?> / <?= $num_staff ?>          
						</td>
					</tr>
					<tr>
						<td class="label">
							Amount
						</td>
						<td class="value">
							$ <?= number_format($subscription->price, 2, ".", "") ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Dash Price
						</td>
						<td class="value">
							$ <?= number_format($subscription->dash_price, 2, ".", "") ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Dash Discount
						</td>
						<td class="value">
							$ <?= number_format($subscription->dash_discount, 2, ".", "") ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Staff Price
						</td>
						<td class="value">
							$ <?= number_format($subscription->staff_price, 2, ".", "") ?>         
						</td>
					</tr>
					<tr>
						<td class="label">
							Staff Discount
						</td>
						<td class="value">
							$ <?= number_format($subscription->staff_discount, 2, ".", "") ?>         
						</td>
					</tr>									
					<tr>
						<td class="label">
							Payment Method
						</td>
						<td class="value">
							<?= $subscription->method ?>        
						</td>
					</tr>
					<tr>
						<td class="label">
							Umbrella Group
						</td>
						<td class="value">
						          
						</td>
					</tr>
					<tr>
						<td class="label">
							Notes
						</td>
						<td class="value">
						<?= $subscription->notes ?>  
						</td>
					</tr>
					<tr>
						<td class="label">
							Sales Rep
						</td>
						<td class="value">
							<?= $rep_name ?>         
						</td>
					</tr>        
					<tr>
						<td></td>
						<td class="value">
							<a href="?part=update_subscription&amp;subscription_id=<?= $subscription_id ?>">Update Subscription Details</a> - 
							<a id="delete_sub" href="<?= $admin_ajax_url ?>?action=getCourseForm&form_name=delete_subscription&amp;org_id=<?= $org_id ?>&subscription_id=<?=$subscription_id?>&amp;portal_subdomain=<?= $portal_subdomain ?>" rel="facebox">Delete Subscription</a><br>
						</td>
					</tr>
				</tbody>
			</table>
<?php
			// Display upgrade history if the director had an upgrade.
		    if($upgrades)
		    {
		    	echo '<h1 class="article_page_title">Upgrades History</h1>';
				// Tables that will be displayed in the front end.
				$upgradeTableObj = new stdClass();
				$upgradeTableObj->rows = array();
			  	$upgradeTableObj->headers = array(
					'Date' => 'center',
					'Total Amount' => 'center',
					'Accounts' => 'center',
					'Sales Rep' => 'center',
					'Discount Note' => 'center',
					'Other Note' => 'center',
				);


				foreach($upgrades as $upgrade)
				{
			  		$upgrade_rep_info = get_userdata($upgrade->rep_id); // Rep Info from WP
	  				$upgrade_rep_name = ($upgrade_rep_info) ? $upgrade_rep_info->first_name . " " . $upgrade_rep_info->last_name : 'Self Upgrade'; // REP first and last name
					// Create table
				 	$upgradeTableObj->rows[] = array(
						$upgrade->date, // Transaction Date
						'$' . number_format($upgrade->price, 2, '.', ''), // Transaction price.
						$upgrade->accounts,
						$upgrade_rep_name, // REP first and last name
						$upgrade->discount_note,
						$upgrade->other_note,
						
					);
				}
			  CreateDataTable($upgradeTableObj); // Print the table in the page
	    	}
?>

			<h1 class="article_page_title"><?= $camp_name ?></h1>
		<?php 
			if($org_id)
			{
				$adress = get_post_meta ($org_id, 'org_address', true);
				$phone = get_post_meta ($org_id, 'org_phone', true);
				$city = get_post_meta ($org_id, 'org_city', true); 
				$state = get_post_meta ($org_id, 'org_state', true);
				$country = get_post_meta ($org_id, 'org_country', true);
		?>
				<table class="data sm">
					<tbody>
						<tr>
							<td class="label">
								Camp ID
							</td>
							<td class="value">
								<?= $org_id ?>         
							</td>
						</tr>
						<tr>
							<td class="label">
							 	Address
							</td>
							<td class="value">
				  				<?= $adress ?>
							</td>
						</tr>
						<tr>
							<td class="label">
								Phone
							</td>
							<td class="value">
								<?= $phone ?>          
							</td>
						</tr>
						<tr>
							<td class="label">
								City
							</td>
							<td class="value">
								<?= $city ?>    
							</td>
						</tr>
						<tr>
							<td class="label">
								State
							</td>
							<td class="value">
								<?= $state ?>         
							</td>
						</tr>
						<tr>
							<td class="label">
								Country
							</td>
							<td class="value">
								<?= $country ?>       
							</td>
						</tr>
						<tr>
							<td class="label">
								Website
							</td>
							<td class="value">
								<a target="_blank" href="<?= $url ?>"><?= $url ?></a>          
							</td>
						</tr>
					</tbody>
				</table>	
				<h2>Courses</h2>
				<ol>    
			<?php 
				if(isset($courses['status']) && $courses['status'] == 0)
				{
					echo $courses['message']; // Expect to have error message in getting courses.
				}
				else
				{	
					foreach( $courses as $course )
					{
						$course_name = $course['name']; // The course name
			?>      
			  			<li>
			    			<b><?= $course_name ?></b>
			  			</li>
			<?php
					}
				}
			?>
				</ol>
		<?php
			}
			else
			{
				echo "Can't find the organization ID.";
			}
		}
		else
		{
			echo "Can't find the library.";
		}
	}
?>
<script>
	$ = jQuery;
	$('a[rel*=facebox]').facebox(); // Initialise Facebox

	/******************************************************************************************
	* Binds a function to the success delete subscription
	* 
	*******************************************************************************************/ 
	$(document).bind('success.delete_subscription',
	function (event,data)
	{
		window.location.replace("<?= get_home_url() ?>/dashboard/?part=admin_view_subscriptions&library_id=" + data.library_id);
  		jQuery(document).trigger('close.facebox');
	});
</script>

