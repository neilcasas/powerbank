<?php
// Make sure to use the proper table columns in your SQL query
$sql = "SELECT lr.request_id, l.loan_id, l.loan_amount, l.loan_type
        FROM loan_request lr
        JOIN loan l ON lr.loan_id = l.loan_id
        WHERE lr.request_id = ?";
        
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    // Bind the request_id (make sure it's the correct parameter)
    $stmt->bind_param("i", $request_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $loan_id = $row['loan_id'];
            $loan_amount = $row['loan_amount'];
            $loan_type = $row['loan_type'];
            // Now you can use $loan_id, $loan_amount, $loan_type
        } else {
            // Handle case where no matching record is found
            $error_message = "No loan found for this request.";
        }
    } else {
        $error_message = "Error executing query: " . $stmt->error;
    }
} else {
    $error_message = "Error preparing statement: " . $mysqli->error;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Payment</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-nav .nav-link {
            color: black !important;
        }

        .navbar-nav .nav-link:hover {
            color: #0056b3 !important;
        }

        .navbar-nav .nav-item.active .nav-link {
            color: #0056b3 !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/powerbank/client/dashboard.php">Home</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Balance
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/powerbank/client/balance/deposit.php">Deposit</a>
                            <a class="dropdown-item" href="/powerbank/client/balance/withdraw.php">Withdraw</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRequest" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        <h1>Loan Payment</h1>

        <!-- Display error message -->
        <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php } ?>

        <!-- Display success message -->
        <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php } ?>

        <form action="/powerbank/client/apply/payment.php" method="POST">
            <div class="form-group">
                <label for="loan_id">Select Loan</label>
                <select name="loan_id" id="loan_id" class="form-control" required>
                    <option value="">Select Loan</option>
                    <?php while ($loan = $result->fetch_assoc()) { ?>
                        <option value="<?php echo $loan['loan_id']; ?>">
                            Loan ID: <?php echo $loan['loan_id']; ?> - Balance: <?php echo $loan['loan_balance']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="payment_amount">Payment Amount</label>
                <input type="number" name="payment_amount" id="payment_amount" class="form-control" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Make Payment</button>
            <a href="/powerbank/client/dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
