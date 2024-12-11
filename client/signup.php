<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form input
    $client_name = mysqli_real_escape_string($mysqli, $_POST['client_name']);
    $address = mysqli_real_escape_string($mysqli, $_POST['address']);
    $phone_number = mysqli_real_escape_string($mysqli, $_POST['phone_number']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $date_of_birth = mysqli_real_escape_string($mysqli, $_POST['date_of_birth']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);

    // Prepare the SQL statement to insert the client data
    $sql = "INSERT INTO client (client_name, address, phone_number, email, date_of_birth) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        // Bind the input parameters to the prepared statement
        $stmt->bind_param("sssss", $client_name, $address, $phone_number, $email, $date_of_birth);
        
        // Execute the prepared statement
        if ($stmt->execute()) {
            // Get the last inserted client_id
            $client_id = $mysqli->insert_id;

            // Prepare the SQL statement to insert credentials (store password as plain text)
            $sql_credentials = "INSERT INTO credentials (username, password, client_id) VALUES (?, ?, ?)";
            $stmt_credentials = $mysqli->prepare($sql_credentials);
            if ($stmt_credentials) {
                // Bind the input parameters for the credentials table
                $stmt_credentials->bind_param("ssi", $email, $password, $client_id);

                // Execute the prepared statement for credentials
                if ($stmt_credentials->execute()) {
                    // Redirect to login page after successful signup
                    header("Location: login.php");
                    exit();
                } else {
                    // Error while inserting credentials
                    echo "Error: " . $stmt_credentials->error;
                }
            } else {
                echo "Error preparing credentials query: " . $mysqli->error;
            }
        } else {
            // Error while inserting client
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing client query: " . $mysqli->error;
    }
}

// Close the database connection
$mysqli->close();
?>

<form method="POST">
    <label>Client Name: </label><input type="text" name="client_name" required><br>
    <label>Address: </label><input type="text" name="address" required><br>
    <label>Phone Number: </label><input type="text" name="phone_number" required><br>
    <label>Email: </label><input type="email" name="email" required><br>
    <label>Date of Birth: </label><input type="date" name="date_of_birth" required><br>
    <label>Password: </label><input type="password" name="password" required><br>
    <input type="submit" value="Sign Up">
</form>

