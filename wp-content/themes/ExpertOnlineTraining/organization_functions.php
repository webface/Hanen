<?php
/**
 * This file handles all the functions involved with an organization
 * @author Patrick Roy
**/

add_action ('init', 'add_organization_custom_post_type');
add_action('save_post', 'save_org_meta', 12, 2);
add_action('admin_head', 'my_custom_styles');

$org_info = array (
	'org_title' => array (
		'label' => 'Camp/Organization Name',
		'type' => 'text'
	),
	'org_subdomain' => array (
		'label' => 'Subdomain',
		'type' => 'text'
	),
	'org_phone' => array (
		'label' => 'Phone Number',
		'type' => 'text'
	),
	'org_url' => array (
		'label' => 'Website URL',
		'type' => 'text'
	),
	'org_address' => array (
		'label' => 'Street Address',
		'type' => 'text'
	),
	'org_author' => array (
		'label' => 'Director',
		'type' => 'director'
	),
	'org_city' => array (
		'label' => 'City',
		'type' => 'text'
	),
	'org_state' => array (
		'label' => 'State/Province',
		'type' => 'state'
	),
	'org_country' => array (
		'label' => 'Country',
		'type' => 'country'
	),
	'org_zip' => array (
		'label' => 'Zip/Postal Code',
		'type' => 'text'
	)
);

$director_info = array (
	'user_email' => array (
		'label' => 'Email Address / Login',
		'type' => 'text'
	),
	'first_name' => array (
		'label' => 'First Name',
		'type' => 'text'
	),
	'last_name' => array (
		'label' => 'Last Name',
		'type' => 'text'
	),
	'user_password' => array (
		'label' => 'Password',
		'type' => 'text'
	),
	'user_password2' => array (
		'label' => 'Password Confirm',
		'type' => 'text'
	)
);

function add_organization_custom_post_type () {
	$labels = array(
		'name'               => _x( 'Organizations', 'post type general name', 'eot' ),
		'singular_name'      => _x( 'Organization', 'post type singular name', 'eot' ),
		'menu_name'          => _x( 'Organizations', 'admin menu', 'eot' ),
		'name_admin_bar'     => _x( 'Organization', 'add new on admin bar', 'eot' ),
		'add_new'            => _x( 'Add New', 'book', 'eot' ),
		'add_new_item'       => __( 'Add New Organization', 'eot' ),
		'new_item'           => __( 'New Organization', 'eot' ),
		'edit_item'          => __( 'Edit Organization', 'eot' ),
		'view_item'          => __( 'View Organization', 'eot' ),
		'all_items'          => __( 'All Organizations', 'eot' ),
		'search_items'       => __( 'Search Organizations', 'eot' ),
		'parent_item_colon'  => __( 'Parent Organizations:', 'eot' ),
		'not_found'          => __( 'No organizations found.', 'eot' ),
		'not_found_in_trash' => __( 'No organizations found in Trash.', 'eot' )
	);

	$args = array(
		'labels'             	=> $labels,
		'public'             	=> true,
		'rewrite'            	=> array( 'slug' => 'org' ),
		'capability_type'   	=> 'post',
		'has_archive'        	=> false,
		'supports'           	=> array(''),
		'register_meta_box_cb' 	=> 'add_org_metaboxes'
	);

	register_post_type( 'org', $args );

}

function add_org_metaboxes () {
	$screen = get_current_screen ();

	add_meta_box ('wp_org_details', 'Organization Details', 'wp_org_details', 'org', 'normal', 'high');
	if ($screen->action == "add" && $screen->post_type == "org") {
		add_meta_box ('wp_director_details', 'Director Details', 'wp_director_details', 'org', 'normal', 'high');
	}
}

function wp_org_details() {
	global $post, $org_info;
	$count = 0;
	$total = count ($org_info);
	$directors = get_users (array ('role' => 'manager')); ?>

	<div class="half">
	<?php foreach ($org_info as $key => $data) { 
		$val = get_post_meta ($post->ID, $key, true);
		if ($data['type'] == 'director') { $val = $post->post_author; }
		if ($key == "org_title") { $val = $post->post_title; }

		if ($count == ceil($total/2)) { echo '</div><div class="half">'; }
                if(!isset($data['status'])){$data['status']='enabled';}
		show_admin_field ($key, $data['label'], $data['type'], $val, $data['status']);
		$count++;
	} ?>
	</div>
<?php }

