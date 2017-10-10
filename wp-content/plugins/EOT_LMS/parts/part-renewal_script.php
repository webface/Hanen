<style type="text/css">
    table, caption, tbody, tfoot, thead, tr, th, td {
        font-size: 95% !important;
    }
</style>
<?php
global $wpdb;
// enqueue required javascripts
wp_enqueue_script('datatables-buttons', get_template_directory_uri() . '/js/dataTables.buttons.min.js', array('datatables-js'), '1.2.4', true);
wp_enqueue_script('buttons-flash', get_template_directory_uri() . '/js/buttons.flash.min.js', array(), '1.2.4', true);
wp_enqueue_script('jszip', get_template_directory_uri() . '/js/jszip.min.js', array(), '2.5.0', true);
wp_enqueue_script('vfs-fonts', get_template_directory_uri() . '/js/vfs_fonts.js', array(), '0.1.24', true);
wp_enqueue_script('buttons-html5', get_template_directory_uri() . '/js/buttons.html5.min.js', array(), '1.2.4', true);
wp_enqueue_script('buttons-print', get_template_directory_uri() . '/js/buttons.print.min.js', array(), '1.2.4', true);
$query = "
        	SELECT u.ID as ID, u.display_name,u.user_email,uma.meta_value AS 'first_name', umb.meta_value AS 'last_name', 'manager' as role, s.trans_date, s.dash_price, s.id AS 'subscription_id', s.library_id   
        	FROM wp_users u 
        	LEFT JOIN wp_usermeta um ON u.ID = um.user_id
                LEFT JOIN wp_subscriptions s ON u.ID = s.manager_id
                LEFT JOIN wp_usermeta uma on uma.user_id = u.ID
                LEFT JOIN wp_usermeta umb on umb.user_id = u.ID
        	WHERE um. meta_key = 'wp_capabilities'
                AND uma.meta_key = 'first_name'
                AND umb.meta_key = 'last_name'
        	AND um.meta_value LIKE '%\"manager\"%'
        ";

$managers = $wpdb->get_results($query, ARRAY_A);
//d($managers);
// Tables that will be displayed in the front end.
$usersTableObj = new stdClass();
$usersTableObj->rows = array();
$usersTableObj->headers = array(
    'ID' => 'center',
    'Name' => 'left',
    'Email' => 'center',
    'First_name' => 'center',
    'Last_name' => 'center',
    'Date' => 'center',
    'Price' => 'center',
    'Hash' => 'center',
    'Library' => 'center',
    'Link' => 'center'
);

foreach ($managers as $manager) {
    $hash = wp_hash($manager['user_email'].$manager['subscription_id']);
    //$subscription_id = getSubscriptionIdByUser($manager['ID']);
    $usersTableObj->rows[] = array(
        $manager['ID'],
        $manager['display_name'],
        $manager['user_email'],
        $manager['first_name'],
        $manager['last_name'],
        $manager['trans_date'],
        $manager['dash_price'],
        $hash,
        $manager['library_id'],
        '<a href="https://www.expertonlinetraining.com/renewal/?id='.$manager["ID"].'&key='.$hash.'">renew</a>'
    );
   // d($manager['user_email'].$manager['subscription_id']);
}
CreateDataTable($usersTableObj, "100%", 25, true, "Renewal List"); // Print the table in the page
