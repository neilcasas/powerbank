<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Prepare the SQL query to fetch client details and related account information
$sql = "
    SELECT c.client_name, c.email, c.address, c.phone_number, c.date_of_birth, 
           a.acct_id, a.acct_type, a.acct_balance, 
           sa.savings_interest_rate, 
           ca.overdraft_limit, 
           l.loan_id, l.loan_type, l.loan_amount, l.loan_interest_rate, l.loan_start_date, l.loan_end_date
    FROM client c
    LEFT JOIN account a ON c.client_id = a.client_id
    LEFT JOIN savings_account sa ON a.acct_id = sa.acct_id
    LEFT JOIN checking_account ca ON a.acct_id = ca.acct_id
    LEFT JOIN loan l ON c.client_id = l.client_id
    WHERE c.email = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // Bind the email parameter to the prepared statement
    $stmt->bind_param("s", $email);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
    }
    
    // Close the prepared statement
    $stmt->close();
} else {
    echo "Error preparing the query: " . $mysqli->error;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-weight: bold;
        }
        .card {
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            width: 10%;
        }
        
    </style>
</head>
<body class="bg-light.bg-gradient">

<div class="container">
    <?php if (isset($client)): ?>
        <h1 class="text-center mb-4">Welcome, <?= $client['client_name'] ?></h1>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Date of Birth</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $client['client_name'] ?></td>
                            <td><?= $client['email'] ?></td>
                            <td><?= $client['address'] ?></td>
                            <td><?= $client['phone_number'] ?></td>
                            <td><?= $client['date_of_birth'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Account ID</th>
                            <th>Account Type</th>
                            <th>Balance</th>
                            <th>Savings Interest Rate</th>
                            <th>Overdraft Limit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $client['acct_id'] ?></td>
                            <td><?= $client['acct_type'] ?></td>
                            <td><?= $client['acct_balance'] ?></td>
                            <td><?= isset($client['savings_interest_rate']) ? $client['savings_interest_rate'] : 'N/A' ?></td>
                            <td><?= isset($client['overdraft_limit']) ? $client['overdraft_limit'] : 'N/A' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Loan Information</h5>
            </div>
            <div class="card-body">
                <?php if ($client['loan_id']): ?>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Loan ID</th>
                                <th>Loan Type</th>
                                <th>Loan Amount</th>
                                <th>Loan Interest Rate</th>
                                <th>Loan Start Date</th>
                                <th>Loan End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $client['loan_id'] ?></td>
                                <td><?= $client['loan_type'] ?></td>
                                <td><?= $client['loan_amount'] ?></td>
                                <td><?= $client['loan_interest_rate'] ?></td>
                                <td><?= $client['loan_start_date'] ?></td>
                                <td><?= $client['loan_end_date'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No loan information found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="alert alert-danger">No client found with this email.</p>
    <?php endif; ?>

    <form method="POST" class="text-center mt-4">
        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>
</div>

<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /powerbank/");
    exit();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
