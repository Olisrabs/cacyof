<?php
include "session_check.php";
include('db_conn.php');
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="zxx" class="js">
<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Send Newsletter | CACYOF BOUESTI</title>
    <link rel="shortcut icon" href="./images/favicon.png">
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
    <style>
        /* ── Email preview box ── */
        .nl-preview-box {
            background: #fff;
            border: 1px solid #dbdfea;
            border-radius: 8px;
            padding: 32px;
            font-family: Georgia, serif;
            font-size: 15px;
            line-height: 1.8;
            color: #333;
        }
        .nl-preview-box .nl-head {
            background: #6576ff;
            color: #fff;
            text-align: center;
            padding: 24px;
            border-radius: 6px 6px 0 0;
            margin: -32px -32px 24px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5px;
        }
        .nl-preview-box .nl-footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #8094ae;
            text-align: center;
        }
        .nl-preview-box p { margin-bottom: 12px; }

        .preview-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: #8094ae; margin-bottom: 8px;
        }

        /* ── Medium icons ── */
        .medium-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .medium-badge.email    { background: #e5edff; color: #6576ff; }
        .medium-badge.sms      { background: #ffefd7; color: #f4a50a; }
        .medium-badge.whatsapp { background: #d4f8e8; color: #1aae6f; }

        /* ── SMS / WhatsApp compose box ── */
        .sms-compose-box {
            background: #f8f9fc;
            border: 1px solid #dbdfea;
            border-radius: 8px;
            padding: 24px;
        }
        .sms-char-counter {
            font-size: 11px; color: #8094ae; text-align: right; margin-top: 4px;
        }
        .sms-char-counter.warn { color: #e85347; }

        /* ── Compose / outcome transition ── */
        #outcome-panel { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* WhatsApp green btn */
        .btn-whatsapp { background: #25d366; color: #fff; border-color: #25d366; }
        .btn-whatsapp:hover { background: #1aae6f; border-color: #1aae6f; color: #fff; }
    </style>
</head>
<body class="nk-body bg-lighter npc-default has-sidebar">
<div class="nk-app-root">
<div class="nk-main">
    <?php include "sidebar.php"?>
    <div class="nk-wrap">
        <?php include "header.php"?>
        <div class="nk-content">
        <div class="container-fluid">
        <div class="nk-content-inner">
        <div class="nk-content-body">

            <!-- Page Head -->
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Send Newsletter</h3>
                        <p class="text-soft">Compose and send a newsletter to your selected audience via Email, SMS, or WhatsApp.</p>
                    </div>
                </div>
            </div>

            <div class="nk-block">
            <div class="row g-gs">

                <!-- ── LEFT: Composer Form ── -->
                <div class="col-lg-6">
                <div class="card">
                <div class="card-inner">

                    <!-- Row 1: Type + Recipients -->
                    <div class="row gy-3 mb-3">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nl-type">Newsletter Type</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="nl-type">
                                        <option value="">— Select type —</option>
                                        <option value="general">General Announcement</option>
                                        <option value="event">Event Invitation</option>
                                        <option value="devotional">Devotional / Word</option>
                                        <option value="prayer">Prayer Request</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="nl-recipients">Send To</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="nl-recipients">
                                        <option value="">— Select recipients —</option>
                                        <option value="members">All Members (Workers + Newsletter)</option>
                                        <option value="workers">Active Workers</option>
                                        <option value="executives">Active Executives</option>
                                        <option value="alumni">Alumni</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Medium -->
                    <div class="form-group mb-4">
                        <label class="form-label" for="nl-medium">Sending Medium <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="nl-medium">
                                <option value="">— Select medium —</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email-specific fields (shown only when medium = email) -->
                    <div id="email-fields" style="display:none">

                        <!-- Type indicator badge -->
                        <div class="mb-3">
                            <span class="badge badge-dim bg-primary" id="nl-type-badge"></span>
                            <span class="text-muted ms-2" style="font-size:12px">→</span>
                            <span class="badge badge-dim bg-success ms-1" id="nl-recipients-badge"></span>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-salutation">Salutation</label>
                            <input type="text" class="form-control" id="nl-salutation" placeholder="e.g. Dear Beloved," value="Dear Beloved,">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-subject">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nl-subject" placeholder="Email subject line">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-heading">Heading / Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nl-heading" placeholder="Main heading of the newsletter">
                        </div>

                        <!-- Event-only fields -->
                        <div id="event-fields" style="display:none">
                            <div class="form-group mb-3">
                                <label class="form-label" for="nl-event-date">Event Date</label>
                                <input type="date" class="form-control" id="nl-event-date">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="nl-event-venue">Venue / Location</label>
                                <input type="text" class="form-control" id="nl-event-venue" placeholder="Event venue or link">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-body">Body / Main Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="nl-body" rows="6" placeholder="Write the main content here..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-closing">Closing / Valediction</label>
                            <input type="text" class="form-control" id="nl-closing" placeholder="e.g. Yours in His service," value="Yours in His service,">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-sender">Sender Name</label>
                            <input type="text" class="form-control" id="nl-sender" placeholder="e.g. CACYOF BOUESTI Secretariat" value="CACYOF BOUESTI Secretariat">
                        </div>
                    </div>

                    <!-- SMS / WhatsApp message field (shown for sms or whatsapp) -->
                    <div id="short-msg-fields" style="display:none">
                        <div class="form-group mb-3">
                            <label class="form-label" for="nl-short-msg" id="short-msg-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="nl-short-msg" rows="5" maxlength="1000"
                                placeholder="Type your message here..."></textarea>
                            <div class="sms-char-counter" id="char-counter">0 / 1000 characters</div>
                        </div>
                    </div>

                    <!-- Compose Button for Email -->
                    <div id="compose-btn-wrap" style="display:none; margin-top:8px;">
                        <button type="button" class="btn btn-primary" id="btn-compose">
                            <em class="icon ni ni-eye"></em> <span class="ms-1">Preview & Send Email</span>
                        </button>
                    </div>

                    <!-- Direct Send Button for SMS -->
                    <div id="sms-send-wrap" style="display:none; margin-top:8px;">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-warning text-white" id="nl-send-sms-btn">
                                <em class="icon ni ni-send"></em> <span class="ms-1">Send SMS Newsletter</span>
                            </button>
                            <span id="nl-sms-status" class="text-soft fs-13px ms-3"></span>
                        </div>
                    </div>

                    <!-- Direct Send Button for WhatsApp -->
                    <div id="wa-send-wrap" style="display:none; margin-top:8px;">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-whatsapp" id="nl-send-wa-btn">
                                <em class="icon ni ni-send"></em> <span class="ms-1">Send WhatsApp Newsletter</span>
                            </button>
                            <span id="nl-wa-status" class="text-soft fs-13px ms-3"></span>
                        </div>
                    </div>

                </div>
                </div>
                </div><!-- col -->

                <!-- ── RIGHT: Outcome Panel (Only for Email) ── -->
                <div class="col-lg-6" id="outcome-col" style="display:none">
                    <div id="outcome-email" style="display:none">
                        <div class="card">
                        <div class="card-inner">
                            <div class="preview-label">Email Template Preview</div>

                            <!-- Inputs summary -->
                            <div class="mb-3 p-3" style="background:#f8f9fc;border-radius:6px;font-size:13px">
                                <div><strong>Type:</strong> <span id="sum-type"></span></div>
                                <div><strong>Recipients:</strong> <span id="sum-recipients"></span></div>
                                <div><strong>Subject:</strong> <span id="sum-subject" class="text-primary"></span></div>
                                <div><strong>Medium:</strong> <span class="medium-badge email">📧 Email</span></div>
                            </div>

                            <!-- Live email template preview -->
                            <div class="nl-preview-box" id="nl-preview-box">
                                <div class="nl-head" id="pv-head">Newsletter Heading</div>
                                <p id="pv-salutation">Dear Beloved,</p>
                                <p id="pv-body" style="white-space:pre-line"></p>
                                <div id="pv-event-block" style="display:none;background:#f5f6fa;border-left:4px solid #6576ff;padding:12px 16px;border-radius:4px;margin:12px 0">
                                    <strong>Event Date:</strong> <span id="pv-event-date"></span><br>
                                    <strong>Venue:</strong> <span id="pv-event-venue"></span>
                                </div>
                                <p id="pv-closing">Yours in His service,</p>
                                <p id="pv-sender"><strong>CACYOF BOUESTI Secretariat</strong></p>
                                <div class="nl-footer">
                                    This email was sent by CACYOF BOUESTI. You are receiving this because you are a registered member.
                                </div>
                            </div>

                            <div class="mt-4 d-flex align-items-center gap-3">
                                <button type="button" class="btn btn-success" id="nl-send-email-btn">
                                    <em class="icon ni ni-send"></em> <span class="ms-1">Send Email Newsletter</span>
                                </button>
                                <span id="nl-email-status" class="text-soft fs-13px ms-3"></span>
                            </div>
                        </div>
                        </div>
                    </div>
                </div><!-- col -->

            </div><!-- .row -->
            </div><!-- .nk-block -->

        </div></div></div>
        </div><!-- nk-content -->

        <div class="nk-footer">
            <div class="container-fluid">
                <div class="nk-footer-wrap">
                    <div class="nk-footer-copyright">&copy; <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI.</div>
                </div>
            </div>
        </div>
    </div><!-- nk-wrap -->
</div><!-- nk-main -->
</div><!-- nk-app-root -->

<script src="./assets/js/bundle.js?ver=2.2.0"></script>
<script src="./assets/js/scripts.js?ver=2.2.0"></script>
<script>
(function(){
'use strict';

/* ── Elements ── */
var typeEl       = document.getElementById('nl-type');
var recipientsEl = document.getElementById('nl-recipients');
var mediumEl     = document.getElementById('nl-medium');
var emailFields  = document.getElementById('email-fields');
var shortFields  = document.getElementById('short-msg-fields');
var eventFields  = document.getElementById('event-fields');
var composeBtnWrap = document.getElementById('compose-btn-wrap');
var smsSendWrap  = document.getElementById('sms-send-wrap');
var waSendWrap   = document.getElementById('wa-send-wrap');
var outcomeCol   = document.getElementById('outcome-col');
var shortMsgEl   = document.getElementById('nl-short-msg');
var charCounter  = document.getElementById('char-counter');

var typeLabels = {
    general:    'General Announcement',
    event:      'Event Invitation',
    devotional: 'Devotional / Word',
    prayer:     'Prayer Request'
};
var recipientLabels = {
    members:    'All Members',
    workers:    'Active Workers',
    executives: 'Active Executives',
    alumni:     'Alumni'
};

/* ── Hide outcome panel ── */
function hideAllOutcomes() {
    if (outcomeCol) outcomeCol.style.display = 'none';
    var outEmail = document.getElementById('outcome-email');
    if (outEmail) outEmail.style.display = 'none';
}

/* ── React to any top-level dropdown change ── */
function onDropdownChange() {
    var type   = typeEl.value;
    var recip  = recipientsEl.value;
    var medium = mediumEl.value;

    // Hide everything first
    emailFields.style.display   = 'none';
    shortFields.style.display   = 'none';
    composeBtnWrap.style.display = 'none';
    smsSendWrap.style.display   = 'none';
    waSendWrap.style.display    = 'none';
    hideAllOutcomes();

    if (!type || !recip || !medium) return;

    if (medium === 'email') {
        emailFields.style.display = '';
        eventFields.style.display = (type === 'event') ? '' : 'none';
        composeBtnWrap.style.display = '';
        document.getElementById('nl-type-badge').textContent       = typeLabels[type] || type;
        document.getElementById('nl-recipients-badge').textContent = 'To: ' + (recipientLabels[recip] || recip);
    } else {
        shortFields.style.display = '';
        var labelEl = document.getElementById('short-msg-label');
        if (medium === 'whatsapp') {
            labelEl.innerHTML = 'WhatsApp Message <span class="text-danger">*</span>';
            waSendWrap.style.display = '';
        } else {
            labelEl.innerHTML = 'SMS Message <span class="text-danger">*</span>';
            smsSendWrap.style.display = '';
        }
    }
}

typeEl.addEventListener('change', onDropdownChange);
recipientsEl.addEventListener('change', onDropdownChange);
mediumEl.addEventListener('change', onDropdownChange);

/* ── Character counter for short message ── */
if (shortMsgEl) {
    shortMsgEl.addEventListener('input', function() {
        var len = shortMsgEl.value.length;
        charCounter.textContent = len + ' / 1000 characters';
        charCounter.className = 'sms-char-counter' + (len > 900 ? ' warn' : '');
    });
}

/* ── Live email preview listeners ── */
['nl-salutation','nl-heading','nl-body','nl-closing','nl-sender','nl-event-date','nl-event-venue']
.forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', updateEmailPreview);
});

function updateEmailPreview() {
    document.getElementById('pv-head').textContent       = document.getElementById('nl-heading').value || 'Newsletter Heading';
    document.getElementById('pv-salutation').textContent = document.getElementById('nl-salutation').value || '';
    document.getElementById('pv-body').textContent       = document.getElementById('nl-body').value || '';
    document.getElementById('pv-closing').textContent    = document.getElementById('nl-closing').value || '';
    document.getElementById('pv-sender').innerHTML       = '<strong>' + (document.getElementById('nl-sender').value || '') + '</strong>';

    var isEvent = (typeEl.value === 'event');
    var evBlock = document.getElementById('pv-event-block');
    evBlock.style.display = isEvent ? '' : 'none';
    if (isEvent) {
        document.getElementById('pv-event-date').textContent  = document.getElementById('nl-event-date').value || '—';
        document.getElementById('pv-event-venue').textContent = document.getElementById('nl-event-venue').value || '—';
    }
}

/* ── Compose button click (Email Only) ── */
document.getElementById('btn-compose').addEventListener('click', function() {
    var type   = typeEl.value;
    var recip  = recipientsEl.value;
    var medium = mediumEl.value;

    if (!type || !recip || !medium) {
        alert('Please select Newsletter Type, Send To, and Sending Medium.');
        return;
    }

    hideAllOutcomes();
    outcomeCol.style.display = '';

    if (medium === 'email') {
        var subject = document.getElementById('nl-subject').value.trim();
        var heading = document.getElementById('nl-heading').value.trim();
        var body    = document.getElementById('nl-body').value.trim();
        if (!subject || !heading || !body) {
            alert('Please fill in the Subject, Heading, and Body fields.');
            outcomeCol.style.display = 'none';
            return;
        }
        document.getElementById('sum-type').textContent       = typeLabels[type] || type;
        document.getElementById('sum-recipients').textContent = recipientLabels[recip] || recip;
        document.getElementById('sum-subject').textContent    = subject;
        updateEmailPreview();
        document.getElementById('outcome-email').style.display = '';
    }
});

/* ── Helper: generic AJAX send ── */
function sendNewsletter(payload, btnEl, statusEl, successLabel) {
    if (!confirm('Send this newsletter to the selected recipients?')) return;
    $(btnEl).prop('disabled', true).find('span').text('Sending…');
    statusEl.textContent = '';

    $.ajax({
        url:      'html/send_newsletter_script.php',
        type:     'POST',
        data:     payload,
        dataType: 'json',
        success: function(res) {
            $(btnEl).prop('disabled', false).find('span').text(successLabel);
            if (res.status === 'success') {
                statusEl.innerHTML = '<span class="text-success"><em class="icon ni ni-check-circle"></em> ' + res.message + '</span>';
            } else {
                statusEl.innerHTML = '<span class="text-danger">' + res.message + '</span>';
            }
        },
        error: function(xhr) {
            $(btnEl).prop('disabled', false).find('span').text(successLabel);
            statusEl.innerHTML = '<span class="text-danger">Error: ' + (xhr.responseText || 'Unknown') + '</span>';
        }
    });
}

/* ── Send email ── */
document.getElementById('nl-send-email-btn').addEventListener('click', function() {
    var payload = {
        medium:      'email',
        type:        typeEl.value,
        recipients:  recipientsEl.value,
        subject:     document.getElementById('nl-subject').value.trim(),
        salutation:  document.getElementById('nl-salutation').value.trim(),
        heading:     document.getElementById('nl-heading').value.trim(),
        body:        document.getElementById('nl-body').value.trim(),
        event_date:  document.getElementById('nl-event-date').value,
        event_venue: document.getElementById('nl-event-venue').value.trim(),
        closing:     document.getElementById('nl-closing').value.trim(),
        sender:      document.getElementById('nl-sender').value.trim()
    };
    sendNewsletter(payload, this, document.getElementById('nl-email-status'), 'Send Email Newsletter');
});

/* ── Send SMS ── */
document.getElementById('nl-send-sms-btn').addEventListener('click', function() {
    var payload = {
        medium:     'sms',
        type:       typeEl.value,
        recipients: recipientsEl.value,
        message:    document.getElementById('nl-short-msg').value.trim()
    };
    if (!payload.message) {
        alert('Please enter your SMS message.');
        return;
    }
    sendNewsletter(payload, this, document.getElementById('nl-sms-status'), 'Send SMS Newsletter');
});

/* ── Send WhatsApp ── */
document.getElementById('nl-send-wa-btn').addEventListener('click', function() {
    var payload = {
        medium:     'whatsapp',
        type:       typeEl.value,
        recipients: recipientsEl.value,
        message:    document.getElementById('nl-short-msg').value.trim()
    };
    if (!payload.message) {
        alert('Please enter your WhatsApp message.');
        return;
    }
    sendNewsletter(payload, this, document.getElementById('nl-wa-status'), 'Send WhatsApp Newsletter');
});

}());
</script>
</body>
</html>
