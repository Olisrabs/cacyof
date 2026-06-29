<?php
include('db_conn.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id <= 0) {
    die("Invalid donation ID.");
}

// Fetch the donation details
$stmt = $conn->prepare("SELECT * FROM donations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();

if (!$donation) {
    die("Donation record not found.");
}

// Handle payment confirmation via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $update_stmt = $conn->prepare("UPDATE donations SET status = 'Successful' WHERE id = ?");
    $update_stmt->bind_param("i", $id);
    if ($update_stmt->execute()) {
        header("Location: donate-now.php?payment=success&id=" . $id);
        exit();
    } else {
        $error = "Failed to update payment status.";
    }
    $update_stmt->close();
}

$method = strtolower($donation['method']);
$amount = (float)$donation['amount'];
$name = $donation['name'];
$email = $donation['email'];

// Set page colors and brand elements based on gateway choice
$gateway_name = "Payment Gateway";
$brand_color = "#3380de"; // default
$logo_html = "";

if ($method === 'stripe') {
    $gateway_name = "Stripe";
    $brand_color = "#635bff";
} elseif ($method === 'paypal') {
    $gateway_name = "PayPal";
    $brand_color = "#003087";
} elseif ($method === 'paystack') {
    $gateway_name = "Paystack";
    $brand_color = "#3bb75e";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment via <?= htmlspecialchars($gateway_name) ?></title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700|Open+Sans:400,600,700" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f6f9fc;
            font-family: 'Open Sans', sans-serif;
            color: #333;
        }
        .payment-container {
            max-width: 500px;
            margin: 60px auto;
            padding: 0 15px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            background-color: #fff;
            overflow: hidden;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e6ebf1;
            padding: 24px;
            text-align: center;
        }
        .brand-header {
            font-size: 24px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 8px;
        }
        .stripe-header { color: #635bff; }
        .paypal-header { color: #003087; }
        .paystack-header { color: #3bb75e; }
        
        .transaction-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
            padding-top: 8px;
            border-top: 1px dashed #e6ebf1;
        }
        .detail-label {
            color: #6b7c93;
        }
        .detail-value {
            font-weight: 600;
            color: #32325d;
        }
        .amount-value {
            font-size: 18px;
            font-weight: 700;
            color: #24252a;
        }
        
        .card-body {
            padding: 24px;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7c93;
            margin-bottom: 8px;
        }
        .form-control {
            height: 46px;
            border: 1px solid #e6ebf1;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            box-shadow: none;
            border-color: <?= $brand_color ?>;
        }
        
        /* Provider-specific simulations */
        .card-inputs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn-pay {
            width: 100%;
            height: 50px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background-color: <?= $brand_color ?>;
            border: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-pay:hover {
            opacity: 0.9;
        }
        .btn-pay:active {
            transform: scale(0.98);
        }
        
        /* PayPal specific simulation styling */
        .paypal-login {
            border: 1px solid #dcdcdc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background-color: #fbfbfb;
        }
        
        /* Paystack specific options */
        .paystack-tabs {
            display: flex;
            border-bottom: 1px solid #e6ebf1;
            margin-bottom: 20px;
        }
        .paystack-tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #6b7c93;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        .paystack-tab.active {
            color: #3bb75e;
            border-bottom-color: #3bb75e;
        }
        
        /* Loading Overlay Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.95);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid <?= $brand_color ?>;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            font-weight: 600;
            color: #32325d;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #9babb4;
        }
    </style>
</head>
<body>

    <div class="payment-container">
        <div class="card position-relative">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
                <div class="loading-text" id="loadingText">Processing transaction securely...</div>
            </div>
            
            <div class="card-header">
                <?php if ($method === 'stripe'): ?>
                    <div class="brand-header stripe-header">stripe</div>
                <?php elseif ($method === 'paypal'): ?>
                    <div class="brand-header paypal-header"><i>PayPal</i></div>
                <?php else: ?>
                    <div class="brand-header paystack-header">paystack</div>
                <?php endif; ?>
                <span class="badge bg-light text-dark border">Test Sandbox Simulator</span>
            </div>
            
            <div class="card-body">
                <div class="transaction-details">
                    <div class="detail-row">
                        <span class="detail-label">Donor Name:</span>
                        <span class="detail-value"><?= htmlspecialchars($name) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?= htmlspecialchars($email) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value" style="text-transform: capitalize;"><?= htmlspecialchars($donation['method']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value amount-value">₦<?= number_format($amount, 2) ?></span>
                    </div>
                </div>
                
                <form id="paymentForm" method="post">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="confirm_payment" value="1">
                    
                    <?php if ($method === 'stripe'): ?>
                        <!-- Stripe Card UI -->
                        <div class="mb-3">
                            <label class="form-label">Cardholder Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" placeholder="4242 4242 4242 4242" value="4242 4242 4242 4242" required>
                        </div>
                        <div class="card-inputs-grid mb-3">
                            <div>
                                <label class="form-label">Expiration Date</label>
                                <input type="text" class="form-control" placeholder="MM / YY" value="12/29" required>
                            </div>
                            <div>
                                <label class="form-label">CVC</label>
                                <input type="text" class="form-control" placeholder="123" value="123" required>
                            </div>
                        </div>
                        
                    <?php elseif ($method === 'paypal'): ?>
                        <!-- PayPal Checkout Login UI -->
                        <div class="paypal-login">
                            <h6 class="mb-3" style="color: #003087; font-weight: 700;">Log in to PayPal to complete donation</h6>
                            <div class="mb-3">
                                <label class="form-label">PayPal Email Address</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" value="••••••••••••" required>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Paystack UI -->
                        <div class="paystack-tabs">
                            <div class="paystack-tab active">Pay with Card</div>
                            <div class="paystack-tab">Bank Transfer</div>
                            <div class="paystack-tab">USSD</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" placeholder="4012 8888 8888 1881" value="4012 8888 8888 1881" required>
                        </div>
                        <div class="card-inputs-grid mb-3">
                            <div>
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" placeholder="MM / YY" value="09/28" required>
                            </div>
                            <div>
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" placeholder="456" value="456" required>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-pay" id="payButton">
                        Pay ₦<?= number_format($amount, 2) ?>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="footer-note">
            <span class="lock-icon">🔒</span> This is a secure simulator connection for demonstration purposes.
        </div>
    </div>

    <!-- Script for simulation delay -->
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var overlay = document.getElementById('loadingOverlay');
            var text = document.getElementById('loadingText');
            overlay.style.display = 'flex';
            
            // Step 1: Connecting
            setTimeout(function() {
                text.innerText = "Authorizing with bank servers...";
            }, 1000);
            
            // Step 2: Confirming
            setTimeout(function() {
                text.innerText = "Payment confirmed! Redirecting back...";
            }, 2500);
            
            // Step 3: Redirect
            var form = e.target;
            setTimeout(function() {
                form.submit();
            }, 3500);
        });
    </script>
</body>
</html>
