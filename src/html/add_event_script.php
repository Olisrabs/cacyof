<?php
session_start();
include('db_conn.php');
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Ensure events table exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uin VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    start_time TIME DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    venue VARCHAR(255) NOT NULL,
    organizers VARCHAR(255) NOT NULL,
    contacts VARCHAR(255) NOT NULL,
    images VARCHAR(500) DEFAULT '',
    description TEXT,
    status ENUM('Completed', 'Upcoming', 'Ongoing') DEFAULT 'Upcoming',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTableQuery);

// Collect and sanitize inputs
$title       = trim($_POST['title'] ?? '');
$start_date  = trim($_POST['start_date'] ?? '');
$start_time  = trim($_POST['start_time'] ?? '');
$end_date    = trim($_POST['end_date'] ?? '');
$end_time    = trim($_POST['end_time'] ?? '');
$venue       = trim($_POST['venue'] ?? '');
$organizers  = trim($_POST['organizers'] ?? '');
$contacts    = trim($_POST['contacts'] ?? '');
$description = trim($_POST['description'] ?? '');
// Automatically detect status (Upcoming, Ongoing, Completed)
$current_time = time();
$start_datetime_str = $start_date . (empty($start_time) ? ' 00:00:00' : ' ' . $start_time);
$start_ts = strtotime($start_datetime_str);
if (!empty($end_date)) {
    $end_datetime_str = $end_date . (empty($end_time) ? ' 23:59:59' : ' ' . $end_time);
    $end_ts = strtotime($end_datetime_str);
} else {
    $end_ts = strtotime($start_date . ' 23:59:59');
}

if ($current_time < $start_ts) {
    $status = 'Upcoming';
} elseif ($current_time > $end_ts) {
    $status = 'Completed';
} else {
    $status = 'Ongoing';
}

$uin         = 'EVT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

// Validate required fields
if (empty($title) || empty($start_date) || empty($venue) || empty($organizers) || empty($contacts) || empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

// Adjust empty time/date variables
$start_time_val = !empty($start_time) ? $start_time : null;
$end_date_val   = !empty($end_date) ? $end_date : null;
$end_time_val   = !empty($end_time) ? $end_time : null;

// Handle multiple image uploads (Max 2)
$uploaded_images = [];
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $files = $_FILES['images'];
    $file_count = count($files['name']);
    
    if ($file_count > 2) {
        echo json_encode(['status' => 'error', 'message' => 'You can upload a maximum of 2 images.']);
        exit();
    }
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploadFileDir = __DIR__ . '/../uploads/events/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $fileTmpPath   = $files['tmp_name'][$i];
            $fileName      = $files['name'][$i];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName   = $uin . '_' . $i . '_' . time() . '.' . $fileExtension;
                $dest_path     = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $uploaded_images[] = $newFileName;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error moving file: ' . $fileName]);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file format for ' . $fileName . '. Allowed: JPG, PNG, GIF, WEBP.']);
                exit();
            }
        }
    }
}

$images_str = implode(',', $uploaded_images);

// Insert into database
$stmt = $conn->prepare("INSERT INTO events (uin, title, start_date, start_time, end_date, end_time, venue, organizers, contacts, images, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssssssssssss", $uin, $title, $start_date, $start_time_val, $end_date_val, $end_time_val, $venue, $organizers, $contacts, $images_str, $description, $status);
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Event added successfully!',
            'uin' => $uin
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database prepare error: ' . $conn->error]);
}
?>
