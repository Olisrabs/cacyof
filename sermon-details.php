<?php
require_once 'db_conn.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM sermons WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $sermon = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (empty($sermon)) {
    header("Location: sermons.php");
    exit();
}

$img_src = !empty($sermon['images']) && file_exists(__DIR__ . '/src/html/uploads/sermons/images/' . $sermon['images'])
    ? 'src/html/uploads/sermons/images/' . htmlspecialchars($sermon['images'])
    : 'images/new logo.png';
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="images/favicon.png" rel="icon" />
    <title>CACYOF BOUESTI - <?= htmlspecialchars($sermon['title']) ?></title>
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
        .media-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-right: 12px;
            margin-bottom: 12px;
        }
        .media-btn-video { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; }
        .media-btn-video:hover { background: #4f46e5; color: #fff; }
        .media-btn-audio { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .media-btn-audio:hover { background: #16a34a; color: #fff; }
        .media-btn-pdf { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .media-btn-pdf:hover { background: #dc2626; color: #fff; }
        
        /* Custom Sermon Meta styling */
        .sermon-meta-custom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .sermon-meta-custom .meta-item {
            display: inline-flex;
            align-items: center;
            margin-right: 24px;
            font-size: 15px;
            color: #666;
            line-height: 1;
            margin-bottom: 8px;
        }
        .sermon-meta-custom .meta-item i {
            font-size: 18px;
            margin-right: 8px;
        }
        .sermon-meta-custom .meta-item strong {
            font-weight: 600;
            color: #333;
            margin-right: 4px;
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
                                        <a href="sermons.php" class="theme-color">Sermons</a>
                                        <span class="current">Sermon Details</span>
                                    </div>
                                    <h1 class="page-title mb-0"><?= htmlspecialchars($sermon['title']) ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-wrapper pad-none">
                <div class="content-inner">
                    <section id="single-sermon" class="single-sermon py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-10 mx-auto">
                                    <div class="bg-white p-4 p-md-5 b-radius-8 box-shadow1 mb-5">
                                        <!-- Main Sermon Image -->
                                        <div class="sermon-thumb mb-5 text-center">
                                            <img src="<?= $img_src ?>" class="img-fluid b-radius-8 w-100" style="max-height:550px; object-fit:cover;" alt="sermons-img" />
                                        </div>

                                        <!-- Media Actions -->
                                        <?php if (!empty($sermon['video']) || !empty($sermon['audio']) || !empty($sermon['pdf'])): ?>
                                        <div class="sermon-media-actions p-4 bg-light b-radius-8 mb-5">
                                            <h4 class="mb-3">Media Resources</h4>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <?php if (!empty($sermon['video'])): ?>
                                                    <a href="<?= htmlspecialchars($sermon['video']) ?>" class="media-btn media-btn-video popup-video" title="Watch Video">
                                                        <i class="ti-control-play fs-5"></i> Watch Video
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($sermon['audio'])): ?>
                                                    <a href="<?= htmlspecialchars($sermon['audio']) ?>" class="media-btn media-btn-audio popup-audio" title="Listen Audio">
                                                        <i class="ti-music fs-5"></i> Listen Audio
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($sermon['pdf'])): ?>
                                                    <a href="src/html/uploads/sermons/pdf/<?= htmlspecialchars($sermon['pdf']) ?>" class="media-btn media-btn-pdf" title="Download PDF" target="_blank">
                                                        <i class="ti-book fs-5"></i> Download PDF
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Sermon Meta -->
                                        <div class="sermon-meta-custom">
                                            <div class="meta-item"><i class="ti-user theme-color"></i> <strong>Sermon By:</strong> <?= htmlspecialchars($sermon['preacher']) ?></div>
                                            <div class="meta-item"><i class="ti-tag theme-color"></i> <strong>Category:</strong> <?= htmlspecialchars($sermon['category']) ?></div>
                                            <div class="meta-item"><i class="ti-calendar theme-color"></i> <strong>Date:</strong> <?= date('F d, Y', strtotime($sermon['date'])) ?></div>
                                        </div>

                                        <!-- Summary & Description -->
                                        <div class="sermons-content fs-16px" style="line-height:1.8; color:#444;">
                                            <?php if (!empty($sermon['summary'])): ?>
                                                <p class="lead fw-bold mb-4" style="color:#222; font-size:18px;"><?= nl2br(htmlspecialchars($sermon['summary'])) ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="sermons-title mt-5 mb-3">
                                                <h3>Sermon <span class="theme-color">Description</span></h3>
                                            </div>
                                            <?= !empty($sermon['description']) ? $sermon['description'] : '<p class="text-muted">No full description available for this sermon.</p>' ?>
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
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/owl.carousel.min.js"></script> 
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>