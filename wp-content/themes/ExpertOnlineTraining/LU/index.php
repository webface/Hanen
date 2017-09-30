<?php

	require('../../../../wp-load.php');
	require('ajax_functions.php');
	
	require_once(get_template_directory() . '/LU/kint/Kint.class.php');

	// define LearnUpon API keys
	define ('LU_USERNAME', '824ef2d7d93928a069ab');
	define ('LU_PASSWORD', '1623b1d32030b6492c00a343e8546c');

	// base courses (course name => LU course ID)
	$base_courses = array (
		"New Staff" => '128158',
		"Returning Staff" => '128144',
		"Program Staff" => '128145',
		"Supervisory Staff" => '128140'
	);

	// define variables for Leadership Essentials LU course ID
	define ('lrn_upon_LE_Course_ID', 143529);

	// define variables for Leadership Essentials Limited LU course ID
	define ('lrn_upon_LEL_Course_ID', 128143);

	// define variables for Child Welfare & Protection (formarly Safety Essentials) LU course ID
	define ('lrn_upon_SE_Course_ID', 128171);

	// define the LU course ids for the starter packs
	define ('lrn_upon_LE_SP_DC_Course_ID',143886);
	define ('lrn_upon_LE_SP_OC_Course_ID',143887);
	define ('lrn_upon_LE_SP_PRP_Course_ID',143888);

	define ('lrn_upon_LE_Course_TITLE', 'Leadership Essentials');

	global $wpdb;

	require_once(get_template_directory() . '/LU/data.php');
	require_once(get_template_directory() . '/LU/LU_functions.php');

	$process = 0; // boolean whether to delete and clone or just debug
	$num_portals_to_process = 10; // the number of portals to process.
	$num_users_to_process = 99999; // the number of users to process.
	$admin_ajax_url = admin_url('admin-ajax.php');

	// make sure were alowed to use this script
	$cid = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 0;
	if ($cid != 'zxasqw12~')
	{
		exit;
	}

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'nothing';
	// ajax functions
	if ($action == 'ajax_find_wrong_num_modules')
	{
		ajax_find_wrong_num_modules();
		die();
	}
	else if ($action == 'ajax_processUser')
	{
		ajax_processUser();
		die();
	}
        else if ($action == 'ajax_processStats')
	{
		ajax_processStats();
		die();
	}

?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://use.fontawesome.com/27982da7ee.js"></script>
<style type="text/css" src="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"></style>
<style type="text/css">
	.red {
		color: red;
	}
</style>
</head>
<body>

