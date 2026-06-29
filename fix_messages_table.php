<?php
header('Content-Type: text/plain');
include('db_conn.php');

$sql = "ALTER TABLE messages MODIFY COLUMN timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
if ($conn->query($sql)) {
    echo "Successfully modified timestamp column in messages table to default to CURRENT_TIMESTAMP.\n";
} else {
    echo "Error modifying messages table: " . $conn->error . "\n";
}
?>
