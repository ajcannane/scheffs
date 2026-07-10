$(function () {

    // The off-canvas #navPanel (see main.js) stops all clicks inside it from
    // bubbling (util.js panel plugin), which stops Bootstrap's document-level
    // [data-toggle="modal"] handler from ever seeing the click. Bind directly
    // on #navPanel instead so it isn't blocked by that same stopPropagation.
    $('#navPanel').on('click', 'a[data-toggle="modal"]', function (e) {
        e.preventDefault();

        var target = $(this).attr('data-target');

        $('#navPanel').removeClass('visible');
        window.setTimeout(function () {
            $(target).modal('show');
        }, 500);
    });

    $('#contact-form').validator();

    $('#contact-form').on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            var url = "contact.php";

            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                success: function (data)
                {
                    if (data.type === 'success') {
                        $('#contact-form').find('.messages').empty();
                        $('#contact-form')[0].reset();
                        grecaptcha.reset();
                        $('#contactModal').modal('hide');
                        $('#successModal').modal('show');
                        return;
                    }

                    var messageAlert = 'alert-' + data.type;
                    var messageText = data.message;

                    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable" tabindex="-1"><a href="#" class="close" data-dismiss="alert" aria-hidden="true">&times;</a>' + messageText + '</div>';
                    if (messageAlert && messageText) {
                        var $alert = $('#contact-form').find('.messages').html(alertBox).find('.alert');
                        $alert[0].scrollIntoView({ block: 'start', behavior: 'smooth' });
                        $alert.focus();
                    }
                }
            });
            return false;
        }
    })
});