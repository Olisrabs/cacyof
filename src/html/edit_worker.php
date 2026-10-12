<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$id              = intval($_POST['id'] ?? 0);
$name            = trim(htmlspecialchars($_POST['name'] ?? ''));
$email           = trim(htmlspecialchars($_POST['email'] ?? ''));
$phone           = trim(htmlspecialchars($_POST['phone'] ?? ''));
$dob             = trim($_POST['dob'] ?? '');
$units           = trim(htmlspecialchars($_POST['units'] ?? ''));
$department      = trim(htmlspecialchars($_POST['department'] ?? ''));
$level           = trim(htmlspecialchars($_POST['level'] ?? ''));
$admission_year  = trim($_POST['admission_year'] ?? '');
$graduation_year = trim($_POST['graduation_year'] ?? '');
$status          = trim(htmlspecialchars($_POST['status'] ?? ''));

if (!$id || empty($name) || empty($phone) || empty($department) || empty($status) || empty($dob) || empty($admission_year)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

// Check worker exists
$chk_worker = $conn->prepare("SELECT id, uin FROM workers WHERE id = ?");
$chk_worker->bind_param("i", $id);
$chk_worker->execute();
$w_res = $chk_worker->get_result();
if ($w_res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found.']);
    $chk_worker->close();
    exit();
}
$worker = $w_res->fetch_assoc();
$uin = $worker['uin'];
$chk_worker->close();

// Check if phone already in use by another worker
$chk_phone = $conn->prepare("SELECT id FROM workers WHERE phone = ? AND id != ?");
$chk_phone->bind_param("si", $phone, $id);
$chk_phone->execute();
$chk_phone->store_result();
if ($chk_phone->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'A worker with this phone number already exists.']);
    $chk_phone->close();
    exit();
}
$chk_phone->close();

// Check if email already in use by another worker (if provided)
if (!empty($email)) {
    $chk_email = $conn->prepare("SELECT id FROM workers WHERE email = ? AND id != ?");
    $chk_email->bind_param("si", $email, $id);
    $chk_email->execute();
    $chk_email->store_result();
    if ($chk_email->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'A worker with this email already exists.']);
        $chk_email->close();
        exit();
    }
    $chk_email->close();
}

// Update worker record
$stmt = $conn->prepare(
    "UPDATE workers 
     SET name = ?, email = ?, phone = ?, dob = ?, units = ?, department = ?, level = ?, admission_year = ?, graduation_year = ?, status = ? 
     WHERE id = ?"
);
$stmt->bind_param(
    "ssssssssssi",
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
    $id
);

if ($stmt->execute()) {
    // Keep name synced in executives table if worker is an executive
    if (!empty($uin)) {
        $upd_exec = $conn->prepare("UPDATE executives SET name = ? WHERE uin = ?");
        $upd_exec->bind_param("ss", $name, $uin);
        $upd_exec->execute();
        $upd_exec->close();
    }

    echo json_encode(['status' => 'success', 'message' => 'Worker details updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update worker details. Database error.']);
}

$stmt->close();
$conn->close();
?>
