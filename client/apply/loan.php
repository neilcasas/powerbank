<?php
session_start();
include '../../includes/db.php';


// maybe replace to 403 forbidden
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'CLIENT') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

// If a post request was made to this page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $client_id = $_SESSION['id'];
    $request_type = strtoupper($_POST['request_type']);
    $request_date = date('Y-m-d');

    // Insert into parent request table
    $sql = "INSERT INTO request (client_id, request_type, request_date) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iss", $client_id, $request_type, $request_date);
    $stmt->execute();

    // Retrieve the generated request_id
    $request_id = $mysqli->insert_id; // Get the last inserted ID

    if ($request_type === "LOAN_CREATE") {
        $new_loan_type = strtoupper($_POST['new_loan_type']);
        $new_loan_amount = intval($_POST['new_loan_amount']);

        // Prepare loan application
        $sql = "INSERT INTO loan_request (request_id, client_id, loan_type, loan_amount) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iisi", $request_id, $client_id, $new_loan_type, $new_loan_amount);
            if ($stmt->execute()) {
                // Redirect to success page
                header("Location: /powerbank/client/apply/success.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Loan Application</h1>
        <form action="/powerbank/client/apply/loan.php" method="POST">
            <input type="hidden" name="request_type" value="LOAN_CREATE">
            <div class="form-group">
                <label for="new_loan_amount">Loan Amount</label>
                <input type="text" name="new_loan_amount" id="new_loan_amount" class="form-control">
            </div>
            <div class="form-group">
                <label for="new_loan_type">Loan Type</label>
                <select name="new_loan_type" id="new_loan_type" class="form-control">
                    <option value="business">Business Loan</option>
                    <option value="car">Car Loan</option>
                    <option value="housing">Housing Loan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>