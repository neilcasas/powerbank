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
    <title>Loan Application</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Override the default navbar link color to remove the blue color */
        .navbar-nav .nav-link {
            color: black !important;
            /* Change to desired color */
        }

        .navbar-nav .nav-link:hover {
            color: #0056b3 !important;
            /* Change to desired hover color */
        }

        .navbar-nav .nav-item.active .nav-link {
            color: #0056b3 !important;
            /* Change active link color */
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
        <h1>Loan Application</h1>
        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php } ?>
        <form action="/powerbank/client/apply/loan.php" method="POST">
            <input type="hidden" name="request_type" value="LOAN_CREATE">
            <div class="form-group">
                <label for="new_loan_amount">Loan Amount</label>
                <input type="number" name="new_loan_amount" id="new_loan_amount" class="form-control" min="100000" max="1000000" required>
            </div>
            <div class="form-group">
                <label for="new_loan_type">Loan Type</label>
                <select name="new_loan_type" id="new_loan_type" class="form-control" required>
                    <option value="business">Business Loan</option>
                    <option value="car">Car Loan</option>
                    <option value="housing">Housing Loan</option>
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