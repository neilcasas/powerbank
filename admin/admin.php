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
    <h1>Admin</h1>
    <?php
      $role = $_SESSION['role'];
      echo $role;
    ?>
  </body>
</html>
