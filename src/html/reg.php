<?php
session_start();
require_once 'db_conn.php'; // Adjust path as needed

if (isset($_POST['submit'])) {

    // Sanitize inputs
    $name     = trim(htmlspecialchars($_POST['name']));
    $email    = trim(htmlspecialchars($_POST['email']));
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];

    // Basic validation
    $errors = [];

    if (empty($name))     $errors[] = "Name is required.";
    if (empty($email))    $errors[] = "Email is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {

        // Check if email or username already exists
        $check_sql = "SELECT id FROM admin WHERE email = ? OR username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $errors[] = "Email or Username already exists. Please use a different one.";
        }
        $check_stmt->close();
    }

    if (empty($errors)) {

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert into admin table using prepared statement
        $sql  = "INSERT INTO admin (name, email, username, password, role, status) VALUES (?, ?, ?, ?, 'Admin', 'Active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $username, $hashed_password);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            // Redirect on success
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: sign-up.php"); // Redirect back to sign-up.php so the modal can show
            exit();

        } else {
            $errors[] = "Something went wrong. Please try again.";
            $stmt->close();
        }
    }

    // If there are errors, store in session and redirect back
    if (!empty($errors)) {
        $_SESSION['reg_errors'] = $errors;
        header("Location: sign-up.php"); // Adjust to your register page
        exit();
    }

    $conn->close();
}
?>
