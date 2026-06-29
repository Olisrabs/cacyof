<?php
include('db_conn.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO newsletter (email) VALUES (?)");
if ($stmt) {
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'You have successfully subscribed to our weekly newsletter!']);
    } else {
        if ($conn->errno === 1062) {
            echo json_encode(['status' => 'success', 'message' => 'Your email is already subscribed to our newsletter.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        }
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Prepare error: ' . $conn->error]);
}
