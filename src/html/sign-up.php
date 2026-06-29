<?php
session_start();

$show_modal = false;
$is_success = false;

if (isset($_SESSION['success'])) {
    $show_modal = true;
    $is_success = true;
}

if (isset($_SESSION['reg_errors']) && !empty($_SESSION['reg_errors'])) {
    $show_modal = true;
    $is_success = false;
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
    <link rel="shortcut icon" href="./images/favicon.png">
    <title>Register | CACYOF BOUESTI Admin</title>
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <div class="nk-main">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content">
                    <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="html/default.php" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="./images/logo.png" srcset="./images/logo2x.png 2x" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Register</h4>
                                        <div class="nk-block-des">
                                            <p>Create New CACYOF BOUESTI Account</p>
                                        </div>
                                    </div>
                                </div>
                                <form action="html/reg.php" method="post">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control form-control-lg" name="name" id="name" placeholder="Enter name">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" id="email" placeholder="Enter email address">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" class="form-control form-control-lg" name="username" id="username" placeholder="Enter username">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">Passcode</label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter passcode">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-control-xs custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkbox" required>
                                            <label class="custom-control-label" for="checkbox">I agree to CACYOF BOUESTI <a href="#">Privacy Policy</a> &amp; <a href="#"> Terms.</a></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block" name="submit">Register</button>
                                    </div>
                                </form>
                                <div class="form-note-s2 text-center pt-4"> Already have an account? <a href="html/index.php"><strong>Sign in instead</strong></a>
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
            </div>
        </div>
    </div>


    <!-- ========== MODAL GOES HERE — outside nk-app-root, before scripts ========== -->
    <div class="modal fade" tabindex="-1" id="modalAlert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross"></em></a>
                <div class="modal-body modal-body-lg text-center">
                    <div class="nk-modal">

                        <?php if ($is_success): ?>
                            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                            <h4 class="nk-modal-title">Congratulations!</h4>
                            <p class="text-success mt-2"><?= $_SESSION['success'] ?></p>
                            <p class="sub-text mt-1">Redirecting you to login...</p>
                            <?php unset($_SESSION['success']); ?>

                        <?php else: ?>
                            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                            <h4 class="nk-modal-title">Registration Failed!</h4>
                            <ul class="text-danger text-left mt-2">
                                <?php if (isset($_SESSION['reg_errors'])): ?>
                                    <?php foreach ($_SESSION['reg_errors'] as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                    <?php unset($_SESSION['reg_errors']); ?>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== MODAL END ========== -->


    <!-- JavaScript — must come AFTER bundle.js so Bootstrap modal is available -->
    <script src="./assets/js/bundle.js?ver=2.2.0"></script>
    <script src="./assets/js/scripts.js?ver=2.2.0"></script>

    <!-- ========== MODAL TRIGGER SCRIPT — must be LAST ========== -->
    <?php if ($show_modal): ?>
    <script>
        $(document).ready(function () {
            $('#modalAlert').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#modalAlert').modal('show');

            <?php if ($is_success): ?>
            setTimeout(function () {
                window.location.href = 'html/index.php'; // your login page
            }, 3000);
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
    <!-- ========== MODAL TRIGGER SCRIPT END ========== -->

</body>
</html>