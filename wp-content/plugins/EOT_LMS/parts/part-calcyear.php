<?php

$sep1_this_year = strtotime("2017-08-01 00:00");
$today = strtotime('now');
$current_year = 2017;
$next_year = 2018;
$last_year = 2016;
$this_year = 2017;
if($today >= $sep1_this_year)
{
    $year = $next_year;
}
else 
{
    $year = $current_year;
}
$today_year = date( 'Y', current_time( 'timestamp', 0 ) );
if($today_year == 2017)
{
    $start = "2016-09-01";
    $end = "2017-12-31";
}
$today_month = strtotime(date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ));
if($today_month >= $sep1_this_year)
{
    $start = "$current_year-09-01";
    $end = "$next_year-10-15";
}
else 
{
    $start = "$last_year-09-01";
    $end = "$this_year-10-15";
    
}
//echo  "Today: ".$today. '<br />';
//echo  "Sep1: ".$sep1. '<br />';
//echo "current_time( 'mysql' ) returns local site time: " . current_time( 'mysql' ) . '<br />';
//echo "current_time( 'mysql', 1 ) returns GMT: " . current_time( 'mysql', 1 ) . '<br />';
//echo "current_time( 'timestamp' ) returns local site time: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
//echo "current_time( 'timestamp', 1 ) returns GMT: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) );

echo  "Year: ".$year. '<br />';
echo  "Start: ".$start. '<br />';
echo  "End: ".$end. '<br />';
