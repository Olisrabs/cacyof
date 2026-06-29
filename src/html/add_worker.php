<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = trim(htmlspecialchars($_POST['name']));
    $email           = trim(htmlspecialchars($_POST['email'] ?? ''));
    $phone           = trim(htmlspecialchars($_POST['phone']));
    $dob             = trim($_POST['dob']);
    $units           = trim(htmlspecialchars($_POST['units'] ?? ''));
    $department      = trim(htmlspecialchars($_POST['department']));
    $level           = trim(htmlspecialchars($_POST['level'] ?? ''));
    $admission_year  = trim($_POST['admission_year']);
    $graduation_year = trim($_POST['graduation_year'] ?? '');
    $status          = trim(htmlspecialchars($_POST['status']));

    // Basic validation
    if (empty($name) || empty($phone) || empty($department) || empty($status) || empty($dob) || empty($admission_year)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit();
    }

    // Check if phone already exists
    $check = $conn->prepare("SELECT id FROM workers WHERE phone = ?");
    $check->bind_param("s", $phone);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'A worker with this phone number already exists.']);
        $check->close();
        exit();
    }
    $check->close();

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM workers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'A worker with this email already exists.']);
        $check->close();
        exit();
    }
    $check->close();

    // ✅ Generate UIN — e.g. CAC-2024-00001
    $year = date('Y');
    $uin_prefix = 'CAC-' . $year . '-';

    $uin_stmt = $conn->prepare("SELECT uin FROM workers WHERE uin LIKE ? ORDER BY id DESC LIMIT 1");
    $uin_like = $uin_prefix . '%';
    $uin_stmt->bind_param("s", $uin_like);
    $uin_stmt->execute();
    $uin_result = $uin_stmt->get_result();

    if ($uin_result->num_rows > 0) {
        $last_uin = $uin_result->fetch_assoc()['uin'];
        $last_number = (int) substr($last_uin, -5); // get last 5 digits
        $new_number = str_pad($last_number + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $new_number = '00001';
    }

    $uin = $uin_prefix . $new_number; // e.g. CAC-2024-00001
    $uin_stmt->close();

    // ✅ Handle passport photo upload
    $passport_name = '';
    if (!empty($_FILES['passport']['name'])) {
        $upload_dir   = 'images/passports/';

        // Create folder if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_ext      = strtolower(pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));
        $allowed_exts  = ['jpg', 'jpeg', 'png'];
        $max_size      = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_ext, $allowed_exts)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG allowed.']);
            exit();
        }

        if ($_FILES['passport']['size'] > $max_size) {
            echo json_encode(['status' => 'error', 'message' => 'File size exceeds 2MB limit.']);
            exit();
        }

        // Rename file to UIN to keep it unique
        $passport_name = $uin . '.' . $file_ext;
        $upload_path   = $upload_dir . $passport_name;

        if (!move_uploaded_file($_FILES['passport']['tmp_name'], $upload_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload passport photo.']);
            exit();
        }
    }

    // ✅ Insert worker into database
    $stmt = $conn->prepare("INSERT INTO workers (uin, name, email, phone, dob, units, department, level, admission_year, graduation_year, status, passport) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssssss",
        $uin,
        $name,
        $email,
        $phone,
        $dob,
        $units,
        $department,
        $level,
        $admission_year,
        $graduation_year,
        $status,
        $passport_name
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => $name . ' has been added with UIN: ' . $uin]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>