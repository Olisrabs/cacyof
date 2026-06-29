<?php
require_once 'db_conn.php';

// Pagination setup
$per_page = 6; // 3 columns × 2 rows
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// Total count
$total_result = $conn->query("SELECT COUNT(*) AS total FROM sermons");
$total_row    = $total_result->fetch_assoc();
$total        = (int)$total_row['total'];
$total_pages  = ceil($total / $per_page);
if ($page > $total_pages && $total_pages > 0) { $page = $total_pages; $offset = ($page - 1) * $per_page; }

// Fetch sermons
$stmt = $conn->prepare("SELECT * FROM sermons ORDER BY date DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$sermons = $stmt->get_result();
?>
<!doctype html>
<html class="no-js" lang="zxx">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="images/favicon.png" rel="icon" />
    <!--Title-->
    <title>CACYOF BOUESTI - Sermons</title>
    <!-- CSS -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="css/simple-line-icons.min.css">
    <!-- Themify Icons -->
    <link rel="stylesheet" href="css/themify-icons.css">
	<!-- Owl Slider -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
	<!-- Magnific Popup -->
    <link rel="stylesheet" href="css/magnific-popup.css">
    <!-- Color Panel -->
    <link rel="stylesheet" href="css/color-schemes/red.css" id="changeable-colors"> 
	<!-- Main Style -->
    <link rel="stylesheet" href="css/style.css" class="main-style">
    <style>
    .premium-sermon-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .premium-sermon-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }
    .sermon-img-container {
        position: relative;
        overflow: hidden;
        height: 220px;
    }
    .sermon-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .premium-sermon-card:hover .sermon-img-container img {
        transform: scale(1.08);
    }
    .sermon-category-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        color: #4f46e5;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .sermon-date-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        color: #fff;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
    }
    .sermon-content-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: space-between;
    }
    .sermon-preacher-name {
        font-size: 13px;
        color: #888;
        font-weight: 500;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .sermon-preacher-name i {
        color: #4f46e5;
    }
    .sermon-card-title {
        font-size: 18px;
        font-weight: 700;
        line-height: 1.4;
        color: #1e1b4b;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 50px;
    }
    .sermon-card-title a {
        color: inherit;
        transition: color 0.2s;
    }
    .sermon-card-title a:hover {
        color: #4f46e5;
    }
    .sermon-action-footer {
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sermon-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .sermon-icon-btn:hover {
        transform: scale(1.1);
    }
    .btn-watch {
        background: #eef2ff;
        color: #4f46e5;
    }
    .btn-watch:hover {
        background: #4f46e5;
        color: #fff;
    }
    .btn-listen {
        background: #f0fdf4;
        color: #16a34a;
    }
    .btn-listen:hover {
        background: #16a34a;
        color: #fff;
    }
    .btn-pdf {
        background: #fef2f2;
        color: #dc2626;
    }
    .btn-pdf:hover {
        background: #dc2626;
        color: #fff;
    }
    .read-more-link {
        font-size: 13px;
        font-weight: 700;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.2s;
    }
    .read-more-link:hover {
        gap: 8px;
        color: #3730a3;
    }
    </style>
</head>
<!--Body Start-->
<body data-res-from="1025">
    <!--Zmm Wrapper-->
    <div class="zmm-wrapper">
        <a href="#" class="zmm-close close"></a>
        <div class="zmm-inner bg-white typo-dark">
            <div class="text-center mobile-logo-part margin-bottom-30">
                 <a href="index.php" class="img-before"><img src="images/logo-dark.png" class="img-fluid changeable-dark" width="170" height="51" alt="Logo"></a>
            </div>
            <div class="zmm-main-nav">
            </div>
        </div>
    </div>
    <!-- Main wrapper-->
    <div class="page-wrapper">
        <div class="page-wrapper-inner">
            <header>
                <!--Mobile Header-->
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
                                <!-- .basic-container -->
                            </div>
                            <!-- .sticky-head -->
                        </div>
                        <!-- .sticky-outer -->
                    </div>
                    <!-- .mobile-header-inner -->
                </div>
                <!-- .mobile-header -->
                <!--Header-->
                <div class="header-inner header-1">
                    <!--Sticky part-->
                    <div class="sticky-outer">
                        <div class="sticky-head">
                            <!--Navbar-->
                            <?php include "navbar.php"?>
                        </div>
                        <!--sticky-head-->
                    </div>
                    <!--sticky-outer-->
                </div>
            </header>
            <!-- header -->
            <!-- page-header -->
            <div class="page-title-wrap typo-white">
                <div class="page-title-wrap-inner section-bg-img" data-bg="images/page-title.jpg">
				<span class="theme-overlay"></span>
                    <div class="container">
                        <div class="row text-center">
                            <div class="col-md-12">
                                <div class="page-title-inner">
					<div id="breadcrumb" class="breadcrumb margin-bottom-10">
                                        <a href="index.php" class="theme-color">Home</a>
                                        <span class="current">Sermons</span>
                                    </div>
                                    <h1 class="page-title mb-0">Sermons</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page-header -->
            <!-- Page Content -->
            <div class="content-wrapper pad-none">
                <div class="content-inner">				
                    <!-- Sermon Section -->
                    <section id="section-sermon" class="sermon-section">
                        <div class="container">
                            <!-- Sermon Main Wrap -->
                            <div class="sermon-main-wrap sermon-list sermon-grid-2">
                                <!-- Row -->
                                <div class="row">
                                    <!-- Col -->
                                    <div class="col-lg-12">
                                        <!-- Row -->
                                        <div class="row">

<?php if ($sermons->num_rows > 0): ?>
    <?php while ($s = $sermons->fetch_assoc()): ?>
                                            <!-- Col -->
                                            <div class="col-md-4 mb-4">
                                                <!--Sermon Inner-->
                                                <div class="premium-sermon-card">
                                                    <!--Sermon Thumb (Image at the Top)-->
                                                    <div class="sermon-img-container">
                                                        <?php
                                                        $img = !empty($s['images']) && file_exists(__DIR__ . '/src/html/uploads/sermons/images/' . $s['images']) ? 'src/html/uploads/sermons/images/' . htmlspecialchars($s['images']) : 'images/new logo.png';
                                                        ?>
                                                        <a href="sermon-details.php?id=<?= $s['id'] ?>">
                                                            <img src="<?= $img ?>" alt="sermon-img" />
                                                        </a>
                                                        <span class="sermon-category-badge"><?= htmlspecialchars($s['category']) ?></span>
                                                        <span class="sermon-date-badge"><?= date('M d, Y', strtotime($s['date'])) ?></span>
                                                    </div>
                                                    
                                                    <!--Sermon Body-->
                                                    <div class="sermon-content-body">
                                                        <div>
                                                            <div class="sermon-preacher-name">
                                                                <i class="fa fa-user-circle-o"></i> By <?= htmlspecialchars($s['preacher']) ?>
                                                            </div>
                                                            <h4 class="sermon-card-title">
                                                                <a href="sermon-details.php?id=<?= $s['id'] ?>"><?= htmlspecialchars($s['title']) ?></a>
                                                            </h4>
                                                        </div>
                                                        
                                                        <!-- Action Buttons -->
                                                        <div class="sermon-action-footer">
                                                            <div class="sermon-play-list">
                                                                <ul class="d-flex gap-2 mb-0 list-unstyled align-items-center" style="display:flex; gap:8px; padding:0; margin:0; list-style:none;">
                                                                    <?php if (!empty($s['video'])): ?>
                                                                    <li>
                                                                        <a href="<?= htmlspecialchars($s['video']) ?>" class="popup-video sermon-icon-btn btn-watch" title="Watch Video"><i class="ti-control-play"></i></a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($s['audio'])): ?>
                                                                    <li>
                                                                        <a href="<?= htmlspecialchars($s['audio']) ?>" class="popup-audio sermon-icon-btn btn-listen" title="Listen Audio"><i class="ti-music"></i></a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($s['pdf'])): ?>
                                                                    <li>
                                                                        <a href="src/html/uploads/sermons/pdf/<?= htmlspecialchars($s['pdf']) ?>" class="sermon-pdf sermon-icon-btn btn-pdf" title="Download PDF" target="_blank"><i class="ti-book"></i></a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </div>
                                                            <div class="sermon-link">
                                                                <a href="sermon-details.php?id=<?= $s['id'] ?>" class="read-more-link">Read More <i class="ti-arrow-right"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--Sermon Inner Ends-->
                                            </div>
                                            <!-- Col Ends -->
    <?php endwhile; ?>