<?php

	if ($action == 'import_users')
	{
/*
$old_id = 61;
$old_org_id = $wpdb->get_var( "SELECT meta_value FROM wp_eot_usermeta WHERE user_id = $old_id AND meta_key = 'org_id'" );
$org = $wpdb->get_row( "SELECT * FROM wp_eot_posts WHERE ID = $old_org_id", ARRAY_A );
$org_meta = $wpdb->get_results ( "SELECT * FROM wp_eot_postmeta WHERE post_id = $old_org_id", ARRAY_A );
$org['ID'] = 0;
//ddd($old_id, $old_org_id, $org, $org_meta);
*/

		echo "going to import all users ...";

		// get sales managers
        $query = "
        	SELECT u.ID as old_id, u.user_login, u.user_pass, u.user_email, 'sales_manager' as role 
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id 
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%sales_manager%'
        ";

        $sales_managers1 = $wpdb->get_results( $query, ARRAY_A );
		$sales_managers1_json = json_encode($sales_managers1, JSON_FORCE_OBJECT);    
d($sales_managers1, $sales_managers1_json);    

		// get sales reps
        $query = "
        	SELECT u.ID as old_id, u.user_login, u.user_pass, u.user_email, 'salesrep' as role 
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id 
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%salesrep%'
        ";

        $sales_reps = $wpdb->get_results( $query, ARRAY_A );
		$sales_reps_json = json_encode($sales_reps, JSON_FORCE_OBJECT);
d($sales_reps, $sales_reps_json);

		// get directors
        $args = array(
            'role__in' => array ('manager'),
            'role__not_in' => array('administrator', 'student', 'salesrep', 'sales_manager'),
            'number' => -1,
            'fields' => array ('ID', 'display_name', 'user_email', 'user_registered')
        );
        
        // get managers
        $query = "
        	SELECT u.ID as old_id, u.user_login, u.user_pass, u.user_email, 'manager' as role 
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id 
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%\"manager\"%'
        ";

        $managers = $wpdb->get_results( $query, ARRAY_A );
        $managers_json = json_encode($managers, JSON_FORCE_OBJECT);
d($managers, $managers_json);

		// get students
        $query = "
        	SELECT u.ID as old_id, u.user_login, u.user_pass, u.user_email, 'student' as role 
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id 
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%\"student\"%'
        ";

        $students = $wpdb->get_results( $query, ARRAY_A );
        $students_json = json_encode($students, JSON_FORCE_OBJECT);
d($students, $students_json);

		// the javascript that will process creating these users
?>
	<div class="processing"></div>
	<p>&nbsp;</p>
	<div class="ajax_response"></div>

		<script type="text/javascript">
			var sales_managers = JSON.parse('<?= $sales_managers1_json ?>');
//dump(sales_managers);
		    var sales_reps = JSON.parse('<?= $sales_reps_json ?>');
		    var managers = JSON.parse('<?= $managers_json ?>');
		    var students = JSON.parse('<?= $students_json ?>');


			var max = <?= $num_users_to_process ?>;
			var count = 0;
			var len = ObjectLength(sales_managers);
			console.log("Count: " + count + " Length = " + len);
			var counter = count + 1;

			var myUser = new Object();
			myUser = sales_managers[count];
//dump3(myUser);

			console.log("PROCESSING: " + myUser.user_login);

 			$('.ajax_response').append("<p>Processing sales_managers</p>");
			$('.processing').html("Processing " + counter + " out of " + len + " " + myUser.role);
		    processUsers(myUser);



		    function processUsers(myUser) 
		    {
			    var data = "action=ajax_processUser&old_id=" + myUser.old_id + "&user_login=" + myUser.user_login + "&user_pass=" + myUser.user_pass + "&user_email=" + myUser.user_email + "&role=" + myUser.role + "&cid=zxasqw12~";

			    $('.ajax_response').append("Processing <b>" + myUser.user_login + "</b> old_id: " + myUser.old_id + " role: " + myUser.role + "<br>");

		        $.ajax(
		        {
		            type: "POST",
		            url: "http://eotv5.dev/wp-content/themes/ExpertOnlineTraining/LU/index.php",
		            data: data,
		            dataType: "json",
		            success: function(response) 
		            {
		            	if (response.status == 1)
		            	{
							$('.ajax_response').append(response.message + "<br>");
		            	}
		            	else
		            	{
							$('.ajax_response').append("ERROR: " + response.message + "<br>");
		            	}
		            	count++;	

		            	if (count < len && count < max)
		            	{
					 		var myNewUser = new Object();
					 		if (myUser.role == 'sales_manager')
					 		{
					 			myNewUser = sales_managers[count];
					 		}
					 		else if (myUser.role == 'salesrep')
					 		{
					 			myNewUser = sales_reps[count];
					 		}
					 		else if (myUser.role == 'manager')
					 		{
					 			myNewUser = managers[count];
					 		}
					 		else if (myUser.role == 'student')
					 		{
					 			myNewUser = students[count];
					 		}
							
							console.log("PROCESSING: " + myNewUser.user_login);
							counter = count + 1;
				        	$('.processing').html("Processing " + counter + " out of " + len + " " + myNewUser.role);
					        processUsers(myNewUser);
		            	}
		            	else if (count >= len || count >= max)
		            	{
		            		// process the next batch
		            		if (myUser.role == 'sales_manager')
		            		{
		            			// do sales reps next
								count = 0;
								len = ObjectLength(sales_reps);
								console.log("Count: " + count + " Length = " + len);
								counter = count + 1;
					 			$('.ajax_response').append("<p>Processing salesrep</p>");

								myUser = new Object();
								myUser = sales_reps[count];
								console.log("PROCESSING: " + myUser.user_login);

								$('.processing').html("Processing " + counter + " out of " + len + " " + myUser.role);
							    processUsers(myUser);
		            		}
		            		else if (myUser.role == 'salesrep')
		            		{
		            			// do directors (manager) next
								count = 0;
								len = ObjectLength(managers);
								console.log("Count: " + count + " Length = " + len);
								counter = count + 1;
					 			$('.ajax_response').append("<p>Processing directors</p>");

								myUser = new Object();
								myUser = managers[count];
								console.log("PROCESSING: " + myUser.user_login);

								$('.processing').html("Processing " + counter + " out of " + len + " " + myUser.role);
							    processUsers(myUser);
		            		}
		            		else if (myUser.role == 'manager')
		            		{
		            			// do students reps next
								count = 0;
								len = ObjectLength(students);
								console.log("Count: " + count + " Length = " + len);
								counter = count + 1;
					 			$('.ajax_response').append("<p>Processing students</p>");

								myUser = new Object();
								myUser = students[count];
								console.log("PROCESSING: " + myUser.user_login);

								$('.processing').html("Processing " + counter + " out of " + len + " " + myUser.role);
							    processUsers(myUser);
		            		}
                                        else if(myUser.role == 'student')
                                        {
                                            //redirect to start loading stats
                                            //$(location).attr("href","http://eotv5.dev/wp-content/themes/ExpertOnlineTraining/LU/index.php?action=import_stats");
                                        }
		            	}
		            },
					error: function(XMLHttpRequest, textStatus, errorThrown) 
					{
						console.log( "ERROR: " + textStatus + " " + errorThrown);
		            	count++;
		            	if (count < len && count < max)
		            	{
					 		var myNewUser = new Object();
					 		if (myUser.role == 'sales_manager')
					 		{
					 			myNewUser = sales_managers[count];
					 		}
					 		else if (myUser.role == 'salesrep')
					 		{
					 			myNewUser = sales_reps[count];
					 		}
					 		else if (myUser.role == 'manager')
					 		{
					 			myNewUser = managers[count];
					 		}
					 		else if (myUser.role == 'student')
					 		{
					 			myNewUser = students[count];
					 		}
							
							console.log("PROCESSING: " + myNewUser.user_login);
							counter = count + 1;
				        	$('.processing').html("Processing " + counter + " out of " + len + " " + myNewUser.role);
					        processUsers(myNewUser);
		            	}

					}		            
		        });
			}


			/* HELPER FUNCTIONS */
			function dump(obj) {
			    var out = '';
			    for (var i in obj) {
			        out += i + ":: " + dump2(obj[i]) + "\n";
			    }

			    alert(out);

			    // or, if you wanted to avoid alerts...

			    var pre = document.createElement('pre');
			    pre.innerHTML = out;
			    document.body.appendChild(pre)
			}

			function dump2(obj) {
			    var out = '';
			    for (var i in obj) {
			        out += i + ": " + obj[i] + "\n";
			    }

			    return out;
			    
			}

			function dump3(obj) {
			    var out = '';
			    for (var i in obj) {
			        out += i + " -> " + obj[i] + "\n";
			    }

			    alert(out);

			    // or, if you wanted to avoid alerts...

			    var pre = document.createElement('pre');
			    pre.innerHTML = out;
			    document.body.appendChild(pre)
			}

			function ObjectLength( object ) {
			    var length = 0;
			    for( var key in object ) {
			        if( object.hasOwnProperty(key) ) {
			            ++length;
			        }
			    }
			    return length;
			};

		</script>

<?php

	}
	else if ($action == 'import_stats')
	{
        
        // get managers
        $query = "
        	SELECT u.ID as old_id, u.user_login, u.user_pass, u.user_email, 'manager' as role 
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id 
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%\"manager\"%'
        ";

        $managers = $wpdb->get_results( $query, ARRAY_A );
        $managers_json = json_encode($managers, JSON_FORCE_OBJECT);
        d($managers, $managers_json);    
        
?>
        <div class="processing"></div>
	<p>&nbsp;</p>
	<div class="ajax_response"></div>
        <script type="text/javascript">
        
            var managers = JSON.parse('<?= $managers_json ?>');
            var max = <?= $num_users_to_process ?>;
            var count = 0;
            var len = ObjectLength(managers);
            console.log("Count: " + count + " Length = " + len);
			var counter = count + 1;

			var myUser = new Object();
			myUser = managers[count];
//dump3(myUser);

			console.log("PROCESSING: " + myUser.user_login);

 			$('.ajax_response').append("<p>Processing sales_managers</p>");
			$('.processing').html("Processing " + counter + " out of " + len + " " + myUser.role);
		    processUsers(myUser);

		    function processUsers(myUser) 
		    {
                        console.log("Process Users")
			    var data = "action=ajax_processStats&old_id=" + myUser.old_id + "&user_login=" + myUser.user_login + "&user_pass=" + myUser.user_pass + "&user_email=" + myUser.user_email + "&role=" + myUser.role + "&cid=zxasqw12~";

			    $('.ajax_response').append("Processing <b>" + myUser.user_login + "</b> old_id: " + myUser.old_id + " role: " + myUser.role + "<br>");

		        $.ajax(
		        {
		            type: "POST",
		            url: "http://eotv5.dev/wp-content/themes/ExpertOnlineTraining/LU/index.php",
		            data: data,
		            dataType: "json",
		            success: function(response) 
		            {
		            	if (response.status == 1)
		            	{
							$('.ajax_response').append(response.message + "<br>");
		            	}
		            	else
		            	{
							$('.ajax_response').append("ERROR: " + response.message + "<br>");
		            	}
		            	count++;	

		            	if (count < len && count < max)
		            	{
					 		var myNewUser = new Object();
					 		
					 			myNewUser = managers[count];
					 		
							
							console.log("PROCESSING: " + myNewUser.user_login);
							counter = count + 1;
				        	$('.processing').html("Processing " + counter + " out of " + len + " " + myNewUser.role);
					        processUsers(myNewUser);
		            	}
		            	else if (count >= len || count >= max)
		            	{
		            		// process the next batch

		            			alert('done');
		            		
		            	}
		            },
					error: function(XMLHttpRequest, textStatus, errorThrown) 
					{
						console.log( "ERROR: " + textStatus + " " + errorThrown);
                                                count++;
                                                if (count < len && count < max)
                                                {
                                                                        var myNewUser = new Object();

                                                                                myNewUser = managers[count];


                                                                        console.log("PROCESSING: " + myNewUser.user_login);
                                                                        counter = count + 1;
                                                                $('.processing').html("Processing " + counter + " out of " + len + " " + myNewUser.role);
                                                                processUsers(myNewUser);
                                                }

					}		            
		        });
			}
                        
                        function ObjectLength( object ) {
			    var length = 0;
			    for( var key in object ) {
			        if( object.hasOwnProperty(key) ) {
			            ++length;
			        }
			    }
			    return length;
			};
            
        </script>
                <?php
	}
	else
	{
		echo 'Doing Nothing!';
	}
?>

	








