<?php
ob_start(); // Turn on output buffers.That stores php codes in output buffer so at first html codes get executed on web page
session_start();
$timezone = date_default_timezone_set("Asia/Kolkata");
$con = mysqli_connect("localhost","root","","social");
if(mysqli_connect_errno()){
    echo "Failed to connect: " . mysqli_connect_error();
}
?>