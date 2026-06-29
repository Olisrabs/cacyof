<?php
session_start();
include('db_conn.php');

// Session Timeout: 20 minutes
$timeout_duration = 1200;

// Capture current URL for redirect after login
$current_url = $_SERVER['REQUEST_URI'];

// Handle session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    $_SESSION['redirect_after_login'] = $current_url;
    session_unset();
    session_destroy();

    // Start a new session just to pass the timeout message
    session_start();
    $_SESSION['auth_error'] = 'Session expired due to inactivity. Please log in again.';
    header("Location: index.php");
    exit();
}

//  Update last activity
$_SESSION['LAST_ACTIVITY'] = time();

// Redirect if not logged in
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['email']) ||
    !isset($_SESSION['role'])
) {
    $_SESSION['redirect_after_login'] = $current_url;
    $_SESSION['auth_error'] = 'Please log in to access this page.';
    header("Location: index.php");
    exit();
}

// Assign session variables
$admin_email = $_SESSION['email'];
$admin_role  = $_SESSION['role'];

// Verify user in database
$query = "SELECT * FROM admin WHERE email = ? AND role = ? AND status = 'Active' LIMIT 1";
$stmt  = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("ss", $admin_email, $admin_role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Assign user data
        $session_id       = $row['id'];
        $session_name     = $row['name'];
        $session_email    = $row['email'];
        $session_username = $row['username'];
        $session_role     = $row['role'];
        $session_status   = $row['status'];

    } else {
        // Account inactive or role mismatch
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['auth_error'] = 'Unauthorized access or inactive account.';
        header("Location: index.php");
        exit();
    }

    $stmt->close();

} else {
    error_log("Session check query failed: " . $conn->error);
    header("Location: index.php");
    exit();
}
?>