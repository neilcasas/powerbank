<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);

    // Prepare the SQL query to fetch user credentials
    $sql = "SELECT * FROM credentials WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        // Bind the parameter (email) to the prepared statement
        $stmt->bind_param("s", $email);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Compare the provided password with the stored password (no hashing involved)
            if ($password === $row['password']) {
                // Store user email in the session (session_start is already called)
                $_SESSION['email'] = $email;
                
                // Redirect to the dashboard
                header("Location: /powerbank/client/dashboard.php");
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "No user found with that email.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing the query: " . $mysqli->error;
    }
}

$mysqli->close();
?>