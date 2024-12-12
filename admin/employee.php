<?php
session_start();
include '../includes/db.php';

// Logic for approving or rejecting requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];

    if (isset($_POST['approve'])) {
        // Get the request details
        $sql = "SELECT * FROM account_request WHERE request_id = ?;";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        $request_type = strtoupper($_POST['request_type']);

        if ($request_type === "ACCOUNT_CREATE") {
            // Insert into account table
            $sql = "INSERT INTO account (client_id, acct_type, acct_level, acct_balance) VALUES (?, ?, ?, 0);";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iss", $_POST['client_id'], $request['acct_type'], $request['acct_level']);
            $stmt->execute();
        }
    }
    // Delete the request
    $sql = "DELETE FROM account_request WHERE request_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    // Redirect to the same page
    header("Location: success.php");
    exit();
}
