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

        // Display Client Information
        echo "<h1>Welcome, " . $client['client_name'] . "</h1>";

        echo "<h2>Client Information</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Client Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Date of Birth</th>
                </tr>
                <tr>
                    <td>" . $client['client_name'] . "</td>
                    <td>" . $client['email'] . "</td>
                    <td>" . $client['address'] . "</td>
                    <td>" . $client['phone_number'] . "</td>
                    <td>" . $client['date_of_birth'] . "</td>
                </tr>
            </table>";


        // Display Account Information
        echo "<h2>Account Information</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Account ID</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Savings Interest Rate</th>
                    <th>Overdraft Limit</th>
                </tr>";

        // Display Account Details
        echo "<tr>
                <td>" . $client['acct_id'] . "</td>
                <td>" . $client['acct_type'] . "</td>
                <td>" . $client['acct_balance'] . "</td>
                <td>" . (isset($client['savings_interest_rate']) ? $client['savings_interest_rate'] : 'N/A') . "</td>
                <td>" . (isset($client['overdraft_limit']) ? $client['overdraft_limit'] : 'N/A') . "</td>
              </tr>";
        echo "</table>";

        // Display Loan Information
        echo "<h2>Loan Information</h2>";
        if ($client['loan_id']) {
            echo "<table border='1'>
                    <tr>
                        <th>Loan ID</th>
                        <th>Loan Type</th>
                        <th>Loan Amount</th>
                        <th>Loan Interest Rate</th>
                        <th>Loan Start Date</th>
                        <th>Loan End Date</th>
                    </tr>";
            echo "<tr>
                    <td>" . $client['loan_id'] . "</td>
                    <td>" . $client['loan_type'] . "</td>
                    <td>" . $client['loan_amount'] . "</td>
                    <td>" . $client['loan_interest_rate'] . "</td>
                    <td>" . $client['loan_start_date'] . "</td>
                    <td>" . $client['loan_end_date'] . "</td>
                  </tr>";
            echo "</table>";
        } else {
            echo "<p>No loan information found.</p>";
        }
    } else {
        echo "No client found with this email.";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "Error preparing the query: " . $mysqli->error;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Close the database connection
$mysqli->close();
?>

<form method="POST">
    <input type="submit" name="logout" value="Logout">
</form>
