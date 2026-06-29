<?php
include('db_conn.php');
$result = $conn->query("SELECT * FROM donations ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Donations | CACYOF BOUESTI Admin Dashboard</title>
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
                                        <div class="nk-block-head-content">
                                            <h4 class="nk-block-title">Donations</h4>
                                            <div class="nk-block-des">
                                                <p>All donations are listed here.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col"><span class="sub-text">S/N</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount (₦)</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Phone</span></th>
                                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text">Email</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Method</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($result && $result->num_rows > 0):
                                                        $sn = 1;
                                                        while ($row = $result->fetch_assoc()):
                                                            // Build initials for avatar
                                                            $name_parts = explode(' ', trim($row['name'] ?? 'UN'));
                                                            $initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
                                                            $status = $row['status'] ?? 'Pending';
                                                            $status_class = ($status === 'Completed' || $status === 'completed') ? 'text-success' : (($status === 'Failed' || $status === 'failed') ? 'text-danger' : 'text-warning');
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col">
                                                            <span><?= $sn++ ?></span>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                    <span><?= htmlspecialchars($initials) ?></span>
                                                                </div>
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></span>
                                                                    <span><?= htmlspecialchars($row['email'] ?? '') ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span class="tb-amount">₦<?= number_format((float)($row['amount'] ?? 0), 2) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span><?= htmlspecialchars($row['email'] ?? 'N/A') ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?= htmlspecialchars($row['method'] ?? 'N/A') ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span class="tb-status <?= $status_class ?>"><?= htmlspecialchars($status) ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?= isset($row['timestamp']) ? date('d M Y', strtotime($row['timestamp'])) : 'N/A' ?></span>
                                                        </td>
                                                    </tr><!-- .nk-tb-item  -->
                                                    <?php
                                                        endwhile;
                                                    else:
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col" colspan="8" style="text-align:center; padding:40px;">
                                                            <em class="icon ni ni-inbox" style="font-size:32px; color:#ccc;"></em>
                                                            <p style="color:#aaa; margin-top:8px;">No donations found.</p>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
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
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=2.2.0"></script>
    <script src="./assets/js/scripts.js?ver=2.2.0"></script>
</body>

</html>