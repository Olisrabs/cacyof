<?php
include "session_check.php";
include('db_conn.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$userName = $session_name ?? 'User';
$defaultImage = "assets/images/default-avatar.png";
$profileImage = !empty($row['profile_photo'])
    ? "uploads/profile_photos/" . htmlspecialchars($row['profile_photo'])
    : $defaultImage;
?>
<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Calendar | CACYOF BOUESTI Admin</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.2.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.2.0">
</head>

<body class="nk-body bg-lighter npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?php include "sidebar.php"?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?php include "header.php"?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Calendar</h3>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addEventPopup"><em class="icon ni ni-plus"></em><span>Add Event</span></a>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-bordered">
                                        <div class="card-inner">
                                            <div id="calendar" class="nk-calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; <?php echo date('Y'); ?> CACYOF BOUESTI.</div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <div class="modal fade" tabindex="-1" id="addEventPopup">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Event</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form id="addEventForm" class="form-validate is-alter" enctype="multipart/form-data">
                        <div class="row gx-4 gy-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="event-title">Event Title <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="event-title" name="title" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-start-date">Start Date <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="date" id="event-start-date" name="start_date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-start-time">Start Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" id="event-start-time" name="start_time" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-end-date">End Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" id="event-end-date" name="end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-end-time">End Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" id="event-end-time" name="end_time" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-venue">Venue <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="event-venue" name="venue" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-organizers">Organizers <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="event-organizers" name="organizers" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-contacts">Contacts <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="event-contacts" name="contacts" class="form-control" placeholder="Contact number/email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label" for="event-status">Status</label>
                                    <div class="form-control-wrap">
                                        <input type="text" id="event-status" class="form-control" value="Auto-detected from Date" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="event-images">Event Images (Max 2)</label>
                                    <div class="form-control-wrap">
                                        <input type="file" id="event-images" name="images[]" class="form-control" accept="image/*" multiple>
                                        <small class="text-muted">Select up to 2 images.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="event-description">Event Description <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="event-description" name="description" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div id="event-form-alert" class="alert" style="display:none; padding:10px; margin-bottom:10px;"></div>
                            </div>
                            <div class="col-12">
                                <ul class="d-flex justify-content-between gx-4 mt-1">
                                    <li>
                                        <button type="submit" id="btnSubmitEvent" class="btn btn-primary">Add Event</button>
                                    </li>
                                    <li>
                                        <button type="button" data-dismiss="modal" class="btn btn-danger btn-dim">Discard</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="editEventPopup">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="#" id="editEventForm" class="form-validate is-alter">
                        <div class="row gx-4 gy-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="edit-event-title">Event Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="edit-event-title" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Start Date & Time</label>
                                    <div class="row gx-2">
                                        <div class="w-55">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-calendar"></em>
                                                </div>
                                                <input type="text" id="edit-event-start-date" class="form-control date-picker" data-date-format="yyyy-mm-dd" required>
                                            </div>
                                        </div>
                                        <div class="w-45">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-clock"></em>
                                                </div>
                                                <input type="text" id="edit-event-start-time" data-time-format="HH:mm:ss" class="form-control time-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">End Date & Time</label>
                                    <div class="row gx-2">
                                        <div class="w-55">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-calendar"></em>
                                                </div>
                                                <input type="text" id="edit-event-end-date" class="form-control date-picker" data-date-format="yyyy-mm-dd">
                                            </div>
                                        </div>
                                        <div class="w-45">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-clock"></em>
                                                </div>
                                                <input type="text" id="edit-event-end-time" data-time-format="HH:mm:ss" class="form-control time-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="edit-event-description">Event Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="edit-event-description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Event Category</label>
                                    <div class="form-control-wrap">
                                        <select id="edit-event-theme" class="select-calendar-theme form-control form-control-lg">
                                            <option value="event-primary">Company</option>
                                            <option value="event-success">Seminars </option>
                                            <option value="event-info">Conferences</option>
                                            <option value="event-warning">Meeting</option>
                                            <option value="event-danger">Business dinners</option>
                                            <option value="event-pink">Private</option>
                                            <option value="event-primary-dim">Auctions</option>
                                            <option value="event-success-dim">Networking events</option>
                                            <option value="event-info-dim">Product launches</option>
                                            <option value="event-warning-dim">Fundrising</option>
                                            <option value="event-danger-dim">Sponsored</option>
                                            <option value="event-pink-dim">Sports events</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <ul class="d-flex justify-content-between gx-4 mt-1">
                                    <li>
                                        <button id="updateEvent" type="submit" class="btn btn-primary">Update Event</button>
                                    </li>
                                    <li>
                                        <button data-dismiss="modal" data-toggle="modal" data-target="#deleteEventPopup" class="btn btn-danger btn-dim">Delete</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="previewEventPopup">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div id="preview-event-header" class="modal-header">
                    <h5 id="preview-event-title" class="modal-title"></h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div id="preview-event-details">
                        <!-- Dynamic details will be loaded here -->
                    </div>
                    <ul class="d-flex justify-content-end gx-4 mt-3">
                        <li>
                            <button data-dismiss="modal" class="btn btn-light">Close</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="deleteEventPopup">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body modal-body-lg text-center">
                    <div class="nk-modal py-4">
                        <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                        <h4 class="nk-modal-title">Are You Sure ?</h4>
                        <div class="nk-modal-text mt-n2">
                            <p class="text-soft">This event data will be removed permanently.</p>
                        </div>
                        <ul class="d-flex justify-content-center gx-4 mt-4">
                            <li>
                                <button data-dismiss="modal" id="deleteEvent" class="btn btn-success">Yes, Delete it</button>
                            </li>
                            <li>
                                <button data-dismiss="modal" data-toggle="modal" data-target="#editEventPopup" class="btn btn-danger btn-dim">Cancel</button>
                            </li>
                        </ul>
                    </div>
                </div><!-- .modal-body -->
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=2.2.0"></script>
    <script src="./assets/js/scripts.js?ver=2.2.0"></script>
    <script src="./assets/js/libs/fullcalendar.js?ver=2.2.0"></script>
    <script>
    $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            timeZone: 'UTC',
            initialView: NioApp.Win.width < NioApp.Break.md ? 'listWeek' : 'dayGridMonth',
            themeSystem: 'bootstrap',
            headerToolbar: {
                left: 'title prev,next',
                center: null,
                right: 'today dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            height: 800,
            contentHeight: 780,
            aspectRatio: 3,
            views: {
                dayGridMonth: {
                    dayMaxEventRows: 2
                }
            },
            nowIndicator: true,
            events: 'src/html/get_events.php', // dynamic event endpoint!
            eventMouseEnter: function(info) {
                $(info.el).popover({
                    template: '<div class="popover"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
                    title: info.event.title,
                    content: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover'
                });
                if (info.event.extendedProps.description) {
                    $(info.el).popover('show');
                }
            },
            eventMouseLeave: function(info) {
                $(info.el).popover('hide');
            },
            eventClick: function(info) {
                var title = info.event.title;
                var description = info.event.extendedProps.description || 'No description';
                var start = info.event.start;
                var end = info.event.end || start;
                
                var venue = info.event.extendedProps.venue || 'N/A';
                var organizers = info.event.extendedProps.organizers || 'N/A';
                var contacts = info.event.extendedProps.contacts || 'N/A';
                var status = info.event.extendedProps.status || 'Upcoming';
                var images = info.event.extendedProps.images || '';
                
                $('#preview-event-title').text(title);
                
                // Color-code header based on status
                var headerClass = 'bg-primary text-white';
                if (status === 'Completed') headerClass = 'bg-success text-white';
                if (status === 'Ongoing') headerClass = 'bg-info text-white';
                $('#preview-event-header').removeClass().addClass('modal-header ' + headerClass);
                
                var options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                var startStr = new Date(start).toLocaleDateString('en-US', options);
                var endStr = new Date(end).toLocaleDateString('en-US', options);
                
                // Format details
                var detailsHtml = '<div class="gy-3 py-1">' +
                                  '<p><strong>Venue:</strong> ' + venue + '</p>' +
                                  '<p><strong>Organizers:</strong> ' + organizers + '</p>' +
                                  '<p><strong>Contact:</strong> ' + contacts + '</p>' +
                                  '<p><strong>Start Date & Time:</strong> ' + startStr + '</p>' +
                                  '<p><strong>End Date & Time:</strong> ' + endStr + '</p>' +
                                  '<p><strong>Status:</strong> <span class="badge badge-pill badge-outline-dark">' + status + '</span></p>' +
                                  '<p><strong>Description:</strong> ' + description + '</p>';
                
                // If there are images, render them
                if (images) {
                    var imgList = images.split(',');
                    detailsHtml += '<div class="row mt-3">';
                    imgList.forEach(function(img) {
                        detailsHtml += '<div class="col-6 mb-2">' +
                                       '<img src="src/uploads/events/' + img + '" class="img-fluid rounded border" style="max-height:150px; width:100%; object-fit:cover;">' +
                                       '</div>';
                    });
                    detailsHtml += '</div>';
                }
                
                detailsHtml += '</div>';
                
                $('#preview-event-details').html(detailsHtml);
                $('#previewEventPopup').modal('show');
            }
        });
        calendar.render();

        // Form submit AJAX
        $('#addEventForm').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var btn = $('#btnSubmitEvent');
            var alertEl = $('#event-form-alert');
            
            // Validate max 2 images
            var fileInput = document.getElementById('event-images');
            if (fileInput.files.length > 2) {
                alertEl.removeClass().addClass('alert alert-danger').text('You cannot upload more than 2 images.').show();
                return;
            }
            
            btn.prop('disabled', true).text('Saving...');
            alertEl.hide();
            
            var formData = new FormData(form);
            
            // Use absolute path to avoid <base href="../"> resolving issues
            var scriptUrl = window.location.origin + '/cacyof/src/html/add_event_script.php';

            fetch(scriptUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.text(); })
            .then(function(text) {
                btn.prop('disabled', false).text('Add Event');
                try {
                    var data = JSON.parse(text);
                    if (data.status === 'success') {
                        alertEl.removeClass().addClass('alert alert-success').text(data.message).show();
                        form.reset();
                        setTimeout(function() {
                            $('#addEventPopup').modal('hide');
                            alertEl.hide();
                            calendar.refetchEvents();
                        }, 1500);
                    } else {
                        alertEl.removeClass().addClass('alert alert-danger').text(data.message).show();
                    }
                } catch(e) {
                    alertEl.removeClass().addClass('alert alert-danger').html('<strong>Server error:</strong><br><pre style="font-size:11px;white-space:pre-wrap;">' + text.substring(0, 500) + '</pre>').show();
                }
            })
            .catch(function(err) {
                btn.prop('disabled', false).text('Add Event');
                alertEl.removeClass().addClass('alert alert-danger').text('Network error: ' + err.message).show();
            });
        });
    });
    </script>
</body>
</html>