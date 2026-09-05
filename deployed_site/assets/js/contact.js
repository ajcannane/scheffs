(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contact-form');
    if (!form) return;

    var messageEl = form.querySelector('.form-message');
    var submitBtn = form.querySelector('[type="submit"]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      messageEl.className = 'form-message';
      messageEl.textContent = '';

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      submitBtn.disabled = true;
      submitBtn.value = 'Sending…';

      fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(new FormData(form)).toString(),
      })
        .then(function (res) {
          if (!res.ok) throw new Error('Network error');
          messageEl.className = 'form-message success';
          messageEl.textContent = 'Thank you — we\'ll be in touch shortly!';
          form.reset();
          if (window.grecaptcha) window.grecaptcha.reset();
          document.dispatchEvent(new CustomEvent('contact:sent'));
          // Fade the message out after 4 s so it doesn't linger
          setTimeout(function () {
            messageEl.style.transition = 'opacity 0.6s';
            messageEl.style.opacity = '0';
            setTimeout(function () {
              messageEl.className = 'form-message';
              messageEl.textContent = '';
              messageEl.style.transition = '';
              messageEl.style.opacity = '';
            }, 600);
          }, 4000);
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
