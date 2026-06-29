<?php
header('Content-Type: text/plain');
include('db_conn.php');

$res = $conn->query("DESCRIBE messages");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error describing messages: " . $conn->error . "\n";
}
?>
