<?php
session_start();
include '../includes/db.php';

// Logic for approving or rejecting requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    try {
        if ($action === 'approve') {
            // Get the request details
            $sql = "SELECT * FROM account_request WHERE request_id = ?;";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }
            $stmt->bind_param("i", $request_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();

            if ($request) {
                $client_id = $_POST['client_id'];
                $acct_type = $request['acct_type'];
                $acct_level = $request['acct_level'];
                $request_type = $request['acct_request_type'];

                if ($request_type === "ACCOUNT_CREATE") {
                    // Insert into account table
                    $sql = "INSERT INTO account (client_id, acct_type, acct_level, acct_balance) VALUES (?, ?, ?, 0);";
                    $stmt = $mysqli->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $mysqli->error);
                    }
                    $stmt->bind_param("iss", $client_id, $acct_type, $acct_level);
                    if (!$stmt->execute()) {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }

                    // Get the last inserted account ID
                    $acct_id = $mysqli->insert_id;

                    // Insert into subtype tables
                    if ($acct_type === "SAVINGS") {
                        if ($acct_level === "VIP") {
                            $sql_savings = "INSERT INTO savings_account (acct_id, savings_interest_rate) VALUES (?, 0.050);";
                        } else if ($acct_level === "PREMIUM") {
                            $sql_savings = "INSERT INTO savings_account (acct_id, savings_interest_rate) VALUES (?, 0.030);";
                        } else {
                            $sql_savings = "INSERT INTO savings_account (acct_id, savings_interest_rate) VALUES (?, 0.025);";
                        }
                        $stmt_savings = $mysqli->prepare($sql_savings);
                        if (!$stmt_savings) {
                            throw new Exception("Prepare failed: " . $mysqli->error);
                        }
                        $stmt_savings->bind_param("i", $acct_id);
                        if (!$stmt_savings->execute()) {
                            throw new Exception("Execute failed: " . $stmt_savings->error);
                        }
                    } else if ($acct_type === "CHECKING") {
                        if ($acct_level === "VIP") {
                            $sql_checking = "INSERT INTO checking_account (acct_id, overdraft_limit) VALUES (?, 20000);";
                        } else if ($acct_level === "PREMIUM") {
                            $sql_checking = "INSERT INTO checking_account (acct_id, overdraft_limit) VALUES (?, 15000);";
                        } else {
                            $sql_checking = "INSERT INTO checking_account (acct_id, overdraft_limit) VALUES (?, 12000);";
                        }
                        $stmt_checking = $mysqli->prepare($sql_checking);
                        if (!$stmt_checking) {
                            throw new Exception("Prepare failed: " . $mysqli->error);
                        }
                        $stmt_checking->bind_param("i", $acct_id);
                        if (!$stmt_checking->execute()) {
                            throw new Exception("Execute failed: " . $stmt_checking->error);
                        }
                    }
                }
            }
        }

        // Delete the request
        $sql = "DELETE FROM account_request WHERE request_id = ?;";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("i", $request_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Redirect to the same page
        header("Location: employee.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get account request tables to be displayed
$sql = "SELECT * FROM account_request;";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo "Prepare failed: " . $mysqli->error;
    exit();
}
$stmt->execute();
$result = $stmt->get_result();
$account_requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Employee Dashboard</h1>
        <div class="account-requests">
            <h2>Account Requests</h2>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Request ID</th>
                        <th>Client ID</th>
                        <th>Account Request Type</th>
                        <th>Account Type</th>
                        <th>Account Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($account_requests as $request) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['client_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['acct_request_type']); ?></td>
                            <td><?php echo htmlspecialchars($request['acct_type']); ?></td>
                            <td><?php echo htmlspecialchars($request['acct_level']); ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>