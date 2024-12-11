<?php
$user = "root";
$password = "admin"; // Adjust with your database password
$server = 'localhost:3310'; // change to 3306 if necessary
$database = "powerbank";

// Create connection
$mysqli = new mysqli($server,$user,$password,$database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>