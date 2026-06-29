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
?>

<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Workers | CACYOF BOUESTI</title>
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
                                            <h4 class="nk-block-title">Worker's List</h4>
                                            <div class="nk-block-head-content">
                                                <a href="#" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#addEventPopup"><em
                                                        class="icon ni ni-plus"></em><span>Add New Worker</span></a>
                                            </div><!-- .nk-block-head-content -->
                                        </div>
                                    </div>
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist"
                                                data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col nk-tb-col-check"><span
                                                                class="sub-text">S/N</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                                        <th class="nk-tb-col tb-col-mb"><span
                                                                class="sub-text">Unit(s)</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span
                                                                class="sub-text">Phone</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span
                                                                class="sub-text">Department</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span
                                                                class="sub-text">Level</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span
                                                                class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // Fetch workers from the database
                                                        $stmt = $conn->prepare("SELECT id, name, email, phone, department, units, level, status, passport FROM workers ORDER BY name ASC");
                                                        $stmt->execute();
                                                        $workers = $stmt->get_result();
                                                        $stmt->close();


                                                        // Check if workers exist and display them
                                                        if ($workers->num_rows > 0):
                                                            while ($worker = $workers->fetch_assoc()):
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col nk-tb-col-check">
                                                            <span>
                                                                <?= htmlspecialchars($worker['id']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <?php
                                                                    $w_pass = $worker['passport'] ?? '';
                                                                    $w_img  = '';
                                                                    if (!empty($w_pass)) {
                                                                        if (file_exists(__DIR__ . '/images/passports/' . $w_pass)) {
                                                                            $w_img = 'html/images/passports/' . htmlspecialchars($w_pass);
                                                                        } elseif (file_exists(__DIR__ . '/uploads/passports/' . $w_pass)) {
                                                                            $w_img = 'html/uploads/passports/' . htmlspecialchars($w_pass);
                                                                        }
                                                                    }
                                                                ?>
                                                                <?php if ($w_img !== ''): ?>
                                                                    <div class="user-avatar d-none d-sm-flex" style="overflow:hidden;border-radius:50%;width:40px;height:40px;flex-shrink:0;">
                                                                        <img src="<?= $w_img ?>" alt="passport" style="width:100%;height:100%;object-fit:cover;object-position:top;">
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                        <span><?php
                                                                            $words = explode(' ', trim($worker['name']));
                                                                            echo strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0], 1, 1)));
                                                                        ?></span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="user-info">
                                                                    <span class="tb-lead">
                                                                        <?= htmlspecialchars($worker['name']) ?>
                                                                    </span>
                                                                    <span>
                                                                        <?= htmlspecialchars($worker['email']) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span class="tb-amount">
                                                                <?= htmlspecialchars($worker['units']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span>
                                                                <?= htmlspecialchars($worker['phone']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <ul class="list-status">
                                                                <li><em
                                                                        class="icon text-success ni ni-check-circle"></em>
                                                                    <span>
                                                                        <?= htmlspecialchars($worker['department']) ?>
                                                                    </span></li>
                                                            </ul>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span>
                                                                <?= htmlspecialchars($worker['level']) ?>
                                                            </span>
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
                                                                        <a href="#"
                                                                            class="dropdown-toggle btn btn-icon btn-trigger"
                                                                            data-toggle="dropdown"><em
                                                                                class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                                <ul class="link-list-opt no-bdr">
                                                                                <?php if ($worker['status'] === 'Active'): ?>
                                                                                    <li>
                                                                                        <a href="#" class="worker-action" 
                                                                                        data-id="<?= $worker['id'] ?>" 
                                                                                        data-action="mark_inactive">
                                                                                            <em class="icon ni ni-cross-circle"></em>
                                                                                            <span>Mark Inactive</span>
                                                                                        </a>
                                                                                    </li>
                                                                                <?php else: ?>
                                                                                    <li>
                                                                                        <a href="#" class="worker-action" 
                                                                                        data-id="<?= $worker['id'] ?>" 
                                                                                        data-action="mark_active">
                                                                                            <em class="icon ni ni-check-round-cut"></em>
                                                                                            <span>Mark Active</span>
                                                                                        </a>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                                <li>
                                                                                    <a href="#" class="worker-action" 
                                                                                    data-id="<?= $worker['id'] ?>" 
                                                                                    data-action="make_executive">
                                                                                        <em class="icon ni ni-users"></em>
                                                                                        <span>Make Executive</span>
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="#" class="worker-action" 
                                                                                    data-id="<?= $worker['id'] ?>" 
                                                                                    data-action="graduated">
                                                                                        <em class="icon ni ni-coffee"></em>
                                                                                        <span>Graduated</span>
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
                                                        <td class="nk-tb-col" colspan="10">
                                                            <span class="tb-lead text-muted">No workers found.</span>
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
                            <div class="nk-footer-copyright"> &copy;
                                <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI.
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- ========== ADD WORKER MODAL ========== -->
    <div class="modal fade" tabindex="-1" id="addEventPopup">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Worker</h5>
                    <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross"></em></a>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="addWorkerForm" enctype="multipart/form-data">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter full name"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email"
                                        placeholder="Enter email address" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="Enter phone number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Unit(s) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="units" placeholder="Enter unit(s)"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="department"
                                        placeholder="Enter department" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Level <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="level" placeholder="Enter level"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Admission Year <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="admission_year"
                                        placeholder="e.g. 2021" max="<?= date('Y') ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Graduation Year <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="graduation_year"
                                        placeholder="e.g. 2025" min="2026" max="<?= date('Y') + 10 ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Passport Photo <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="passport" accept="image/*" required>
                                    <small class="text-muted">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</small>
                                </div>
                            </div>

                        </div><!-- .row -->

                        <div class="form-group mt-4 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_worker" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em> Add Worker
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== ADD WORKER MODAL END ========== -->


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

    <!-- ========== FORM SUBMIT SCRIPT ========== -->
    <script>
        $(document).ready(function () {
            $('#addWorkerForm').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this); // ← FormData handles file uploads

                $.ajax({
                    url: 'html/add_worker.php',
                    type: 'POST',
                    data: formData,
                    processData: false,  // ← Required for FormData
                    contentType: false,  // ← Required for FormData
                    success: function (response) {
                        console.log(typeof response, response);

                        var data = response;

                        $('#addEventPopup').modal('hide');

                        if (data.status === 'success') {
                            $('#resultModalBody').html(`
                        <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                        <h4 class="nk-modal-title">Worker Added!</h4>
                        <p class="sub-text mt-2">${data.message}</p>
                    `);
                        } else {
                            $('#resultModalBody').html(`
                        <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                        <h4 class="nk-modal-title">Failed!</h4>
                        <p class="text-danger mt-2">${data.message}</p>
                        <div class="mt-3">
                            <a href="#" class="btn btn-primary" data-dismiss="modal">Try Again</a>
                        </div>
                    `);
                        }

                        $('#resultModal').modal({ backdrop: 'static', keyboard: false });
                        $('#resultModal').modal('show');

                        if (data.status === 'success') {
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Status: ' + status + '\nError: ' + error + '\nResponse: ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
    <!-- ========== FORM SUBMIT SCRIPT END ========== -->
    <!-- ========== MAKE EXECUTIVE MODAL ========== -->
    <div class="modal fade" tabindex="-1" id="makeExecutiveModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make Executive</h5>
                    <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross"></em></a>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Please provide the executive details for this worker.</p>
                    <div class="form-group">
                        <label class="form-label">Office <span class="text-danger">*</span></label>
                        <input type="text" id="execOffice" class="form-control" placeholder="e.g. President, Secretary, Treasurer">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Regime <span class="text-danger">*</span></label>
                        <input type="text" id="execRegime" class="form-control" placeholder="e.g. 2024/2025">
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size:13px;border-radius:6px;">
                        <em class="icon ni ni-info-fill me-1"></em> The worker's passport photo uploaded during registration will be used automatically.
                    </div>
                    <div id="execModalError" class="text-danger mt-2" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmMakeExecBtn">
                        <em class="icon ni ni-check"></em> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== MAKE EXECUTIVE MODAL END ========== -->

    <!-- ========== ACTION SCRIPT START ========== -->
    <script>
        $(document).ready(function () {

            var pendingExecId = null;

            // Intercept make_executive — show the office/regime modal
            $(document).on('click', '.worker-action[data-action="make_executive"]', function (e) {
                e.preventDefault();
                pendingExecId = $(this).data('id');
                $('#execOffice').val('');
                $('#execRegime').val('');
                $('#execPassport').val('');
                $('#execModalError').hide().text('');
                $('#makeExecutiveModal').modal('show');
            });

            // Submit from the executive modal
            $('#confirmMakeExecBtn').on('click', function () {
                var office   = $.trim($('#execOffice').val());
                var regime   = $.trim($('#execRegime').val());

                if (!office || !regime) {
                    $('#execModalError').text('Please fill in both Office and Regime.').show();
                    return;
                }

                $('#execModalError').hide();
                $('#makeExecutiveModal').modal('hide');

                // Send without passport — worker's own passport will be used by server
                $.ajax({
                    url: 'html/worker_action.php',
                    type: 'POST',
                    data: { id: pendingExecId, action: 'make_executive', office: office, regime: regime },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                                <h4 class="nk-modal-title">Done!</h4>
                                <p class="sub-text mt-2">${data.message}</p>
                            `);
                        } else {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                                <h4 class="nk-modal-title">Failed!</h4>
                                <p class="text-danger mt-2">${data.message}</p>
                            `);
                        }
                        $('#resultModal').modal({ backdrop: 'static', keyboard: false });
                        $('#resultModal').modal('show');
                        if (data.status === 'success') {
                            setTimeout(function () { location.reload(); }, 2000);
                        }
                    },
                    error: function (xhr) {
                        alert('Request failed: ' + xhr.responseText);
                    }
                });
            });

            // All other actions — use a simple confirm
            $(document).on('click', '.worker-action:not([data-action="make_executive"])', function (e) {
                e.preventDefault();

                var id     = $(this).data('id');
                var action = $(this).data('action');

                var labels = {
                    mark_active:   'mark this worker as Active',
                    mark_inactive: 'mark this worker as Inactive',
                    mark_absent:   'mark this worker as Absent',
                    graduated:     'mark this worker as Graduated'
                };

                if (!confirm('Are you sure you want to ' + (labels[action] || action) + '?')) {
                    return;
                }

                $.ajax({
                    url: 'html/worker_action.php',
                    type: 'POST',
                    data: { id: id, action: action },
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                                <h4 class="nk-modal-title">Done!</h4>
                                <p class="sub-text mt-2">${data.message}</p>
                            `);
                        } else {
                            $('#resultModalBody').html(`
                                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                                <h4 class="nk-modal-title">Failed!</h4>
                                <p class="text-danger mt-2">${data.message}</p>
                            `);
                        }
                        $('#resultModal').modal({ backdrop: 'static', keyboard: false });
                        $('#resultModal').modal('show');
                        if (data.status === 'success') {
                            setTimeout(function () { location.reload(); }, 2000);
                        }
                    },
                    error: function (xhr) {
                        alert('Request failed: ' + xhr.responseText);
                    }
                });
            });

        });
    </script>
    <!-- =========== ACTION SCRIPT END =========== -->
</body>

</html>