<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $wpdb;
$sql = "SELECT * FROM " . TABLE_COURSES_MODULES . " WHERE course_id IN (1,2,3,4)";
$courses = $wpdb->get_results($sql);

$course_module_ids = array_column($courses, 'module_id');
$course_modules_string = implode(',', $course_module_ids);
d($courses, $course_module_ids, $course_modules_string);

foreach ($courses as $course) {
    $modules = $wpdb->get_results("SELECT * FROM " . TABLE_MODULE_RESOURCES . " WHERE module_id=" . $course->module_id);
    $course_id = $course->course_id;
    foreach ($modules as $module) {
        $module_id = $module->module_id;
        $resource_id = $module->resource_id;
        $type=$module->resource_type;
        $sql = "INSERT INTO " . TABLE_COURSE_MODULE_RESOURCES . " (course_id,module_id,resource_id,type) VALUES($course_id,$module_id,$resource_id,'$type')";
        //$result = $wpdb->query($sql);
    }
}