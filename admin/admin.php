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
    include '../includes/views/employee.php';
  }
  ?>
</body>

</html>