<?php
header('Content-Type: application/json');
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$exec_id = intval($_POST['exec_id'] ?? 0);
$regime  = trim($_POST['regime'] ?? '');
$uin     = trim($_POST['uin'] ?? '');

if (!$exec_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing executive ID.']);
    exit();
}

// 1. Fetch executive record to verify existence and get details
$check = $conn->prepare("SELECT id, uin, name, office, regime FROM executives WHERE id = ?");
$check->bind_param("i", $exec_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Executive record not found.']);
    $check->close();
    exit();
}

$exec = $res->fetch_assoc();
$check->close();

$exec_name = $exec['name'];
$exec_uin  = !empty($uin) ? $uin : $exec['uin'];
$regime    = !empty($regime) ? $regime : $exec['regime'];

// 2. Mark this executive as unexecutive (is_executive = 'No')
$upd = $conn->prepare("UPDATE executives SET is_executive = 'No' WHERE id = ?");
$upd->bind_param("i", $exec_id);
if (!$upd->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to unmark executive.']);
    $upd->close();
    exit();
}
$upd->close();

// 3. Check how many active executives remain for that regime
$chk_regime = $conn->prepare("SELECT COUNT(*) AS active_cnt FROM executives WHERE regime = ? AND is_executive != 'No'");
$chk_regime->bind_param("s", $regime);
$chk_regime->execute();
$reg_res = $chk_regime->get_result();
$active_cnt = $reg_res->fetch_assoc()['active_cnt'] ?? 0;
$chk_regime->close();

$regime_deleted = false;
// If no executives left for this regime, delete all records for this regime from executives table
if ($active_cnt == 0) {
    $del = $conn->prepare("DELETE FROM executives WHERE regime = ?");
    $del->bind_param("s", $regime);
    $del->execute();
    $del->close();
    $regime_deleted = true;
}

// 4. Check if the worker has any other active executive roles remaining
$chk_worker = $conn->prepare("SELECT COUNT(*) AS exec_cnt FROM executives WHERE uin = ? AND is_executive != 'No'");
$chk_worker->bind_param("s", $exec_uin);
$chk_worker->execute();
$w_res = $chk_worker->get_result();
$worker_exec_cnt = $w_res->fetch_assoc()['exec_cnt'] ?? 0;
$chk_worker->close();

// If worker has no remaining executive roles, set is_executive = 'No' in workers table
if ($worker_exec_cnt == 0) {
    $upd_w = $conn->prepare("UPDATE workers SET is_executive = 'No' WHERE uin = ?");
    $upd_w->bind_param("s", $exec_uin);
    $upd_w->execute();
    $upd_w->close();
}

$message = $exec_name . ' has been unmarked as executive.';
if ($regime_deleted) {
    $message .= ' There are no executives left for the ' . $regime . ' regime, so the regime has been deleted from records.';
}

echo json_encode([
    'status' => 'success',
    'message' => $message,
    'regime_deleted' => $regime_deleted
]);

$conn->close();
?>
