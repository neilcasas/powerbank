<?php

session_start();
include '../../includes/db.php';

// maybe show error page
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'CLIENT') {
    header("Location: /powerbank/auth/login.php");
    exit();
}

$client_id = $_SESSION['id'];

// Query for all accounts of user
$sql = "SELECT * FROM account WHERE client_id = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $client_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $accounts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    echo "Error: " . $stmt->error;
}


// Send account delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_type = $_POST['request_type'];
    $client_id = $_SESSION['id'];
    $request_date = date('Y-m-d');


    // Insert into parent request table
    $sql = "INSERT INTO request (client_id, request_type, request_date) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iss", $client_id, $request_type, $request_date);
    $stmt->execute();

    // Retrieve the generated request_id
    $request_id = $mysqli->insert_id; // Get the last inserted ID

    // Check if the request type is account_delete
    if ($request_type === "account_delete") {
        $acct_id = $_POST['acct_id'];
        $acct_type = $_POST['acct_type'];

        // Prepare the SQL statement to insert the account application
        $sql = "INSERT INTO account_request (request_id, client_id, acct_id, acct_type, acct_request_type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iisss", $request_id, $client_id, $acct_id, $acct_type, $request_type);
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
    <title>Account Closure Request</title>
</head>

<body>
    <h1>Account Closure Request</h1>

    <h2>Select an account to close</h2>
    <?php $counter = 0; ?>
    <table>
        <tr>
            <th>#</th>
            <th>Account Type</th>
            <th>Account Level</th>
            <th>Account Balance</th>
            <th>Close Account</th>
        </tr>
        <?php foreach ($accounts as $account) : ?>
            <tr>
                <td><?php echo ++$counter ?></td>
                <td><?php echo $account['acct_type']; ?></td>
                <td><?php echo $account['acct_balance']; ?></td>
                <td><?php echo $account['acct_level']; ?></td>
                <td>
                    <form action="closure.php" method="POST">
                        <input type="hidden" name="acct_id" value="<?php echo $account['acct_id']; ?>">
                        <input type="hidden" name="acct_type" value="<?php echo $account['acct_type']; ?>">
                        <input type="hidden" name="acct_level" value="<?php echo $account['acct_level']; ?>">
                        <input type="hidden" name="request_type" value="account_delete">
                        <button type="submit">Close Account</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>