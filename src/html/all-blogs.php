<?php
include "session_check.php";
include('db_conn.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$userName = $session_name ?? 'User';

// Fetch all blogs with robust order fallback
$query = "SELECT * FROM blogs ORDER BY timestamp DESC";
$checkCol = $conn->query("SHOW COLUMNS FROM blogs LIKE 'timestamp'");
if (!$checkCol || $checkCol->num_rows === 0) {
    $checkCol2 = $conn->query("SHOW COLUMNS FROM blogs LIKE 'created_at'");
    if ($checkCol2 && $checkCol2->num_rows > 0) {
        $query = "SELECT * FROM blogs ORDER BY created_at DESC";
    } else {
        $query = "SELECT * FROM blogs ORDER BY id DESC";
    }
}
$stmt = $conn->prepare($query);
$stmt->execute();
$blogs = $stmt->get_result();
$stmt->close();

$totalBlogs = $blogs->num_rows;
?>
<!DOCTYPE html>
<html lang="zxx" class="js">
<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="All blogs — CACYOF BOUESTI admin panel.">
    <link rel="shortcut icon" href="./images/favicon.png">
    <title>All Blogs | CACYOF BOUESTI</title>
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
    <style>
        /* ── Blog Card ─────────────────────────────────── */
        .blog-card-item .card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(54,74,99,.07);
            transition: box-shadow .25s, transform .25s;
            border: 1px solid #e5e9f2;
            display: flex;
            flex-direction: column;
        }
        .blog-card-item .card:hover {
            box-shadow: 0 8px 28px rgba(54,74,99,.15);
            transform: translateY(-3px);
        }

        /* Cover image / gradient banner */
        .blog-cover {
            width: 100%;
            height: 185px;
            object-fit: cover;
            display: block;
            flex-shrink: 0;
        }
        .blog-cover-banner {
            width: 100%;
            height: 185px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 52px;
            font-weight: 800;
            color: rgba(255,255,255,.88);
            letter-spacing: 3px;
            user-select: none;
            flex-shrink: 0;
        }

        /* Card body */
        .blog-card-body-wrap {
            padding: 18px 20px 4px;
            flex: 1;
        }

        /* Category badge */
        .blog-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            background: #ede7ff;
            color: #6576ff;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 9px;
        }

        /* Blog title */
        .blog-card-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #364a63;
            line-height: 1.45;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Author + date */
        .blog-meta-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .blog-author-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }
        .blog-avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .blog-author-name {
            font-size: 12px;
            font-weight: 600;
            color: #526484;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }
        .blog-date-chip {
            font-size: 11px;
            color: #8094ae;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-left: auto;
            white-space: nowrap;
        }

        /* Excerpt */
        .blog-excerpt-short {
            font-size: 13px;
            color: #6e82a5;
            line-height: 1.7;
        }
        .blog-excerpt-full {
            font-size: 13px;
            color: #6e82a5;
            line-height: 1.7;
            display: none;
        }
        .blog-see-more-btn {
            background: none;
            border: none;
            padding: 0;
            font-size: 12px;
            font-weight: 600;
            color: #6576ff;
            cursor: pointer;
            margin-top: 4px;
            display: inline-block;
        }
        .blog-see-more-btn:hover { text-decoration: underline; }

        /* Card footer */
        .blog-card-footer {
            padding: 11px 20px 14px;
            border-top: 1px solid #f0f3f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
        }
        .blog-read-link {
            font-size: 12.5px;
            font-weight: 600;
            color: #6576ff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color .2s;
        }
        .blog-read-link:hover { color: #3c40c4; }
        .blog-delete-btn {
            background: none;
            border: none;
            padding: 4px;
            color: #c4c4d4;
            font-size: 17px;
            cursor: pointer;
            line-height: 1;
            transition: color .2s;
        }
        .blog-delete-btn:hover { color: #e85347; }

        /* Pagination */
        .blog-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 32px;
            flex-wrap: wrap;
        }
        .blog-page-btn {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1px solid #e5e9f2;
            border-radius: 6px;
            background: #fff;
            color: #526484;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .blog-page-btn:hover:not(:disabled) { background: #6576ff; color: #fff; border-color: #6576ff; }
        .blog-page-btn.active { background: #6576ff; color: #fff; border-color: #6576ff; }
        .blog-page-btn:disabled { opacity: .4; cursor: not-allowed; }
        .blog-page-info { font-size: 13px; color: #8094ae; }

        /* Empty state */
        .blog-empty-state {
            text-align: center;
            padding: 64px 20px;
            color: #8094ae;
        }
        .blog-empty-state em.icon {
            font-size: 52px;
            opacity: .3;
            display: block;
            margin-bottom: 14px;
        }
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
                                        <h3 class="nk-block-title page-title">All Blogs</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Showing <strong id="blog-count"><?= $totalBlogs ?></strong> of <strong><?= $totalBlogs ?></strong> blog<?= $totalBlogs !== 1 ? 's' : '' ?>.</p>
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content d-flex align-items-center flex-wrap g-3">
                                        <div class="form-control-wrap me-3" style="width:300px;max-width:100%;">
                                            <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                                            <input type="text" id="search-blogs" class="form-control form-control-md" placeholder="Search title, author, category…">
                                        </div>
                                        <a href="html/add-blog.php" class="btn btn-primary">
                                            <em class="icon ni ni-plus"></em><span>Add New Blog</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Blog Grid -->
                            <div class="nk-block">
                                <?php if ($totalBlogs > 0): ?>
                                <div class="row g-gs blog-grid-container">
                                    <?php
                                    $palette = [
                                        ['#6576ff','#8f9fff'],
                                        ['#1ee0ac','#0fba8f'],
                                        ['#f4bd0e','#e5a800'],
                                        ['#09c2de','#00a6be'],
                                        ['#e85347','#c93a30'],
                                        ['#8950fc','#6a30e0'],
                                        ['#ff63a5','#e04080'],
                                    ];
                                    $ci = 0;
                                    while ($blog = $blogs->fetch_assoc()):
                                        $title    = htmlspecialchars($blog['title'] ?? 'Untitled');
                                        $name     = htmlspecialchars($blog['name'] ?? 'Anonymous');
                                        $category = htmlspecialchars($blog['category'] ?? '');
                                        $date     = !empty($blog['date']) ? date('M d, Y', strtotime($blog['date'])) : '';

                                        // Strip HTML for plain-text excerpt
                                        $rawDesc = strip_tags($blog['description'] ?? '');
                                        $excerpt = mb_strlen($rawDesc) > 220 ? mb_substr($rawDesc, 0, 220) . '…' : $rawDesc;

                                        // Author initials
                                        $words    = array_values(array_filter(explode(' ', trim($blog['name'] ?? ''))));
                                        $initials = strtoupper(
                                            substr($words[0] ?? 'B', 0, 1) .
                                            (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0] ?? 'B', 1, 1))
                                        );

                                        $pal      = $palette[$ci % count($palette)];
                                        $gradFrom = $pal[0];
                                        $gradTo   = $pal[1];
                                        $ci++;

                                        // Cover image (stored in src/html/uploads/blogs/)
                                        $imgFile  = $blog['image'] ?? '';
                                        $hasCover = $imgFile && file_exists(__DIR__ . '/uploads/blogs/' . $imgFile);

                                        // Search data attribute (pre-computed lowercase)
                                        $searchText = strtolower($title . ' ' . $name . ' ' . $category . ' ' . $rawDesc);
                                    ?>
                                    <div class="col-sm-6 col-xl-4 blog-card-item" data-search-text="<?= htmlspecialchars($searchText) ?>">
                                        <div class="card h-100">

                                            <!-- Cover image or gradient banner -->
                                            <?php if ($hasCover): ?>
                                            <img src="html/uploads/blogs/<?= htmlspecialchars($imgFile) ?>" class="blog-cover" alt="<?= $title ?>">
                                            <?php else: ?>
                                            <div class="blog-cover-banner" style="background:linear-gradient(135deg,<?= $gradFrom ?>,<?= $gradTo ?>);">
                                                <?= $initials ?>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Card body -->
                                            <div class="blog-card-body-wrap">

                                                <?php if ($category): ?>
                                                <span class="blog-badge"><?= $category ?></span>
                                                <?php endif; ?>

                                                <h6 class="blog-card-title" title="<?= $title ?>"><?= $title ?></h6>

                                                <div class="blog-meta-row">
                                                    <div class="blog-author-chip">
                                                        <div class="blog-avatar-sm" style="background:<?= $gradFrom ?>;"><?= $initials ?></div>
                                                        <span class="blog-author-name" title="<?= $name ?>"><?= $name ?></span>
                                                    </div>
                                                    <?php if ($date): ?>
                                                    <div class="blog-date-chip">
                                                        <em class="icon ni ni-calendar"></em>
                                                        <?= $date ?>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="blog-excerpt-wrap">
                                                    <?php
                                                    $shortLimit = 120;
                                                    $hasMore = mb_strlen($rawDesc) > $shortLimit;
                                                    $shortText = $hasMore ? mb_substr($rawDesc, 0, $shortLimit) . '…' : $rawDesc;
                                                    ?>
                                                    <span class="blog-excerpt-short"><?= htmlspecialchars($shortText) ?: '<em class="text-soft">No content preview.</em>' ?></span>
                                                    <?php if ($hasMore): ?>
                                                    <span class="blog-excerpt-full"><?= htmlspecialchars($rawDesc) ?></span>
                                                    <button class="blog-see-more-btn" onclick="blogToggleExcerpt(this)">See more</button>
                                                    <?php endif; ?>
                                                </div>

                                            </div><!-- .blog-card-body-wrap -->

                                            <!-- Footer actions -->
                                            <div class="blog-card-footer">
                                                <a href="../blog-single.php?id=<?= (int)$blog['id'] ?>" target="_blank" class="blog-read-link">
                                                    <em class="icon ni ni-external-link"></em> View on Site
                                                </a>
                                                <button class="blog-delete-btn" title="Delete this blog" onclick="confirmDelete(<?= (int)$blog['id'] ?>)">
                                                    <em class="icon ni ni-trash"></em>
                                                </button>
                                            </div>

                                        </div><!-- .card -->
                                    </div><!-- .blog-card-item -->
                                    <?php endwhile; ?>
                                </div><!-- .row -->

                                <!-- Pagination controls (rendered by JS) -->
                                <div id="blog-pagination" class="blog-pagination" style="display:none;"></div>

                                <!-- No-match state -->
                                <div id="no-matching-blogs" class="blog-empty-state" style="display:none;">
                                    <em class="icon ni ni-search"></em>
                                    <p class="fs-15px">No blogs match your search. Try a different keyword.</p>
                                </div>

                                <?php else: ?>
                                <div class="blog-empty-state">
                                    <em class="icon ni ni-file-text"></em>
                                    <p class="fs-15px">No blogs yet. <a href="html/add-blog.php" class="text-primary fw-bold">Add your first blog →</a></p>
                                </div>
                                <?php endif; ?>

                            </div><!-- .nk-block -->

                        </div>
                    </div>
                </div>
            </div><!-- .nk-content -->

            <div class="nk-footer">
                <div class="container-fluid">
                    <div class="nk-footer-wrap">
                        <div class="nk-footer-copyright">&copy; <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI.</div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-wrap -->
    </div>
</div>

<script src="./assets/js/bundle.js?ver=2.2.0"></script>
<script src="./assets/js/scripts.js?ver=2.2.0"></script>
<script>
// ── Configuration ─────────────────────────────────────────
var BLOGS_PER_PAGE = 10;
var currentPage    = 1;
var allCards       = Array.from(document.querySelectorAll('.blog-card-item'));
var visibleCards   = allCards.slice(); // filtered subset

// ── See more / See less ────────────────────────────────────
function blogToggleExcerpt(btn) {
    var wrap  = btn.closest('.blog-excerpt-wrap');
    var short = wrap.querySelector('.blog-excerpt-short');
    var full  = wrap.querySelector('.blog-excerpt-full');
    var open  = full.style.display === 'inline';
    if (open) {
        full.style.display = 'none';
        short.style.display = 'inline';
        btn.textContent = 'See more';
    } else {
        full.style.display = 'inline';
        short.style.display = 'none';
        btn.textContent = 'See less';
    }
}

// ── Pagination renderer ────────────────────────────────────
function renderPage(page) {
    currentPage = page;
    var start = (page - 1) * BLOGS_PER_PAGE;
    var end   = start + BLOGS_PER_PAGE;
    allCards.forEach(function(card) { card.style.display = 'none'; });
    var pageCards = visibleCards.slice(start, end);
    pageCards.forEach(function(card) { card.style.display = ''; });
    buildPagination();
    // Update count label
    var countEl = document.getElementById('blog-count');
    if (countEl) countEl.textContent = visibleCards.length;
}

function buildPagination() {
    var pager = document.getElementById('blog-pagination');
    if (!pager) return;
    var totalPages = Math.ceil(visibleCards.length / BLOGS_PER_PAGE);
    if (totalPages <= 1) { pager.style.display = 'none'; return; }
    pager.style.display = 'flex';
    pager.innerHTML = '';

    // Prev button
    var prev = document.createElement('button');
    prev.className = 'blog-page-btn';
    prev.innerHTML = '<em class="icon ni ni-chevron-left"></em>';
    prev.disabled = currentPage === 1;
    prev.onclick = function() { if (currentPage > 1) renderPage(currentPage - 1); };
    pager.appendChild(prev);

    // Page number buttons
    for (var i = 1; i <= totalPages; i++) {
        (function(pg) {
            var btn = document.createElement('button');
            btn.className = 'blog-page-btn' + (pg === currentPage ? ' active' : '');
            btn.textContent = pg;
            btn.onclick = function() { renderPage(pg); };
            pager.appendChild(btn);
        })(i);
    }

    // Next button
    var next = document.createElement('button');
    next.className = 'blog-page-btn';
    next.innerHTML = '<em class="icon ni ni-chevron-right"></em>';
    next.disabled = currentPage === totalPages;
    next.onclick = function() { if (currentPage < totalPages) renderPage(currentPage + 1); };
    pager.appendChild(next);

    // Page info
    var info = document.createElement('span');
    info.className = 'blog-page-info';
    info.textContent = 'Page ' + currentPage + ' of ' + totalPages;
    pager.appendChild(info);
}

// ── Live search ────────────────────────────────────────────
document.getElementById('search-blogs').addEventListener('input', function() {
    var query = this.value.toLowerCase().trim();
    visibleCards = allCards.filter(function(card) {
        var text = card.getAttribute('data-search-text') || '';
        return !query || text.includes(query);
    });
    var noEl = document.getElementById('no-matching-blogs');
    if (noEl) noEl.style.display = (visibleCards.length === 0 && allCards.length > 0) ? '' : 'none';
    currentPage = 1;
    renderPage(1);
});

// ── Initial render ─────────────────────────────────────────
renderPage(1);

// ── Delete blog ────────────────────────────────────────────
function confirmDelete(id) {
    if (!confirm('Delete this blog? This action cannot be undone.')) return;
    fetch(window.location.origin + '/cacyof/src/html/delete_blog_script.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(function(r) { return r.text(); })
    .then(function(txt) {
        try {
            var data = JSON.parse(txt);
            if (data.status === 'success') { window.location.reload(); }
            else { alert(data.message || 'Delete failed.'); }
        } catch(e) { alert('Unexpected server response.'); }
    })
    .catch(function() { alert('Network error. Please try again.'); });
}
</script>
</body>
</html>