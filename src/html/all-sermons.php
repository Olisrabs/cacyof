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
    <title>All Sermons - Sermons | CACYOF BOUESTI</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
    <style>
        #sermonSearchBox {
            max-width: 320px;
        }
        #sermonSearchBox .form-control {
            border-radius: 4px 0 0 4px;
        }
        #sermonSearchBox .btn {
            border-radius: 0 4px 4px 0;
        }
        #noResultsMsg {
            display: none;
            text-align: center;
            padding: 40px;
            color: #aaa;
        }
        /* Pagination */
        #sermonPagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 16px 0 4px;
            flex-wrap: wrap;
        }
        #sermonPagination button {
            min-width: 36px;
            height: 36px;
            border: 1px solid #d9d9d9;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            color: #526484;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        #sermonPagination button:hover:not(:disabled) {
            background: #f5f6fa;
            border-color: #6576ff;
            color: #6576ff;
        }
        #sermonPagination button.active {
            background: #6576ff;
            border-color: #6576ff;
            color: #fff;
            font-weight: 600;
        }
        #sermonPagination button:disabled {
            opacity: 0.4;
            cursor: default;
        }
        #paginationInfo {
            text-align: center;
            font-size: 12px;
            color: #8094ae;
            padding-bottom: 8px;
        }
    </style>
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
                                            <div>
                                                <h4 class="nk-block-title">All Sermons</h4>
                                                <div class="nk-block-des">
                                                    <p>Find all sermons from the starting date to current as uploaded.</p>
                                                </div>
                                            </div>
                                            <!-- Search Box -->
                                            <div id="sermonSearchBox" class="input-group">
                                                <input type="text" id="sermonSearch" class="form-control" placeholder="Search title, preacher, category...">
                                                <div class="input-group-append">
                                                    <span class="btn btn-outline-secondary" style="cursor:default;">
                                                        <em class="icon ni ni-search"></em>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-preview">
                                        <div class="card-inner">
                                            <table class="table" id="sermonsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Category</th>
                                                        <th>Preacher</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sermonsBody">
                                                    <?php
                                                        // Fetch sermons from the database
                                                        $stmt = $conn->prepare("SELECT * FROM sermons ORDER BY date DESC");
                                                        $stmt->execute();
                                                        $sermons = $stmt->get_result();
                                                        $stmt->close();

                                                        // Check if sermons exist and display them
                                                        if ($sermons->num_rows > 0):
                                                            while ($sermon = $sermons->fetch_assoc()):
                                                    ?>
                                                    <tr class="sermon-row">
                                                        <td><?= htmlspecialchars($sermon['title']) ?></td>
                                                        <td><?= htmlspecialchars($sermon['category']) ?></td>
                                                        <td><?= htmlspecialchars($sermon['preacher']) ?></td>
                                                        <td><?= htmlspecialchars($sermon['date']) ?></td>
                                                    </tr>
                                                    <?php 
                                                            endwhile;
                                                        else:
                                                    ?>
                                                    <tr class="nk-tb-item">
                                                        <td colspan="4" style="text-align:center; padding:40px;">
                                                            <em class="icon ni ni-inbox" style="font-size:32px; color:#ccc;"></em><br>
                                                            <span class="tb-lead text-muted">No sermon found.</span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        endif;
                                                    ?>
                                                </tbody>
                                            </table>
                                            <div id="noResultsMsg">
                                                <em class="icon ni ni-search" style="font-size:32px;color:#ccc;"></em><br>
                                                <span class="text-muted">No sermons match your search.</span>
                                            </div>
                                            <div id="paginationInfo"></div>
                                            <div id="sermonPagination"></div>
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

    <!-- ========= PAGINATED SEARCH SCRIPT ========= -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput  = document.getElementById('sermonSearch');
            var allRows      = Array.from(document.querySelectorAll('#sermonsBody .sermon-row'));
            var noMsg        = document.getElementById('noResultsMsg');
            var pagination   = document.getElementById('sermonPagination');
            var paginInfo    = document.getElementById('paginationInfo');

            var ROWS_PER_PAGE = 10;
            var currentPage  = 1;
            var filteredRows = allRows.slice(); // starts as all rows

            function showPage(page) {
                currentPage = page;
                var start = (page - 1) * ROWS_PER_PAGE;
                var end   = start + ROWS_PER_PAGE;

                // Hide all filtered rows, then show only current page slice
                filteredRows.forEach(function(row, idx) {
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                renderPagination();
            }

            function renderPagination() {
                var total = filteredRows.length;
                var totalPages = Math.ceil(total / ROWS_PER_PAGE);

                // Update info text
                if (total > 0) {
                    var start = (currentPage - 1) * ROWS_PER_PAGE + 1;
                    var end   = Math.min(currentPage * ROWS_PER_PAGE, total);
                    paginInfo.textContent = 'Showing ' + start + '–' + end + ' of ' + total + ' sermons';
                } else {
                    paginInfo.textContent = '';
                }

                // Only show pagination UI if more than one page
                if (totalPages <= 1) {
                    pagination.innerHTML = '';
                    return;
                }

                var html = '';

                // Prev button
                html += '<button id="pg-prev" ' + (currentPage === 1 ? 'disabled' : '') + '>&#8249; Prev</button>';

                // Page number buttons (show up to 5 around current)
                var startPage = Math.max(1, currentPage - 2);
                var endPage   = Math.min(totalPages, startPage + 4);
                startPage     = Math.max(1, endPage - 4); // adjust if near end

                if (startPage > 1) html += '<button disabled style="border:none;background:none;">…</button>';
                for (var p = startPage; p <= endPage; p++) {
                    html += '<button class="pg-num' + (p === currentPage ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
                }
                if (endPage < totalPages) html += '<button disabled style="border:none;background:none;">…</button>';

                // Next button
                html += '<button id="pg-next" ' + (currentPage === totalPages ? 'disabled' : '') + '>Next &#8250;</button>';

                pagination.innerHTML = html;

                // Attach events
                var prevBtn = document.getElementById('pg-prev');
                var nextBtn = document.getElementById('pg-next');
                if (prevBtn) prevBtn.addEventListener('click', function() { showPage(currentPage - 1); });
                if (nextBtn) nextBtn.addEventListener('click', function() { showPage(currentPage + 1); });
                pagination.querySelectorAll('.pg-num').forEach(function(btn) {
                    btn.addEventListener('click', function() { showPage(parseInt(this.getAttribute('data-page'))); });
                });
            }

            function applyFilter(query) {
                // Hide all rows first
                allRows.forEach(function(row) { row.style.display = 'none'; });

                if (!query) {
                    filteredRows = allRows.slice();
                } else {
                    filteredRows = allRows.filter(function(row) {
                        return row.textContent.toLowerCase().includes(query);
                    });
                }

                noMsg.style.display = (filteredRows.length === 0 && query) ? 'block' : 'none';
                currentPage = 1;
                showPage(1);
            }

            // Initial render
            applyFilter('');

            // Search listener
            searchInput.addEventListener('keyup', function () {
                applyFilter(this.value.trim().toLowerCase());
            });
        });
    </script>
    <!-- ========= PAGINATED SEARCH SCRIPT END ========= -->
</body>

</html>