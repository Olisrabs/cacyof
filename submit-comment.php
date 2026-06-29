<?php
include('db_conn.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$blog_id = (int)($_POST['blog_id'] ?? 0);
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($blog_id <= 0 || empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO blog_comments (blog_id, name, email, message) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("isss", $blog_id, $name, $email, $message);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Your comment has been posted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Prepare error: ' . $conn->error]);
}
