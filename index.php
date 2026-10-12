<?php
require_once 'db_conn.php';

$default_img = 'images/logo.png'; // universal fallback image

// Fetch latest upcoming/ongoing events for homepage carousel (show all statuses)
$events_result = $conn->query("SELECT * FROM events ORDER BY start_date DESC LIMIT 9");
$homepage_events = [];
if ($events_result) { while ($row = $events_result->fetch_assoc()) { $homepage_events[] = $row; } }

// Fetch the nearest upcoming event for countdown
$now = time();
$nearest_event = null;
$current_date_str = date('Y-m-d');
$stmt_nearest = $conn->prepare("SELECT * FROM events WHERE start_date >= ? ORDER BY start_date ASC, start_time ASC");
if ($stmt_nearest) {
    $stmt_nearest->bind_param("s", $current_date_str);
    $stmt_nearest->execute();
    $nearest_res = $stmt_nearest->get_result();
    while ($row = $nearest_res->fetch_assoc()) {
        $start_time_str = !empty($row['start_time']) ? $row['start_time'] : '00:00:00';
        $start_dt = strtotime($row['start_date'] . ' ' . $start_time_str);
        if ($start_dt > $now) {
            $nearest_event = $row;
            break;
        }
    }
    $stmt_nearest->close();
}

$countdown_date = '';
$countdown_heading = '';
if ($nearest_event) {
    $start_time_str = !empty($nearest_event['start_time']) ? $nearest_event['start_time'] : '00:00:00';
    $countdown_date = date('Y/m/d H:i:s', strtotime($nearest_event['start_date'] . ' ' . $start_time_str));
    $event_title = htmlspecialchars($nearest_event['title']);
    $start_dt = strtotime($nearest_event['start_date'] . ' ' . $start_time_str);
    $formatted_date = date('M d, Y - l h:i a', $start_dt);
    if (!empty($nearest_event['end_date'])) {
        $end_time_str = !empty($nearest_event['end_time']) ? $nearest_event['end_time'] : '00:00:00';
        $end_dt = strtotime($nearest_event['end_date'] . ' ' . $end_time_str);
        $formatted_date .= ' to ' . date('h:i a', $end_dt);
    }
    $countdown_heading = $event_title . ' | ' . $formatted_date . ' | Venue: ' . htmlspecialchars($nearest_event['venue']);
} else {
    $countdown_heading = 'No upcoming events scheduled at the moment.';
    $countdown_date = '1970/01/01 00:00:00'; // past date to trigger all 0s in jquery.countdown
}

// Fallback event images
$event_img_fallbacks = [
    'images/events/grid1/event-1.jpg',
    'images/events/grid1/event-2.jpg',
    'images/events/grid1/event-3.jpg',
    'images/events/grid1/event-4.jpg',
    'images/events/grid1/event-5.jpg',
    'images/events/grid1/event-6.jpg',
];

