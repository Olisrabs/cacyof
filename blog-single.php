<?php
require_once 'db_conn.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $blog = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (empty($blog)) {
    header("Location: blog.php");
    exit();
}

// Ensure comments table exists
$conn->query("CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$img_src = !empty($blog['image']) && file_exists(__DIR__ . '/src/html/uploads/blogs/' . $blog['image'])
    ? 'src/html/uploads/blogs/' . htmlspecialchars($blog['image'])
    : 'images/logo.png';
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="images/favicon.png" rel="icon" />
    <title>CACYOF BOUESTI - <?= htmlspecialchars($blog['title']) ?></title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/simple-line-icons.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/color-schemes/red.css" id="changeable-colors"> 
    <link rel="stylesheet" href="css/style.css" class="main-style">
    <style>
        .blog-content {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 16px 0;
        }
        .comment-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            border-left: 4px solid #6576ff;
        }
        
        /* Custom Blog Meta styling */
        .blog-meta-custom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .blog-meta-custom .meta-item {
            display: inline-flex;
            align-items: center;
            margin-right: 24px;
            font-size: 14px;
            color: #666;
            line-height: 1;
        }
        .blog-meta-custom .meta-item i {
            font-size: 16px;
            margin-right: 8px;
        }
        .blog-meta-custom .meta-item strong {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>

<body data-res-from="1025">
    <div class="zmm-wrapper">
        <a href="#" class="zmm-close close"></a>
        <div class="zmm-inner bg-white typo-dark">
            <div class="text-center mobile-logo-part margin-bottom-30">
                 <a href="index.php" class="img-before"><img src="images/logo-dark.png" class="img-fluid changeable-dark" width="170" height="51" alt="Logo"></a>
            </div>
            <div class="zmm-main-nav"></div>
        </div>
    </div>
    
    <div class="page-wrapper">
        <div class="page-wrapper-inner">
            <header>
                <div class="mobile-header bg-white typo-dark">
                    <div class="mobile-header-inner">
                        <div class="sticky-outer">
                            <div class="sticky-head">
                                <div class="basic-container clearfix">
                                    <ul class="nav mobile-header-items pull-left">
                                        <li class="nav-item"><a href="#" class="zmm-toggle img-before"><i class="ti-menu"></i></a></li>
                                    </ul>
                                    <ul class="nav mobile-header-items pull-center">
                                        <li>
                                            <a href="index.php" class="img-before"><img src="images/logo-dark.png" class="img-fluid changeable-dark" width="149" height="45" alt="Logo"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-inner header-1">
                    <div class="sticky-outer">
                        <div class="sticky-head">
                            <?php include "navbar.php"?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Title -->
            <div class="page-title-wrap typo-white">
                <div class="page-title-wrap-inner section-bg-img" data-bg="images/page-title.jpg">
                    <span class="theme-overlay"></span>
                    <div class="container">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <div class="page-title-inner">
                                    <div id="breadcrumb" class="breadcrumb margin-bottom-10">
                                        <a href="index.php" class="theme-color">Home</a>
                                        <a href="blog.php" class="theme-color">Blog</a>
                                        <span class="current">Blog Details</span>
                                    </div>
                                    <h1 class="page-title mb-0"><?= htmlspecialchars($blog['title']) ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-wrapper pad-none">
                <div class="content-inner">
                    <section id="blog-single" class="blog-single py-5">
                        <div class="container">
                            <div class="row">
                                <!-- Centralized Layout -->
                                <div class="col-lg-10 mx-auto">
                                    <div class="blog-wrap mb-5 bg-white p-4 p-md-5 b-radius-8 box-shadow1">
                                        <div class="blog-info-wrap">
                                            <!-- Blog Cover Image -->
                                            <div class="blog-thumb mb-4 text-center">
                                                <img src="<?= $img_src ?>" class="img-fluid b-radius-8" style="max-height:500px; width:100%; object-fit:cover;" alt="blog-img">
                                            </div>
                                            <!-- Meta -->
                                            <div class="blog-meta-custom">
                                                <div class="meta-item">
                                                    <i class="ti-user theme-color"></i>
                                                    <strong><?= htmlspecialchars($blog['name'] ?? 'CACYOF Team') ?></strong>
                                                </div>
                                                <?php if (!empty($blog['category'])): ?>
                                                <div class="meta-item">
                                                    <i class="ti-tag theme-color"></i>
                                                    <span><?= htmlspecialchars($blog['category']) ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <div class="meta-item">
                                                    <i class="ti-time theme-color"></i>
                                                    <span><?= date('F d, Y', strtotime($blog['date'])) ?></span>
                                                </div>
                                            </div>
                                            <!-- Main Content (Exact Formatted HTML) -->
                                            <div class="blog-content pb-4">
                                                <?= $blog['description'] ?>
                                            </div>
                                            <hr class="my-5">
                                        </div>
                                        
                                        <!-- Comments Display Section -->
                                        <div class="comments-list-section mb-5">
                                            <h4 class="mb-4" id="comments-count-title">0 Comments</h4>
                                            <div id="comments-container">
                                                <!-- Dynamic Comments Loaded Here -->
                                            </div>
                                            <div id="show-more-comments-container" style="display:none; text-align:center; margin-top:20px;">
                                                <button id="show-more-comments-btn" class="btn btn-secondary btn-dim px-4" style="background-color: #6576ff; color: #fff; border: none; border-radius: 4px; padding: 10px 20px; font-weight: 600; cursor: pointer;">Show More Comments</button>
                                            </div>
                                        </div>

                                        <!-- Comments Submit Form -->
                                        <div class="comments-form bg-light p-4 p-md-5 b-radius-8">
                                            <h4 class="comment-reply-title mb-2">Leave a Reply</h4>
                                            <p class="mb-4 text-soft">Your email address will not be published. Required fields are marked *</p>
                                            <div class="contact-form-wrap">
                                                <form id="contact-form-1" class="contact-form" method="post">
                                                    <input type="hidden" name="blog_id" value="<?= $id ?>">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input id="name" class="form-control py-3" name="name" placeholder="Your Name *" required type="text" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input id="email" class="form-control py-3" name="email" placeholder="Your Email *" required type="email" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <textarea id="message" class="form-control py-3" rows="6" name="message" placeholder="Write your comment here..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button type="submit" id="comment-submit-btn" class="btn btn-primary btn-lg theme-btn px-5 mt-2">Post Comment</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "footer.php"?>
    
    <!-- jQuery Lib -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/owl.carousel.min.js"></script> 
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/custom.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var blogId = <?= $id ?>;

        function loadComments() {
            var cached = sessionStorage.getItem('blog_comments_' + blogId);
            if (cached) {
                try {
                    var comments = JSON.parse(cached);
                    renderComments(comments);
                    return;
                } catch (e) {
                    sessionStorage.removeItem('blog_comments_' + blogId);
                }
            }

            fetch('get-comments.php?blog_id=' + blogId)
                .then(response => response.json())
                .then(data => {
                    sessionStorage.setItem('blog_comments_' + blogId, JSON.stringify(data));
                    renderComments(data);
                })
                .catch(err => {
                    console.error('Error loading comments:', err);
                    document.getElementById('comments-container').innerHTML = '<p class="text-danger">Failed to load comments.</p>';
                });
        }

        function renderComments(comments) {
            var container = document.getElementById('comments-container');
            var titleEl = document.getElementById('comments-count-title');
            var showMoreContainer = document.getElementById('show-more-comments-container');
            var showMoreBtn = document.getElementById('show-more-comments-btn');

            var total = comments.length;
            titleEl.textContent = total + ' Comment' + (total !== 1 ? 's' : '');

            if (total === 0) {
                container.innerHTML = '<p class="text-soft fs-15px">No comments yet. Be the first to share your thoughts!</p>';
                showMoreContainer.style.display = 'none';
                return;
            }

            container.innerHTML = '';

            // Render first 3 comments
            var limit = Math.min(total, 3);
            for (var i = 0; i < limit; i++) {
                container.appendChild(createCommentElement(comments[i]));
            }

            if (total > 3) {
                showMoreContainer.style.display = 'block';
                showMoreBtn.onclick = function() {
                    for (var i = 3; i < total; i++) {
                        container.appendChild(createCommentElement(comments[i]));
                    }
                    showMoreContainer.style.display = 'none';
                };
            } else {
                showMoreContainer.style.display = 'none';
            }
        }

        function createCommentElement(comment) {
            var div = document.createElement('div');
            div.className = 'comment-item';
            div.innerHTML = '<div class="d-flex justify-content-between mb-2">' +
                '<strong class="text-dark fs-16px">' + comment.name + '</strong>' +
                '<span class="text-muted fs-12px">' + comment.created_at + '</span>' +
                '</div>' +
                '<p class="mb-0 text-secondary">' + comment.message + '</p>';
            return div;
        }

        loadComments();

        var commentForm = document.getElementById('contact-form-1');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var formEl = this;
                var btn = document.getElementById('comment-submit-btn');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Posting...';

                var formData = new FormData(formEl);
                fetch('submit-comment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data.status === 'success') {
                        // Clear comments cache
                        sessionStorage.removeItem('blog_comments_' + blogId);
                        formEl.reset();
                        if (typeof showGlobalSuccessModal === 'function') {
                            showGlobalSuccessModal('Comment Posted!', data.message);
                        } else {
                            alert(data.message);
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        alert(data.message || 'Error posting comment.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert('Communication error. Please try again.');
                });
            });
        }
    });
    </script>
</body>

</html>