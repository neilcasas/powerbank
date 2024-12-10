<?php 
  include '../helper/helper.php';
  session_start();


  // Get account details
  $client_id = $_SESSION["client_id"];

  $mysqli = create_connection();

  if ($mysqli->connect_error) {
    die('Connect Error(' . $mysqli->connect_errno . ')' . $mysqli->connect_error);     
  }
  
  $accounts_query = "SELECT * FROM account WHERE client_id = $client_id";
  $accounts_result = $mysqli->query($accounts_query);
  $accounts = $accounts_result->fetch_all(MYSQLI_ASSOC);

  $loans_query = "SELECT * FROM loan WHERE client_id = $client_id";
  $loans_result = $mysqli->query($loans_query);
  $loans = $loans_result->fetch_all(MYSQLI_ASSOC);
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

    <h2>Accounts</h2>
    <table>
      <tr>
        <th>Account ID</th>
        <th>Account Type</th>
        <th>Balance</th>
      </tr>
      <?php foreach ($accounts as $account) { ?>
        <tr>
          <td><?php echo $account["acct_id"]; ?></td>
          <td><?php echo $account["acct_type"]; ?></td>
          <td><?php echo $account["acct_balance"]; ?></td>
        </tr>
      <?php } ?>
    </table>
    <button>Request Account Creation</button>
    <h2>Loans</h2>
    <table>
      <tr>
        <th>Loan ID</th>
        <th>Loan Type</th>
        <th>Loan Amount</th>
      </tr>
      <?php foreach ($loans as $loan) { ?>
        <tr>
          <td><?php echo $loan["loan_id"]; ?></td>
          <td><?php echo $loan["loan_type"]; ?></td>
          <td><?php echo $loan["loan_amount"]; ?></td>
        </tr>
      <?php } ?>
    </table>
    <button>Request Loan</button>
  </body>
</html>
