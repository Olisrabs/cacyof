<?php
ob_start();
error_reporting(0);
session_start();
include('db_conn.php');
ob_clean();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit();
}

$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid blog ID.']);
    exit();
}

// Fetch image filename so we can delete the file too
$stmt = $conn->prepare("SELECT image FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Blog not found.']);
    exit();
}

// Delete the blog record
$del = $conn->prepare("DELETE FROM blogs WHERE id = ?");
$del->bind_param("i", $id);
if ($del->execute()) {
    // Delete the image file if it exists
    if (!empty($row['image'])) {
        $imgPath = __DIR__ . '/uploads/blogs/' . $row['image'];
        if (file_exists($imgPath)) {
            @unlink($imgPath);
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Blog deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $del->error]);
}
$del->close();
