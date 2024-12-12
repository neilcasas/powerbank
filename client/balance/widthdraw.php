<?php
session_start();
include '../../includes/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'CLIENT') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

// If a post request was made to this page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $client_id = $_SESSION['id'];
    $new_balance_amount = intval($_POST['new_balance_amount']);

    // Start a transaction to ensure data integrity
    $mysqli->begin_transaction();

    try {
        // First, check if the client has a suitable account with sufficient balance
        $sql = "SELECT acct_id, acct_balance FROM account 
                WHERE client_id = ? AND acct_balance >= ? 
                LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("id", $client_id, $new_balance_amount);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Insufficient account balance");
        }

        // Fetch the account details
        $account = $result->fetch_assoc();
        $acct_id = $account['acct_id'];
        $current_balance = $account['acct_balance'];
        

        // Insert into parent request table
        $sql = "INSERT INTO request (client_id, acct_id, acct_type, acct_level, acct_balance) 
                VALUES (?, ?, (SELECT acct_type FROM account WHERE acct_id = ?), 
                        (SELECT acct_level FROM account WHERE acct_id = ?), ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiiid", $client_id, $acct_id, $acct_type, $acct_level, a );
        $stmt->execute();

        // Retrieve the generated request_id
        $request_id = $mysqli->insert_id;

        // Insert loan request
        $sql = "INSERT INTO loan_request (request_id, client_id, loan_type, loan_amount) 
                VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iisi", $request_id, $client_id, $new_loan_type, $new_loan_amount);
        $stmt->execute();

        // Update account balance
        $sql = "UPDATE account SET acct_balance = acct_balance - ? WHERE acct_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("di", $new_loan_amount, $acct_id);
        $stmt->execute();

        // Commit the transaction
        $mysqli->commit();

        // Redirect to success page
        header("Location: /powerbank/client/apply/success.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $mysqli->rollback();

        // Store error message in session to display on the next page
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: /powerbank/client/apply/loan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Widthdrawal</h1>
        
        <?php
        // Display error message if exists
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        
        <form action="/powerbank/client/apply/loan.php" method="POST">
            <div class="form-group">
                <label for="withdraw">Withdraw Amount</label>
                <input type="number" name="withdraw" id="withdraw" class="form-control" required min="1">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>