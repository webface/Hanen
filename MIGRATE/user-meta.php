<?php

include 'dbconfig.php';

$sql = "SELECT * FROM wp_eot_usermeta";
$query = $old_db->query($sql);
while ($result = mysqli_fetch_array($query)) {

    $umeta_id = $result['umeta_id'];
    $user_id = $result['user_id'];
    $meta_key = $result['meta_key'];
    $meta_value = $result['meta_value'];


    $insert_sql = "INSERT INTO wp_usermeta (umeta_id,user_id,meta_key,meta_value) ";
    $insert_sql.= "VALUES($umeta_id,$user_id,'$meta_key','$meta_value');";
    //$insert = mysqli_query($new_db, $insert_sql);
    echo $insert_sql;
    
}
// Close the connection
mysqli_close($old_db);
mysqli_close($new_db);
?>
                