<?php
include 'helper/helper.php';

// Start the session
session_start();

// Get username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

$mysqli = create_connection();

if ($mysqli->connect_error) {
    die('Connect Error(' . $mysqli->connect_errno . ')' . $mysqli->connect_error);     
}

// Admin credentials check
if ($username === "admin" && $password === "admin123") {
    $_SESSION['username'] = $username;
    header("Location: admin.php");
    exit();
}

// Query the database
$sql = "SELECT * FROM credentials WHERE username = '$username' AND password = '$password'";
$result = $mysqli->query($sql);

if ($result->num_rows === 1) {
    $sql = "SELECT client_id  FROM client WHERE email = \"$username\"";
    $result = $mysqli->query($sql);
    $client_id = $result->fetch_assoc()['client_id'];
    $_SESSION['client_id'] = $client_id;
    header("Location: client/client.php");
    exit();
} else {
    echo "Login failed";
}
?>
