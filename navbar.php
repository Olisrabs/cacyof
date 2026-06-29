<nav class="navbar">
    <div class="basic-container bg-light b-radius-10 clearfix">
        <div class="navbar-inner">
            <!--Overlay Menu Switch-->
            <ul class="nav navbar-items pull-left">
                <li class="list-item">
                    <a href="index.php" class="logo-general"><img src="images/logo-dark.png"
                            class="img-fluid changeable-dark" width="166" height="50" alt="Logo" /></a>
                    <a href="index.php" class="logo-sticky"><img src="images/logo-dark.png"
                            class="img-fluid changeable-dark" width="166" height="50" alt="Logo" /></a>
                </li>
            </ul>
            <?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Helper function to check if a link is active
function isActive($page, $current) {
    return $page === $current ? 'active' : '';
}

// For the Pages dropdown, check if any sub-page is active
$pages_sub = ['about-us.php', 'events.php', 'campaigns.php'];
$pages_active = in_array($current_page, $pages_sub) ? 'active' : '';
?>

            <!-- Menu -->
            <ul class="nav navbar-items pull-right">
                <!--List Item-->
                <li class="list-item">
                    <ul class="nav navbar-main menu-dark">
                        <li class="dropdown <?= isActive('index.php', $current_page) ?>">
                            <a href="index.php">Home</a>
                        </li>
                        <li class="dropdown dropdown-sub <?= $pages_active ?>">
                            <a href="#">Pages</a>
                            <ul class="dropdown-menu">
                                <li class="<?= isActive('about-us.php', $current_page) ?>">
                                    <a href="about-us.php">About Us</a>
                                </li>
                                <li class="<?= isActive('events.php', $current_page) ?>">
                                    <a href="events.php">Events</a>
                                </li>
                                <!-- <li class="<?= isActive('campaigns.php', $current_page) ?>">
                                    <a href="campaigns.php">Campaigns</a>
                                </li> -->
                            </ul>
                        </li>
                        <li class="dropdown <?= isActive('sermons.php', $current_page) ?>">
                            <a href="sermons.php">Sermons</a>
                        </li>
                        <li class="dropdown <?= isActive('blog.php', $current_page) ?>">
                            <a href="blog.php">Blogs</a>
                        </li>
                        <li class="dropdown <?= isActive('contact-us.php', $current_page) ?>">
                            <a href="contact-us.php">Contact Us</a>
                        </li>
                    </ul>
                </li>
                <!--List Item End-->
                <!--List Item-->
                <li class="list-item">
                    <div class="header-navbar-text-1">
                        <a href="donate-now.php"
                            class="h-donate-btn <?= isActive('donate-now.php', $current_page) ?>">Donate</a>
                    </div>
                </li>
                <!--List Item End-->
            </ul>
            <!-- Menu -->
        </div>
    </div>
    <!--Search-->
</nav>