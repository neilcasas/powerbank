<?php

session_start();
include '../../includes/db.php';

// maybe show error page
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'CLIENT') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

$client_id = $_SESSION['id'];

// Query for all accounts of user
$sql = "SELECT * FROM account WHERE client_id = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $client_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $accounts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    echo "Error: " . $stmt->error;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Account Closure</h1>

    <h2>Select an account to close</h2>

    <table>
    </table>
</body>

</html>