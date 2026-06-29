<?php
include('db_conn.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(15,2) NOT NULL,
    method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name']  ?? '');
$email      = trim($_POST['email']      ?? '');
$address    = trim($_POST['address']    ?? '');
$country    = trim($_POST['country']    ?? '');
$phone      = trim($_POST['phone']      ?? '');
$amount     = trim($_POST['donation_amount'] ?? '0');
$method     = trim($_POST['fav_language'] ?? 'N/A');
$full_name  = $first_name . ' ' . $last_name;

if (empty($first_name) || empty($last_name) || empty($email) || empty($amount) || (float)$amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields and select or enter a valid donation amount.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO donations (name, email, phone, country, amount, method, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
if ($stmt) {
    $stmt->bind_param("ssssds", $full_name, $email, $phone, $country, $amount, $method);
    if ($stmt->execute()) {
        $donation_id = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'redirect_url' => 'gateway.php?id=' . $donation_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Prepare error: ' . $conn->error]);
}
