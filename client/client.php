<?php 
  include '../helper/helper.php';
  session_start();


  // Get account details
  $client_id = $_SESSION["client_id"];

  $mysqli = create_connection();

  if ($mysqli->connect_error) {
    die('Connect Error(' . $mysqli->connect_errno . ')' . $mysqli->connect_error);     
  }
  
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <body>
    <h1>Client</h1>
    <p>Your client id is <?php echo $_SESSION["client_id"]; ?></p>
  </body>
</html>
