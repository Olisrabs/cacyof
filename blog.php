<?php
require_once 'db_conn.php';

// Pagination setup
$per_page = 6; // 3 columns × 2 rows
$page     = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// Total count
$total_result = $conn->query("SELECT COUNT(*) AS total FROM blogs");
$total_row    = $total_result->fetch_assoc();
$total        = (int)$total_row['total'];
$total_pages  = ceil($total / $per_page);
if ($page > $total_pages && $total_pages > 0) { $page = $total_pages; $offset = ($page - 1) * $per_page; }

// Fetch blogs
$stmt = $conn->prepare("SELECT * FROM blogs ORDER BY date DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$blogs = $stmt->get_result();

// Default blog image — use new logo as placeholder
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
    <title>CACYOF BOUESTI - Blog</title>
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
                                        <span class="current">Blog</span>
                                    </div>
                                    <h1 class="page-title mb-0">Our Blogs</h1>
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
					<!-- Blog Section -->
                    <section id="blog-section" class="blog-section pad-bottom-70">
                        <div class="container">
							<!-- Blog Main Wrap -->
							<div class="blog-main-wrap blog-grid">
								<!-- Row -->
								<div class="row">
									<!-- Col -->
									<div class="col-lg-12">
										<!-- Row -->
										<div class="row">

<?php if ($blogs->num_rows > 0): ?>
    <?php while ($b = $blogs->fetch_assoc()):
        $img_src = !empty($b['image']) && file_exists(__DIR__ . '/src/html/uploads/blogs/' . $b['image'])
            ? 'src/html/uploads/blogs/' . htmlspecialchars($b['image'])
            : $default_images[$img_index % count($default_images)];
        $img_index++;
    ?>
											<!-- Col -->
											<div class="col-md-4 mb-4">
												<div class="blog-style-1 h-100 box-shadow1 b-radius-8" style="background:#fff;">
													<!--Blog Inner-->
													<div class="blog-inner h-100 d-flex flex-column">
														<div class="blog-thumb relative">
															<img src="<?= $img_src ?>" class="img-fluid" style="width:100%; height:250px; object-fit:cover; border-radius:8px 8px 0 0;" alt="blog-img" />
															<div class="top-meta">
																<ul class="top-meta-list">
																<li><div class="post-date"><a href="blog-single.php?id=<?= $b['id'] ?>"><i class="ti-calendar"></i> <?= date('M d, Y', strtotime($b['date'])) ?></a></div></li>
																</ul>
															</div>
														</div>
														<div class="blog-details p-4 flex-grow-1 d-flex flex-column justify-content-between">
															<div>
																<div class="blog-title">
																	<h4 class="margin-bottom-10"><a href="blog-single.php?id=<?= $b['id'] ?>" class="blog-name" style="color:#222; font-weight:700;"><?= htmlspecialchars($b['title']) ?></a></h4>
																</div>
																<div class="post-meta mb-2" style="font-size:13px; color:#888;">
																	<span><i class="ti-user me-1" style="color:#6576ff;"></i> <?= htmlspecialchars($b['name'] ?? 'CACYOF Team') ?></span>
																	<?php if (!empty($b['category'])): ?>
																		<span class="ms-3"><i class="ti-tag me-1" style="color:#6576ff;"></i> <?= htmlspecialchars($b['category']) ?></span>
																	<?php endif; ?>
																</div>
															</div>
															<div class="post-desc mt-3 pt-3 border-top">
																<div class="blog-link">
																	<a href="blog-single.php?id=<?= $b['id'] ?>" class="link font-w-600" style="color:#6576ff;">Read More <i class="ti-arrow-right ms-1"></i></a>
																</div>
															</div>
														</div>
													</div>
													<!--Blog Inner Ends-->
												</div>
											</div>
											<!-- Col -->
    <?php endwhile; ?>
<?php else: ?>
											<div class="col-md-12 text-center py-5">
												<p>No blog posts found. Check back soon!</p>
											</div>
<?php endif; ?>

											<!-- Pagination -->
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
											<!-- Pagination End -->

										</div>
										<!-- row -->
									</div>								
                                    <!-- Col -->
								</div>
								<!-- Row -->
							</div>
							<!-- Blog Main Wrap -->
						</div>
						<!-- Container -->
                    </section>
                    <!-- Blog Section End -->
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