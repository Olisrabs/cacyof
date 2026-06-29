<?php
require_once 'db_conn.php';

// Pagination setup
$per_page = 6; // 3 columns × 2 rows
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// Total count
$total_result = $conn->query("SELECT COUNT(*) AS total FROM events");
$total_row    = $total_result->fetch_assoc();
$total        = (int)$total_row['total'];
$total_pages  = ceil($total / $per_page);
if ($page > $total_pages && $total_pages > 0) { $page = $total_pages; $offset = ($page - 1) * $per_page; }

// Fetch events
$stmt = $conn->prepare("SELECT * FROM events ORDER BY start_date DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$events = $stmt->get_result();

// Default event image — use new logo as placeholder
$default_images = [
    'images/new logo.png',
];

$img_index = 0;
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
    <title>CACYOF BOUESTI - Events</title>
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
                                        <span class="current">Events</span>
                                    </div>
                                    <h1 class="page-title mb-0">Events</h1>
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
                    <!-- Events Section -->
                    <section id="events-section" class="events-section pad-top-120 pad-bottom-70">
                        <!-- Screan Reader Text -->
                        <h2 class="screen-reader-text">Events</h2>
                        <div class="container">
                            <!-- Row -->
                            <div class="row">
                                <!-- Col -->
                                <div class="col-md-12">
                                    <!--events Main wrap-->
                                    <div class="events-main-wrapper events-grid events-style-2">
                                        <div class="row">

                                            <?php if ($events->num_rows > 0): ?>
                                                <?php while ($e = $events->fetch_assoc()): ?>
                                            <!-- Col-md -->
                                            <div class="col-lg-4 col-md-6">
                                                <!--events Inner-->
                                                <div class="events-inner margin-bottom-30">
                                                    <!--events Thumb-->
                                                    <div class="events-thumb mb-0 relative">
                                                        <?php
                                                        $img_list = !empty($e['images']) ? explode(',', $e['images']) : [];
                                                        $img = !empty($img_list) && file_exists(__DIR__ . '/src/uploads/events/' . $img_list[0]) ? 'src/uploads/events/' . htmlspecialchars($img_list[0]) : $default_images[$img_index % count($default_images)];
                                                        $img_index++;
                                                        ?>
                                                        <img src="<?= $img ?>" class="img-fluid thumb w-100" style="height:250px; object-fit:cover;" alt="events-img" />
                                                    </div>
                                                    <!--events details-->
                                                    <div class="events-details pad-lr-30 pad-bottom-35">
                                                        <div class="event-date margin-bottom-30">
                                                            <?= date('M d', strtotime($e['start_date'])) ?>
                                                            <span class="event-time"><?= !empty($e['start_time']) ? date('h:i A', strtotime($e['start_time'])) : '' ?></span>
                                                        </div>
                                                        <div class="event-title mb-3">
                                                            <h5><a href="event-details.php?id=<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></a></h5>
                                                        </div>
                                                        <div class="read-more">
                                                            <a href="event-details.php?id=<?= $e['id'] ?>">Event Details</a>
                                                        </div>
                                                    </div>
                                                    <!--events details-->
                                                </div>
                                                <!--events Inner Ends-->
                                            </div>
                                            <!--Col-md Ends-->
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-md-12 text-center py-5">
                                                <p>No events found. Check back soon!</p>
                                            </div>
                                        <?php endif; ?>

                                        </div>
                                        <!-- events Row -->

                                        <!-- Pagination -->
                                        <div class="row">
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
                                        </div>
                                        <!-- Pagination End -->
                                    </div>
                                    <!-- events Main wrap Ends -->
                                </div>
                                <!-- Col -->
                            </div>
                            <!-- Row -->
                        </div>
                    </section>
                    <!-- events Section Ends -->
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