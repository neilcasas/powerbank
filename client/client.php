<?php
session_start();
include '../includes/db.php';  // Database connection

// Function to handle the sign-up
function signUp($client_name, $address, $phone_number, $email, $date_of_birth, $password, $conn) {
    // Sanitize inputs
    $client_name = mysqli_real_escape_string($conn, $client_name);
    $address = mysqli_real_escape_string($conn, $address);
    $phone_number = mysqli_real_escape_string($conn, $phone_number);
    $email = mysqli_real_escape_string($conn, $email);
    $date_of_birth = mysqli_real_escape_string($conn, $date_of_birth);
    $password = mysqli_real_escape_string($conn, $password);

    // Insert into client table
    $sql = "INSERT INTO client (client_name, address, phone_number, email, date_of_birth) 
            VALUES ('$client_name', '$address', '$phone_number', '$email', '$date_of_birth')";

    if ($conn->query($sql) === TRUE) {
        // Insert into credentials table (hashed password)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_credentials = "INSERT INTO credentials (username, password) VALUES ('$email', '$hashed_password')";

        if ($conn->query($sql_credentials) === TRUE) {
            return true;  // Return success
        }
    }

    return false;  // Return failure
}

// Function to handle login
function login($email, $password, $conn) {
    // Sanitize input
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Query to check if the user exists
    $sql = "SELECT * FROM credentials WHERE username = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $email;  // Store email in session
            return true;  // Return login success
        }
    }

    return false;  // Return login failure
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// If the user is logged in, load the dashboard
if (isset($_SESSION['email'])) {
    include 'dashboard.php';
    exit();
}

// Default behavior if no session: show login or signup page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signup'])) {
        // Process the sign-up form
        if (signUp($_POST['client_name'], $_POST['address'], $_POST['phone_number'], $_POST['email'], $_POST['date_of_birth'], $_POST['password'], $conn)) {
            header("Location: login.php");  // Redirect to login page
            exit();
        } else {
            echo "Error in sign-up process.";
        }
    } elseif (isset($_POST['login'])) {
        // Process the login form
        if (login($_POST['email'], $_POST['password'], $conn)) {
            header("Location: dashboard.php");  // Redirect to dashboard
            exit();
        } else {
            echo "Invalid credentials.";
        }
    }
}
?>

<!-- You can include here the signup/login forms or include them dynamically -->
