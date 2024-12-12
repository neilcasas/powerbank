<?php
session_start();
include '../includes/db.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <?php
    if ($_SESSION['role'] === 'EMPLOYEE' || $_SESSION['role'] === 'MANAGER' || $_SESSION['role'] === 'EXECUTIVE') {

      // Get account tables to be displayed
      $sql = "SELECT * FROM account_request;";
      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $account_requests = $result->fetch_all(MYSQLI_ASSOC);

    ?>

      <div class="employee-dashboard mt-4">
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
                <?php
                // Get client id for request
                $sql = "SELECT client_id FROM request WHERE request_id = ?;";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $request['request_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $client_id = $result->fetch_assoc()['client_id'];
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                  <td><?php echo htmlspecialchars($client_id); ?></td>
                  <td><?php echo htmlspecialchars($request['acct_request_type']); ?></td>
                  <td><?php echo htmlspecialchars($request['acct_type']); ?></td>
                  <td><?php echo htmlspecialchars($request['acct_level']); ?></td>
                  <td>
                    <form method="post" class="d-inline" action="employee.php">
                      <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                      <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                      <input type="hidden" name="request_type" value="<?php echo $request['acct_request_type']; ?>">
                      <input type="hidden" name="acct_id" value="<?php echo $request['acct_id']; ?>">
                      <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                      <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php
    }
    ?>
    <?php
    if ($_SESSION['role'] === 'MANAGER' || $_SESSION['role'] === 'EXECUTIVE') {
      echo "<h1 class='mt-5'>Loan Dashboard</h1>";

      // Get all loan requests from loan table
      $sql = "SELECT * FROM loan_request;";
      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $loan_requests = $result->fetch_all(MYSQLI_ASSOC);

      // Get all employees whose role is EMPLOYEE 
      $sql = "
      SELECT 
        e.employee_id, 
        e.employee_name, 
        e.employee_position, 
        e.employee_email, 
        e.date_of_birth, 
        e.salary 
      FROM 
        employee e
      INNER JOIN 
        credentials c
      ON 
        e.employee_id = c.employee_id
      WHERE 
        c.role = 'EMPLOYEE';";

      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $employees = $result->fetch_all(MYSQLI_ASSOC);
    ?>
      <div class="loan-requests">
        <h2>Loan Requests</h2>
        <table class="table table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>Request ID</th>
              <th>Client ID</th>
              <th>Loan Amount</th>
              <th>Loan Request Type</th>
              <th>Assigned Employee</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($loan_requests as $request) { ?>
              <?php
              // Get client id for request
              $sql = "SELECT client_id FROM request WHERE request_id = ?;";
              $stmt = $mysqli->prepare($sql);
              $stmt->bind_param("i", $request['request_id']);
              $stmt->execute();
              $result = $stmt->get_result();
              $client_id = $result->fetch_assoc()['client_id'];
              ?>
              <tr>
                <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                <td><?php echo htmlspecialchars($client_id); ?></td>
                <td><?php echo htmlspecialchars($request['loan_amount']); ?></td>
                <td><?php echo htmlspecialchars($request['loan_type']); ?></td>
                <form method="post" class="d-inline" action="manager.php">
                  <td>
                    <select name="employee_id" class="form-control">
                      <?php foreach ($employees as $employee) { ?>
                        <option value="<?php echo $employee['employee_id']; ?>"><?php echo $employee['employee_name']; ?></option>
                      <?php } ?>
                    </select>
                  </td>
                  <td>
                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                    <input type="hidden" name="request_type" value="<?php echo $request['loan_type']; ?>">
                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                  </td>
                </form>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php
    }
    ?>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>