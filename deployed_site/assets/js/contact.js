$(function () {

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

                    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-hidden="true">&times;</a>' + messageText + '</div>';
                    if (messageAlert && messageText) {
                        $('#contact-form').find('.messages').html(alertBox);
                    }
                }
            });
            return false;
        }
    })
});