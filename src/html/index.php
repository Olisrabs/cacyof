<?php
session_start();
include('db_conn.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Modal state
$showModal      = false;
$modalType      = '';
$modalTitle     = '';
$modalMessage   = '';
$redirectUrl    = '';

// Catch session auth errors (timeout, unauthorized)
if (isset($_SESSION['auth_error'])) {
    $showModal    = true;
    $modalType    = 'error';
    $modalTitle   = 'Access Denied';
    $modalMessage = $_SESSION['auth_error'];
    unset($_SESSION['auth_error']);
}

// ✅ ADD THIS — catch logout success message
if (isset($_SESSION['auth_error_type']) && $_SESSION['auth_error_type'] === 'success') {
    $showModal    = true;
    $modalType    = 'success';
    $modalTitle   = $_SESSION['auth_error_title'];
    $modalMessage = $_SESSION['auth_error_message'];
    unset($_SESSION['auth_error_type']);
    unset($_SESSION['auth_error_title']);
    unset($_SESSION['auth_error_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['identifier'], $_POST['password'])) {

    $identifier = trim($_POST['identifier']);
    $password   = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        $showModal      = true;
        $modalType      = 'error';
        $modalTitle     = 'Login Failed';
        $modalMessage   = 'Please fill in all fields.';
    } else {

        $stmt = $conn->prepare("
            SELECT id, email, username, password, status, role
            FROM admin
            WHERE (email = ? OR username = ?)
            LIMIT 1
        ");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // ✅ Verify password
            if (password_verify($password, $user['password'])) {

                if ($user['status'] !== 'Active') {
                    $showModal      = true;
                    $modalType      = 'error';
                    $modalTitle     = 'Account Inactive';
                    $modalMessage   = 'Your account is not active yet.';
                } else {
                    // ✅ Login success
                    $_SESSION['user_id']       = $user['id'];
                    $_SESSION['email']         = $user['email'];
                    $_SESSION['username']      = $user['username'];
                    $_SESSION['role']          = $user['role'];
                    $_SESSION['LAST_ACTIVITY'] = time();
                    $_SESSION['logged_in']     = true;

                    session_regenerate_id(true);

                    $showModal    = true;
                    $modalType    = 'success';
                    $modalTitle   = 'Welcome Back!';
                    $modalMessage = 'Login successful. Redirecting...';
                    $redirectUrl  = 'html/default.php';

                }
            } else {
                $showModal      = true;
                $modalType      = 'error';
                $modalTitle     = 'Invalid Credentials';
                $modalMessage   = 'Incorrect email/username or password.';
            }
        } else {
            $showModal      = true;
            $modalType      = 'error';
            $modalTitle     = 'Login Failed';
            $modalMessage   = 'Account not found.';
        }
    }
}
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
    <title>Login | CACYOF BOUESTI Admin</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="html/index.php" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="./images/logo.png" srcset="./images/logo2x.png 2x" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Sign In</h4>
                                        <div class="nk-block-des">
                                            <p>Access the CACYOF BOUESTI Admin panel using login details.</p>
                                        </div>
                                    </div>
                                </div>
                                <form action="" method="post">
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email or Username</label>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" id="default-01" placeholder="Enter email address or username" name="identifier" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password">Passcode</label>
                                            <a class="link link-primary link-sm" href="html/forget-password.php">Forgot Code?</a>
                                        </div>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" id="password" placeholder="Enter passcode" name="password" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block">Sign in</button>
                                    </div>
                                </form>
                                <div class="form-note-s2 text-center pt-4"> New on our platform? <a href="html/sign-up.php">Create an account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="nk-block-content text-center text-lg-left">
                                        <p class="text-soft">&copy; <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI. All Rights Reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=2.2.0"></script>
    <script src="./assets/js/scripts.js?ver=2.2.0"></script>

    <!-- ========== MODAL ========== -->
    <div class="modal fade" tabindex="-1" id="modalAlert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross"></em></a>
                <div class="modal-body modal-body-lg text-center">
                    <div class="nk-modal">

                        <?php if ($modalType === 'success'): ?>
                            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                            <h4 class="nk-modal-title"><?= $modalTitle ?></h4>
                            <p class="sub-text mt-2"><?= $modalMessage ?></p>

                        <?php elseif ($modalType === 'error'): ?>
                            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                            <h4 class="nk-modal-title"><?= $modalTitle ?></h4>
                            <p class="text-danger mt-2"><?= $modalMessage ?></p>
                            <div class="mt-3">
                                <a href="#" class="btn btn-lg btn-primary" data-dismiss="modal">Try Again</a>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== MODAL END ========== -->

    <!-- ========== MODAL SCRIPT ========== -->
    <?php if ($showModal): ?>
    <script>
        $(document).ready(function () {
            $('#modalAlert').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#modalAlert').modal('show');

            <?php if ($modalType === 'success' && !empty($redirectUrl)): ?>
            setTimeout(function () {
                window.location.href = '<?= $redirectUrl ?>';
            }, 3000);
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
    <!-- ========== MODAL SCRIPT END ========== -->

</html>