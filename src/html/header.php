<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                        class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="html/index.html" class="logo-link">
                    <img class="logo-light logo-img" src="./images/logo.png" srcset="./images/logo2x.png 2x" alt="logo">
                    <img class="logo-dark logo-img" src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x"
                        alt="logo-dark">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-search ml-3 ml-xl-0">
                <em class="icon ni ni-search"></em>
                <input type="text" class="form-control border-transparent form-focus-none"
                    placeholder="Search anything">
            </div><!-- .nk-header-news -->
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-status user-status-unverified">Admin</div>
                                    <div class="user-name dropdown-indicator">CACYOF BOUESTI</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span>CB</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">CACYOF BOUESTI</span>
                                        <span class="sub-text">cacyofikere1@gmail.com</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="html/user-profile-regular.html"><em
                                                class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                    <li><a href="html/user-profile-setting.html"><em
                                                class="icon ni ni-setting-alt"></em><span>Account Setting</span></a>
                                    </li>
                                    <li><a class="dark-switch" href="#" id="globalDarkModeToggle"><em class="icon ni ni-moon"></em><span>Dark
                                                Mode</span></a></li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="html/logout.php"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
    <script>
    (function() {
        // Apply dark mode from localStorage before page renders visually
        var darkEnabled = localStorage.getItem('cacyof_dark_mode') === '1';
        if (darkEnabled) {
            document.documentElement.classList.add('dark-mode');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.getElementById('globalDarkModeToggle');
            if (!toggleBtn) return;

            function applyDarkMode(enable) {
                if (enable) {
                    document.documentElement.classList.add('dark-mode');
                    localStorage.setItem('cacyof_dark_mode', '1');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    localStorage.setItem('cacyof_dark_mode', '0');
                }
            }

            // Sync toggle button icon/text with current state
            function syncToggleUI() {
                var isDark = document.documentElement.classList.contains('dark-mode');
                var icon = toggleBtn.querySelector('em');
                var label = toggleBtn.querySelector('span');
                if (icon) icon.className = isDark ? 'icon ni ni-sun' : 'icon ni ni-moon';
                if (label) label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            }

            syncToggleUI();

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var isDark = document.documentElement.classList.contains('dark-mode');
                applyDarkMode(!isDark);
                syncToggleUI();
                // Also trigger NioApp dark mode if available (for sidebar/header CSS classes)
                if (window.NioApp && NioApp.ThemeSwitch) {
                    NioApp.ThemeSwitch(!isDark ? 'dark' : 'light');
                }
            });
        });
    })();
    </script>
</div>