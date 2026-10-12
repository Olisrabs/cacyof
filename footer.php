<?php
$gen_sec_phone = '';
if (!isset($conn)) {
    include_once 'db_conn.php';
}
if (isset($conn)) {
    // 1. Fetch all distinct regimes to find the latest
    $reg_res = $conn->query("SELECT DISTINCT regime FROM executives WHERE is_executive != 'No'");
    $latest_regime = '';
    if ($reg_res && $reg_res->num_rows > 0) {
        $regimes_list = [];
        while ($row_reg = $reg_res->fetch_assoc()) {
            $regimes_list[] = $row_reg['regime'];
        }
        usort($regimes_list, function($a, $b) {
            preg_match_all('/\d+/', $a, $mA);
            preg_match_all('/\d+/', $b, $mB);
            $yA = isset($mA[0][0]) ? intval($mA[0][0]) : 0;
            $yB = isset($mB[0][0]) ? intval($mB[0][0]) : 0;
            if ($yA !== $yB) return $yA - $yB;
            $yA2 = isset($mA[0][1]) ? intval($mA[0][1]) : 0;
            $yB2 = isset($mB[0][1]) ? intval($mB[0][1]) : 0;
            return $yA2 - $yB2;
        });
        $latest_regime = end($regimes_list);
    }

    if (!empty($latest_regime)) {
        // 2. Query General Secretary for this regime
        $stmt_gs = $conn->prepare("
            SELECT w.phone 
            FROM executives e 
            INNER JOIN workers w ON e.uin = w.uin 
            WHERE e.regime = ? AND (LOWER(e.office) = 'general secretary' OR LOWER(e.office) LIKE '%general secretary%')
            LIMIT 1
        ");
        if ($stmt_gs) {
            $stmt_gs->bind_param("s", $latest_regime);
            $stmt_gs->execute();
            $res_gs = $stmt_gs->get_result();
            if ($res_gs && $res_gs->num_rows > 0) {
                $row_gs = $res_gs->fetch_assoc();
                $gen_sec_phone = trim($row_gs['phone']);
            }
            $stmt_gs->close();
        }
    }
}
?>
<!-- Font Awesome 6 (latest icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
/* ===== Footer Layout Fix ===== */
#footer .footer-widgets .container { max-width: 1200px; }
#footer .footer-middle-wrap .row { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; }
#footer .footer-middle-wrap .row > [class*="col-"] { flex: 1 1 220px; min-width: 180px; }
#footer .social-icons { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 10px; }
#footer .social-icons a {
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px; border-radius: 50%;
    font-size: 16px; text-decoration: none; transition: transform 0.2s, opacity 0.2s;
    color: #fff;
}
#footer .social-icons a:hover { transform: scale(1.15); opacity: 0.85; }
#footer .social-fb   { background: #1877F2; }
#footer .social-mail { background: #EA4335; }
#footer .social-ig   { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
#footer .social-yt   { background: #FF0000; }
/* Newsletter input group */
#footer .mailchimp-widget-wrap .input-group { display: flex; width: 100%; }
#footer .mailchimp-widget-wrap .form-control { flex: 1 1 auto; min-width: 0; border-right: none; border-radius: 4px 0 0 4px; }
#footer .mailchimp-widget-wrap .input-group-btn { flex: 0 0 auto; }
#footer .mailchimp-widget-wrap .subscribe-btn { border-radius: 0 4px 4px 0; white-space: nowrap; }
/* Footer copyright bar */
#footer .footer-bottom-wrap .row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; }
#footer .footer-bottom-items { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; list-style: none; margin: 0; padding: 0; }
</style>

<footer id="footer" class="footer footer-1 footer-bg-img" data-bg="images/bg/footer-bg.jpg">
    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="footer-middle-wrap footer-overlay-dark">
            <div class="color-overlay"></div>
            <div class="container">
                <div class="row">
                    <!-- Col 1: About -->
                    <div class="col-lg-3 col-md-6 widget text-widget mb-4 mb-lg-0">
                        <div class="widget-title">
                            <h3 class="title typo-white">About CACYOF BOUESTI</h3>
                        </div>
                        <div class="widget-text margin-bottom-20">
                            <p>CACYOF BOUESTI is a Christ-centred youth fellowship committed to raising godly leaders, spreading the Gospel, and building lives for the Kingdom of Jesus Christ.</p>
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com/cacyofbouesti" class="social-fb" title="Facebook" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <a href="mailto:cacyofikere1@gmail.com" class="social-mail" title="Email" aria-label="Email">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                            <a href="https://instagram.com/cacyofbouesti" class="social-ig" title="Instagram" aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <a href="https://youtube.com/cacyofikerechapter" class="social-yt" title="YouTube" aria-label="YouTube">
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                    <!-- Col -->

                    <!-- Col 2: Quick Links -->
                    <div class="col-lg-3 col-md-6 widget text-widget mb-4 mb-lg-0">
                        <div class="widget-title">
                            <h3 class="title typo-white">Quick Links</h3>
                        </div>
                        <div class="menu-quick-links">
                            <ul class="menu">
                                <li class="menu-item"><a href="about-us.php">Who We Are?</a></li>
                                <li class="menu-item"><a href="events.php">Events</a></li>
                                <li class="menu-item"><a href="sermons.php">Sermons</a></li>
                                <li class="menu-item"><a href="donate-now.php">Donations</a></li>
                                <li class="menu-item"><a href="contact-us.php">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Col -->

                    <!-- Col 3: Contact Info -->
                    <div class="col-lg-3 col-md-6 widget contact-info-widget mb-4 mb-lg-0">
                        <div class="widget-title">
                            <h3 class="title typo-white">Contact Us</h3>
                        </div>
                        <ul class="menu" style="padding:0; list-style:none;">
                            <li style="margin-bottom:10px; display:flex; align-items:flex-start; gap:10px;">
                                <i class="fa-solid fa-location-dot" style="color:#faad33; margin-top:3px; flex-shrink:0;"></i>
                                <span>P.M.B 280, Ikere-Ekiti, Ekiti, Nigeria.</span>
                            </li>
                            <?php if (!empty($gen_sec_phone)): ?>
                            <li style="margin-bottom:10px; display:flex; align-items:center; gap:10px;">
                                <i class="fa-solid fa-phone" style="color:#faad33; flex-shrink:0;"></i>
                                <a href="tel:<?= htmlspecialchars($gen_sec_phone) ?>" style="color:inherit;"><?= htmlspecialchars($gen_sec_phone) ?></a>
                            </li>
                            <?php endif; ?>
                            <li style="display:flex; align-items:center; gap:10px;">
                                <i class="fa-solid fa-envelope" style="color:#faad33; flex-shrink:0;"></i>
                                <a href="mailto:cacyofikere1@gmail.com" style="color:inherit;">cacyofikere1@gmail.com</a>
                            </li>
                        </ul>
                    </div>
                    <!-- Col -->

                    <!-- Col 4: Newsletter -->
                    <div class="col-lg-3 col-md-6 widget contact-info-widget mb-4 mb-lg-0">
                        <div class="widget-title">
                            <h3 class="title typo-white">Newsletter</h3>
                        </div>
                        <p>Sign up to stay updated on news and events at CACYOF BOUESTI. Get updates on Announcements, Events, and more.</p>
                        <div class="mailchimp-widget-wrap">
                            <!-- subscribe form -->
                            <form id="subscribe-form-1" method="POST" class="subscribe-form">
                                <div class="input-group add-on">
                                    <input type="email" class="form-control" name="email" autocomplete="off"
                                        id="mcemail-1" placeholder="Email Address" required>
                                    <div class="input-group-btn">
                                        <button class="btn btn-default subscribe-btn theme-btn" name="submit" type="submit">Sign Up</button>
                                    </div>
                                </div>
                                <p class="subscribe-status-msg hide"></p>
                            </form>
                        </div>
                    </div>
                    <!-- Col -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="footer-copyright">
        <div class="footer-bottom-wrap pad-tb-20 typo-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <ul class="footer-bottom-items">
                            <li class="nav-item">
                                <div class="nav-item-inner">
                                    Copyrights &copy; <?php echo date('Y'); ?> <a href="index.php">CACYOF BOUESTI</a>. All Rights Reserved.
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-md-right text-center mt-2 mt-md-0">
                        <span style="font-size:13px; opacity:0.7;">Designed with <i class="fa-solid fa-heart" style="color:#e85347;"></i> for the Kingdom</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ===== Global Notification Modal (Success & Error) ===== -->
<div class="modal fade" id="globalNotifyModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:99999;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:420px;">
        <div class="modal-content b-radius-8" style="border:none; box-shadow:0 10px 40px rgba(0,0,0,0.25); overflow:hidden;">
            <div class="modal-body text-center p-5">
                <div class="mb-4" id="gnm-icon-wrap">
                    <!-- icon injected by JS -->
                </div>
                <h3 id="gnm-title" class="mb-3" style="color:#222; font-weight:700;">Notice</h3>
                <p id="gnm-msg" style="color:#666; font-size:15px; margin-bottom:24px;"></p>
                <button type="button" id="gnm-ok-btn" class="btn btn-primary btn-lg px-5 theme-btn"
                    onclick="closeGlobalNotifyModal()">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop fix overlay (in case Bootstrap modal backdrop is invisible) -->
<style>
#globalNotifyModal { z-index: 99999 !important; }
#globalNotifyModal .modal-dialog { margin-top: 80px; }
.modal-backdrop { z-index: 99998 !important; }

/* Legacy alias: globalSuccessModal → globalNotifyModal */
#globalSuccessModal { display: none !important; }
</style>
<script>
// ============================================================
//  Deferred notification queue — works even if called before
//  jQuery / Bootstrap scripts load (footer loads first!)
// ============================================================
window._gnmQueue = window._gnmQueue || [];

function showGlobalSuccessModal(title, message) {
    window._gnmQueue.push({ type: 'success', title: title, message: message });
    _tryFlushGnm();
}
function showGlobalErrorModal(title, message) {
    window._gnmQueue.push({ type: 'error', title: title, message: message });
    _tryFlushGnm();
}
function _tryFlushGnm() {
    // If scripts not yet ready, wait for window.load
    if (typeof $ === 'undefined' && (typeof bootstrap === 'undefined')) {
        // Will be flushed by the window.load handler below
        return;
    }
    _flushGnm();
}
function _flushGnm() {
    while (window._gnmQueue.length) {
        var item = window._gnmQueue.shift();
        _openGlobalNotifyModal(item.type, item.title, item.message);
    }
}
function _openGlobalNotifyModal(type, title, message) {
    var isSuccess = type !== 'error';
    var iconHtml = isSuccess
        ? '<div style="width:80px;height:80px;background:#e8f5e9;color:#2e7d32;border-radius:50%;font-size:34px;margin:0 auto;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-circle-check"></i></div>'
        : '<div style="width:80px;height:80px;background:#fdecea;color:#c62828;border-radius:50%;font-size:34px;margin:0 auto;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-circle-xmark"></i></div>';

    var iconEl  = document.getElementById('gnm-icon-wrap');
    var titleEl = document.getElementById('gnm-title');
    var msgEl   = document.getElementById('gnm-msg');
    var btn     = document.getElementById('gnm-ok-btn');
    var el      = document.getElementById('globalNotifyModal');
    if (!el) return;

    if (iconEl)  iconEl.innerHTML      = iconHtml;
    if (titleEl) titleEl.textContent   = title   || (isSuccess ? 'Success' : 'Error');
    if (msgEl)   msgEl.textContent     = message || '';
    if (btn) {
        btn.style.background  = isSuccess ? '' : '#c62828';
        btn.style.borderColor = isSuccess ? '' : '#c62828';
    }

    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $(el).modal({ backdrop: true, keyboard: true });
        $(el).modal('show');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var m = bootstrap.Modal.getOrCreateInstance
            ? bootstrap.Modal.getOrCreateInstance(el, { backdrop: true })
            : new bootstrap.Modal(el, { backdrop: true });
        m.show();
    } else {
        // Manual fallback: full-screen overlay
        el.style.cssText = 'display:flex!important;position:fixed;inset:0;z-index:99999;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);';
        el.classList.add('show');
    }
}
function closeGlobalNotifyModal() {
    var el = document.getElementById('globalNotifyModal');
    if (!el) return;
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        $(el).modal('hide');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var m = bootstrap.Modal.getInstance(el);
        if (m) m.hide();
    } else {
        el.style.display = 'none';
        el.classList.remove('show');
        el.removeAttribute('style');
    }
}

