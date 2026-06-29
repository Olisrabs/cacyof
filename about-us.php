<?php
require_once 'db_conn.php';

// Ensure executives table exists
$conn->query("CREATE TABLE IF NOT EXISTS `executives` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uin` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `office` VARCHAR(255) NOT NULL,
    `regime` VARCHAR(100) NOT NULL,
    `passport` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)");

// Fetch all distinct regimes for dropdown
$all_regimes_res = $conn->query("SELECT DISTINCT regime FROM executives ORDER BY regime DESC");
$all_regimes = [];
if ($all_regimes_res && $all_regimes_res->num_rows > 0) {
    while ($r = $all_regimes_res->fetch_assoc()) {
        $all_regimes[] = $r['regime'];
    }
}

// Latest regime = first item (sorted DESC)
$latest_regime = $all_regimes[0] ?? '';

// Selected regime from query param or default to latest
$selected_regime = isset($_GET['regime']) && in_array($_GET['regime'], $all_regimes)
    ? $_GET['regime']
    : $latest_regime;

$execs_res = null;
if (!empty($selected_regime)) {
    $stmt_exec = $conn->prepare("SELECT * FROM executives WHERE regime = ? ORDER BY id ASC");
    if ($stmt_exec) {
        $stmt_exec->bind_param("s", $selected_regime);
        $stmt_exec->execute();
        $execs_res = $stmt_exec->get_result();
        $stmt_exec->close();
    }
}

// Helper to resolve passport image src
function about_passport_src($passport_file) {
    if (empty($passport_file)) return 'images/logo.png';
    // Check uploads/passports (from make_executive flow)
    if (file_exists(__DIR__ . '/src/html/uploads/passports/' . $passport_file)) {
        return 'src/html/uploads/passports/' . htmlspecialchars($passport_file);
    }
    // Check images/passports (from add_worker flow)
    if (file_exists(__DIR__ . '/src/html/images/passports/' . $passport_file)) {
        return 'src/html/images/passports/' . htmlspecialchars($passport_file);
    }
    // Legacy paths
    if (file_exists(__DIR__ . '/src/html/uploads/passport/' . $passport_file)) {
        return 'src/html/uploads/passport/' . htmlspecialchars($passport_file);
    }
    return 'images/logo.png';
}
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
    <title>CACYOF BOUESTI - About Us</title>
    <!-- CSS -->
    <!-- Font CSS -->
     
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
                                        <span class="current">About Us</span>
                                    </div>
                                    <h1 class="page-title mb-0">About Us</h1>                                    
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
                    <!-- About Section -->
                    <?php include "about.php"?>
                    <!-- About Section End -->
					<!-- Get a Quote Section -->
                    <?php include "get-a-quote.php"?>
                    <!-- Get a Quote Section End -->
                    					<!-- Team Section -->
					<section id="team-section" class="team-section pad-top-95 pad-bottom-90">
						<div class="container">
							<!-- Row -->
							<div class="row">
								<div class="offset-md-2 col-md-8">
									<div class="title-wrap text-center">
										<div class="section-title">
											<span class="sub-title theme-color text-uppercase">Our Team</span>
											<h2 class="section-title margin-top-5">Our Executives <?= !empty($selected_regime) ? '(' . htmlspecialchars($selected_regime) . ')' : '' ?></h2>
											<span class="border-bottom center"></span>
										</div>
									</div>
								</div>
							</div>
							<?php if (count($all_regimes) > 1): ?>
							<!-- Past Executives Regime Dropdown -->
							<div class="row justify-content-center mb-4">
								<div class="col-md-6 col-lg-4 text-center">
									<form method="GET" action="" id="regime-filter-form">
										<div class="input-group" style="border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
											<label class="input-group-text" style="background:#6576ff; color:#fff; border:none; font-weight:600; font-size:14px;">
												<i class="ti-calendar" style="margin-right:6px;"></i> Regime
											</label>
											<select name="regime" id="regimeDropdown" class="form-control form-select" onchange="document.getElementById('regime-filter-form').submit()" style="border:2px solid #6576ff; font-weight:600; color:#333;">
												<?php foreach ($all_regimes as $reg): ?>
													<option value="<?= htmlspecialchars($reg) ?>" <?= $selected_regime === $reg ? 'selected' : '' ?>>
														<?= htmlspecialchars($reg) ?> <?= ($reg === $latest_regime) ? '(Current)' : '(Past)' ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
									</form>
								</div>
							</div>
							<?php endif; ?>
							<!--Team Main Slider-->
							<div class="owl-carousel team-main-wrapper" data-loop="1" data-nav="0" data-dots="1" data-autoplay="1" data-autoplaypause="1" data-autoplaytime="5000" data-smartspeed="1000" data-margin="30" data-items="3" data-items-tab="2" data-items-mob="1">
								<?php if ($execs_res && $execs_res->num_rows > 0): ?>
									<?php while ($exec = $execs_res->fetch_assoc()):
										$passport_src = about_passport_src($exec['passport'] ?? '');
										$exec_name    = htmlspecialchars($exec['name']);
										$name_len     = strlen($exec['name']);
										$name_fs      = $name_len > 28 ? '13px' : ($name_len > 20 ? '16px' : '19px');
									?>
									<div class="item">
										<div class="team-style-1">
											<div class="team-inner margin-bottom-20 box-shadow1 b-radius-8" style="background:#fff; overflow:hidden; display:flex; flex-direction:column;">
												<div class="team-thumb mb-0 relative" style="flex:0 0 auto;">
													<div class="team-overlay"></div>
													<img src="<?= $passport_src ?>" class="img-fluid thumb w-100" style="height:300px; object-fit:cover; object-position:top;" alt="team-img" />
												</div>
												<div class="team-details text-center pad-20" style="flex:0 0 80px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:80px; max-height:80px; overflow:hidden;">
													<div class="team-name" style="width:100%;">
														<h3 class="mb-1" style="font-size:<?= $name_fs ?>; font-weight:700; color:#222; line-height:1.2; word-break:break-word;"><?= $exec_name ?></h3>
													</div>
													<div class="team-designation">
														<p class="mb-0 text-soft fw-600" style="color:#6576ff; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;"><?= htmlspecialchars($exec['office']) ?></p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php endwhile; ?>

								<?php else: ?>
									<div class="item">
										<div class="team-style-1">
											<div class="team-inner margin-bottom-20 box-shadow1 b-radius-8" style="background:#fff; overflow:hidden;">
												<div class="team-thumb mb-0 relative">
													<img src="images/new logo.png" class="img-fluid thumb w-100" style="height:380px; object-fit:cover;" alt="team-img" />
												</div>
												<div class="team-details text-center pad-20">
													<div class="team-name">
														<h3 class="mb-1" style="font-size:20px; font-weight:700; color:#222;">No Executives Found</h3>
													</div>
													<div class="team-designation"><p class="mb-0 text-soft fw-600" style="color:#6576ff; font-size:15px;">—</p></div>
												</div>
											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<!--Team Owl Slider-->
						</div>
						<!-- Container -->
					</section>
					<!-- Team Section End -->				
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