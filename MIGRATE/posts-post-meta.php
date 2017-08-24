<?php

include 'dbconfig.php';

$sql = "SELECT * FROM wp_eot_posts where post_type = 'org'";
$query = $old_db->query($sql);
$post_ids = array();
while ($result = mysqli_fetch_array($query)) {

    $ID = $result['ID'];
    $post_author = $result['post_author'];
    $post_date = $result['post_date'];
    $post_date_gmt = $result['post_date_gmt'];
    $post_content = $result['post_content'];
    $post_title = $result['post_title'];
    $post_excerpt = $result['post_excerpt'];
    $post_status = $result['post_status'];
    $comment_status = $result['comment_status'];
    $ping_status = $result['ping_status'];
    $post_password = $result['post_password'];
    $post_name = $result['post_name'];
    $to_ping = $result['to_ping'];
    $pinged = $result['pinged'];
    $post_modified = $result['post_modified'];
    $post_modified_gmt = $result['post_modified_gmt'];
    $post_content_filtered = $result['post_content_filtered'];
    $post_parent = $result['post_parent'];
    $guid = $result['guid'];
    $menu_order = $result['menu_order'];
    $post_type = $result['post_type'];
    
    array_push($post_ids, $result['ID']);


    $insert_sql = "INSERT INTO wp_posts (ID,post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type) ";
    $insert_sql.= "VALUES($ID,$post_author,'$post_date','$post_date_gmt','$post_content','$post_title','$post_excerpt','$post_status','$comment_status','$ping_status','$post_password','$post_name','$to_ping','$pinged','$post_modified','$post_modified_gmt','$post_content_filtered',$post_parent,'$guid',$menu_order,'$post_type');";
    //$insert = mysqli_query($new_db, $insert_sql);
    echo $insert_sql;
    
}
$post_ids = implode(',',$post_ids);
$sql2 = "SELECT * FROM wp_eot_postmeta WHERE post_id IN($post_ids)";
$query = $old_db->query($sql2);
while ($result = mysqli_fetch_array($query)) {
    $meta_id = $result['meta_id'];
    $post_id = $result['post_id'];
    $meta_key = $result['meta_key'];
    $meta_value = $result['meta_value'];
    $insert_sql = "INSERT INTO wp_postmeta (meta_id,post_id,meta_key,meta_value) ";
    $insert_sql.= "VALUES($meta_id,$post_id,'$meta_key','$meta_value');";
    //$insert = mysqli_query($new_db, $insert_sql);
    echo $insert_sql;
}
// Close the connection
mysqli_close($old_db);
mysqli_close($new_db);
?>
                