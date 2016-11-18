<?php
$con = mysqli_connect("localhost","root","","sadik");

if (mysqli_connect_errno()){
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$query = "SET NAMES UTF8";

mysqli_query($con, $query);
?>