// Fetch latest 4 blogs for homepage
$blogs_result = $conn->query("SELECT * FROM blogs ORDER BY date DESC LIMIT 4");
$homepage_blogs = [];
if ($blogs_result) { while ($row = $blogs_result->fetch_assoc()) { $homepage_blogs[] = $row; } }
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
	<title>CACYOF BOUESTI - Home</title>
	<!-- CSS -->
	<!-- Bootstrap -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Font Awesome 6 (latest icons) -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<!-- Simple Line Icons -->
	<link rel="stylesheet" href="css/simple-line-icons.min.css">
	<!-- Themify Icons -->
	<link rel="stylesheet" href="css/themify-icons.css">
	<!-- Owl Slider -->
	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	<!-- Magnific Popup -->
	<link rel="stylesheet" href="css/magnific-popup.css">
	<!-- Revolution Slider -->
	<link rel="stylesheet" type="text/css" href="rs-plugin/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
	<!-- REVOLUTION STYLE SHEETS -->
	<link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css">
	<link rel="stylesheet" type="text/css" href="rs-plugin/css/home-2/rs6.css">
	<!-- Color Panel -->
	<link rel="stylesheet" href="css/color-schemes/red.css" id="changeable-colors">
	<!-- Main Style -->
	<link rel="stylesheet" href="css/style.css" class="main-style">
	<style>
		#rev_slider_6_1_wrapper .tp-loader.spinner1 {
			background-color: #FFFFFF !important;
		}
		/* Custom overlay to fade the background of the hero slides */
		#rev_slider_2_1 rs-sbg-wrap::after,
		#rev_slider_2_1 rs-sbg::after,
		#rev_slider_2_1 .slotholder::after,
		#rev_slider_2_1 .tp-bgimg::after {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.72) !important;
			z-index: 2 !important;
			pointer-events: none;
		}
	</style>
	<style>
		.rs-layer.Concept-Content a,
		.rs-layer.Concept-Content a:visited {
			color: #fff !important;
			border-bottom: 1px solid #fff !important;
			font-weight: 700 !important
		}

		.rs-layer.Concept-Content a:hover {
			border-bottom: 1px solid transparent !important
		}

		.rs-layer.Concept-Content-Dark a,
		.rs-layer.Concept-Content-Dark a:visited {
			color: #000 !important;
			border-bottom: 1px solid #000 !important;
			font-weight: 700 !important
		}

		.rs-layer.Concept-Content-Dark a:hover {
			border-bottom: 1px solid transparent !important
		}

		@media only screen and (max-width:575px) {
			rs-layer.res-slide-btn {
				padding: 7px 16px !important;
				font-size: 13px !important
			}
		}

		#rev_slider_2_1_wrapper .uranus.tparrows {
			width: 50px;
			height: 50px;
			background: rgba(255, 255, 255, 0)
		}

		#rev_slider_2_1_wrapper .uranus.tparrows:before {
			width: 50px;
			height: 50px;
			line-height: 50px;
			font-size: 40px;
			transition: all 0.3s;
			-webkit-transition: all 0.3s
		}

		#rev_slider_2_1_wrapper .uranus.tparrows.rs-touchhover:before {
			opacity: 0.75
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
				<a href="index.php" class="img-before"><img src="images/logo-dark.png"
						class="img-fluid changeable-dark" width="170" height="51" alt="Logo"></a>
			</div>
			<div class="zmm-main-nav">
			</div>
		</div>
	</div>
	<!-- Main wrapper-->
	<div class="page-wrapper">
		<div class="page-wrapper-inner">
			<header class="header-floating">
				<!--Mobile Header-->
				<div class="mobile-header bg-white typo-dark">
					<div class="mobile-header-inner">
						<div class="sticky-outer">
							<div class="sticky-head">
								<div class="basic-container clearfix">
									<ul class="nav mobile-header-items pull-left">
										<li class="nav-item"><a href="#" class="zmm-toggle img-before"><i
													class="ti-menu"></i></a></li>
									</ul>
									<ul class="nav mobile-header-items pull-center">
										<li>
											<a href="index.php" class="img-before"><img src="images/logo-dark.png"
													class="img-fluid changeable-dark" width="149" height="45"
													alt="Logo"></a>
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
				<div class="header-inner header-2 header-absolute">
					<!--Topbar-->
					<div class="topbar">
						<div class="basic-container clearfix">
							<ul class="nav topbar-items pull-left">
								<li class="nav-item">
									<ul class="nav header-info">
										<div class="header-address typo-white"><span class="ti-location-pin"></span> P.M.B 280, IKERE-EKITI.</div>
									</ul>
								</li>
							</ul>
							<ul class="nav topbar-items pull-right">
								<li class="nav-item">
									<div class="social-icons typo-white">
										<a href="https://facebook.com/cacyofbouesti" class="social-fb"><i class="fa-brands fa-facebook-f"></i></a>
										<a href="mailto:cacyofikere1@gmail.com" class="social-twitter"><i class="fa-solid fa-envelope"></i></a>
										<a href="https://instagram.com/cacyofbouesti" class="social-instagram"><i class="fa-brands fa-instagram"></i></a>
										<a href="https://youtube.com/cacyofikerechapter" class="social-youtube"><i class="fa-brands fa-youtube"></i></a>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<!--Topbar-->
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
			<!-- START CACYOF BOUESTI Home 2 REVOLUTION SLIDER 6.6.5 -->
			<p class="rs-p-wp-fix"></p>
			<rs-module-wrap id="rev_slider_2_1_wrapper" data-alias="CACYOF BOUESTI-Home-2" data-source="gallery"
				style="visibility:hidden;background:#000000;padding:0;margin:0px auto;margin-top:0;margin-bottom:0;">
				<rs-module id="rev_slider_2_1" style="" data-version="6.6.5">
					<rs-slides style="overflow: hidden; position: absolute;">
						<rs-slide style="position: absolute;" data-key="rs-7" data-title="Web Show"
							data-thumb="images/DSC_0033.webp" data-anim="adpr:false;f:random;"
							data-in="o:0;r:ran(-45|45);sx:0;sy:0;row:5;col:5;" data-out="a:false;">
							<img src="rs-plugin/assets/dummy.png" alt="Non Profit Wordpress Theme - Slider"
								title="Slider 2" width="1920" height="1080" class="rev-slidebg tp-rs-img rs-lazyload"
								data-lazyload="images/DSC_0033.webp" data-parallax="5" data-no-retina>
							<!--
							-->
							<h1 id="slider-2-slide-7-layer-2" class="rs-layer Concept-Title" data-type="text"
								data-color="#ffffff||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)"
								data-rsp_ch="on" data-xy="x:c;y:m;yo:7px,0,-10px,-54px;"
								data-text="w:normal,nowrap,nowrap,normal;s:75,50,45,28;l:82,55,50,30;fw:800,700,700,700;a:center;"
								data-dim="w:1098px,845px,736px,478px;h:92px,auto,auto,35px;" data-padding="b:10;"
								data-frame_0="o:1;" data-frame_0_chars="d:5;x:105%;o:1;rY:45deg;rZ:90deg;"
								data-frame_0_mask="u:t;" data-frame_1="st:2110;sp:1200;sR:2110;"
								data-frame_1_chars="e:power4.inOut;d:10;rZ:0deg;" data-frame_1_mask="u:t;"
								data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:4190;"
								data-frame_999_reverse="x:true;" style="z-index:10;font-family:'Open Sans';">
								<!-- God Gives Us Power -->
							</h1><!--

							--><rs-layer id="slider-2-slide-7-layer-4" class="Concept-SubTitle" data-type="text" data-color=""
								data-rsp_ch="on" data-xy="x:c;xo:0,0,0,-1px;y:m;yo:-52px,-47px,-55px,-87px;"
								data-text="w:normal,nowrap,nowrap,nowrap;s:23,20,20,15;l:21,25,20,20;ls:1px,0px,0px,0px;fw:700;a:center,center,left,left;"
								data-dim="w:612px,424px,auto,auto;" data-padding="b:10;" data-frame_0="sX:2;sY:2;"
								data-frame_0_mask="u:t;" data-frame_1="e:power2.out;st:640;sp:1000;sR:640;"
								data-frame_1_mask="u:t;" data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:7360;"
								data-frame_999_reverse="x:true;" style="z-index:11;font-family:'Poppins';">We're
								Building Them A Home
							</rs-layer><!--

							--><rs-layer id="slider-2-slide-7-layer-14" data-type="text" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,7px;y:m,t,t,t;yo:91px,312px,271px,212px;"
								data-text="w:normal;s:18,18,16,15;l:31,30,30,27;a:center;"
								data-dim="w:806px,805px,689px,388px;h:auto,auto,auto,89px;" data-frame_0="y:100%;"
								data-frame_0_mask="u:t;" data-frame_1="st:2680;sp:1360;sR:2680;"
								data-frame_1_mask="u:t;" data-frame_999="o:0;st:w;sR:4960;"
								style="z-index:9;font-family:'Open Sans';">
								<!-- Jesus is holy, loving, and worthy of all our
								worship and devotion. You will feel the heaven in our CACYOF BOUESTI Church. Join with us and
								Praise the Lord Jesus. -->
							</rs-layer><!--

							--><a id="slider-2-slide-7-layer-19" class="rs-layer rev-btn" href="donate-now.php" target="_self"
								data-type="button" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,5px;yo:526px,404px,361px,317px;"
								data-text="w:normal;s:18,14,14,12;l:50,41,36,32;fw:700;"
								data-dim="minh:0px,none,none,none;"
								data-padding="t:0,0,10,10;r:40,40,25,25;b:0,0,10,10;l:40,40,25,25;"
								data-border="bor:6px,6px,6px,6px;" data-frame_0="y:100%;"
								data-frame_1="e:power4.inOut;st:3530;sp:1200;" data-frame_999="o:0;st:w;sR:3720;"
								data-frame_hover="bgc:#000;bor:6px,6px,6px,6px;sp:100;e:power1.inOut;bri:120%;"
								style="z-index:12;font-family:'Poppins';">Donate Now
							</a><!---->
						</rs-slide>
						<rs-slide style="position: absolute;" data-key="rs-8" data-title="Web Show"
							data-thumb="rs-plugin/assets/zmain-slider-3-1536x864.jpg" data-anim="adpr:false;f:random;"
							data-in="o:0;r:ran(-45|45);sx:0;sy:0;row:5;col:5;" data-out="a:false;">
							<img src="rs-plugin/assets/dummy.png" alt="Non Profit Wordpress Theme - Slider"
								title="Slider 3" width="1920" height="1080" class="rev-slidebg tp-rs-img rs-lazyload"
								data-lazyload="rs-plugin/assets/zmain-slider-3-1536x864.jpg" data-parallax="5" data-no-retina>
							<!--
							-->
							<h1 id="slider-2-slide-8-layer-2" class="rs-layer Concept-Title" data-type="text"
								data-color="#ffffff||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)"
								data-rsp_ch="on" data-xy="x:c;y:m;yo:7px,0,-10px,-56px;"
								data-text="w:normal,nowrap,nowrap,normal;s:75,50,45,28;l:82,55,50,30;fw:800,700,700,700;a:center;"
								data-dim="w:1098px,845px,736px,478px;h:92px,auto,auto,35px;" data-padding="b:10;"
								data-frame_0="o:1;" data-frame_0_chars="d:5;x:105%;o:1;rY:45deg;rZ:90deg;"
								data-frame_0_mask="u:t;" data-frame_1="st:2110;sp:1200;sR:2110;"
								data-frame_1_chars="e:power4.inOut;d:10;rZ:0deg;" data-frame_1_mask="u:t;"
								data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:3890;"
								data-frame_999_reverse="x:true;" style="z-index:10;font-family:'Poppins';">United we stand
							</h1><!----><rs-layer id="slider-2-slide-8-layer-4" class="Concept-SubTitle" data-type="text" data-color=""
								data-rsp_ch="on" data-xy="x:c;xo:0,0,0,-1px;y:m;yo:-52px,-47px,-55px,-89px;"
								data-text="w:normal,nowrap,nowrap,nowrap;s:23,20,20,15;l:21,25,20,20;ls:1px,0px,0px,0px;fw:700;a:center,center,left,left;"
								data-dim="w:612px,424px,auto,auto;" data-padding="b:10;" data-frame_0="sX:2;sY:2;"
								data-frame_0_mask="u:t;" data-frame_1="e:power2.out;st:640;sp:1000;sR:640;"
								data-frame_1_mask="u:t;" data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:7360;"
								data-frame_999_reverse="x:true;" style="z-index:11;font-family:'Poppins';">Let's Bring
								Them Home
							</rs-layer><!--

							--><rs-layer id="slider-2-slide-8-layer-14" data-type="text" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,7px;y:m,t,t,t;yo:91px,312px,271px,210px;"
								data-text="w:normal;s:18,18,16,15;l:31,30,30,27;a:center;"
								data-dim="w:806px,805px,689px,388px;h:auto,auto,auto,89px;" data-frame_0="y:100%;"
								data-frame_0_mask="u:t;" data-frame_1="st:2680;sp:1360;sR:2680;"
								data-frame_1_mask="u:t;" data-frame_999="o:0;st:w;sR:4960;"
								style="z-index:9;font-family:'Open Sans';">CACYOF BOUESTI a spirit-led student church that shows and preaches the power and the sustainability of the Holy Spirit. Leading young and old into the marvelous light of Jesus.
							</rs-layer><!--

							--><a id="slider-2-slide-8-layer-19" class="rs-layer rev-btn" href="events.php" target="_self"
								data-type="button" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,5px;yo:526px,404px,361px,315px;"
								data-text="w:normal;s:18,14,14,12;l:50,41,36,32;fw:700;"
								data-dim="minh:0px,none,none,none;"
								data-padding="t:0,0,10,10;r:40,40,25,25;b:0,0,10,10;l:40,40,25,25;"
								data-border="bor:6px,6px,6px,6px;" data-frame_0="y:100%;"
								data-frame_1="e:power4.inOut;st:3490;sp:1200;" data-frame_999="o:0;st:w;sR:3760;"
								data-frame_hover="bgc:#000;bor:6px,6px,6px,6px;sp:100;e:power1.inOut;bri:120%;"
								style="z-index:12;font-family:'Poppins';">View Events
							</a><!---->
						</rs-slide>
						<rs-slide style="position: absolute;" data-key="rs-5" data-title="Web Show"
							data-thumb="rs-plugin/assets/zmain-slider-1-1536x864.jpg" data-anim="adpr:false;f:random;"
							data-in="o:0;r:ran(-45|45);sx:0;sy:0;row:5;col:5;" data-out="a:false;">
							<img src="rs-plugin/assets/dummy.png" alt="Non Profit Wordpress Theme - Slider"
								title="Slider 1" width="1920" height="1080" class="rev-slidebg tp-rs-img rs-lazyload"
								data-lazyload="rs-plugin/assets/zmain-slider-1-1536x864.jpg" data-parallax="5" data-no-retina>
							<!--
							-->
							<h1 id="slider-2-slide-5-layer-2" class="rs-layer Concept-Title" data-type="text"
								data-color="#ffffff||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)||rgba(255, 255, 255, 1)"
								data-rsp_ch="on" data-xy="x:c;y:m;yo:7px,0,-10px,-48px;"
								data-text="w:normal,nowrap,nowrap,normal;s:75,50,45,28;l:82,55,50,30;fw:800,700,700,700;a:center;"
								data-dim="w:1098px,845px,736px,478px;h:92px,auto,auto,35px;" data-padding="b:10;"
								data-frame_0="o:1;" data-frame_0_chars="d:5;x:105%;o:1;rY:45deg;rZ:90deg;"
								data-frame_0_mask="u:t;" data-frame_1="st:2110;sp:1200;sR:2110;"
								data-frame_1_chars="e:power4.inOut;d:10;rZ:0deg;" data-frame_1_mask="u:t;"
								data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:3790;"
								data-frame_999_reverse="x:true;" style="z-index:10;font-family:'Open Sans';">DESTINED FAMILY
							</h1><!--

							--><rs-layer id="slider-2-slide-5-layer-4" class="Concept-SubTitle" data-type="text" data-color=""
								data-rsp_ch="on" data-xy="x:c;xo:0,0,0,-1px;y:m;yo:-55px,-47px,-55px,-81px;"
								data-text="w:normal,nowrap,nowrap,nowrap;s:23,20,20,15;l:21,25,20,20;ls:1px,0px,0px,0px;fw:700;a:center,center,left,left;"
								data-dim="w:612px,424px,auto,auto;" data-padding="b:10;" data-frame_0="sX:2;sY:2;"
								data-frame_0_mask="u:t;" data-frame_1="e:power2.out;st:640;sp:1000;sR:640;"
								data-frame_1_mask="u:t;" data-frame_999="x:left;e:power3.in;st:w;sp:1000;sR:7360;"
								data-frame_999_reverse="x:true;" style="z-index:11;font-family:'Poppins';">Welcome Home Where You Belong
							</rs-layer><!--

							--><rs-layer id="slider-2-slide-5-layer-14" data-type="text" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,7px;y:m,t,t,t;yo:91px,312px,271px,218px;"
								data-text="w:normal;s:18,18,16,15;l:31,30,30,27;a:center;"
								data-dim="w:806px,805px,689px,388px;h:auto,auto,auto,89px;" data-frame_0="y:100%;"
								data-frame_0_mask="u:t;" data-frame_1="st:2680;sp:1360;sR:2680;"
								data-frame_1_mask="u:t;" data-frame_999="o:0;st:w;sR:4960;"
								style="z-index:9;font-family:'Open Sans';">Jesus is holy, loving, and worthy of all our
								worship and devotion. You will feel heaven in our midst @CACYOF BOUESTI Church. Join with us and
								Praise the Lord Jesus.
							</rs-layer><!--

							--><a id="slider-2-slide-5-layer-19" class="rs-layer rev-btn" href="sermons.php" target="_self"
								data-type="button" data-rsp_ch="on"
								data-xy="x:c;xo:0,0,0,5px;yo:526px,404px,361px,323px;"
								data-text="w:normal;s:18,14,14,12;l:50,41,36,32;fw:700;"
								data-dim="minh:0px,none,none,none;"
								data-padding="t:0,0,10,10;r:40,40,25,25;b:0,0,10,10;l:40,40,25,25;"
								data-border="bor:6px,6px,6px,6px;" data-frame_0="y:100%;"
								data-frame_1="e:power4.inOut;st:3790;sp:1200;" data-frame_999="o:0;st:w;sR:3460;"
								data-frame_hover="bgc:#000;bor:6px,6px,6px,6px;sp:100;e:power1.inOut;bri:120%;"
								style="z-index:12;font-family:'Poppins';">View Sermons
							</a><!---->
						</rs-slide>
					</rs-slides>
					<rs-static-layers><!--
					--></rs-static-layers>
				</rs-module>
				<script>

				</script>
				<script>
					if (typeof revslider_showDoubleJqueryError === "undefined") { function revslider_showDoubleJqueryError(sliderID) { console.log("You have some jquery.js library include that comes after the Slider Revolution files js inclusion."); console.log("To fix this, you can:"); console.log("1. Set 'Module General Options' -> 'Advanced' -> 'jQuery & OutPut Filters' -> 'Put JS to Body' to on"); console.log("2. Find the double jQuery.js inclusion and remove it"); return "Double Included jQuery Library"; } }
				</script>
			</rs-module-wrap>
			<!-- END REVOLUTION SLIDER -->
			<!-- Page Content -->
			<div class="content-wrapper pad-none">
				<div class="content-inner">
					<!-- Sermon Section -->
					<section id="section-sermon" class="sermon-section pad-top-95 pad-bottom-md-none">
						<div class="container">
							<!-- Sermon Main Wrap -->
							<div class="sermon-main-wrap sermon-grid sermon-grid-2">

								<!-- Row -->
								<div class="row">
									<!-- Col -->
									<div class="col-lg-12">
										<!-- Row -->
										<div class="row">
											<div class="offset-md-2 col-md-8">
												<div class="title-wrap text-center">
													<div class="section-title">
														<span
															class="sub-title theme-color text-uppercase">Features</span>
														<h2 class="section-title margin-top-5">Our Features</h2>
														<span class="border-bottom center"></span>
													</div>
												</div>
											</div>
											<!-- Col -->
											<div class="col-lg-6 mb-4 mb-lg-0">
												<div class="f-box-style-2 text-center">
													<div class="f-box-inner div-bg-img b-radius-20"
														data-bg="images/bg/ft-bg1.jpg">
														<div class="feature-icon margin-bottom-15"> <i
																class="ti-announcement theme-color"></i> </div>
														<div class="feature-content mb-3">
															<div class="feature-title">
																<h5 class="margin-bottom-15">Our Sermons</h5>
															</div>
															<p class="mb-0">We preach insipirational sermons on a wide
																range of
																themes and topics as Holy Spirit directs.</p>
														</div>
														<div class="feature-box-btn"> <a href="sermons.php"
																class="btn btn-link">View All</a> </div>
													</div>
												</div>
											</div>
											<!-- Col -->
											<!-- Col -->
											<div class="col-lg-6">
												<div class="f-box-style-2 text-center">
													<div class="f-box-inner div-bg-img b-radius-20"
														data-bg="images/bg/ft-bg3.jpg">
														<div class="feature-icon margin-bottom-15"> <i
																class="ti-gift theme-color"></i> </div>
														<div class="feature-content mb-3">
															<div class="feature-title">
																<h5 class="margin-bottom-15">Our Events</h5>
															</div>
															<p class="mb-0">Be a part of our spirit led events and help us to change the earth into a wonderful place to live for everyone.</p>
														</div>
														<div class="feature-box-btn"> <a href="events.php"
																class="btn btn-link">View All</a> </div>
													</div>
												</div>
											</div>
											<!-- Col -->
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
					<!-- About Section -->
					<?php include "about.php"?>
					<!-- About Section End -->
					<!-- Get a Quote Section -->
					<?php include "get-a-quote.php"?>
					<!-- Get a Quote Section End -->
					<!-- Events Section -->
					<section class="events-section pad-top-95">
						<div class="container">
							<!-- Row -->
							<div class="row">
								<div class="offset-md-2 col-md-8">
									<div class="title-wrap text-center">
										<div class="section-title">
											<span class="sub-title theme-color text-uppercase">Events</span>
											<h2 class="section-title margin-top-5">Upcoming Events</h2>
											<span class="border-bottom center"></span>
										</div>
									</div>
								</div>
								<!--Events Main Slider-->
								<div class="owl-carousel events-main-wrapper events-style-2" data-loop="1" data-nav="0"
									data-dots="1" data-autoplay="0" data-autoplaypause="1" data-autoplaytime="5000"
									data-smartspeed="1000" data-margin="30" data-items="3" data-items-tab="1"
									data-items-mob="1">
									<?php
									if (!empty($homepage_events)):
										foreach ($homepage_events as $ev):
											$first_img = !empty($ev['images']) ? trim(explode(',', $ev['images'])[0]) : '';
											$ev_img = ($first_img && file_exists(__DIR__ . '/src/uploads/events/' . $first_img))
												? 'src/uploads/events/' . htmlspecialchars($first_img)
												: $default_img;
									?>
									<!--Item-->
									<div class="item">
										<!--events Inner-->
										<div class="events-inner">
											<!--events Thumb-->
											<div class="events-thumb mb-0 relative">
												<img src="<?= $ev_img ?>" class="img-fluid thumb w-100" style="height: 260px; width: 100%; object-fit: cover;" alt="events-img" />
											</div>
											<!--events details-->
											<div class="events-details pad-lr-30 pad-bottom-35">
												<div class="event-date margin-bottom-30"><?= date('M d', strtotime($ev['start_date'])) ?><span class="event-time"><?= htmlspecialchars($ev['time'] ?? '') ?></span>
												</div>
												<div class="event-title mb-3">
													<h5><a href="event-details.php?id=<?= $ev['id'] ?>"><?= htmlspecialchars($ev['title']) ?></a></h5>
												</div>
												<div class="read-more">
													<a href="event-details.php?id=<?= $ev['id'] ?>">Event Details</a>
												</div>
											</div>
											<!--events details-->
										</div>
										<!--events Inner Ends-->
									</div>
									<!--Item Ends-->
									<?php
										endforeach;
									else:
									?>
									<div class="item">
										<div class="events-inner">
											<div class="events-details pad-lr-30 pad-bottom-35">
												<div class="event-title mb-3"><h5>No events found yet. Check back soon!</h5></div>
											</div>
										</div>
									</div>
									<?php endif; ?>
								</div>
								<!--Events Owl Slider-->
							</div>
							<!-- Row -->
						</div>
						<!-- Container -->
					</section>
					<!-- Events Section End -->
					<!-- Day Counter Section -->
					<section class="day-counter-section typo-white section-bg-img o-visible pad-top-150 pad-bottom-180"
						data-bg="images/bg/bg-4.jpg">
						<span class="theme-overlay"></span>
						<div class="shape-top" data-negative="false">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 27.8"
								preserveAspectRatio="xMidYMax slice">
								<path class="shape-fill"
									d="M0 0v6.7c1.9-.8 4.7-1.4 8.5-1 9.5 1.1 11.1 6 11.1 6s2.1-.7 4.3-.2c2.1.5 2.8 2.6 2.8 2.6s.2-.5 1.4-.7c1.2-.2 1.7.2 1.7.2s0-2.1 1.9-2.8c1.9-.7 3.6.7 3.6.7s.7-2.9 3.1-4.1 4.7 0 4.7 0 1.2-.5 2.4 0 1.7 1.4 1.7 1.4h1.4c.7 0 1.2.7 1.2.7s.8-1.8 4-2.2c3.5-.4 5.3 2.4 6.2 4.4.4-.4 1-.7 1.8-.9 2.8-.7 4 .7 4 .7s1.7-5 11.1-6c9.5-1.1 12.3 3.9 12.3 3.9s1.2-4.8 5.7-5.7c4.5-.9 6.8 1.8 6.8 1.8s.6-.6 1.5-.9c.9-.2 1.9-.2 1.9-.2s5.2-6.4 12.6-3.3c7.3 3.1 4.7 9 4.7 9s1.9-.9 4 0 2.8 2.4 2.8 2.4 1.9-1.2 4.5-1.2 4.3 1.2 4.3 1.2.2-1 1.4-1.7 2.1-.7 2.1-.7-.5-3.1 2.1-5.5 5.7-1.4 5.7-1.4 1.5-2.3 4.2-1.1c2.7 1.2 1.7 5.2 1.7 5.2s.3-.1 1.3.5c.5.4.8.8.9 1.1.5-1.4 2.4-5.8 8.4-4 7.1 2.1 3.5 8.9 3.5 8.9s.8-.4 2 0 1.1 1.1 1.1 1.1 1.1-1.1 2.3-1.1 2.1.5 2.1.5 1.9-3.6 6.2-1.2 1.9 6.4 1.9 6.4 2.6-2.4 7.4 0c3.4 1.7 3.9 4.9 3.9 4.9s3.3-6.9 10.4-7.9 11.5 2.6 11.5 2.6.8 0 1.2.2c.4.2.9.9.9.9s4.4-3.1 8.3.2c1.9 1.7 1.5 5 1.5 5s.3-1.1 1.6-1.4c1.3-.3 2.3.2 2.3.2s-.1-1.2.5-1.9 1.9-.9 1.9-.9-4.7-9.3 4.4-13.4c5.6-2.5 9.2.9 9.2.9s5-6.2 15.9-6.2 16.1 8.1 16.1 8.1.7-.2 1.6-.4V0H0z">
								</path>
							</svg>
						</div>
						<div class="container">
							<div class="row">
								<div class="offset-md-2 col-md-8">
									<div class="title-wrap text-center mb-5">
										<div class="section-title">
											<span class="sub-title theme-color text-uppercase">Events</span>
											<h2 class="section-title margin-top-5">Nearest Event Countdown</h2>
											<span class="border-bottom center"></span>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<!-- Day Counter -->
									<div
										class="day-counter-wrapper pt-3 day-counter-1 day-counter-transparent text-center">
										<div class="day-counter day-counter-progress" data-date="<?php echo $countdown_date; ?>"
											data-size="140">
											<div class="counter-elements counter-day rounded">
												<div class="counter-item">
													<div class="day-count-details">
														<h3 class="count-view theme-color mt-2 mb-2">Number</h3>
														<span>Days</span>
													</div>
												</div>
											</div>
											<!-- .counter-day -->
											<div class="counter-elements counter-hour rounded">
												<div class="counter-item">
													<div class="day-count-details">
														<h3 class="count-view theme-color mt-2 mb-2">Number</h3>
														<span>Hours</span>
													</div>
												</div>
											</div>
											<!-- .counter-hour -->
											<div class="counter-elements counter-min rounded">
												<div class="counter-item">
													<div class="day-count-details">
														<h3 class="count-view theme-color mt-2 mb-2">Number</h3>
														<span>Minutes</span>
													</div>
												</div>
											</div>
											<!-- .counter-min -->
											<div class="counter-elements counter-sec rounded">
												<div class="counter-item">
													<div class="day-count-details">
														<h3 class="count-view theme-color mt-2 mb-2">Number</h3>
														<span>Seconds</span>
													</div>
												</div>
											</div>
											<!-- .counter-sec -->
										</div>
										<!-- .day-counter -->
										<span class="custom-heading-title relative"><?php echo $countdown_heading; ?></span>
									</div>
									<!-- .day-counter-wrapper -->
								</div>
							</div>
						</div>
						<div class="shape-bottom" data-negative="false">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 27.8"
								preserveAspectRatio="xMidYMax slice">
								<path class="shape-fill"
									d="M0 0v6.7c1.9-.8 4.7-1.4 8.5-1 9.5 1.1 11.1 6 11.1 6s2.1-.7 4.3-.2c2.1.5 2.8 2.6 2.8 2.6s.2-.5 1.4-.7c1.2-.2 1.7.2 1.7.2s0-2.1 1.9-2.8c1.9-.7 3.6.7 3.6.7s.7-2.9 3.1-4.1 4.7 0 4.7 0 1.2-.5 2.4 0 1.7 1.4 1.7 1.4h1.4c.7 0 1.2.7 1.2.7s.8-1.8 4-2.2c3.5-.4 5.3 2.4 6.2 4.4.4-.4 1-.7 1.8-.9 2.8-.7 4 .7 4 .7s1.7-5 11.1-6c9.5-1.1 12.3 3.9 12.3 3.9s1.2-4.8 5.7-5.7c4.5-.9 6.8 1.8 6.8 1.8s.6-.6 1.5-.9c.9-.2 1.9-.2 1.9-.2s5.2-6.4 12.6-3.3c7.3 3.1 4.7 9 4.7 9s1.9-.9 4 0 2.8 2.4 2.8 2.4 1.9-1.2 4.5-1.2 4.3 1.2 4.3 1.2.2-1 1.4-1.7 2.1-.7 2.1-.7-.5-3.1 2.1-5.5 5.7-1.4 5.7-1.4 1.5-2.3 4.2-1.1c2.7 1.2 1.7 5.2 1.7 5.2s.3-.1 1.3.5c.5.4.8.8.9 1.1.5-1.4 2.4-5.8 8.4-4 7.1 2.1 3.5 8.9 3.5 8.9s.8-.4 2 0 1.1 1.1 1.1 1.1 1.1-1.1 2.3-1.1 2.1.5 2.1.5 1.9-3.6 6.2-1.2 1.9 6.4 1.9 6.4 2.6-2.4 7.4 0c3.4 1.7 3.9 4.9 3.9 4.9s3.3-6.9 10.4-7.9 11.5 2.6 11.5 2.6.8 0 1.2.2c.4.2.9.9.9.9s4.4-3.1 8.3.2c1.9 1.7 1.5 5 1.5 5s.3-1.1 1.6-1.4c1.3-.3 2.3.2 2.3.2s-.1-1.2.5-1.9 1.9-.9 1.9-.9-4.7-9.3 4.4-13.4c5.6-2.5 9.2.9 9.2.9s5-6.2 15.9-6.2 16.1 8.1 16.1 8.1.7-.2 1.6-.4V0H0z">
								</path>
							</svg>
						</div>
					</section>
					<!-- Day Counter Section End -->
					<!-- Contact Section -->
					<section
						class="contact-form-section typo-white section-bg-img o-visible pad-top-120 pad-bottom-190 pad-md-bottom-in"
						data-bg="images/bg/bg-3.jpg">
						<span class="theme-overlay"></span>
						<div class="shape-top" data-negative="false">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 27.8"
								preserveAspectRatio="xMidYMax slice">
								<path class="shape-fill"
									d="M0 0v6.7c1.9-.8 4.7-1.4 8.5-1 9.5 1.1 11.1 6 11.1 6s2.1-.7 4.3-.2c2.1.5 2.8 2.6 2.8 2.6s.2-.5 1.4-.7c1.2-.2 1.7.2 1.7.2s0-2.1 1.9-2.8c1.9-.7 3.6.7 3.6.7s.7-2.9 3.1-4.1 4.7 0 4.7 0 1.2-.5 2.4 0 1.7 1.4 1.7 1.4h1.4c.7 0 1.2.7 1.2.7s.8-1.8 4-2.2c3.5-.4 5.3 2.4 6.2 4.4.4-.4 1-.7 1.8-.9 2.8-.7 4 .7 4 .7s1.7-5 11.1-6c9.5-1.1 12.3 3.9 12.3 3.9s1.2-4.8 5.7-5.7c4.5-.9 6.8 1.8 6.8 1.8s.6-.6 1.5-.9c.9-.2 1.9-.2 1.9-.2s5.2-6.4 12.6-3.3c7.3 3.1 4.7 9 4.7 9s1.9-.9 4 0 2.8 2.4 2.8 2.4 1.9-1.2 4.5-1.2 4.3 1.2 4.3 1.2.2-1 1.4-1.7 2.1-.7 2.1-.7-.5-3.1 2.1-5.5 5.7-1.4 5.7-1.4 1.5-2.3 4.2-1.1c2.7 1.2 1.7 5.2 1.7 5.2s.3-.1 1.3.5c.5.4.8.8.9 1.1.5-1.4 2.4-5.8 8.4-4 7.1 2.1 3.5 8.9 3.5 8.9s.8-.4 2 0 1.1 1.1 1.1 1.1 1.1-1.1 2.3-1.1 2.1.5 2.1.5 1.9-3.6 6.2-1.2 1.9 6.4 1.9 6.4 2.6-2.4 7.4 0c3.4 1.7 3.9 4.9 3.9 4.9s3.3-6.9 10.4-7.9 11.5 2.6 11.5 2.6.8 0 1.2.2c.4.2.9.9.9.9s4.4-3.1 8.3.2c1.9 1.7 1.5 5 1.5 5s.3-1.1 1.6-1.4c1.3-.3 2.3.2 2.3.2s-.1-1.2.5-1.9 1.9-.9 1.9-.9-4.7-9.3 4.4-13.4c5.6-2.5 9.2.9 9.2.9s5-6.2 15.9-6.2 16.1 8.1 16.1 8.1.7-.2 1.6-.4V0H0z">
								</path>
							</svg>
						</div>
						<div class="container">
							<div class="row mb-5 mb-md-0">
								<div class="offset-md-2 col-md-8">
									<div class="title-wrap text-center mb-5">
										<div class="section-title">
											<span class="sub-title theme-color text-uppercase">Contact Us</span>
											<h2 class="section-title margin-top-5">Get In Touch</h2>
											<span class="border-bottom center"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- <div class="shape-bottom" data-negative="false">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none">
								<path class="shape-fill" d="M500,98.9L0,6.1V0h1000v6.1L500,98.9z"></path>
							</svg>
						</div> -->
					</section>
					<!-- Contact Form Section End -->
					<!-- Contact Section -->
					<section class="contact-form-section form-with-img pad-none pb-5 broken-top-165 pad-bottom-md-none">
						<div class="container">
							<div class="row">
								<!-- col -->
								<div class="col-xl-8 px-3 pe-xl-0 mb-lg-5 mb-xl-0">
									<!-- Contact Form -->
									<div class="contact-form-4 bg-white">
										<!-- Form -->
										<div class="contact-form-wrap">
											<!-- form inputs -->
											<?php include "contact-form.php"?>
											<!-- form inputs end -->
											<p id="contact-status-msg" class="hide"></p>
										</div>
										<!-- Form End-->
									</div>
									<!-- contact-form-1 -->
								</div>
								<!-- .col -->
								<div class="col-xl-4 px-3 ps-xl-0">
									<div class="contact-img">
										<img class="img-fluid" src="images/contact/contact_bg4.jpg" width="752"
											height="888" alt="Contact Map">
									</div>
								</div>
								<!-- Col -->
							</div>
						</div>
					</section>
					<!-- Contact Form Section End -->
					<!-- Blog Section -->
					<section class="blog-section pad-top-50">
						<div class="container">
							<!-- Blog Wrap -->
							<div class="row">
								<div class="col-md-12">
									<div class="title-wrap text-center">
										<div class="section-title">
											<span class="sub-title theme-color text-uppercase">Our Blog</span>
											<h2 class="section-title margin-top-5">Latest Posts</h2>
											<span class="border-bottom center"></span>
										</div>
									</div>
									<div class="row">
										<!--Blog Main Slider-->
										<div class="owl-carousel blog-main-wrapper blog-style-2" data-loop="1"
											data-nav="1" data-dots="0" data-autoplay="0" data-autoplaypause="1"
											data-autoplaytime="5000" data-smartspeed="1000" data-margin="30"
											data-items="3" data-items-tab="2" data-items-mob="1">
											<?php
											if (!empty($homepage_blogs)):
												foreach ($homepage_blogs as $bl):
													$bl_img = (!empty($bl['image']) && file_exists(__DIR__ . '/src/html/uploads/blogs/' . $bl['image']))
														? 'src/html/uploads/blogs/' . htmlspecialchars($bl['image'])
														: $default_img;
											?>
											<!--Item-->
											<div class="item">
												<!--Blog Inner-->
												<div class="blog-inner">
													<div class="blog-thumb relative">
														<img src="<?= $bl_img ?>" class="img-fluid" style="height: 260px; width: 100%; object-fit: cover;" alt="blog-img" />
													</div>
													<div class="blog-details">
														<div class="top-meta">
															<ul class="top-meta-list">
																<li>
																	<div class="post-date"><a href="#"><i class="ti-calendar"></i> <?= date('M d, Y', strtotime($bl['date'])) ?></a></div>
																</li>
															</ul>
														</div>
														<div class="blog-title">
															<h4 class="margin-bottom-10"><a href="blog-single.php?id=<?= $bl['id'] ?>" class="blog-name"><?= htmlspecialchars($bl['title']) ?></a></h4>
														</div>
														<div class="post-desc mt-2">
															<div class="blog-link">
																<a target="_blank" href="blog-single.php?id=<?= $bl['id'] ?>" class="link font-w-500">Read More</a>
															</div>
														</div>
													</div>
												</div>
												<!--Blog Inner Ends-->
											</div>
											<!--Item Ends-->
											<?php
												endforeach;
											else:
											?>
											<div class="item"><div class="blog-inner"><div class="blog-details"><div class="blog-title"><h4>No blog posts yet. Check back soon!</h4></div></div></div></div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
							<!-- Blog Wrap -->
						</div>
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
	<!-- Bootstrap Js -->
	<script src="js/bootstrap.bundle.min.js"></script>
	<!-- Easing Js -->
	<script src="js/jquery.easing.min.js"></script>
	<!-- Carousel Js Code -->
	<script src="js/owl.carousel.min.js"></script>
	<!-- Isotope Js -->
	<script src="js/isotope.pkgd.min.js"></script>
	<!-- Magnific Popup Js -->
	<script src="js/jquery.magnific-popup.min.js"></script>
	<!-- Day Counter Js -->
	<script src="js/jquery.countdown.min.js"></script>
	<!-- Circle Progress Js -->
	<script src="js/jquery.circle.progress.min.js"></script>
	<!-- Validator Js -->
	<script src="js/validator.min.js"></script>
	<!-- Smart Resize Js -->
	<script src="js/smartresize.min.js"></script>
	<!-- Appear Js -->
	<script src="js/jquery.appear.min.js"></script>
	<!-- Theme Custom Js -->
	<script src="js/custom.js"></script>
	<!-- REVOLUTION JS FILES -->
	<script src="rs-plugin/js/home-2/rbtools.min.js"></script>
	<script src="rs-plugin/js/home-2/rs6.min.js"></script>
	<script>
		// Fallback mapping for Slider Revolution 6 legacy compatibility check
		if (typeof jQuery !== 'undefined' && jQuery.fn) {
			if (!jQuery.fn.revolution) {
				Object.defineProperty(jQuery.fn, 'revolution', {
					get: function() { return this.revolutionInit; },
					configurable: true
				});
			}
		}
	</script>
	<script src="rs-plugin/js/home-2/home-2.js"></script>
</body>
<!-- Body End -->

</html>