var recaptchaWidgetId;

$(function () {

    $('#contactModal').on('shown.bs.modal', function () {
        // Render recaptcha on first open — avoids rendering into a display:none element
        if (recaptchaWidgetId === undefined) {
            recaptchaWidgetId = grecaptcha.render('recaptcha-widget', {
                sitekey: document.getElementById('recaptcha-widget').getAttribute('data-sitekey')
            });
        }
    });

    $('#contactModal').on('hidden.bs.modal', function () {
        // Reset form state when modal is closed without submitting
        $('#contact-form')[0].reset();
        $('#contact-form').find('.messages').html('');
        $('#contact-form .form-group').removeClass('has-error has-success');
        $('#contact-form .help-block').html('');
        if (recaptchaWidgetId !== undefined) {
            grecaptcha.reset(recaptchaWidgetId);
        }
    });

    $('#contact-form').validator();

    $('#contact-form').on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            $.ajax({
                type: "POST",
                url: "contact.php",
                data: $(this).serialize(),
                success: function (data) {
                    if (data.type === 'success') {
                        $('#contact-form')[0].reset();
                        if (recaptchaWidgetId !== undefined) {
                            grecaptcha.reset(recaptchaWidgetId);
                        }
                        $('#contactModal').modal('hide');
                        $('#successModal').modal('show');
                    } else {
                        var alertBox = '<div class="alert alert-danger alert-dismissable">'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                            + data.message + '</div>';
                        $('#contact-form').find('.messages').html(alertBox);
                    }
                }
            });
            return false;
        }
    });
});
