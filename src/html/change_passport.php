<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Worker ID is required.']);
    exit();
}

if (!isset($_FILES['passport']) || $_FILES['passport']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a valid image file.']);
    exit();
}

// Fetch worker to get uin and current passport
$chk = $conn->prepare("SELECT id, uin, name, passport FROM workers WHERE id = ?");
$chk->bind_param("i", $id);
$chk->execute();
$res = $chk->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found.']);
    $chk->close();
    exit();
}
$worker = $res->fetch_assoc();
$chk->close();

$uin = $worker['uin'];
$old_passport = $worker['passport'];

$file_ext = strtolower(pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));
$allowed_exts = ['jpg', 'jpeg', 'png'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($file_ext, $allowed_exts)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file format. Only JPG, JPEG, and PNG allowed.']);
    exit();
}

if ($_FILES['passport']['size'] > $max_size) {
    echo json_encode(['status' => 'error', 'message' => 'File size exceeds 2MB limit.']);
    exit();
}

// Directories
$img_dir = __DIR__ . '/images/passports/';
$upload_dir = __DIR__ . '/uploads/passports/';

if (!is_dir($img_dir)) {
    mkdir($img_dir, 0755, true);
}
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate new filename with timestamp to bust cache
$passport_name = $uin . '_' . time() . '.' . $file_ext;
$dest_img = $img_dir . $passport_name;
$dest_upload = $upload_dir . $passport_name;

if (!move_uploaded_file($_FILES['passport']['tmp_name'], $dest_img)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save passport photo.']);
    exit();
}

// Also copy to uploads/passports for executive display compatibility
copy($dest_img, $dest_upload);

// Update workers table
$stmt = $conn->prepare("UPDATE workers SET passport = ? WHERE id = ?");
$stmt->bind_param("si", $passport_name, $id);

if ($stmt->execute()) {
    // Also update executives table if this worker is an executive
    if (!empty($uin)) {
        $upd_exec = $conn->prepare("UPDATE executives SET passport = ? WHERE uin = ?");
        $upd_exec->bind_param("ss", $passport_name, $uin);
        $upd_exec->execute();
        $upd_exec->close();
    }

    // Optionally remove old passport files if they differ
    if (!empty($old_passport) && $old_passport !== $passport_name) {
        if (file_exists($img_dir . $old_passport)) {
            @unlink($img_dir . $old_passport);
        }
        if (file_exists($upload_dir . $old_passport)) {
            @unlink($upload_dir . $old_passport);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Passport photo updated successfully!',
        'passport' => $passport_name
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error while saving passport record.']);
}

$stmt->close();
$conn->close();
?>