<?php else: ?>
                                            <div class="col-md-12 text-center py-5">
                                                <p>No sermons found. Check back later!</p>
                                            </div>
<?php endif; ?>

                                            <!-- Pagination Col -->
                                            <div class="col-lg-12">
                                                <div class="post-pagination-wrap margin-top-30">
                                                    <ul class="nav pagination post-pagination justify-content-center test-pagination">
                                                        <?php if ($page > 1): ?>
                                                        <li class="nav-item"><a href="?page=<?= $page - 1 ?>">Prev</a></li>
                                                        <?php endif; ?>

                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li class="nav-item <?= $i === $page ? 'active' : '' ?>">
                                                            <?php if ($i === $page): ?>
                                                                <span class="active"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></span>
                                                            <?php else: ?>
                                                                <a href="?page=<?= $i ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></a>
                                                            <?php endif; ?>
                                                        </li>
                                                        <?php endfor; ?>

                                                        <?php if ($page < $total_pages): ?>
                                                        <li class="nav-item"><a href="?page=<?= $page + 1 ?>" class="next-page" title="next">Next</a></li>
                                                        <li class="nav-item"><a href="?page=<?= $total_pages ?>" class="last-page" title="last">Last</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Pagination Col -->

                                        </div>
                                        <!-- row -->
                                    </div>
                                    <!-- Col -->
                                </div>
                                <!-- Row -->
                            </div>
                            <!-- Sermon Main Wrap -->
                        </div>
                        <!-- Container -->
                    </section>
                    <!-- Sermon Section End -->
                </div>
            </div>
        </div>
    <!-- .page-wrapper-inner -->
    </div>
    <!--page-wrapper-->

    <!-- Footer -->
    <?php include "footer.php"?>
    <!-- Footer -->
    
    <!-- jQuery Lib -->
    <script src="js/jquery.min.js"></script>
	<!-- Popper Js Support for Bootstrap -->
    <script src="js/popper.min.js"></script>
    <!-- Bootstrap Js -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Easing Js -->
    <script src="js/jquery.easing.min.js"></script>
    <!-- Carousel Js Code -->
    <script src="js/owl.carousel.min.js"></script> 
    <!-- Paroller Js -->
    <script src="js/jquery.paroller.min.js"></script>
    <!-- Isotope Js -->
    <script src="js/isotope.pkgd.min.js"></script>
    <!-- Magnific Popup Js -->
    <script src="js/jquery.magnific-popup.min.js"></script>
	<!-- Validator Js -->
    <script src="js/validator.min.js"></script>
    <!-- Smart Resize Js -->
    <script src="js/smartresize.min.js"></script>
    <!-- Appear Js -->
    <script src="js/jquery.appear.min.js"></script>
    <!-- Theme Custom Js -->
    <script src="js/custom.js"></script>
</body>
<!-- Body End -->
</html>