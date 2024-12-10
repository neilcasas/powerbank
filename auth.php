<?php
// Establish DB connection
$user = 'root';
$password = 'admin'; // change to mysql password
$server = 'localhost:3310'; // change to 3306 if necessary
$database = 'restaurant';

$mysqli = new mysqli($server,$user,$password,$database);

if ($mysqli->connect_error) {
  die('Connect Error('.$mysqli->maxdb_connect_errno().')'.$mysqli->maxdb_connect_error);     
}

?>