function wp_director_details() {
	global $post, $director_info;
	$count = 0;
	$total = count ($director_info);
	?>
	
	<div class="half">
	<?php foreach ($director_info as $key => $data) {
		$val = get_user_meta ($post->post_author, $key, true);
		if ($data['type'] == 'director') { $val = $post->post_author; }

		if ($count == ceil($total/2)) { echo '</div><div class="half">'; }
                if(!isset($data['status'])){$data['status']='enabled';}
		show_admin_field ($key, $data['label'], $data['type'], $val, $data['status']);
		$count++;
	} ?>
	</div>
<?php }

function save_org_meta($post_id, $post) {
	global $org_info;

	$screen = get_current_screen ();

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	if (!isset($_POST['org_author']) || $_POST['org_author'] == '') 
		return $post->ID;

	if ( ! wp_is_post_revision( $post_id ) ){
		$old_author = $post->post_author;

		foreach ($org_info as $key => $value) {
			if ($key == "org_author" || $key == "org_title") { continue; }

			if ($key == "org_subdomain") {
				$_POST[$key] = preg_replace("/[^a-z]/", '', strtolower ($_POST[$key]));
			}

			update_post_meta ($post->ID, $key, sanitize_text_field($_POST[$key]));
		}

		foreach ($director_info as $key => $value) {
			if ($key == "user_email") {
				$user_id = wp_update_user (array ('ID' => $post->post_author, 'user_email' => $_POST[$key]));
				continue;
			}
			update_user_meta ($post->post_author, $key, sanitize_text_field($_POST[$key]));
		}

		$author = $_POST['org_author'];
		$title = sanitize_text_field ($_POST['org_title']);
		$the_post = array (
			'ID' => $post->ID,
			'post_author' => $author,
			'post_title' => $title
		);

		remove_action('save_post', 'save_org_meta', 12, 2);
		wp_update_post ($the_post, true);
		add_action('save_post', 'save_org_meta', 12, 2);

		//update the user meta to link to this organization instead
		update_user_meta ($author, 'org_id', $post_id);
		if ($author != $old_author) {
			delete_user_meta ($old_author, 'org_id', $post_id);
		}
	}
}

function my_custom_styles() {?>
	<style type="text/css">
		.half { 
			float:left;
			width:48%;
			padding-right:2%;
		}
		.inside:after {
			display:block;
			content:'';
			clear:both;
		} 
	</style>
<?php }

function show_admin_field ($name, $label, $type, $value, $status) { 
	global $states, $countries;

	$screen = get_current_screen ();
	if ($screen->action == "add" && $screen->post_type == "org" && $type == "director") {
		return;
	}

	$directors = get_users (array ('role' => 'manager'));?>
	<div class="row">
		<p><strong><?php echo $label; ?></strong></p>
		<?php switch ($type) {
			case 'text':
			case 'password':?>
				<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" id="<?php echo $name; ?>" class="widefat" <?php if ($status == 'disabled') { ?>disabled<?php } ?>/>
			<?php
				break;
			case 'state':?>
				<select name="<?php echo $name; ?>" class="widefat" id="<?php echo $name; ?>">
					<option value="">State / Province</option>
					<?php foreach ($states as $state) { ?>
						<option value="<?php echo $state; ?>" <?php if ($value == $state) { ?>selected="selected"<?php } ?>><?php echo $state; ?></option>
					<?php } ?>
				</select>
			<?php
				break;
			case 'country': ?>
				<select name="<?php echo $name; ?>" class="widefat" id="<?php echo $name; ?>">
					<option value="">Country</option>
					<?php foreach ($countries as $country) { ?>
						<option value="<?php echo $country; ?>" <?php if ($value == $country) { ?>selected="selected"<?php } ?>><?php echo $country; ?></option>
					<?php } ?>
				</select>
			<?php
				break;
			case 'director': ?>
				<select name="<?php echo $name; ?>" class="widefat" id="<?php echo $name; ?>">
					<option value="">Select Director</option>
					<?php foreach ($directors as $director) { ?>
						<option value="<?php echo $director->ID; ?>" <?php if ($value == $director->ID) { ?>selected="selected"<?php } ?>><?php echo $director->user_firstname . " " . $director->user_lastname . " -- " . $director->user_email; ?></option>
					<?php } ?>
				</select>
			<?php
				break;
		} ?>
	</div>
<?php
}

/**
 * Returns an array of users in the specified organization
 * @return users
**/
function get_users_in_org ($org_id) {
	return get_users (array(
		'meta_key' => 'org_id',
		'meta_value' => $org_id,
		'fields' => 'ID'
	));
}

/**
 * Returns the organization that the user belongs to
 * The user can only belong to one organization
 * @return org_id
**/
function get_org_from_user ($user_id) {
	if($user_id == 0)
	{
		return 0;
	}
	return get_user_meta ($user_id, 'org_id', true);
}