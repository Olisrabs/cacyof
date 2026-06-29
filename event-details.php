<?php
require_once 'db_conn.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: events.php");
    exit();
}

$uploaded_images = !empty($event['images']) ? explode(',', $event['images']) : [];
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
    <title><?= htmlspecialchars($event['title']) ?> | CACYOF BOUESTI</title>
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
                                        <span class="current">Event Details</span>
                                    </div>
                                    <h1 class="page-title mb-0"><?= htmlspecialchars($event['title']) ?></h1>
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
                    <!-- Event Single Section -->
                    <section id="single-event" class="single-event">
                        <div class="container">
                            <div class="single-event-wrap">
                                <!-- Row -->
                                <div class="row">
                                    <!-- Col -->
                                    <div class="col-md-12">
                                        <div class="zoom-gallery mb-5">
                                            <div class="events-thumb relative">
                                                <?php
                                                $main_img = !empty($uploaded_images) && file_exists(__DIR__ . '/src/uploads/events/' . $uploaded_images[0])
                                                    ? 'src/uploads/events/' . htmlspecialchars($uploaded_images[0])
                                                    : 'images/logo-dark.png';
                                                ?>
                                                <a class="popup-img" href="<?= $main_img ?>" title="<?= htmlspecialchars($event['title']) ?>">
                                                    <img src="<?= $main_img ?>" class="img-fluid single-event-img b-radius-10" style="width: 100%; max-height: 500px; object-fit: cover;" alt="events-img" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Col -->
                                </div>
                                <!-- Row -->
                                <!-- Row 2 -->
                                <div class="row">
                                    <!-- Col -->
                                    <div class="col-lg-8">
										<!-- events-title-wrap -->
                                        <div class="events-title-wrap pt-1 margin-bottom-35">
                                            <div class="events-content">
                                                <div class="events-title pad-none margin-none">
                                                    <h3 class="margin-bottom-15">Event <span class="theme-color">Description</span></h3>
                                                </div>
                                                <p style="white-space: pre-line; line-height: 1.8; color: #555; font-size: 16px;"><?= htmlspecialchars($event['description']) ?></p>
                                            </div>
                                        </div>
                                        <!-- events-title-wrap -->
                                        
                                        <?php if (count($uploaded_images) > 1): ?>
										<!-- events-title-wrap -->
                                        <div class="events-title-wrap margin-top-40">
                                            <div class="events-content margin-bottom-35">
                                                <div class="events-title pad-none margin-none">
                                                    <h3 class="margin-bottom-15">Event <span class="theme-color">Gallery</span></h3>
                                                </div>
                                                <p>View images from the event below.</p>
                                            </div>
                                        </div>
                                        <!-- events-title-wrap -->
										<!-- Row -->
                                        <div class="row">
                                             <?php foreach ($uploaded_images as $g_img): ?>
                                             <?php
                                             $g_path = 'src/uploads/events/' . htmlspecialchars($g_img);
                                             if (file_exists(__DIR__ . '/' . $g_path)):
                                             ?>
                                             <div class="col-md-6 mb-4">
                                                <div class="events-thumb relative">
                                                     <img src="<?= $g_path ?>" class="img-fluid b-radius-6" style="width:100%; height:260px; object-fit:cover;" alt="events-img" />
                                                 </div>
                                             </div>
                                             <?php endif; ?>
                                             <?php endforeach; ?>
                                        </div>
                                        <!-- Row -->
                                        <?php endif; ?>
                                        <!-- events-title-wrap -->
										<!-- Get a Quote Section -->
										<section id="get-quote-section" class="get-quote-section pad-none">											
											<div class="div-bg-img b-radius-8 pad-top-50 pad-bottom-45" data-bg="images/bg/bg-1.jpg">
												<!-- Row -->						
												<div class="row get-quote-2">
													<!-- Col -->
													<div class="col-md-3">
														<!-- video wrap -->
														<div class="video-wrap wrap-stretch relative margin-top-15"><!-- video details -->
															<div class="video-wrap-details">
																<!-- video button -->
																<div class="video-play-btn text-center">			
																	<div class="video-icon">
																		<a class="popup-youtube box-shadow1" href="https://www.youtube.com/watch?v=1hLBCOlptq8">
																			<i class="ti-control-play"></i>
																		</a>
																	</div>														
																</div>
															</div>
															<!-- video details End-->
														</div>
														<!-- video wrap end -->
													</div>
													<!-- Col -->
													<!-- Col -->
													<div class="col-md-9 pe-5">
														<div class="title-wrap mb-0">
															<div class="section-title typo-white mb-0">
																<h2 class="title mb-0">“Pray! And listen to God! You can do this alone, but find somebody to do it with you”</h2>
															</div>																					
														</div>
													</div>
													<!-- Col -->								                           
												</div>
												<!-- Row -->
											</div>											
										</section>
										<!-- Get a Quote Section End -->
                                    </div>
                                     <!-- Col -->
                                    <!-- Sidebar -->
                                     <!-- Col -->
                                    <div class="col-lg-4">
                                        <div class="event-details-wrap">
											<div class="event-info pad-bottom-30">
												<h4>Event Details</h4>
												<p class="event-organizer">
													<span class="event-subtitle"><strong>Organizer: </strong></span><?= htmlspecialchars($event['organizers']) ?>
												</p>
												<p class="event-start-date">
													<span class="event-subtitle"><strong>Start Date: </strong></span><?= date('M d, Y', strtotime($event['start_date'])) ?>
												</p>
												<p class="event-end-date">
													<span class="event-subtitle"><strong>End Date: </strong></span><?= !empty($event['end_date']) ? date('M d, Y', strtotime($event['end_date'])) : 'N/A' ?>
												</p>
												<p class="event-time">
													<span class="event-subtitle"><strong>Time: </strong></span><?= !empty($event['start_time']) ? date('h:i A', strtotime($event['start_time'])) : 'N/A' ?>
												</p>
                                                <p class="event-status">
                                                    <span class="event-subtitle"><strong>Status: </strong></span><span class="badge badge-pill badge-outline-dark" style="padding: 5px 12px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($event['status']) ?></span>
                                                </p>
											</div><!-- .event-info -->
											<div class="event-venue pad-bottom-30">
												<h4>Event Venue</h4>
												<p class="event-venue-name">
													<span class="event-subtitle"><strong>Venue: </strong></span><?= htmlspecialchars($event['venue']) ?>
												</p>
												<p class="event-phone"><span class="event-subtitle"><strong>Contact: </strong></span>
													<?= htmlspecialchars($event['contacts']) ?>
												</p>
											</div>
										</div>
                                    </div>
                                     <!-- Sidebar -->
                                </div>
                                <!-- Row 2 -->
                            </div>
                            <!-- Portfolio Single Wrap -->
                        </div>
                    </section>
                    <!-- Portfolios Section End -->
                </div>
            </div>
        </div>
    <!-- .page-wrapper-inner -->
    </div>
    <!--page-wrapper-->

    <!-- Footer -->
    <?php include "footer.php"?>
    
    <!-- jQuery Lib -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/owl.carousel.min.js"></script> 
    <script src="js/jquery.paroller.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/validator.min.js"></script>
    <script src="js/smartresize.min.js"></script>
    <script src="js/jquery.appear.min.js"></script>
    <script src="js/custom.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var eventRegForm = document.getElementById('event-reg-form');
        if (eventRegForm) {
            eventRegForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = document.getElementById('event-submit');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Registering...';

                var formData = new FormData(this);
                fetch('submit-event-registration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data.status === 'success') {
                        eventRegForm.reset();
                        if (typeof showGlobalSuccessModal === 'function') {
                            showGlobalSuccessModal('Registration Successful!', data.message);
                        } else {
                            alert(data.message);
                        }
                    } else {
                        alert(data.message || 'Error registering for event.');
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