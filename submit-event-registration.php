<?php
include('db_conn.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    event_name VARCHAR(255) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$name       = trim($_POST['name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$event_name = trim($_POST['services'] ?? '');

if (empty($name) || empty($email) || empty($event_name) || $event_name === 'Select Event') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields and select an event.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO event_registrations (name, email, phone, event_name) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $phone, $event_name);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'You have successfully registered for "' . htmlspecialchars($event_name) . '". We look forward to seeing you there!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Prepare error: ' . $conn->error]);
}
