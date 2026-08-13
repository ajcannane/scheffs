<?php
$mapsKey     = getenv('GOOGLE_MAPS_API_KEY') ?: '';
$recaptchaKey = getenv('RECAPTCHA_SITE_KEY') ?: '';

const CATEGORIES = [
    'kitchens' => ['label' => 'Kitchens',             'heading' => 'Kitchens',                    'folder' => 'Kitchen',        'alt' => 'Kitchen'],
    'benchtop' => ['label' => 'Benchtops',            'heading' => 'Benchtops',                   'folder' => 'Benchtop',       'alt' => 'Benchtop'],
    'doors'    => ['label' => 'Doors &amp; Drawers',  'heading' => 'Doors &amp; Drawers',         'folder' => 'DoorsAndDrawers','alt' => 'Doors and drawers'],
    'pantry'   => ['label' => 'Pantry',               'heading' => 'Pantry',                      'folder' => 'Pantry',         'alt' => 'Pantry'],
    'vanity'   => ['label' => 'Vanity',               'heading' => 'Vanity',                      'folder' => 'Vanity',         'alt' => 'Vanity'],
    'wallunit' => ['label' => 'Wall Units',           'heading' => 'Wall &amp; Entertainment Units','folder' => 'WallUnit',      'alt' => 'Wall unit'],
    'wardrobe' => ['label' => 'Wardrobes',            'heading' => 'Wardrobes',                   'folder' => 'Wardrobe',       'alt' => 'Wardrobe'],
    'workshop' => ['label' => 'Workshop',             'heading' => 'Workshop',                    'folder' => 'Workshop',       'alt' => 'Workshop'],
];

