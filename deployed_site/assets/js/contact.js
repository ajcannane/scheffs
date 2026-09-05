(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contact-form');
    if (!form) return;

    var messageEl  = form.querySelector('.form-message');
    var submitBtn  = form.querySelector('[type="submit"]');
    var panel      = form.closest('.modal-panel');
    var successEl  = panel && panel.querySelector('.modal-success');

    function showSuccess() {
      // Hide the modal header + form, reveal the success panel
      panel.querySelectorAll('.section-eyebrow, .scribe, #contact-form').forEach(function (el) {
        el.hidden = true;
      });
      successEl.hidden = false;
      document.dispatchEvent(new CustomEvent('contact:sent'));
    }

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
          if (!res.ok) throw new Error('Something went wrong. Please try again.');
          form.reset();
          if (window.grecaptcha) window.grecaptcha.reset();
          if (successEl) {
            showSuccess();
          } else {
            messageEl.className = 'form-message success';
            messageEl.textContent = 'Thank you — we\'ll be in touch shortly!';
            document.dispatchEvent(new CustomEvent('contact:sent'));
          }
        })
        .catch(function (err) {
          messageEl.className = 'form-message error';
          messageEl.textContent = err.message || 'Something went wrong. Please try again.';
          submitBtn.disabled = false;
          submitBtn.value = 'Send message';
        });
    });
  });

})();
