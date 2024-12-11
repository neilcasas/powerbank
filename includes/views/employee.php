<?php
$sql = "SELECT * FROM account_request;";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$account_requests = $result->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT * FROM loan_request;";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$stmt->execute();
$result = $stmt->get_result();
$loan_requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="employee-dashboard">
    <div class="account-requests">
        <h1>Account Requests</h1>

        <table>
            <tr>
                <th>Request ID</th>
                <th>Client ID</th>
                <th>Account Request Type</th>
                <th>Account Type</th>
                <th>Account Level</th>
            </tr>
            <?php foreach ($account_requests as $request) : ?>
                <tr>
                    <td><?= $request['request_id'] ?></td>
                    <td><?= $request['client_id'] ?></td>
                    <td><?= $request['acct_request_type'] ?></td>
                    <td><?= $request['acct_type'] ?></td>
                    <td><?= $request['acct_level'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="loan-requests">
        <h1>Loan Requests</h1>
        <table>
            <tr>
                <th>Request ID</th>
                <th>Client ID</th>
                <th>Loan Type</th>
                <th>Loan Amount</th>
            </tr>
            <?php foreach ($loan_requests as $request) : ?>
                <tr>
                    <td><?= $request['request_id'] ?></td>
                    <td><?= $request['client_id'] ?></td>
                    <td><?= $request['loan_type'] ?></td>
                    <td><?= $request['loan_amount'] ?></td>
                </tr>
            <?php endforeach; ?>
    </div>
</div>