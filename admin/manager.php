<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $request_id = $_POST['request_id'];

    if (isset($_POST['approve'])) {
        // Get the request details
        $sql = "SELECT * FROM loan_request WHERE request_id = ?;";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        // Get type of request
        $request_type = strtoupper($_POST['request_type']);

        // Get loan details
        $loan_type = $request['loan_type'];
        $loan_amount = $request['loan_amount'];
        $client_id = $_POST['client_id'];
        $employee_id = $_POST['employee_id'];
        $loan_start_date = date("Y-m-d");
        $loan_end_date = date("Y-m-d");
        $loan_interest_rate = 0.1;

        if ($loan_type === "CAR") {
            $loan_end_date = date("Y-m-d", strtotime($loan_start_date . " + 5 years"));
            $loan_interest_rate = 0.05;
        } else if ($loan_type === "HOUSING") {
            $loan_end_date = date("Y-m-d", strtotime($loan_start_date . " + 15 years"));
            $loan_interest_rate = 0.15;
        } else if ($loan_type === "BUSINESS") {
            $loan_interest_rate = 0.10;
            $loan_end_date = date("Y-m-d", strtotime($loan_start_date . " + 5 years"));
        }

        if ($request_type === "LOAN_CREATE") {
            // Insert into loan table
            $sql = "INSERT INTO loan (client_id, employee_id, loan_type, loan_amount, loan_start_date, loan_end_date, loan_interest_rate) VALUES (?, ?, ?, ?, ?, ?, ?);";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $mysqli->error);
            }
            $stmt->bind_param("iisdsdd", $client_id, $employee_id, $loan_type, $loan_amount, $loan_start_date, $loan_end_date, $loan_interest_rate);
            $stmt->execute();
            if ($stmt->error) {
                die("Execute failed: " . $stmt->error);
            }
        }
    }

    // Delete the request from loan sub table
    $sql = "DELETE FROM loan_request WHERE request_id = ?;";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }
    // Delete request from request table
    $sql = "DELETE FROM request WHERE request_id = ?;";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }

    // Redirect to the success page
    header("Location: success.php");
    exit();
}
