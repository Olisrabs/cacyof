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
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Default Dashboard | CACYOF BOUESTI Template</title>
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
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Dashboard</h3>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="row g-gs">
                                        <div class="col-xxl-3 col-sm-6">
                                            <div class="card">
                                                <div class="nk-ecwg nk-ecwg6">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">Active Workers</h6>
                                                            </div>
                                                        </div>
                                                        <div class="data">
                                                            <div class="data-group">
                                                                <div class="amount">
                                                                    <?php
                                                                        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM workers WHERE status = ?");
                                                                        $status = 'Active';
                                                                        $stmt->bind_param("s", $status);
                                                                        $stmt->execute();
                                                                        echo number_format($stmt->get_result()->fetch_assoc()['total'] ?? 0);
                                                                        $stmt->close();
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!-- .card-inner -->
                                                </div><!-- .nk-ecwg -->
                                            </div><!-- .card -->
                                        </div><!-- .col -->
                                        <div class="col-xxl-3 col-sm-6">
                                            <div class="card">
                                                <div class="nk-ecwg nk-ecwg6">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">Total Workers</h6>
                                                            </div>
                                                        </div>
                                                        <div class="data">
                                                            <div class="data-group">
                                                                <div class="amount">
                                                                    <?php
                                                                        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM workers");
                                                                        $stmt->execute();
                                                                        echo number_format($stmt->get_result()->fetch_assoc()['total'] ?? 0);
                                                                        $stmt->close();
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!-- .card-inner -->
                                                </div><!-- .nk-ecwg -->
                                            </div><!-- .card -->
                                        </div><!-- .col -->
                                        <div class="col-xxl-3 col-sm-6">
                                            <div class="card">
                                                <div class="nk-ecwg nk-ecwg6">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">Sermons</h6>
                                                            </div>
                                                        </div>
                                                        <div class="data">
                                                            <div class="data-group">
                                                                <div class="amount">
                                                                    <?php
                                                                        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM sermons");
                                                                        $stmt->execute();
                                                                        echo number_format($stmt->get_result()->fetch_assoc()['total'] ?? 0);
                                                                        $stmt->close();
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!-- .card-inner -->
                                                </div><!-- .nk-ecwg -->
                                            </div><!-- .card -->
                                        </div><!-- .col -->
                                        <div class="col-xxl-3 col-sm-6">
                                            <div class="card">
                                                <div class="nk-ecwg nk-ecwg6">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">Messages</h6>
                                                            </div>
                                                        </div>
                                                        <div class="data">
                                                            <div class="data-group">
                                                                <div class="amount">
                                                                    <?php
                                                                        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM messages");
                                                                        $stmt->execute();
                                                                        echo number_format($stmt->get_result()->fetch_assoc()['total'] ?? 0);
                                                                        $stmt->close();
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><!-- .card-inner -->
                                                </div><!-- .nk-ecwg -->
                                            </div><!-- .card -->
                                        </div><!-- .col -->
                                        <div class="col-xxl-8">
                                            <div class="card card-full">
                                                <div class="card-inner">
                                                    <div class="card-title-group">
                                                        <div class="card-title">
                                                            <h6 class="title">Latest Messages</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-list mt-n2">
                                                    <div class="nk-tb-item nk-tb-head">
                                                        <div class="nk-tb-col"><span>S/N</span></div>
                                                        <div class="nk-tb-col tb-col-sm"><span>Name</span></div>
                                                        <div class="nk-tb-col tb-col-md"><span>Email</span></div>
                                                        <div class="nk-tb-col"><span>Phone</span></div>
                                                        <div class="nk-tb-col"><span>Date</span></div>
                                                        <div class="nk-tb-col"><span class="d-none d-sm-inline">Message</span></div>
                                                    </div>
                                                    <?php
                                                        $stmt = $conn->prepare("SELECT name, email, phone, date, message FROM messages ORDER BY timestamp DESC LIMIT 10");
                                                        $stmt->execute();
                                                        $messages = $stmt->get_result();
                                                        $stmt->close();

                                                        if ($messages->num_rows > 0):
                                                            $sn = 1;
                                                            while ($msg = $messages->fetch_assoc()):
                                                    ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead"><?= $sn++ ?></span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="user-card">
                                                                <div class="user-name">
                                                                    <span class="tb-lead"><?= htmlspecialchars($msg['name']) ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <span class="tb-sub"><?= htmlspecialchars($msg['email']) ?></span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="tb-sub tb-amount"><?= htmlspecialchars($msg['phone']) ?></span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <span class="tb-sub"><?= htmlspecialchars($msg['date']) ?></span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead">
                                                                <?php
                                                                $full_msg = htmlspecialchars($msg['message']);
                                                                if (strlen($full_msg) > 60) {
                                                                    $truncated = substr($full_msg, 0, 60) . '...';
                                                                    echo '<span class="truncated-text">' . $truncated . '</span>';
                                                                    echo '<span class="full-text" style="display: none;">' . $full_msg . '</span>';
                                                                    echo ' <a href="javascript:void(0)" class="toggle-msg-btn text-primary fs-11px ms-1" onclick="toggleMessage(this)">See More</a>';
                                                                } else {
                                                                    echo $full_msg;
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <?php
                                                            endwhile;
                                                        else:
                                                    ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col" colspan="6">
                                                            <span class="tb-lead text-muted">No messages found.</span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div><!-- .card -->
                                        </div>
                                    </div><!-- .row -->
                                </div><!-- .nk-block -->
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
    <script src="./assets/js/charts/chart-ecommerce.js?ver=2.2.0"></script>
    <script>
    function toggleMessage(btn) {
        var parent = btn.parentElement;
        var truncated = parent.querySelector('.truncated-text');
        var full = parent.querySelector('.full-text');
        if (full.style.display === 'none') {
            full.style.display = 'inline';
            truncated.style.display = 'none';
            btn.textContent = 'See Less';
        } else {
            full.style.display = 'none';
            truncated.style.display = 'inline';
            btn.textContent = 'See More';
        }
    }
    </script>
</body>

</html>