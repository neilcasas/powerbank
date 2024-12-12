<?php
session_start();
include '../../includes/db.php';

// Check if the client is logged in and has the 'CLIENT' role
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'CLIENT') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

$client_id = $_SESSION['id'];

// Fetch all accounts for the logged-in client
$query = "SELECT acct_id, acct_type, acct_level, acct_balance FROM account WHERE client_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle the deposit process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['account_id']) && isset($_POST['amount'])) {
        $account_id = $_POST['account_id'];
        $deposit_amount = $_POST['amount'];

        // Fetch the selected account details
        $query = "SELECT acct_balance FROM account WHERE acct_id = ? AND client_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ii', $account_id, $client_id);
        $stmt->execute();
        $stmt->bind_result($acct_balance);
        $stmt->fetch();
        
        // After fetching the result, free the result set before executing the next query
        $stmt->free_result();

        // Check if the account exists and if the deposit amount is valid
        if ($acct_balance === null) {
            echo "Account not found.";
        } else if ($deposit_amount <= 0) {
            echo "Please enter a valid deposit amount.";
        } else {
            // Proceed with the deposit
            $new_balance = $acct_balance + $deposit_amount;
            $update_query = "UPDATE account SET acct_balance = ? WHERE acct_id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param('di', $new_balance, $account_id);
            if ($update_stmt->execute()) {
                echo "Deposit successful. New balance: " . number_format($new_balance, 2);
                // Refresh the balance after deposit
                header("Location: " . $_SERVER['PHP_SELF']); // Redirect to refresh page
                exit();
            } else {
                echo "Error processing the deposit.";
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/powerbank/client/dashboard.php">Home</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Balance
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/powerbank/client/balance/deposit.php">Deposit</a></li>
                <li><a class="dropdown-item" href="/powerbank/client/balance/withdraw.php">Withdraw</a></li>
            </ul>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Request
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/powerbank/client/apply/loan.php">Loan Request</a></li>
                <li><a class="dropdown-item" href="/powerbank/client/apply/account.php">Account Create</a></li>
                <li><a class="dropdown-item" href="/powerbank/client/apply/closure.php">Account Delete</a></li>
            </ul>
            </li>
        </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2>Select an Account</h2>
    <form method="POST" class="mt-4">
        <div class="row mb-3">
            <label for="account_id" class="form-label">Choose an Account:</label>
            <div class="col-md-6">
                <select name="account_id" id="account_id" class="form-select" required>
                    <option value="">-- Select an Account --</option>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        // Show account details and current balance
                        echo "<option value='" . $row['acct_id'] . "'>" . ucfirst($row['acct_type']) . " - " . ucfirst($row['acct_level']) . " | Balance: " . number_format($row['acct_balance'], 2) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="amount" class="form-label">Deposit Amount:</label>
            <div class="col-md-6">
                <input type="number" name="amount" id="amount" class="form-control" min="500.00" step="0.01" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Deposit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
