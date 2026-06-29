<?php
ob_start(); // Buffer all output so PHP warnings don't corrupt JSON
error_reporting(0); // Suppress notices/warnings from breaking JSON

session_start();
include('db_conn.php');

// Discard any buffered output (warnings, notices, etc.) before JSON header
ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit();
    }

    // Ensure blogs table exists with all required columns
    $createTableQuery = "CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uin VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT '',
        image VARCHAR(255) DEFAULT '',
        date DATE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($createTableQuery);

    // Collect and sanitize inputs
    $name        = trim($_POST['author_name'] ?? '');
    $title       = trim($_POST['blog_title'] ?? '');
    $category    = trim($_POST['blog_category'] ?? '');
    $date        = trim($_POST['blog_date'] ?? date('Y-m-d'));
    $description = trim($_POST['blog_details'] ?? '');
    $uin         = 'BLG-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    if (empty($name) || empty($title) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in the author name, blog title, and details.']);
        exit();
    }

    // Handle image upload
    $imageFilename = '';
    if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['blog_image']['tmp_name'];
        $fileName      = $_FILES['blog_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = __DIR__ . '/uploads/blogs/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $newFileName = $uin . '_' . time() . '.' . $fileExtension;
            $dest_path   = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imageFilename = $newFileName;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error moving uploaded file. Check folder permissions on uploads/blogs/.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP.']);
            exit();
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO blogs (uin, title, name, category, image, date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssss", $uin, $title, $name, $category, $imageFilename, $date, $description);
        if ($stmt->execute()) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Blog published successfully!'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database execute error: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
    }

} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
