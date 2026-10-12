<?php
include "session_check.php";
include('db_conn.php');

// Use the correct session key set during login and session_check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Assign display variables from session_check.php result
$userName = $session_name ?? 'User';

// Default profile image
$defaultImage = "assets/images/default-avatar.png";

// Profile photo — use $row from session_check.php
$profileImage = !empty($row['profile_photo'])
    ? "uploads/profile_photos/" . htmlspecialchars($row['profile_photo'])
    : $defaultImage;

// Fetch all distinct regimes for the dropdown
$regimes_stmt = $conn->prepare("SELECT DISTINCT regime FROM executives WHERE is_executive != 'No' ORDER BY regime DESC");
$regimes_stmt->execute();
$regimes_result = $regimes_stmt->get_result();
$regimes = [];
while ($r = $regimes_result->fetch_assoc()) {
    $regimes[] = $r['regime'];
}
$regimes_stmt->close();

// Determine selected regime (default to latest)
$selected_regime = $_GET['regime'] ?? ($regimes[0] ?? '');

// Fetch executives for selected regime
if ($selected_regime !== '') {
    $exec_stmt = $conn->prepare(
        "SELECT w.id, w.uin, w.email, w.name, w.phone, w.department, w.units, w.level, w.status,
                e.id AS exec_id, e.office, e.regime, e.passport, e.whatsapp,
                w.passport AS worker_passport
         FROM workers w
         INNER JOIN executives e ON w.uin = e.uin
         WHERE e.is_executive != 'No' AND e.regime = ?
         ORDER BY w.name ASC"
    );
    $exec_stmt->bind_param("s", $selected_regime);
} else {
    $exec_stmt = $conn->prepare(
        "SELECT w.id, w.uin, w.email, w.name, w.phone, w.department, w.units, w.level, w.status,
                e.id AS exec_id, e.office, e.regime, e.passport, e.whatsapp,
                w.passport AS worker_passport
         FROM workers w
         INNER JOIN executives e ON w.uin = e.uin
         WHERE e.is_executive != 'No' AND w.status = 'Active'
         ORDER BY w.name ASC"
    );
}
$exec_stmt->execute();
$workers = $exec_stmt->get_result();
$exec_stmt->close();

