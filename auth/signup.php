<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input
    $client_name = $_POST['client_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];
    $password = $_POST['password'];

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
                    header("Location: /powerbank/");
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


        $stmt->close();
    } else {
        echo "Error preparing client query: " . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">
    <style>
        @font-face{
            font-family: 'Poppins';
            src: url('../assets/Poppins-Regular.woff') format('opentype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }

        h1 {
            color: #333;
        }

        p {
            margin-top: 20px;
            text-align: center;
        }
        
        .login-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .card {
            width: 90%;
            max-width: 600px; 
            min-height: 550px; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px; 
            transition: all 0.3s ease;
            padding: 30px; 
            background-color: #fff; 
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; 
        }

        .form-control {
            margin-bottom: 15px; 
            border-radius: 4px; 
        }

        .btn-success {
            width: 100%;
            padding: 12px;
            margin-top: 30px;
        }

        .form-label {
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <h1 class="card-title text-center mb-4"><strong>Create Account</strong></h1>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name: </label>
                        <input type="text" name="client_name" class="form-control" required>
                    </div>  
                    <div class="mb-3">
                        <label class="form-label">Address: </label>
                        <input type="text" name="address" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Phone Number: </label>
                        <input type="text" name="phone_number" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Email: </label>
                        <input type="email" name="email" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Date of Birth: </label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Password: </label>
                        <input type="password" name="password" class="form-control" required>
                    </div> 
                    <input type="submit" value="Sign Up" class="btn btn-success">
                </form>
                <p>Already have an account? <a href="/powerbank/">Login here</a>.</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>