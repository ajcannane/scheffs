(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contact-form');
    if (!form) return;

    var messageEl = form.querySelector('.form-message');
    var submitBtn = form.querySelector('[type="submit"]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Clear previous message
      messageEl.className = 'form-message';
      messageEl.textContent = '';

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      submitBtn.disabled = true;
      submitBtn.value = 'Sending…';

      fetch('contact.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
      })
        .then(function (res) {
          if (!res.ok) throw new Error('Network error');
          return res.json();
        })
        .then(function (data) {
          if (data.type === 'success') {
            messageEl.className = 'form-message success';
            messageEl.textContent = data.message;
            form.reset();
            if (window.grecaptcha) window.grecaptcha.reset();
          } else {
            throw new Error(data.message);
          }
        })
        .catch(function (err) {
          messageEl.className = 'form-message error';
          messageEl.textContent = err.message || 'Something went wrong. Please try again.';
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.value = 'Send message';
        });
    });
  });

})();
