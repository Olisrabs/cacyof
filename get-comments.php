<?php
require_once 'db_conn.php';
header('Content-Type: application/json');

$blog_id = isset($_GET['blog_id']) && is_numeric($_GET['blog_id']) ? (int)$_GET['blog_id'] : 0;

if ($blog_id <= 0) {
    echo json_encode([]);
    exit();
}

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$comments = [];
$stmt = $conn->prepare("SELECT name, message, created_at FROM blog_comments WHERE blog_id = ? ORDER BY created_at DESC");
if ($stmt) {
    $stmt->bind_param("i", $blog_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $comments[] = [
                'name' => htmlspecialchars($row['name']),
                'message' => nl2br(htmlspecialchars($row['message'])),
                'created_at' => date('M d, Y h:i A', strtotime($row['created_at']))
            ];
        }
    }
    $stmt->close();
}

echo json_encode($comments);
?>