// Flush queue once all scripts have loaded
window.addEventListener('load', function() {
    if (window._gnmQueue && window._gnmQueue.length) {
        _flushGnm();
    }
});

// ============================================================
//  Newsletter form submit handler
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    var nlForm = document.getElementById('subscribe-form-1');
    if (nlForm) {
        nlForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formEl   = this;
            var btn      = formEl.querySelector('button[type="submit"]');
            var emailInp = formEl.querySelector('input[name="email"]');
            var orig     = btn ? btn.innerHTML : 'Sign Up';
            if (btn) { btn.disabled = true; btn.innerHTML = 'Subscribing&hellip;'; }

            var fd = new FormData();
            fd.append('email', emailInp ? emailInp.value : '');

            fetch('subscribe-newsletter.php', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (btn) { btn.disabled = false; btn.innerHTML = orig; }
                if (data.status === 'success') {
                    if (emailInp) emailInp.value = '';
                    showGlobalSuccessModal('Subscription Successful!', data.message || 'You have been subscribed.');
                } else {
                    showGlobalErrorModal('Subscription Failed', data.message || 'Could not subscribe. Please try again.');
                }
            })
            .catch(function() {
                if (btn) { btn.disabled = false; btn.innerHTML = orig; }
                showGlobalErrorModal('Network Error', 'Could not reach the server. Please try again.');
            });
        });
    }
});
</script>