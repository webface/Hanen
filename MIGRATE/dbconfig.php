<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('kint/Kint.class.php');

$hostname = 'localhost';
$username = 'root';
$password = 'Ch33tah$1';

$old_db = mysqli_connect($hostname,$username,$password,"EOT_ONE") or die("Some error occurred during connection " . mysqli_error($old_db));  

$hostname2 = 'localhost';
$username2 = 'root';
$password2 = 'Ch33tah$1';

$new_db = mysqli_connect($hostname2,$username2,$password2,"EOT_NEW") or die("Some error occurred during connection " . mysqli_error($new_db));  


