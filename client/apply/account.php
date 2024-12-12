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

    // Check if the request type is account_create
    if ($request_type === "ACCOUNT_CREATE") {
        $new_acct_type = strtoupper($_POST['new_acct_type']);
        $new_acct_level = strtoupper($_POST['new_acct_level']);

        // Prepare the SQL statement to insert the account application
        $sql = "INSERT INTO account_request (request_id, acct_type, acct_level, acct_request_type) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("isss", $request_id, $new_acct_type, $new_acct_level, $request_type);
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
    <title>Account Application</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Override the default navbar link color to remove the blue color */
        .navbar-nav .nav-link {
            color: black !important; /* Change to desired color */
        }

        .navbar-nav .nav-link:hover {
            color: #0056b3 !important; /* Change to desired hover color */
        }

        .navbar-nav .nav-item.active .nav-link {
            color: #0056b3 !important; /* Change active link color */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/powerbank/client/dashboard.php">Home</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Balance
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/powerbank/client/balance/deposit.php">Deposit</a>
                            <a class="dropdown-item" href="/powerbank/client/balance/withdraw.php">Withdraw</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRequest" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Request
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownRequest">
                            <a class="dropdown-item" href="/powerbank/client/apply/loan.php">Loan Request</a>
                            <a class="dropdown-item" href="/powerbank/client/apply/account.php">Account Create</a>
                            <a class="dropdown-item" href="/powerbank/client/apply/closure.php">Account Delete</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Account Application</h1>
        <h2>Select Account Type to Open</h2>
        <form action="/powerbank/client/apply/account.php" method="post">
            <input type="hidden" name="request_type" value="account_create">
            <div class="form-group">
                <label for="new_acct_type">Request Type</label>
                <select name="new_acct_type" id="new_acct_type" class="form-control" required>
                    <option value="savings">New Savings Account</option>
                    <option value="checking">New Checking Account</option>
                </select>
            </div>
            <div class="form-group">
                <label for="new_acct_level">Account Type</label>
                <select name="new_acct_level" id="new_acct_level" class="form-control" required>
                    <option value="regular">Regular</option>
                    <option value="premium">Premium</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
            <div class="buttons" class="d-flex mx-2">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="/powerbank/client/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
