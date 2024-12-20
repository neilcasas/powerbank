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
        $sql = "INSERT INTO account_request (request_id, acct_id, acct_type, acct_request_type) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("isss", $request_id, $acct_id, $acct_type, $request_type);
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
        <h1>Account Closure Request</h1>

        <h2>Select an account to close</h2>
        <?php $counter = 0; ?>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Account Type</th>
                    <th>Account Level</th>
                    <th>Account Balance</th>
                    <th>Close Account</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account) : ?>
                    <tr>
                        <td><?php echo ++$counter ?></td>
                        <td><?php echo $account['acct_type']; ?></td>
                        <td><?php echo $account['acct_level']; ?></td>
                        <td><?php echo $account['acct_balance']; ?></td>
                        <td>
                            <form action="closure.php" method="POST">
                                <input type="hidden" name="acct_id" value="<?php echo $account['acct_id']; ?>">
                                <input type="hidden" name="acct_type" value="<?php echo $account['acct_type']; ?>">
                                <input type="hidden" name="acct_level" value="<?php echo $account['acct_level']; ?>">
                                <input type="hidden" name="request_type" value="account_delete">
                                <button type="submit" class="btn btn-danger btn-sm">Close Account</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="buttons" class="d-flex mx-2">
            <a href="/powerbank/client/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>