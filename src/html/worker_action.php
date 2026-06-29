<?php
header('Content-Type: application/json');
include('db_conn.php');

function compare_regimes_helper($a, $b) {
    preg_match_all('/\d+/', $a, $mA);
    preg_match_all('/\d+/', $b, $mB);
    $yA = isset($mA[0][0]) ? intval($mA[0][0]) : 0;
    $yB = isset($mB[0][0]) ? intval($mB[0][0]) : 0;
    if ($yA !== $yB) return $yA - $yB;
    $yA2 = isset($mA[0][1]) ? intval($mA[0][1]) : 0;
    $yB2 = isset($mB[0][1]) ? intval($mB[0][1]) : 0;
    return $yA2 - $yB2;
}

function increment_worker_level($level) {
    if (preg_match('/(\d+)/', $level, $matches)) {
        $num = intval($matches[1]);
        if ($num >= 100) {
            $new_num = $num + 100;
            $new_level = str_replace($num, $new_num, $level);
            return [$new_level, $new_num];
        } else {
            $new_num = $num + 1;
            $new_level = str_replace($num, $new_num, $level);
            return [$new_level, $new_num * 100];
        }
    }
    return [$level, 0];
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}

$id     = intval($_POST['id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if (!$id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit();
}

// Verify worker exists — also fetch uin, name, and passport for executive insert
$check = $conn->prepare("SELECT id, uin, name, status, passport FROM workers WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found.']);
    exit();
}

$worker = $result->fetch_assoc();
$check->close();

switch ($action) {
    case 'mark_active':
        $stmt = $conn->prepare("UPDATE workers SET status = 'Active' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $msg = $worker['name'] . ' has been marked as Active.';
        break;

    case 'mark_inactive':
        $stmt = $conn->prepare("UPDATE workers SET status = 'Inactive' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $msg = $worker['name'] . ' has been marked as Inactive.';
        break;

    case 'mark_absent':
        $stmt = $conn->prepare("UPDATE workers SET status = 'Absent' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $msg = $worker['name'] . ' has been marked as Absent.';
        break;

    case 'make_executive':
        $office = trim($_POST['office'] ?? '');
        $regime = trim($_POST['regime'] ?? '');

        if (!$office || !$regime) {
            echo json_encode(['status' => 'error', 'message' => 'Office and Regime are required.']);
            exit();
        }

        // Get the latest regime currently in the executives table before inserting the new one
        $prev_latest_regime = '';
        $reg_res = $conn->query("SELECT DISTINCT regime FROM executives");
        if ($reg_res && $reg_res->num_rows > 0) {
            $regimes_list = [];
            while ($row_reg = $reg_res->fetch_assoc()) {
                $regimes_list[] = $row_reg['regime'];
            }
            usort($regimes_list, 'compare_regimes_helper');
            $prev_latest_regime = end($regimes_list);
        }

        // Use the worker's own passport (uploaded during registration)
        $worker_passport = $worker['passport'] ?? '';
        $passport_name   = null;

        if (!empty($worker_passport)) {
            // Worker passport saved to src/html/images/passports/ by add_worker.php
            $src_path = __DIR__ . '/images/passports/' . $worker_passport;
            $dest_dir = __DIR__ . '/uploads/passports/';
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }
            $dest_path = $dest_dir . $worker_passport;
            // Copy if not already there
            if (file_exists($src_path) && !file_exists($dest_path)) {
                copy($src_path, $dest_path);
            }
            $passport_name = $worker_passport;
        }

        // Update is_executive flag on the workers table
        $stmt = $conn->prepare("UPDATE workers SET is_executive = 'Yes' WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update worker record.']);
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();

        // Insert into executives table (with worker's passport)
        $ins = $conn->prepare("INSERT INTO executives (uin, name, office, regime, passport) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("sssss", $worker['uin'], $worker['name'], $office, $regime, $passport_name);

        if ($ins->execute()) {
            // Check if the new regime is greater than the previous latest regime
            $is_bigger = false;
            if (!empty($prev_latest_regime)) {
                if (compare_regimes_helper($regime, $prev_latest_regime) > 0) {
                    $is_bigger = true;
                }
            }

            $update_count = 0;
            $graduated_count = 0;
            if ($is_bigger) {
                // Update all active/inactive/absent workers' levels (exclude alumni)
                $workers_to_update = $conn->query("SELECT id, name, level, status FROM workers WHERE status != 'Alumni'");
                if ($workers_to_update && $workers_to_update->num_rows > 0) {
                    $update_stmt = $conn->prepare("UPDATE workers SET level = ?, status = ? WHERE id = ?");
                    while ($w_row = $workers_to_update->fetch_assoc()) {
                        $w_id = $w_row['id'];
                        $old_level = $w_row['level'];
                        $old_status = $w_row['status'];

                        list($new_level, $numeric_level) = increment_worker_level($old_level);
                        $new_status = $old_status;
                        if ($numeric_level > 500) {
                            $new_status = 'Alumni';
                            $graduated_count++;
                        } else {
                            $update_count++;
                        }

                        $update_stmt->bind_param("ssi", $new_level, $new_status, $w_id);
                        $update_stmt->execute();
                    }
                    $update_stmt->close();
                }
            }

            $msg = $worker['name'] . ' has been made an Executive (' . $office . ', ' . $regime . ').';
            if ($is_bigger) {
                $msg .= ' A new regime was detected! All worker academic levels have been incremented (+1). ' . $graduated_count . ' workers graduated to Alumni.';
            }

            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Worker updated but failed to insert into executives table: ' . $conn->error]);
        }

        $ins->close();
        $conn->close();
        exit();

    case 'graduated':
        $stmt = $conn->prepare("UPDATE workers SET status = 'Alumni' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $msg = $worker['name'] . ' has been marked as Graduated.';
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
        exit();
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => $msg]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
}

$stmt->close();
$conn->close();
?>