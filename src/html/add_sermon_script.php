<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Sanitize — store raw text; htmlspecialchars() is applied on output, NOT here
$title         = trim($_POST['title'] ?? '');
$category      = trim($_POST['category'] ?? '');
$date          = trim($_POST['date'] ?? '');
$preacher      = trim($_POST['preacher'] ?? '');

// Full message — store as-is
$message = trim($_POST['message'] ?? '');

// Required field validation
if (empty($title) || empty($category) || empty($date) || empty($preacher) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

// Validate date format
if (!strtotime($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format.']);
    exit();
}

// ── Upload directories ──────────────────────────────────────────────────────
$base_dir  = 'uploads/sermons/';
$image_dir = $base_dir . 'images/';
$pdf_dir   = $base_dir . 'pdf/';

foreach ([$image_dir, $pdf_dir] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

// Unique slug for filenames
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title))) . '-' . date('YmdHis');

// ── Helper: upload a single file ────────────────────────────────────────────
function uploadFile($file_key, $dir, $allowed_types, $max_size, $slug) {
    if (empty($_FILES[$file_key]['name'])) return null;  // optional — skip

    $file     = $_FILES[$file_key];
    $tmp      = $file['tmp_name'];
    $size     = $file['size'];
    $mime     = mime_content_type($tmp);
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($mime, $allowed_types)) {
        return ['error' => 'Invalid file type for ' . $file_key . '. Detected: ' . $mime];
    }
    if ($size > $max_size) {
        $mb = $max_size / (1024 * 1024);
        return ['error' => ucfirst($file_key) . ' exceeds the ' . $mb . 'MB size limit.'];
    }

    $filename = $slug . '.' . $ext;
    $dest     = $dir . $filename;
    if (!move_uploaded_file($tmp, $dest)) {
        return ['error' => 'Failed to upload ' . $file_key . '. Check folder permissions.'];
    }
    return $filename;
}

// ── Image upload (file) ─────────────────────────────────────────────────────
$image_result = uploadFile(
    'images',
    $image_dir,
    ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    5 * 1024 * 1024,   // 5 MB
    $slug
);
if (is_array($image_result) && isset($image_result['error'])) {
    echo json_encode(['status' => 'error', 'message' => $image_result['error']]);
    exit();
}
$image_name = $image_result; // filename string or null

// ── PDF upload (file) ───────────────────────────────────────────────────────
$pdf_result = uploadFile(
    'pdf',
    $pdf_dir,
    ['application/pdf'],
    10 * 1024 * 1024,  // 10 MB
    $slug
);
if (is_array($pdf_result) && isset($pdf_result['error'])) {
    echo json_encode(['status' => 'error', 'message' => $pdf_result['error']]);
    exit();
}
$pdf_name = $pdf_result; // filename string or null

// ── Audio / Video — URL links ───────────────────────────────────────────────
$audio_url = trim($_POST['audio'] ?? '');
$video_url = trim($_POST['video'] ?? '');

foreach (['audio' => $audio_url, 'video' => $video_url] as $field => $url) {
    if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid URL provided for ' . $field . '.']);
        exit();
    }
}

// ── Insert into database ────────────────────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO sermons 
        (title, category, date, preacher, description, images, audio, video, pdf) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// ── Coalesce optional fields to '' so NOT NULL columns never receive PHP null ──
$image_name = $image_name ?? '';
$pdf_name   = $pdf_name   ?? '';
$audio_url  = $audio_url  !== '' ? $audio_url : '';
$video_url  = $video_url  !== '' ? $video_url : '';

$stmt->bind_param(
    "sssssssss",
    $title,
    $category,
    $date,
    $preacher,
    $message,
    $image_name,
    $audio_url,
    $video_url,
    $pdf_name
);

if ($stmt->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => '"' . $title . '" by ' . $preacher . ' has been added successfully.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>