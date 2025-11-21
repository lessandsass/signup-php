<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "signup";

if (!$connection =mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("Failed to connect!");
} 

