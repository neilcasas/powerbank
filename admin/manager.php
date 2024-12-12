<?php

if ($_SERVER === "POST") {
    $request_id = $_POST['request_id'];
    if (isset($_POST['approve'])) {
        // Get the request details
        $sql = "SELECT * FROM loan_request WHERE request_id = ?;";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        $request_type = $request['loan_request_type'];

        if ($request_type === "LOAN_CREATE") {
            // Insert into loan table
            $sql = "INSERT INTO loan (client_id, loan_amount, loan_interest_rate, loan_duration, loan_status) VALUES (?, ?, ?, ?, 'APPROVED');";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("idii", $request['client_id'], $request['loan_amount'], $request['loan_interest_rate'], $request['loan_duration']);
            $stmt->execute();
        }
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
