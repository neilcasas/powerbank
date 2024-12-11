<?php
session_start();
include '../../includes/db.php';


// maybe replace to 403 forbidden
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'client') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

// If a post request was made to this page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $client_id = $_SESSION['id'];
    $request_type = $_POST['request_type'];
    $request_date = date('Y-m-d');

    if ($request_type === "savings_create" || $request_type === "checking_create") {
        // Prepare the SQL query
        $sql = 'INSERT INTO request (client_id, request_type, request_date) VALUES (?, ?, ?)';
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            // Bind the parameters to the prepared statement
            $stmt->bind_param('iss', $client_id, $request_type, $request_date);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the success
                header("Location: /powerbank/client/apply/success.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing query: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Application</title>
</head>

<body>
    <h1>Account Application</h1>
    <h2>Step 1: Send Application Form</h2>
    <p>Fill out the form below to apply for a new account.</p>
    <h2>Step 2: Select Account Type to Open</h2>
    <form action="/powerbank/client/apply/account.php" method="post">
        <label for="request_type">Request Type</label>
        <select name="request_type" id="request_type">
            <option value="savings_create">New Savings Account</option>
            <option value="checking_create">New Checking Account</option>
        </select>

        <label for="account_type">Account Type</label>
        <select name="acct_level" id="acct_level">
            <option value="regular">Regular</option>
            <option value="premium">Premium</option>
            <option value="vip">VIP</option>
        </select>

        <button type="submit">Submit</button>
    </form>
</body>

</html>