$manifestPath = __DIR__ . '/images/gallery-manifest.json';
$manifest = [];
if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true) ?? [];
}
foreach (array_keys(CATEGORIES) as $slug) {
    if (!isset($manifest[$slug])) $manifest[$slug] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gallery &mdash; Scheff's Kitchens &amp; Cabinets</title>
  <meta name="description" content="Browse photos of custom kitchens, vanities, benchtops, wardrobes, and cabinetry handcrafted by Scheff's Kitchens &amp; Cabinets in Adelaide.">
  <link rel="icon" href="images/logo.png" type="image/png">
  <meta property="og:type" content="website">
  <meta property="og:title" content="Gallery &mdash; Scheff's Kitchens &amp; Cabinets">
  <meta property="og:description" content="Browse photos of custom kitchens, vanities, benchtops, wardrobes, and cabinetry handcrafted in Adelaide since 1996.">
  <meta property="og:image" content="images/Kitchen/45_960x720.jpg">
  <meta property="og:url" content="https://www.scheffskitchens.com.au/gallery.php">
  <link rel="stylesheet" href="assets/css/main.css?v=3">
  <link rel="stylesheet" href="assets/js/dist/photoswipe.css">
  <link rel="stylesheet" href="assets/js/dist/default-skin/default-skin.css">
</head>
<body>

<!-- Navigation -->
<nav class="site-nav" aria-label="Main navigation">
  <div class="nav-inner">
    <a href="index.html" class="nav-wordmark" aria-label="Scheff's Kitchens &amp; Cabinets &mdash; home">
      <span class="nav-wordmark-name">Scheff's</span>
      <span class="nav-wordmark-sub">Kitchens &amp; Cabinets</span>
    </a>
    <ul class="nav-links" role="list">
      <li><a href="index.html">Home</a></li>
      <li><a href="gallery.php">Gallery</a></li>
      <li><a href="#" data-modal="contact-modal" class="nav-enquiry-link">Contact</a></li>
    </ul>
    <button class="nav-toggle" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Mobile menu -->
<div class="mobile-menu" aria-hidden="true">
  <a href="index.html">Home</a>
  <a href="gallery.php">Gallery</a>
  <a href="#" data-modal="contact-modal">Contact</a>
</div>

<!-- Gallery hero -->
<section class="gallery-hero" aria-label="Gallery hero">
  <div class="gallery-hero-content">
    <p class="gallery-hero-eyebrow">Handcrafted in Adelaide</p>
    <h1 class="gallery-hero-title">Our work</h1>
    <button class="btn btn-outline gallery-hero-cta" data-modal="contact-modal">Make an enquiry</button>
  </div>
</section>

<!-- Category nav -->
<nav class="gallery-cat-nav" aria-label="Gallery categories">
  <div class="gallery-cat-nav-inner">
    <?php foreach (CATEGORIES as $slug => $cat): ?>
    <a href="#<?= $slug ?>"><?= $cat['label'] ?></a>
    <?php endforeach; ?>
  </div>
</nav>

<?php foreach (CATEGORIES as $slug => $cat): ?>
<!-- <?= $cat['heading'] ?> -->
<section class="gallery-section" id="<?= $slug ?>">
  <div class="gallery-section-inner">
    <div class="scribe">
      <h2 class="gallery-section-heading"><?= $cat['heading'] ?></h2>
    </div>
    <div class="gallery-grid my-gallery">
      <?php if (empty($manifest[$slug])): ?>
      <p style="color:var(--text-muted);font-size:.85rem;padding:.5rem 0">No photos yet.</p>
      <?php endif; ?>
      <?php foreach ($manifest[$slug] as $img):
        $folder   = $cat['folder'];
        $thumbJpg = "images/{$folder}/{$img['id']}_{$img['tw']}x{$img['th']}.jpg";
        $thumbWebp = "images/{$folder}/{$img['id']}_{$img['tw']}x{$img['th']}.webp";
        $fullJpg  = "images/{$folder}/{$img['id']}_{$img['fw']}x{$img['fh']}.jpg";
        $dataSize = "{$img['fw']}x{$img['fh']}";
        $alt      = htmlspecialchars($cat['alt']);
      ?>
      <figure>
        <a href="./<?= $fullJpg ?>" data-size="<?= $dataSize ?>">
          <?php if (!empty($img['webp'])): ?>
          <picture>
            <source srcset="./<?= $thumbWebp ?>" type="image/webp">
            <img src="./<?= $thumbJpg ?>" alt="<?= $alt ?>" loading="lazy">
          </picture>
          <?php else: ?>
          <img src="./<?= $thumbJpg ?>" alt="<?= $alt ?>" loading="lazy">
          <?php endif; ?>
        </a>
        <figcaption><?= $cat['alt'] ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<!-- Contact -->
<section class="section section--dark" id="contact">
  <div class="section-inner">
    <p class="section-eyebrow">Get in touch</p>
    <div class="scribe">
      <h2 class="section-heading">Contact us</h2>
    </div>
    <div class="contact-grid">

      <!-- Info -->
      <div class="contact-info">
        <img src="images/Director-Grant-Scheuffele_180x240.jpg" alt="Grant Scheuffele, director" class="contact-info-photo" loading="lazy">
        <p class="contact-info-name">Grant Scheuffele</p>
        <address>
          Scheff's Kitchens &amp; Cabinets<br>
          4/18 Circuit Drive<br>
          Hendon SA 5014
        </address>
        <div class="contact-info-item">
          <svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
          <a href="mailto:grant@scheffskitchens.com.au">grant@scheffskitchens.com.au</a>
        </div>
        <div class="contact-info-item">
          <svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
          <a href="tel:+61884456234">08 8445 6234</a>
        </div>
        <div class="contact-info-item">
          <svg class="contact-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 2H7c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-5 18c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm5-4H7V4h10v12z"/></svg>
          <a href="tel:+61418991079">0418 991 079</a>
        </div>
        <div class="contact-social">
          <a href="https://www.facebook.com/scheffskitchens/" aria-label="Facebook">fb</a>
          <a href="https://www.instagram.com/scheffskitchens/" aria-label="Instagram">ig</a>
        </div>
        <button class="btn btn-primary contact-enquiry-btn" data-modal="contact-modal">Make an enquiry</button>
      </div>

      <!-- Map -->
      <div class="contact-map">
        <iframe
          src="https://www.google.com/maps/embed/v1/place?q=place_id:Eio0LzE4IENpcmN1aXQgRHIsIEhlbmRvbiBTQSA1MDE0LCBBdXN0cmFsaWE&key=<?= htmlspecialchars($mapsKey) ?>"
          allowfullscreen
          referrerpolicy="strict-origin-when-cross-origin"
          loading="lazy"
          title="Scheff's Kitchens location map"></iframe>
      </div>

    </div>
  </div>
</section>

<!-- Footer -->
<footer class="site-footer">
  <p>&copy; Scheff's Kitchens &amp; Cabinets &mdash; 2026</p>
</footer>

<!-- PhotoSwipe root element -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="pswp__bg"></div>
  <div class="pswp__scroll-wrap">
    <div class="pswp__container">
      <div class="pswp__item"></div>
      <div class="pswp__item"></div>
      <div class="pswp__item"></div>
    </div>
    <div class="pswp__ui pswp__ui--hidden">
      <div class="pswp__top-bar">
        <div class="pswp__counter"></div>
        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
        <div class="pswp__preloader">
          <div class="pswp__preloader__icn">
            <div class="pswp__preloader__cut">
              <div class="pswp__preloader__donut"></div>
            </div>
          </div>
        </div>
      </div>
      <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
      <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
      <div class="pswp__caption">
        <div class="pswp__caption__center"></div>
      </div>
    </div>
  </div>
</div>

<!-- Contact modal -->
<div class="modal-overlay" id="contact-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal-panel">
    <button class="modal-close" aria-label="Close enquiry form">&times;</button>
    <p class="section-eyebrow">Get in touch</p>
    <div class="scribe">
      <h2 class="modal-heading" id="modal-title">Make an enquiry</h2>
    </div>
    <form id="contact-form" method="post" action="contact.php" novalidate>
      <div class="form-message" role="alert"></div>
      <div class="contact-form">
        <div class="form-row">
          <div class="form-field">
            <label for="form_name">First name *</label>
            <input type="text" id="form_name" name="name" placeholder="First name" required autocomplete="given-name">
          </div>
          <div class="form-field">
            <label for="form_lastname">Last name *</label>
            <input type="text" id="form_lastname" name="surname" placeholder="Last name" required autocomplete="family-name">
          </div>
        </div>
        <div class="form-row">
          <div class="form-field">
            <label for="form_email">Email *</label>
            <input type="email" id="form_email" name="email" placeholder="you@example.com" required autocomplete="email">
          </div>
          <div class="form-field">
            <label for="form_phone">Phone</label>
            <input type="tel" id="form_phone" name="phone" placeholder="Optional" autocomplete="tel">
          </div>
        </div>
        <div class="form-field">
          <label for="form_message">Message *</label>
          <textarea id="form_message" name="message" placeholder="Tell us about your project&hellip;" required></textarea>
        </div>
        <div class="form-recaptcha">
          <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaKey) ?>" data-theme="dark"></div>
        </div>
        <div class="form-submit">
          <input type="submit" class="btn btn-primary" value="Send message">
        </div>
      </div>
    </form>
  </div>
</div>

<script src="assets/js/main.js?v=3"></script>
<script src="assets/js/contact.js?v=3"></script>
<script src="assets/js/dist/photoswipe.min.js?v=3"></script>
<script src="assets/js/dist/photoswipe-ui-default.min.js?v=3"></script>
<script src="assets/js/ps.js?v=3"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>
