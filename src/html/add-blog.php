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
    <title>Add Blog | CACYOF BOUESTI</title>
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
                                <div class="nk-block-head nk-block-head-lg wide-sm">
                                    <div class="nk-block-head-content">
                                        <h2 class="nk-block-title fw-normal">Add New Blog</h2>
                                        <div class="nk-block-des">
                                            <p class="lead">Add a new blog and share the gospel through writing.</p>
                                        </div>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <div class="nk-block nk-block-lg">
                                    <div class="card card-bordered">
                                        <div class="card-inner">
                                            <form id="add-blog-form" method="post" enctype="multipart/form-data">
                                                <div class="row gy-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="author_name">Author's Name <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="author_name" name="author_name" placeholder="e.g. Pastor John Doe" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="blog_title">Blog Title <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="blog_title" name="blog_title" placeholder="Enter blog title" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="blog_category">Category</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="blog_category" name="blog_category" placeholder="e.g. Faith, Grace, Youth">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="blog_date">Publication Date</label>
                                                            <div class="form-control-wrap">
                                                                <input type="date" class="form-control" id="blog_date" name="blog_date" value="<?= date('Y-m-d') ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="blog_image">Blog Cover Image</label>
                                                            <div class="form-control-wrap">
                                                                <input type="file" class="form-control" id="blog_image" name="blog_image" accept="image/*">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="blog_details">Blog Details <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <textarea id="blog_details" name="blog_details" rows="12"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="form-alert" class="alert mb-3" style="display:none; border-radius:8px; padding:16px;"></div>
                                                        <button type="submit" id="btn-submit-blog" class="btn btn-lg btn-primary">
                                                            <em class="icon ni ni-send"></em><span class="ms-1">Publish Blog</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    tinymce.init({
        selector: '#blog_details',
        menubar: false,
        plugins: 'lists link image preview code',
        toolbar: 'undo redo | blocks | bold italic underline blockquote | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code preview',
        height: 400
    });

    document.getElementById('add-blog-form').addEventListener('submit', function(e) {
        e.preventDefault();
        tinymce.triggerSave();
        var formEl = this;
        var btn = document.getElementById('btn-submit-blog');
        var alertEl = document.getElementById('form-alert');
        btn.disabled = true;
        btn.querySelector('span').textContent = 'Publishing...';
        alertEl.style.display = 'none';

        var formData = new FormData(formEl);

        // Use absolute path to avoid <base href="../"> resolving issues
        var scriptUrl = window.location.origin + '/cacyof/src/html/add_blog_script.php';

        fetch(scriptUrl, {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.text(); // Read as text first so we can see what PHP actually returned
        })
        .then(function(text) {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Publish Blog';
            alertEl.style.display = 'block';
            try {
                var data = JSON.parse(text);
                if (data.status === 'success') {
                    alertEl.className = 'alert alert-success';
                    alertEl.innerHTML = '<em class="icon ni ni-check-circle me-2"></em>' + data.message;
                    setTimeout(function() {
                        window.location.href = window.location.origin + '/cacyof/src/html/all-blogs.php';
                    }, 1500);
                } else {
                    alertEl.className = 'alert alert-danger';
                    alertEl.innerHTML = '<em class="icon ni ni-cross-circle me-2"></em>' + (data.message || 'Unknown error.');
                }
            } catch (parseError) {
                // PHP returned non-JSON — show the raw output for debugging
                alertEl.className = 'alert alert-danger';
                alertEl.innerHTML = '<em class="icon ni ni-cross-circle me-2"></em><strong>Server error:</strong><br><pre style="font-size:11px;white-space:pre-wrap;">' + text.substring(0, 500) + '</pre>';
            }
        })
        .catch(function(error) {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Publish Blog';
            alertEl.style.display = 'block';
            alertEl.className = 'alert alert-danger';
            alertEl.innerHTML = '<em class="icon ni ni-cross-circle me-2"></em>Network error: ' + error.message;
        });
    });
    </script>
</body>

</html>