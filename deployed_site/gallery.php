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
      <li><a href="contact-us.php">Contact</a></li>
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
  <a href="contact-us.php">Contact</a>
</div>

<!-- Gallery hero -->
<section class="gallery-hero" aria-label="Gallery hero">
  <div class="gallery-hero-content">
    <p class="gallery-hero-eyebrow">Handcrafted in Adelaide</p>
    <h1 class="gallery-hero-title">Our work</h1>
    <a href="contact-us.php" class="btn btn-outline gallery-hero-cta">Make an enquiry</a>
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

<script src="assets/js/main.js?v=3"></script>
<script src="assets/js/dist/photoswipe.min.js?v=3"></script>
<script src="assets/js/dist/photoswipe-ui-default.min.js?v=3"></script>
<script src="assets/js/ps.js?v=3"></script>

</body>
</html>