function get_passport_src($passport_file) {
    if (empty($passport_file)) return '';
    // uploads/passports/ — copied during make_executive
    if (file_exists(__DIR__ . '/uploads/passports/' . $passport_file)) {
        return 'html/uploads/passports/' . htmlspecialchars($passport_file);
    }
    // images/passports/ — direct from add_worker.php
    if (file_exists(__DIR__ . '/images/passports/' . $passport_file)) {
        return 'html/images/passports/' . htmlspecialchars($passport_file);
    }
    // Legacy
    if (file_exists(__DIR__ . '/uploads/passport/' . $passport_file)) {
        return 'html/uploads/passport/' . htmlspecialchars($passport_file);
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Executives | CACYOF BOUESTI</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
</head>

<body class="nk-body bg-lighter npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?php include "sidebar.php"?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?php include "header.php"?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block nk-block-lg">
                                    <div class="nk-block-head">
                                        <div class="nk-block-head-content nk-block-between">
                                            <h4 class="nk-block-title">Executive's List</h4>
                                            <!-- Regime Filter Dropdown -->
                                            <div class="nk-block-head-content">
                                                <form method="GET" action="" class="d-flex align-items-center" style="gap:8px;">
                                                    <label class="form-label mb-0" style="white-space:nowrap;font-weight:600;">Filter by Regime:</label>
                                                    <select name="regime" id="regimeFilter" class="form-control form-select" style="min-width:160px;" onchange="this.form.submit()">
                                                        <option value="">-- All Regimes --</option>
                                                        <?php foreach ($regimes as $regime): ?>
                                                            <option value="<?= htmlspecialchars($regime) ?>" <?= $selected_regime === $regime ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($regime) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col nk-tb-col-check"><span class="sub-text">S/N</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Unit(s)</span></th>
                                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Office</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Phone</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">WhatsApp</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text">Department</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text">Level</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text">Regime</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $sn = 1;
                                                        if ($workers->num_rows > 0):
                                                            while ($worker = $workers->fetch_assoc()):
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col nk-tb-col-check">
                                                            <span><?= $sn++ ?></span>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <?php
                                                                    // Try exec passport first, then worker's own photo
                                                                    $p_file = !empty($worker['passport']) ? $worker['passport'] : ($worker['worker_passport'] ?? '');
                                                                    $p_src  = get_passport_src($p_file);
                                                                ?>
                                                                <?php if ($p_src !== ''): ?>
                                                                    <div class="user-avatar d-none d-sm-flex" style="overflow:hidden;border-radius:50%;width:40px;height:40px;">
                                                                        <img src="<?= $p_src ?>" alt="passport" style="width:100%;height:100%;object-fit:cover;">
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                        <span>
                                                                            <?php
                                                                                $words = explode(' ', trim($worker['name']));
                                                                                echo strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0], 1, 1)));
                                                                            ?>
                                                                        </span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?= htmlspecialchars($worker['name']) ?></span>
                                                                    <span><?= htmlspecialchars($worker['email']) ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span class="tb-amount"><?= htmlspecialchars($worker['units']) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span><?= htmlspecialchars($worker['office']) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?= htmlspecialchars($worker['phone']) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if (!empty($worker['whatsapp'])): ?>
                                                                <a href="<?= htmlspecialchars($worker['whatsapp']) ?>" target="_blank" class="text-primary d-inline-flex align-items-center" style="gap:4px;">
                                                                    <em class="icon ni ni-whatsapp" style="font-size:16px;"></em> Chat
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">—</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <ul class="list-status">
                                                                <li><em class="icon text-success ni ni-check-circle"></em>
                                                                <span><?= htmlspecialchars($worker['department']) ?></span></li>
                                                            </ul>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span><?= htmlspecialchars($worker['level']) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span><?= htmlspecialchars($worker['regime']) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if ($worker['status'] === 'Active'): ?>
                                                                <span class="tb-status text-success">
                                                                    <?= htmlspecialchars($worker['status']) ?>
                                                                </span>
                                                            <?php elseif ($worker['status'] === 'Alumni'): ?>
                                                                <span class="tb-status text-secondary">
                                                                    <?= htmlspecialchars($worker['status']) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="tb-status text-danger">
                                                                    <?= htmlspecialchars($worker['status']) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li>
                                                                                    <a href="#" class="unmark-exec-btn" 
                                                                                       data-exec-id="<?= $worker['exec_id'] ?>" 
                                                                                       data-name="<?= htmlspecialchars($worker['name']) ?>" 
                                                                                       data-regime="<?= htmlspecialchars($worker['regime']) ?>"
                                                                                       data-uin="<?= htmlspecialchars($worker['uin']) ?>">
                                                                                        <em class="icon ni ni-user-remove"></em>
                                                                                        <span>Unmark Executive</span>
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr><!-- .nk-tb-item  -->
                                                    <?php 
                                                            endwhile;
                                                        else:
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col" colspan="10" style="text-align:center;padding:40px;">
                                                            <em class="icon ni ni-inbox" style="font-size:32px;color:#ccc;"></em><br>
                                                            <span class="tb-lead text-muted">No executives found<?= $selected_regime ? ' for regime "' . htmlspecialchars($selected_regime) . '"' : '' ?>.</span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        endif;
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- .card-preview -->
                                </div> <!-- nk-block -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI.</div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- ========== RESULT MODAL ========== -->
    <div class="modal fade" tabindex="-1" id="resultModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross"></em></a>
                <div class="modal-body modal-body-lg text-center">
                    <div class="nk-modal" id="resultModalBody">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== RESULT MODAL END ========== -->

    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=2.2.0"></script>
    <script src="./assets/js/scripts.js?ver=2.2.0"></script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.unmark-exec-btn', function (e) {
                e.preventDefault();
                var execId = $(this).data('exec-id');
                var name   = $(this).data('name');
                var regime = $(this).data('regime');
                var uin    = $(this).data('uin');

                if (!confirm('Are you sure you want to unmark ' + name + ' as an Executive for the ' + regime + ' regime?')) {
                    return;
                }

                $.ajax({
                    url: 'html/unmark_executive.php',
                    type: 'POST',
                    data: { exec_id: execId, regime: regime, uin: uin },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                                <h4 class="nk-modal-title">Executive Unmarked!</h4>
                                <p class="sub-text mt-2">${data.message}</p>
                            `);
                        } else {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                                <h4 class="nk-modal-title">Action Failed</h4>
                                <p class="text-danger mt-2">${data.message}</p>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                </div>
                            `);
                        }
                        $('#resultModal').modal({ backdrop: 'static', keyboard: false });
                        $('#resultModal').modal('show');

                        if (data.status === 'success') {
                            setTimeout(function () {
                                if (data.regime_deleted) {
                                    window.location.href = 'html/executives.php';
                                } else {
                                    location.reload();
                                }
                            }, 2000);
                        }
                    },
                    error: function (xhr) {
                        alert('Request failed: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>