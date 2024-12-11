<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Prepare the SQL query to fetch client details
$sql = "SELECT * FROM client WHERE email = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    // Bind the email parameter to the prepared statement
    $stmt->bind_param("s", $email);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
        echo "<h1>Welcome, " . htmlspecialchars($client['client_name']) . "</h1>";
        echo "<p>Email: " . htmlspecialchars($client['email']) . "</p>";
        echo "<p>Address: " . htmlspecialchars($client['address']) . "</p>";
        echo "<p>Phone Number: " . htmlspecialchars($client['phone_number']) . "</p>";
        echo "<p>Date of Birth: " . htmlspecialchars($client['date_of_birth']) . "</p>";
    } else {
        echo "No client found with this email.";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "Error preparing the query: " . $mysqli->error;
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Close the database connection
$mysqli->close();
?>

<form method="POST">
    <input type="submit" name="logout" value="Logout">
</form>
