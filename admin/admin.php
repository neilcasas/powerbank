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
    if ($_SESSION['role'] === 'EMPLOYEE' || $_SESSION['role'] === 'EXECUTIVE') {

      // Get account tables to be displayed
      $sql = "SELECT * FROM account_request;";
      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $account_requests = $result->fetch_all(MYSQLI_ASSOC);

    ?>

      <div class="employee-dashboard mt-4">
        <div class="account-requests">
          <h2>Account Dashboard</h2>

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
      echo "<h2 class='mt-5'>Loan Dashboard</h2>";

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
                    <select name="employee_id" class="form-control" required>
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

    <?php
    if ($_SESSION['role'] === 'ADMIN') {
      // Get all employee accounts from employee table
      $sql = "SELECT * FROM employee;";
      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      $employees = $result->fetch_all(MYSQLI_ASSOC);
    ?>
      <div class="it-admin-dashboard">
        <h2>Employee Accounts Dashboard</h2>
        <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#createEmployeeModal">Create New Employee</button>
        <table class="table table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>Employee ID</th>
              <th>Employee Name</th>
              <th>Employee Position</th>
              <th>Employee Email</th>
              <th>Date of Birth</th>
              <th>Salary</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employees as $employee) { ?>
              <tr>
                <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                <td><?php echo htmlspecialchars($employee['employee_name']); ?></td>
                <td><?php echo htmlspecialchars($employee['employee_position']); ?></td>
                <td><?php echo htmlspecialchars($employee['employee_email']); ?></td>
                <td><?php echo htmlspecialchars($employee['date_of_birth']); ?></td>
                <td><?php echo htmlspecialchars($employee['salary']); ?></td>
                <td>
                  <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#changePasswordModal" data-employee-id="<?php echo $employee['employee_id']; ?>">Change Password</button>
                  <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#changeRoleModal" data-employee-id="<?php echo $employee['employee_id']; ?>">Change Role</button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="administrator.php">
            <div class="modal-body">
              <input type="hidden" name="employee_id" id="changePasswordEmployeeId" value="">
              <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="new_password" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Role Modal -->
    <div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog" aria-labelledby="changeRoleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changeRoleModalLabel">Change Role</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="administrator.php">
            <div class="modal-body">
              <input type="hidden" name="employee_id" id="changeRoleEmployeeId" value="">
              <div class="form-group">
                <label for="newRole">New Role</label>
                <select class="form-control" id="newRole" name="new_role" required>
                  <option value="EMPLOYEE">EMPLOYEE</option>
                  <option value="MANAGER">MANAGER</option>
                  <option value="EXECUTIVE">EXECUTIVE</option>
                  <option value="ADMIN">ADMIN</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Create Employee Modal -->
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createEmployeeModalLabel">Create New Employee</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="post" action="administrator.php">
            <div class="modal-body">
              <div class="form-group">
                <label for="employeeName">Employee Name</label>
                <input type="text" class="form-control" id="employeeName" name="employee_name" required>
              </div>
              <div class="form-group">
                <label for="employeePosition">Employee Position</label>
                <input type="text" class="form-control" id="employeePosition" name="employee_position" required>
              </div>
              <div class="form-group">
                <label for="employeeEmail">Employee Email</label>
                <input type="email" class="form-control" id="employeeEmail" name="employee_email" required>
              </div>
              <div class="form-group">
                <label for="employeeDOB">Date of Birth</label>
                <input type="date" class="form-control" id="employeeDOB" name="date_of_birth" required>
              </div>
              <div class="form-group">
                <label for="employeeSalary">Salary</label>
                <input type="number" class="form-control" id="employeeSalary" name="salary" required>
              </div>
              <div class="form-group">
                <label for="employeePassword">Password</label>
                <input type="password" class="form-control" id="employeePassword" name="password" required>
              </div>
              <div class="form-group">
                <label for="employeeRole">Role</label>
                <select class="form-control" id="employeeRole" name="role" required>
                  <option value="EMPLOYEE">EMPLOYEE</option>
                  <option value="MANAGER">MANAGER</option>
                  <option value="EXECUTIVE">EXECUTIVE</option>
                  <option value="ADMIN">ADMIN</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Create Employee</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
      $('#changePasswordModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var employeeId = button.data('employee-id');
        var modal = $(this);
        modal.find('#changePasswordEmployeeId').val(employeeId);
      });

      $('#changeRoleModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var employeeId = button.data('employee-id');
        var modal = $(this);
        modal.find('#changeRoleEmployeeId').val(employeeId);
      });
    </script>
    <form action="/powerbank/auth/logout.php" method="post">
      <button type="submit" class="btn btn-danger">Logout</button>
    </form>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>