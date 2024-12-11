<?php
$user = "root";
$password = "1234"; // Adjust with your database password
$server = 'localhost:3306'; // change to 3306 if necessary
$database = "powerbank";

// Create connection
$mysqli = new mysqli($server,$user,$password,$database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>