<?php
$con = mysqli_connect("localhost","root","3259604","sadik");

if (mysqli_connect_errno()){
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$query = "SET NAMES UTF8";

mysqli_query($con, $query);
date_default_timezone_set('Europe/Istanbul');


define('ADMIN_MAIL', 'yonetim@retasoft.com');

?>