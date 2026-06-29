<?php
session_start();
include('db_conn.php');
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([]);
    exit();
}

$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY start_date ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Build FullCalendar start and end ISO-like strings
        $start = $row['start_date'];
        if (!empty($row['start_time'])) {
            $start .= 'T' . $row['start_time'];
        }
        
        $end = !empty($row['end_date']) ? $row['end_date'] : $row['start_date'];
        if (!empty($row['end_time'])) {
            $end .= 'T' . $row['end_time'];
        }
        
        // Color-coding based on status
        $className = 'fc-event-primary';
        if ($row['status'] === 'Completed') {
            $className = 'fc-event-success';
        } elseif ($row['status'] === 'Ongoing') {
            $className = 'fc-event-info';
        }
        
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $start,
            'end' => $end,
            'className' => $className,
            'extendedProps' => [
                'description' => $row['description'],
                'venue' => $row['venue'],
                'organizers' => $row['organizers'],
                'contacts' => $row['contacts'],
                'status' => $row['status'],
                'images' => $row['images']
            ]
        ];
    }
}

echo json_encode($events);
?>
