<?php

include 'dbconfig.php';

$sql = "SELECT * FROM wp_eot_users";
$query = $old_db->query($sql);
while ($result = mysqli_fetch_array($query)) {

    $ID = $result['ID'];
    $user_login = $result['user_login'];
    $user_pass = $result['user_pass'];
    $user_nicename = $result['user_nicename'];
    $user_email = $result['user_email'];
    $user_url = $result['user_url'];
    $user_registered = $result['user_registered'];
    $user_activation_key = $result['user_activation_key'];
    $user_status = $result['user_status'];
    $display_name = $result['display_name'];
    $insert_sql = "INSERT INTO wp_users (ID,user_login,user_pass,user_nicename,user_email,user_url,user_registered,user_activation_key,user_status,display_name) ";
    $insert_sql.= "VALUES($ID,'$user_login','$user_pass','$user_nicename','$user_email','$user_url','$user_registered','$user_activation_key',$user_status,'$display_name');";
    //$insert = mysqli_query($new_db, $insert_sql);
    echo $insert_sql;
    
}
// Close the connection
mysqli_close($old_db);
mysqli_close($new_db);
?>
                