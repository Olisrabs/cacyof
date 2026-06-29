<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Add a new sermon to the CACYOF BOUESTI admin panel.">
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title -->
    <title>Add Sermon - Sermons | CACYOF BOUESTI Admin</title>
    <!-- StyleSheets -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
    <style>
        /* ── Wizard step indicator ── */
        .sw-steps {
            display: flex;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 0 0 2rem;
        }
        .sw-steps li {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .sw-steps li:last-child { flex: 0 0 auto; }

        .sw-circle {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: #8094ae;
            font-weight: 700; font-size: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background .25s, color .25s;
        }
        .sw-steps li.active .sw-circle { background: #6576ff; color: #fff; }
        .sw-steps li.done  .sw-circle  { background: #1ee0ac; color: #fff; }

        .sw-label {
            font-size: 12px; font-weight: 600;
            color: #8094ae; margin-left: 8px; white-space: nowrap;
        }
        .sw-steps li.active .sw-label { color: #6576ff; }
        .sw-steps li.done  .sw-label  { color: #1ee0ac; }

        .sw-line {
            flex: 1; height: 2px;
            background: #e9ecef; margin: 0 10px;
            transition: background .25s;
        }
        .sw-line.done { background: #1ee0ac; }

        /* ── Panels ── */
        .sw-panel { display: none; }
        .sw-panel.active { display: block; }

        /* ── Nav row ── */
        .sw-nav {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 1.5rem; padding-top: 1.25rem;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>

<body class="nk-body bg-lighter npc-default has-sidebar">
<div class="nk-app-root">

    <!-- main @s -->
    <div class="nk-main">

        <!-- sidebar @s -->
        <?php include "sidebar.php"?>
        <!-- sidebar @e -->

        <!-- wrap @s -->
        <div class="nk-wrap">

            <!-- header @s -->
            <?php include "header.php"?>
            <!-- header @e -->

            <!-- content @s -->
            <div class="nk-content">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <div class="nk-content-body">
                            <div class="nk-block nk-block-lg">

                                <!-- page heading -->
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="title nk-block-title">Add Sermon</h4>
                                        <div class="nk-block-des">
                                            <p>Add a sermon to be displayed on the user frontend.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- wizard card -->
                                <div class="card">
                                    <div class="card-inner">

                                        <!-- Step indicator -->
                                        <ul class="sw-steps" id="swIndicator">
                                            <li class="active" data-step="0">
                                                <div class="sw-circle">1</div>
                                                <span class="sw-label">Details</span>
                                            </li>
                                            <div class="sw-line" id="swLine1"></div>
                                            <li data-step="1">
                                                <div class="sw-circle">2</div>
                                                <span class="sw-label">Media</span>
                                            </li>
                                            <div class="sw-line" id="swLine2"></div>
                                            <li data-step="2">
                                                <div class="sw-circle">3</div>
                                                <span class="sw-label">Message</span>
                                            </li>
                                        </ul>

                                        <!--
                                            FORM — no id, no wizard-library class.
                                            No external script will intercept this.
                                        -->
                                        <form enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">

                                            <!-- ── STEP 1: Details ── -->
                                            <div class="sw-panel active" data-panel="0">
                                                <h6 class="title mb-3">Step 1 &mdash; Sermon Details</h6>
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-title">Title <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="sw-title" name="title" placeholder="Sermon title" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-category">Category <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="sw-category" name="category" placeholder="E.g Faith, Salvation" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-date">Date <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="date" class="form-control" id="sw-date" name="date" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-preacher">Preacher <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="sw-preacher" name="preacher" placeholder="Preacher's name" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sw-nav">
                                                    <span></span>
                                                    <button type="button" class="btn btn-primary" id="swNext1">Next &rarr;</button>
                                                </div>
                                            </div><!-- /panel 0 -->

                                            <!-- ── STEP 2: Media ── -->
                                            <div class="sw-panel" data-panel="1">
                                                <h6 class="title mb-3">Step 2 &mdash; Media <span class="text-muted" style="font-weight:400">(all optional)</span></h6>
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-images">Image</label>
                                                            <div class="form-control-wrap">
                                                                <input type="file" class="form-control" id="sw-images" name="images" accept="image/jpeg,image/png,image/webp,image/gif">
                                                            </div>
                                                            <small class="text-muted">Accepted: JPG, PNG, WEBP, GIF &mdash; Max 5 MB</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-audio">Audio Link</label>
                                                            <div class="form-control-wrap">
                                                                <input type="url" class="form-control" id="sw-audio" name="audio" placeholder="https://example.com/sermon.mp3">
                                                            </div>
                                                            <small class="text-muted">Direct link to audio file or stream</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-video">Video Link</label>
                                                            <div class="form-control-wrap">
                                                                <input type="url" class="form-control" id="sw-video" name="video" placeholder="https://youtube.com/watch?v=...">
                                                            </div>
                                                            <small class="text-muted">YouTube, Vimeo, or direct video link</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-pdf">PDF File</label>
                                                            <div class="form-control-wrap">
                                                                <input type="file" class="form-control" id="sw-pdf" name="pdf" accept="application/pdf">
                                                            </div>
                                                            <small class="text-muted">PDF only &mdash; Max 10 MB</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sw-nav">
                                                    <button type="button" class="btn btn-dim btn-outline-light" id="swPrev2">&larr; Previous</button>
                                                    <button type="button" class="btn btn-primary" id="swNext2">Next &rarr;</button>
                                                </div>
                                            </div><!-- /panel 1 -->

                                            <!-- ── STEP 3: Full Message ── -->
                                            <div class="sw-panel" data-panel="2">
                                                <h6 class="title mb-3">Step 3 &mdash; Full Message</h6>
                                                <div class="row gy-2">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sw-message">Full Message <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <textarea class="form-control" id="sw-message" name="message" rows="10" placeholder="Type the full sermon message here..." required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sw-nav">
                                                    <button type="button" class="btn btn-dim btn-outline-light" id="swPrev3">&larr; Previous</button>
                                                    <button type="button" class="btn btn-success" id="swSubmit">
                                                        <em class="icon ni ni-check-circle-fill"></em>
                                                        <span>Submit Sermon</span>
                                                    </button>
                                                </div>
                                            </div><!-- /panel 2 -->

                                        </form>

                                    </div><!-- .card-inner -->
                                </div><!-- .card -->

                            </div><!-- .nk-block -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- content @e -->

            <!-- footer @s -->
            <div class="nk-footer">
                <div class="container-fluid">
                    <div class="nk-footer-wrap">
                        <div class="nk-footer-copyright">&copy; <script>document.write(new Date().getFullYear())</script> CACYOF BOUESTI.</div>
                    </div>
                </div>
            </div>
            <!-- footer @e -->

        </div><!-- .nk-wrap -->
    </div><!-- .nk-main -->
</div><!-- .nk-app-root -->

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5" id="resultModalBody">
                <!-- filled by JS -->
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-dim btn-outline-secondary"
                        data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="./assets/js/bundle.js?ver=2.2.0"></script>
<script src="./assets/js/scripts.js?ver=2.2.0"></script>

<!-- ========= SERMON WIZARD SCRIPT ========= -->
<script>
(function () {
    'use strict';

    /* ─── State ─── */
    var current = 0;

    /* ─── DOM ─── */
    var panels    = document.querySelectorAll('.sw-panel');
    var stepItems = document.querySelectorAll('#swIndicator li[data-step]');
    var line1     = document.getElementById('swLine1');
    var line2     = document.getElementById('swLine2');

    /* ─── Go to a step ─── */
    function goTo(index) {
        panels.forEach(function (p, i) {
            p.classList.toggle('active', i === index);
        });
        stepItems.forEach(function (li, i) {
            li.classList.remove('active', 'done');
            if (i < index)  li.classList.add('done');
            if (i === index) li.classList.add('active');
        });
        line1.classList.toggle('done', index > 0);
        line2.classList.toggle('done', index > 1);
        current = index;
        window.scrollTo(0, 0);
    }

    /* ─── Validate required fields in current panel ─── */
    function validate() {
        var panel  = panels[current];
        var fields = panel.querySelectorAll('[required]');
        var ok = true;
        fields.forEach(function (f) {
            var empty = !f.value.trim();
            f.style.borderColor = empty ? '#e85347' : '';
            if (empty) ok = false;
        });
        if (!ok) {
            var bad = panel.querySelector('[required]');
            if (bad) bad.focus();
        }
        return ok;
    }

    /* ─── Button wiring ─── */
    document.getElementById('swNext1').addEventListener('click', function () {
        if (validate()) goTo(1);
    });

    document.getElementById('swPrev2').addEventListener('click', function () { goTo(0); });
    document.getElementById('swNext2').addEventListener('click', function () { goTo(2); });

    document.getElementById('swPrev3').addEventListener('click', function () { goTo(1); });

    document.getElementById('swSubmit').addEventListener('click', function () {
        if (validate()) submitForm();
    });

    /* ─── AJAX submit ─── */
    function submitForm() {
        var $btn = $('#swSubmit');
        $btn.prop('disabled', true).find('span').text('Submitting…');

        var fd = new FormData();
        fd.append('title',         document.getElementById('sw-title').value.trim());
        fd.append('category',      document.getElementById('sw-category').value.trim());
        fd.append('date',          document.getElementById('sw-date').value);
        fd.append('preacher',      document.getElementById('sw-preacher').value.trim());
        fd.append('audio',         document.getElementById('sw-audio').value.trim());
        fd.append('video',         document.getElementById('sw-video').value.trim());
        fd.append('message',       document.getElementById('sw-message').value.trim());

        var img = document.getElementById('sw-images').files[0];
        var pdf = document.getElementById('sw-pdf').files[0];
        if (img) fd.append('images', img);
        if (pdf) fd.append('pdf',    pdf);

        $.ajax({
            url:         'html/add_sermon_script.php',
            type:        'POST',
            data:        fd,
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).find('span').text('Submit Sermon');

                if (res.status === 'success') {
                    $('#resultModalBody').html(
                        '<em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>' +
                        '<h4 class="nk-modal-title">Sermon Added!</h4>' +
                        '<p class="sub-text mt-2">' + res.message + '</p>'
                    );
                    setTimeout(function () { location.reload(); }, 2500);
                } else {
                    $('#resultModalBody').html(
                        '<em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>' +
                        '<h4 class="nk-modal-title">Failed!</h4>' +
                        '<p class="text-danger mt-2">' + res.message + '</p>'
                    );
                }

                $('#resultModal').modal({ backdrop: 'static', keyboard: false }).modal('show');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).find('span').text('Submit Sermon');
                alert('Request failed: ' + (xhr.responseText || 'Unknown error'));
            }
        });
    }

}());
</script>
<!-- =========== SERMON WIZARD SCRIPT END ========= -->

</body>
</html>