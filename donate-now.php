<?php
require_once 'db_conn.php';

$show_thank_you = false;
$thank_you_amount = 0;
$thank_you_name = "";

if (isset($_GET['payment']) && $_GET['payment'] === 'success' && isset($_GET['id'])) {
    $donation_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM donations WHERE id = ? AND status = 'Successful'");
    if ($stmt) {
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($donation_data = $res->fetch_assoc()) {
            $show_thank_you = true;
            $thank_you_amount = (float)$donation_data['amount'];
            $thank_you_name = $donation_data['name'];
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="images/favicon.png" rel="icon" />
    <title>CACYOF BOUESTI - Donate Now</title>
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
        /* Custom styles for donation page to solve Bootstrap 3/4 layout bugs */
        .donate-form .d-flex {
            display: flex !important;
            align-items: center;
        }
        .donate-form .flex-wrap {
            flex-wrap: wrap;
        }
        /* Custom spacing for gateway choices */
        .donate-form .gateway-container {
            display: flex;
            flex-wrap: wrap;
        }
        .donate-form .gateway-option {
            display: inline-flex;
            align-items: center;
            margin-right: 24px;
            margin-bottom: 12px;
            cursor: pointer;
        }
        .donate-form .gateway-option input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }
        .donate-form .gateway-option label {
            cursor: pointer;
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Custom spacing for fixed donation amount boxes */
        .donate-form .amount-box {
            display: flex;
            align-items: center;
            padding: 16px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .donate-form .amount-box:hover {
            border-color: #6576ff;
            background-color: #f1f3ff;
        }
        .donate-form .amount-box input[type="radio"] {
            margin-right: 12px;
            cursor: pointer;
            width: 20px;
            height: 20px;
        }
        .donate-form .amount-box label {
            margin-bottom: 0;
            font-size: 18px;
            font-weight: 700;
            color: #333;
            cursor: pointer;
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
                                        <span class="current">Donate Now</span>
                                    </div>
                                    <h1 class="page-title mb-0">Donate Now</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-wrapper pad-none">
                <div class="content-inner">				
                    <section id="section-sermon" class="sermon-section pad-bottom-45">
                        <div class="container">
                            <div class="sermon-main-wrap sermon-list sermon-grid-2">
                                <div class="row">
                                    <div class="col-lg-8 mx-auto">
                                        <div class="title-wrap text-center margin-bottom-35">
                                            <div class="section-title">
                                                <span class="sub-title theme-color text-uppercase">DONATE HERE!</span>
                                                <h2 class="section-title margin-top-5">Save the Humanity, Save Life</h2>
                                                <span class="border-bottom center"></span>
                                            </div>
                                            <div class="pad-top-15">
                                                <p class="margin-bottom-20">Contribute to the growth of the body of Christ by helping lives.</p>
                                            </div>
                                        </div>

                                        <!-- Contact Form -->
                                        <div class="contact-form-4 pt-2 bg-white box-shadow1 p-4 p-md-5 b-radius-8 mb-5">
                                            <div class="contact-form-wrap">
                                                <form id="donate-form" class="contact-form donate-form" method="post">
                                                    <div class="row donation-fields mb-4 pb-4" style="border-bottom: 1px solid #ebebeb;">
                                                        <h3 class="form-title mb-3">1. Select Donation Amount</h3>
                                                        <div class="col-md-6 pe-md-2">
                                                            <div class="amount-box">								
                                                                <input id="donation_amount1" class="donation_amount" name="donation_amount" type="radio" value="20000" checked>
                                                                <label for="donation_amount1">₦20,000.00</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 ps-md-2">
                                                            <div class="amount-box">
                                                                <input id="donation_amount2" class="donation_amount" name="donation_amount" type="radio" value="50000">
                                                                <label for="donation_amount2">₦50,000.00</label>					
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 custom-donation-amount mt-2">
                                                            <label class="fw-bold mb-2">Custom Amount (₦)</label>
                                                            <input id="custom_donation_amount" class="form-control py-3" name="donation_amount_custom" type="number" min="100" placeholder="Enter custom amount (e.g. 10000)">
                                                        </div>
                                                    </div>	

                                                    <div class="row mb-4 pb-4" style="border-bottom: 1px solid #ebebeb;">
                                                        <h3 class="form-title mb-3">2. Aim for Donation</h3>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-0">
                                                                <label class="fw-bold mb-2">What is this donation for? <span style="color:red">*</span></label>
                                                                <select id="donation_aim" class="form-control py-3" name="donation_aim" required>
                                                                    <option value="">-- Select an aim --</option>
                                                                    <option value="General Offering">General Offering</option>
                                                                    <option value="Building Fund">Building Fund</option>
                                                                    <option value="Mission Work">Mission Work</option>
                                                                    <option value="Welfare &amp; Support">Welfare &amp; Support</option>
                                                                    <option value="Youth Ministry">Youth Ministry</option>
                                                                    <option value="Evangelism">Evangelism</option>
                                                                    <option value="Education Support">Education Support</option>
                                                                    <option value="Medical Outreach">Medical Outreach</option>
                                                                    <option value="Other">Other</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row regular-fields mb-4 pb-4 gy-3" style="border-bottom: 1px solid #ebebeb;">
                                                        <h3 class="form-title mb-3">3. Your Personal Details</h3>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">First name <span style="color:red">*</span></label>
                                                                <input id="first-name" class="form-control py-3" name="first_name" type="text" required />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">Last name <span style="color:red">*</span></label>
                                                                <input id="last-name" class="form-control py-3" name="last_name" type="text" required />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">Email Address <span style="color:red">*</span></label>
                                                                <input id="email" class="form-control py-3" name="email" type="email" required />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">Address</label>
                                                                <input id="address" class="form-control py-3" name="address" type="text" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">Country</label>
                                                                <input id="country" class="form-control py-3" name="country" type="text" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label class="fw-bold mb-2">Phone Number</label>
                                                                <input id="phone" class="form-control py-3" name="phone" type="text" />
                                                            </div>
                                                        </div>												
                                                    </div>

                                                    <div class="row payment-fields mb-4 pb-2">
                                                        <h3 class="form-title mb-3">4. Payment Gateway</h3>
                                                        <div class="gateway-container">
                                                            <div class="gateway-option">
                                                                <input type="radio" id="stripe" name="fav_language" value="Stripe" checked>
                                                                <label for="stripe">Stripe</label>
                                                            </div>
                                                            <div class="gateway-option">
                                                                <input type="radio" id="paypal" name="fav_language" value="PayPal">
                                                                <label for="paypal">PayPal</label>
                                                            </div>
                                                            <div class="gateway-option">
                                                                <input type="radio" id="paystack" name="fav_language" value="Paystack">
                                                                <label for="paystack">Paystack</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 text-center">									
                                                            <button type="submit" id="donate-submit" class="btn btn-primary btn-lg px-5 theme-btn mt-3 py-3 fs-5 fw-bold w-100">Proceed to Donate</button>
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
    <?php if ($show_thank_you): ?>
    <!-- Thank You Modal -->
    <div class="modal fade" id="thankYouModal" tabindex="-1" role="dialog" aria-labelledby="thankYouModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
                <div class="modal-header text-center" style="background-color: #fcfcfc; border-bottom: 1px solid #f1f1f1; padding: 24px; display: block; position: relative;">
                    <div style="width: 70px; height: 70px; background-color: #d4edda; color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; box-shadow: 0 4px 10px rgba(40,167,69,0.2);">
                        ✓
                    </div>
                    <h4 class="modal-title" id="thankYouModalLabel" style="font-weight: 700; font-family: 'Poppins', sans-serif; color: #252525; margin: 0;">Donation Successful!</h4>
                </div>
                <div class="modal-body text-center" style="padding: 30px 24px; font-family: 'Open Sans', sans-serif;">
                    <p style="font-size: 16px; color: #495057; line-height: 1.6; margin-bottom: 0;">
                        Dear <strong style="color: #252525;"><?= htmlspecialchars($thank_you_name) ?></strong>,<br><br>
                        Thank you so much for your generous donation of <strong style="color: #faad33; font-size: 18px;">₦<?= number_format($thank_you_amount, 2) ?></strong>. Your contribution has been verified successfully. Your kind support plays a vital role in our ministry and helps us continue populating the Kingdom of Jesus!
                    </p>
                </div>
                <div class="modal-footer" style="border-top: none; padding: 15px 24px 24px; justify-content: center;">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background-color: #faad33; border: none; padding: 10px 32px; font-weight: 600; font-family: 'Poppins', sans-serif; border-radius: 6px; color: #fff; cursor: pointer; transition: opacity 0.2s;" onclick="jQuery('#thankYouModal').modal('hide');">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>


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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var radioInputs = document.querySelectorAll('.donation_amount[type="radio"]');
        var customInput = document.getElementById('custom_donation_amount');

        radioInputs.forEach(function(radio) {
            radio.addEventListener('change', function() {
                customInput.value = '';
                customInput.name = 'donation_amount_custom';
                radioInputs.forEach(r => r.name = 'donation_amount');
            });
        });

        customInput.addEventListener('input', function() {
            radioInputs.forEach(r => {
                r.checked = false;
                r.name = 'donation_amount_radio';
            });
            this.name = 'donation_amount';
        });

        var donateForm = document.getElementById('donate-form');
        if (donateForm) {
            donateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = document.getElementById('donate-submit');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Processing Donation...';

                var formData = new FormData(this);
                fetch('submit-donation.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data.status === 'success') {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message || 'Error processing donation.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert('Communication error. Please try again.');
                });
            });
        }
        <?php if ($show_thank_you): ?>
        // Show thank you modal (supporting both jQuery BS4 and BS5 syntax)
        if (window.bootstrap && window.bootstrap.Modal) {
            var myModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
            myModal.show();
        } else if (window.jQuery && window.jQuery.fn.modal) {
            jQuery('#thankYouModal').modal('show');
        }
        <?php endif; ?>
    });
    </script>
</body>

</html>