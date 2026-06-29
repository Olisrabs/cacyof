<form id="contact-form" class="contact-form" method="post" enctype="multipart/form-data">
    <div class="row gy-3">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <input id="name" class="form-control py-3" name="name" placeholder="Your Name *" type="text" required />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-3">
                <input id="email" class="form-control py-3" name="email" placeholder="Your Email *" type="email" required />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-3">
                <input id="phone" class="form-control py-3" name="phone" placeholder="Phone Number" type="text" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group mb-3">
                <textarea id="message" class="form-control py-3 textarea" rows="6" name="message" placeholder="Your Message *" required></textarea>
            </div>
            <button type="submit" name="submit" id="contact-submit" class="btn btn-primary btn-lg px-5 theme-btn mt-3">Send Message</button>
        </div>
    </div>
    <span class="clearfix"></span>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var contactForms = document.querySelectorAll('.contact-form');
    contactForms.forEach(function(formEl) {
        if (formEl.dataset.ajaxAttached) return;
        formEl.dataset.ajaxAttached = "true";
        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = formEl.querySelector('button[type="submit"]');
            var originalText = btn ? btn.innerHTML : 'Send Message';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Sending...';
            }

            var formData = new FormData(formEl);
            fetch('submit-contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                if (data.status === 'success') {
                    formEl.reset();
                    if (typeof showGlobalSuccessModal === 'function') {
                        showGlobalSuccessModal('Message Sent!', data.message);
                    } else {
                        alert(data.message);
                    }
                } else {
                    alert(data.message || 'Error sending message.');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                alert('Communication error. Please try again.');
            });
        });
    });
});
</script>