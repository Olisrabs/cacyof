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

// Verify worker exists — also fetch uin, name, status, passport, and phone for executive insert
$check = $conn->prepare("SELECT id, uin, name, status, passport, phone FROM workers WHERE id = ?");
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
        $office   = trim($_POST['office'] ?? '');
        $regime   = trim($_POST['regime'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');

        if (!$office || !$regime) {
            echo json_encode(['status' => 'error', 'message' => 'Office and Regime are required.']);
            exit();
        }

        // Check if the user is already recorded as an executive for the inputted regime
        $chk_dup = $conn->prepare("SELECT id, office, is_executive FROM executives WHERE (uin = ? OR name = ?) AND regime = ?");
        $chk_dup->bind_param("sss", $worker['uin'], $worker['name'], $regime);
        $chk_dup->execute();
        $dup_res = $chk_dup->get_result();

        if ($dup_res->num_rows > 0) {
            $existing = $dup_res->fetch_assoc();
            if ($existing['is_executive'] !== 'No') {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Duplicate Record: ' . $worker['name'] . ' is already registered as an executive (' . $existing['office'] . ') for the ' . $regime . ' regime.'
                ]);
                $chk_dup->close();
                $conn->close();
                exit();
            } else {
                // If previously marked unexecutive ('No'), remove old record so fresh one can be saved
                $del_old = $conn->prepare("DELETE FROM executives WHERE id = ?");
                $del_old->bind_param("i", $existing['id']);
                $del_old->execute();
                $del_old->close();
            }
        }
        $chk_dup->close();

        // Get the latest regime currently in the executives table before inserting the new one
        $prev_latest_regime = '';
        $reg_res = $conn->query("SELECT DISTINCT regime FROM executives WHERE is_executive != 'No'");
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

        // Ensure whatsapp column exists in executives table
        $check_cols = $conn->query("SHOW COLUMNS FROM `executives` LIKE 'whatsapp'");
        if ($check_cols && $check_cols->num_rows == 0) {
            $conn->query("ALTER TABLE `executives` ADD COLUMN `whatsapp` VARCHAR(255) DEFAULT NULL AFTER `passport`");
        }

        // If no whatsapp link is provided, default to a wa.me link using the worker's phone number
        if (empty($whatsapp) && !empty($worker['phone'])) {
            $clean_phone = preg_replace('/[^0-9]/', '', $worker['phone']);
            if (strlen($clean_phone) === 11 && $clean_phone[0] === '0') {
                $clean_phone = '234' . substr($clean_phone, 1);
            }
            $whatsapp = 'https://wa.me/' . $clean_phone;
        }

        // Insert into executives table (with worker's passport, whatsapp, and is_executive = 'Yes')
        $ins = $conn->prepare("INSERT INTO executives (uin, name, office, regime, passport, whatsapp, is_executive) VALUES (?, ?, ?, ?, ?, ?, 'Yes')");
        $ins->bind_param("ssssss", $worker['uin'], $worker['name'], $office, $regime, $passport_name, $whatsapp);

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