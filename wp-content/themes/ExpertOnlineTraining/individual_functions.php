<?php
/**
 * This file handles all the functions involved with an individual
**/

add_action ('init', 'add_individual_custom_post_type');
add_action('save_post', 'save_indiv_meta', 12, 2);

$indiv_info = array (
	'indiv_title' => array (
		'label' => 'Org/Individual Name',
		'type' => 'text'
	),
        'user_email' => array (
		'label' => 'Email Address / Login',
		'type' => 'text'
	),
	'indiv_phone' => array (
		'label' => 'Phone Number',
		'type' => 'text'
	),
	'indiv_url' => array (
		'label' => 'Website URL',
		'type' => 'text'
	),
	'indiv_address' => array (
		'label' => 'Street Address',
		'type' => 'text'
	),
	'indiv_city' => array (
		'label' => 'City',
		'type' => 'text'
	),
	'indiv_state' => array (
		'label' => 'State/Province',
		'type' => 'state'
	),
	'indiv_country' => array (
		'label' => 'Country',
		'type' => 'country'
	),
	'indiv_zip' => array (
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

function add_individual_custom_post_type () {
	$labels = array(
		'name'               => _x( 'Individuals', 'post type general name', 'eot' ),
		'singular_name'      => _x( 'Individual', 'post type singular name', 'eot' ),
		'menu_name'          => _x( 'Individuals', 'admin menu', 'eot' ),
		'name_admin_bar'     => _x( 'Individual', 'add new on admin bar', 'eot' ),
		'add_new'            => _x( 'Add New', 'book', 'eot' ),
		'add_new_item'       => __( 'Add New Individual', 'eot' ),
		'new_item'           => __( 'New Individual', 'eot' ),
		'edit_item'          => __( 'Edit Individual', 'eot' ),
		'view_item'          => __( 'View Individual', 'eot' ),
		'all_items'          => __( 'All Individuals', 'eot' ),
		'search_items'       => __( 'Search Individuals', 'eot' ),
		'parent_item_colon'  => __( 'Parent Individuals:', 'eot' ),
		'not_found'          => __( 'No individuals found.', 'eot' ),
		'not_found_in_trash' => __( 'No individuals found in Trash.', 'eot' )
	);

	$args = array(
		'labels'             	=> $labels,
		'public'             	=> true,
		'rewrite'            	=> array( 'slug' => 'indiv' ),
		'capability_type'   	=> 'post',
		'has_archive'        	=> false,
		'supports'           	=> array(''),
		'register_meta_box_cb' 	=> 'add_indiv_metaboxes'
	);

	register_post_type( 'indiv', $args );

}

function add_indiv_metaboxes () {
	$screen = get_current_screen ();

	add_meta_box ('wp_indiv_details', 'Individual Details', 'wp_indiv_details', 'indiv', 'normal', 'high');
}

function wp_indiv_details() {
	global $post, $indiv_info;
	$count = 0;
	$total = count ($indiv_info);
	$directors = get_users (array ('role' => 'manager')); ?>

	<div class="half">
	<?php foreach ($indiv_info as $key => $data) { 
		$val = get_post_meta ($post->ID, $key, true);
		if ($data['type'] == 'director') { $val = $post->post_author; }
		if ($key == "indiv_title") { $val = $post->post_title; }

		if ($count == ceil($total/2)) { echo '</div><div class="half">'; }
                if(!isset($data['status'])){$data['status']='enabled';}
		show_admin_field ($key, $data['label'], $data['type'], $val, $data['status']);
		$count++;
	} ?>
	</div>
<?php }



function save_indiv_meta($post_id, $post) {
	global $indiv_info;

	$screen = get_current_screen ();

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	if (!isset($_POST['indiv_author']) || $_POST['indiv_author'] == '') 
		return $post->ID;

	if ( ! wp_is_post_revision( $post_id ) ){
		$old_author = $post->post_author;

		foreach ($indiv_info as $key => $value) {
			if ($key == "indiv_author" || $key == "indiv_title") { continue; }

			if ($key == "indiv_subdomain") {
				$_POST[$key] = preg_replace("/[^a-z]/", '', strtolower ($_POST[$key]));
			}

			update_post_meta ($post->ID, $key, sanitize_text_field($_POST[$key]));
		}

		$author = $_POST['indiv_author'];
		$title = sanitize_text_field ($_POST['indiv_title']);
		$the_post = array (
			'ID' => $post->ID,
			'post_author' => $author,
			'post_title' => $title
		);

		remove_action('save_post', 'save_indiv_meta', 12, 2);
		wp_update_post ($the_post, true);
		add_action('save_post', 'save_indiv_meta', 12, 2);

		//update the user meta to link to this individual instead
		update_user_meta ($author, 'indiv_id', $post_id);
		if ($author != $old_author) {
			delete_user_meta ($old_author, 'indiv_id', $post_id);
		}
	}
}



/**
 * Returns an array of users in the specified individual
 * @return users
**/
function get_users_in_indiv ($indiv_id) {
	return get_users (array(
		'meta_key' => 'indiv_id',
		'meta_value' => $indiv_id,
		'fields' => 'ID'
	));
}

/**
 * Returns the individual that the user belongs to
 * The user can only belong to one individual
 * @return indiv_id
**/
function get_indiv_from_user ($user_id) {
	return get_user_meta ($user_id, 'indiv_id', true);
}