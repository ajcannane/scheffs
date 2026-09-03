(function () {
  'use strict';

  var nav = document.querySelector('.site-nav');
  var toggle = document.querySelector('.nav-toggle');
  var mobileMenu = document.querySelector('.mobile-menu');

  // Nav darkens on scroll (stays dark on gallery page which has no .hero)
  function updateNav() {
    if (!document.querySelector('.hero')) {
      nav.classList.add('scrolled');
      return;
    }
    nav.classList.toggle('scrolled', window.scrollY > 30);
  }

  updateNav();
  window.addEventListener('scroll', updateNav, { passive: true });

  // Mobile hamburger
  function closeMenu() {
    toggle.classList.remove('open');
    mobileMenu.classList.remove('open');
    mobileMenu.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
  }

  if (toggle && mobileMenu) {
    toggle.addEventListener('click', function () {
      var open = toggle.classList.toggle('open');
      mobileMenu.classList.toggle('open', open);
      mobileMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    mobileMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });
  }

  // Contact modal
  var modalOverlay = document.getElementById('contact-modal');
  var modalCloseBtn = modalOverlay && modalOverlay.querySelector('.modal-close');

  function openModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.add('open');
    modalOverlay.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    var firstInput = modalOverlay.querySelector('input, textarea');
    if (firstInput) setTimeout(function () { firstInput.focus(); }, 50);
  }

  function closeModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.remove('open');
    modalOverlay.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
  }

  document.querySelectorAll('[data-modal="contact-modal"]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      openModal();
    });
  });

  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);

  if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
      if (e.target === modalOverlay) closeModal();
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (modalOverlay && modalOverlay.classList.contains('open')) closeModal();
      if (mobileMenu && mobileMenu.classList.contains('open')) closeMenu();
    }
  });

  document.addEventListener('contact:sent', function () {
    setTimeout(closeModal, 1800);
  });

  // Gallery category nav — highlight active section on scroll
  var catLinks = document.querySelectorAll('.gallery-cat-nav a');
  if (catLinks.length && 'IntersectionObserver' in window) {
    var sections = document.querySelectorAll('.gallery-section[id]');

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          catLinks.forEach(function (l) { l.classList.remove('active'); });
          var link = document.querySelector('.gallery-cat-nav a[href="#' + entry.target.id + '"]');
          if (link) link.classList.add('active');
        }
      });
    }, { rootMargin: '-25% 0px -65% 0px' });

    sections.forEach(function (s) { observer.observe(s); });
  }

})();
