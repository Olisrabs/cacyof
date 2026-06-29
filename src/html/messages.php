<?php
include "session_check.php";
include('db_conn.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$userName = $session_name ?? 'User';
$defaultImage = "assets/images/default-avatar.png";
$profileImage = !empty($row['profile_photo'])
    ? "uploads/profile_photos/" . htmlspecialchars($row['profile_photo'])
    : $defaultImage;

// Fetch all messages
$stmt = $conn->prepare("SELECT * FROM messages ORDER BY id DESC");
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();

$totalMessages = $messages->num_rows;
?>
<!DOCTYPE html>
<html lang="zxx" class="js">
<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Messages received via the contact form — CACYOF BOUESTI admin.">
    <link rel="shortcut icon" href="./images/favicon.png">
    <title>Messages | CACYOF BOUESTI</title>
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
    <style>
        .msg-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; color: #fff;
            flex-shrink: 0;
        }
        .msg-card-body { font-size: 14px; color: #526484; line-height: 1.6; }
        .msg-more-btn {
            background: none; border: none; padding: 0;
            color: #6576ff; font-size: 13px; font-weight: 600;
            cursor: pointer;
        }
        .msg-more-btn:hover { text-decoration: underline; }
        .msg-full { display: none; }
        .msg-full.open { display: inline; }
        .msg-preview.hidden { display: none; }
    </style>
</head>
<body class="nk-body bg-lighter npc-default has-sidebar">
<div class="nk-app-root">
    <div class="nk-main">
        <?php include "sidebar.php"?>
        <div class="nk-wrap">
            <?php include "header.php"?>
            <div class="nk-content">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <div class="nk-content-body">

                            <!-- Page Head -->
                            <div class="nk-block-head nk-block-head-sm pb-4">
                                <div class="nk-block-between flex-wrap g-3">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">Messages</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Showing <strong id="msg-count"><?= $totalMessages ?></strong> of <strong><?= $totalMessages ?></strong> message<?= $totalMessages !== 1 ? 's' : '' ?>.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <div class="form-control-wrap" style="width: 320px; max-width: 100%;">
                                            <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                                            <input type="text" id="search-messages" class="form-control form-control-md" placeholder="Search name, email, message...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards Grid -->
                            <div class="nk-block">
                                <?php if ($totalMessages > 0): ?>
                                <div class="row g-gs msg-grid-container">
                                    <?php
                                    // Avatar bg colors cycling
                                    $colors = ['#6576ff','#1ee0ac','#f4bd0e','#e85347','#09c2de','#8950fc','#ff63a5'];
                                    $ci = 0;
                                    while ($msg = $messages->fetch_assoc()):
                                        $name    = htmlspecialchars($msg['name'] ?? '');
                                        $email   = htmlspecialchars($msg['email'] ?? '');
                                        $text    = htmlspecialchars($msg['message'] ?? '');
                                        $limit   = 160;
                                        $short   = mb_strlen($text) > $limit ? mb_substr($text, 0, $limit) . '…' : $text;
                                        $hasMore = mb_strlen($text) > $limit;
                                        // Initials
                                        $words   = array_filter(explode(' ', trim($msg['name'] ?? '')));
                                        $w       = array_values($words);
                                        $initials = strtoupper(
                                            substr($w[0] ?? 'U', 0, 1) .
                                            (isset($w[1]) ? substr($w[1], 0, 1) : substr($w[0] ?? 'U', 1, 1))
                                        );
                                        $color = $colors[$ci % count($colors)];
                                        $ci++;
                                        $uid = 'msg-' . ($msg['uin'] ?? $ci);
                                    ?>
                                    <div class="col-sm-6 col-lg-4 msg-card-item">
                                        <div class="card h-100">
                                            <div class="card-inner">
                                                <!-- Header: avatar + name + email -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="msg-avatar me-3" style="background:<?= $color ?>">
                                                        <?= $initials ?>
                                                    </div>&nbsp;
                                                    <div style="min-width:0">
                                                        <h6 class="title mb-0" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= $name ?></h6>
                                                        <span class="sub-text" style="font-size:12px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= $email ?></span>
                                                    </div>
                                                </div>
                                                <!-- Message body -->
                                                <div class="msg-card-body">
                                                    <?php if ($hasMore): ?>
                                                        <span class="msg-preview" id="prev-<?= $uid ?>"><?= $short ?></span>
                                                        <span class="msg-full" id="full-<?= $uid ?>"><?= $text ?></span>
                                                        <br>
                                                        <button class="msg-more-btn mt-1" onclick="toggleMsg('<?= $uid ?>', this)">See more</button>
                                                    <?php else: ?>
                                                        <span><?= $text ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div><!-- .row -->
                                <?php else: ?>
                                <div class="nk-block-head">
                                    <p class="text-soft">No messages yet.</p>
                                </div>
                                <?php endif; ?>
                            </div><!-- .nk-block -->

                        </div>
                    </div>
                </div>
            </div>
            <div class="nk-footer">
                <div class="container-fluid">
                    <div class="nk-footer-wrap">
                        <div class="nk-footer-copyright">&copy; <?php echo date('Y'); ?> CACYOF BOUESTI.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="./assets/js/bundle.js?ver=2.2.0"></script>
<script src="./assets/js/scripts.js?ver=2.2.0"></script>
<script>
function toggleMsg(uid, btn) {
    var prev = document.getElementById('prev-' + uid);
    var full = document.getElementById('full-' + uid);
    var expanded = full.classList.contains('open');
    if (expanded) {
        full.classList.remove('open');
        prev.classList.remove('hidden');
        btn.textContent = 'See more';
    } else {
        full.classList.add('open');
        prev.classList.add('hidden');
        btn.textContent = 'See less';
    }
}

// Real-time search filter for messages
document.getElementById('search-messages').addEventListener('input', function() {
    var query = this.value.toLowerCase().trim();
    var cards = document.querySelectorAll('.msg-card-item');
    var count = 0;
    cards.forEach(function(card) {
        var text = card.textContent.toLowerCase();
        if (text.includes(query)) {
            card.style.display = '';
            count++;
        } else {
            card.style.display = 'none';
        }
    });
    var noMsgEl = document.getElementById('no-matching-messages');
    if (count === 0 && cards.length > 0) {
        if (!noMsgEl) {
            noMsgEl = document.createElement('div');
            noMsgEl.id = 'no-matching-messages';
            noMsgEl.className = 'col-12 text-center py-5 text-soft';
            noMsgEl.innerHTML = '<em class="icon ni ni-info fs-1" style="font-size:36px; color:#8094ae;"></em><p class="mt-2 fs-15px">No messages match your search query.</p>';
            var container = document.querySelector('.msg-grid-container');
            if (container) container.appendChild(noMsgEl);
        }
        noMsgEl.style.display = '';
    } else if (noMsgEl) {
        noMsgEl.style.display = 'none';
    }
    var countEl = document.getElementById('msg-count');
    if (countEl) countEl.textContent = count;
});
</script>
</body>
</html>