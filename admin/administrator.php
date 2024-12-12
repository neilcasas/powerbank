<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_password'])) {
        // Handle password change
        $employee_id = $_POST['employee_id'];
        $new_password = $_POST['new_password'];

        try {
            $sql = "UPDATE credentials SET password = ? WHERE employee_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $new_password, $employee_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: success.php");
                exit();
            } else {
                echo "No changes made or employee not found.";
            }
        } catch (Exception $e) {
            echo "Error updating password: " . $e->getMessage();
        }
    } elseif (isset($_POST['new_role'])) {
        // Handle role change
        $employee_id = $_POST['employee_id'];
        $new_role = $_POST['new_role'];

        try {
            $sql = "UPDATE credentials SET role = ? WHERE employee_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $new_role, $employee_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: success.php");
                exit();
            } else {
                echo "No changes made or employee not found.";
            }
        } catch (Exception $e) {
            echo "Error updating role: " . $e->getMessage();
        }
    } elseif (isset($_POST['employee_name'])) {
        // Handle new employee creation
        $employee_name = $_POST['employee_name'];
        $employee_position = $_POST['employee_position'];
        $employee_email = $_POST['employee_email'];
        $date_of_birth = $_POST['date_of_birth'];
        $salary = $_POST['salary'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $mysqli->begin_transaction();

        try {
            // Insert into employee table
            $sql = "INSERT INTO employee (employee_name, employee_position, employee_email, date_of_birth, salary) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssssd", $employee_name, $employee_position, $employee_email, $date_of_birth, $salary);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $employee_id = $stmt->insert_id;

                // Insert into credentials table
                $sql = "INSERT INTO credentials (username, password, employee_id, role) VALUES (?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssis", $employee_email, $password, $employee_id, $role);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $mysqli->commit();
                    header("Location: success.php");
                    exit();
                } else {
                    throw new Exception("Error creating employee credentials.");
                }
            } else {
                throw new Exception("Error creating employee.");
            }
        } catch (Exception $e) {
            $mysqli->rollback();
            echo "Error creating employee: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_employee'])) {
        // Handle employee deletion
        $employee_id = $_POST['employee_id'];

        try {
            // Check if the employee is assigned to any loan
            $sql = "SELECT COUNT(*) AS loan_count FROM loan WHERE employee_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['loan_count'] > 0) {
                echo "Error: Cannot delete employee assigned to a loan.";
            } else {
                // Delete from credentials table
                $sql = "DELETE FROM credentials WHERE employee_id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $employee_id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Delete from employee table
                    $sql = "DELETE FROM employee WHERE employee_id = ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $employee_id);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        header("Location: success.php");
                        exit();
                    } else {
                        throw new Exception("Error deleting employee.");
                    }
                } else {
                    throw new Exception("Error deleting employee credentials.");
                }
            }
        } catch (Exception $e) {
            echo "Error deleting employee: " . $e->getMessage();
        }
    }
}
