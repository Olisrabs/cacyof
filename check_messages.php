<?php
header('Content-Type: text/plain');
include('db_conn.php');

$res = $conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $conn->error . "\n";
}
?>
