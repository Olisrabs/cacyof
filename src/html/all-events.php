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
    <meta name="description" content="A powerful and conceptual apps base dashboard that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>All Events - Events | CACYOF BOUESTI Admin</title>
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
                                            <h4 class="nk-block-title">All Events</h4>
                                            <div class="nk-block-des">
                                                <p>Find all events from the starting date to cuurrent as uploaded.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                            <table class="datatable-init table">
                                                <thead>
                                                    <tr>
                                                        <th>TItle</th>
                                                        <th>Organizers</th>
                                                        <th>Start Date</th>
                                                        <th>Venue</th>
                                                        <th>Contact</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        // Fetch events from the database
                                                        $stmt = $conn->prepare("SELECT * FROM events ORDER BY title ASC");
                                                        $stmt->execute();
                                                        $events = $stmt->get_result();
                                                        $stmt->close();

                                                        // Check if events exist and display them
                                                        if ($events->num_rows > 0):
                                                            while ($event = $events->fetch_assoc()):
                                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($event['title']) ?></td>
                                                        <td><?= htmlspecialchars($event['organizers']) ?></td>
                                                        <td><?= htmlspecialchars($event['start_date']) ?></td>
                                                        <td><?= htmlspecialchars($event['venue']) ?></td>
                                                        <td><?= htmlspecialchars($event['contacts']) ?></td>
                                                    </tr>
                                                    <?php 
                                                            endwhile;
                                                        else:
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col" colspan="8" style="text-align:center; padding:40px;">
                                                            <em class="icon ni ni-inbox" style="font-size:32px; color:#ccc;"></em><br>
                                                            <span class="tb-lead text-muted">No event found.</span>
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
                            <div class="nk-footer-copyright"> &copy; <?php echo date('Y'); ?> CACYOF BOUESTI.</div>
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