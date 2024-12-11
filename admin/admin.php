<?php
session_start();
include '../includes/db.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
</head>

<body>
  <?php
  if ($_SESSION['role'] === 'EMPLOYEE') {
    echo "<h1>Employee</h1>";
    // Get account and loan request tables to be displayed
    $sql = "SELECT * FROM account_request;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $account_requests = $result->fetch_all(MYSQLI_ASSOC);
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
            <form action="employee.php" method="post">
              <tr>
                <td><?= $request['request_id'] ?></td>
                <td><?= $request['client_id'] ?></td>
                <td><?= $request['acct_request_type'] ?></td>
                <td><?= $request['acct_type'] ?></td>
                <td><?= $request['acct_level'] ?></td>
                <td>
                  <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                  <button type="submit" name="approve">Approve</button>
                  <button type="submit" name="reject">Reject</button>
                </td>
              </tr>
            </form>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  <?php } ?>
</body>

</html>