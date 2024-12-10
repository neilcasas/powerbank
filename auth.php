<?php
// Establish DB connection
$user = 'root';
$password = 'admin'; // change to MySQL password
$server = 'localhost:3310'; // change to 3306 if necessary
$database = 'powerbank';

$mysqli = new mysqli($server, $user, $password, $database);

if ($mysqli->connect_error) {
  die('Connect Error(' . $mysqli->connect_errno . ')' . $mysqli->connect_error);     
}

// Get username and password from auth form
$username = $_POST['username'];
$password = $_POST['password'];

// Check if username and password are admin credentials
if ($username === "admin" && $password === "admin123") {
    header("Location: admin.php");
    exit();
}

// Query the credentials table for other users
$sql = "SELECT * FROM credentials";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['username'] === $username && $row['password'] === $password) {
            header("Location: client.php");
            exit();
        }
    }
}

// If no match is found
echo "Login failed";
?>
