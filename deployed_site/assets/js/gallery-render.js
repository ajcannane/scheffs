(function () {
  'use strict';

  var CATEGORIES = {
    kitchens: { label: 'Kitchens',             heading: 'Kitchens',                      folder: 'Kitchen',         alt: 'Kitchen' },
    benchtop: { label: 'Benchtops',            heading: 'Benchtops',                     folder: 'Benchtop',        alt: 'Benchtop' },
    doors:    { label: 'Doors &amp; Drawers',  heading: 'Doors &amp; Drawers',           folder: 'DoorsAndDrawers', alt: 'Doors and drawers' },
    pantry:   { label: 'Pantry',               heading: 'Pantry',                        folder: 'Pantry',          alt: 'Pantry' },
    vanity:   { label: 'Vanity',               heading: 'Vanity',                        folder: 'Vanity',          alt: 'Vanity' },
    wallunit: { label: 'Wall Units',           heading: 'Wall &amp; Entertainment Units', folder: 'WallUnit',        alt: 'Wall unit' },
    wardrobe: { label: 'Wardrobes',            heading: 'Wardrobes',                     folder: 'Wardrobe',        alt: 'Wardrobe' },
    workshop: { label: 'Workshop',             heading: 'Workshop',                      folder: 'Workshop',        alt: 'Workshop' },
  };

  fetch('images/gallery-manifest.json')
    .then(function (r) {
      if (!r.ok) throw new Error('Manifest not found');
      return r.json();
    })
    .then(function (manifest) {
      Object.keys(CATEGORIES).forEach(function (slug) {
        var cat = CATEGORIES[slug];
        var images = manifest[slug] || [];
        if (!images.length) return;

        var section = document.getElementById(slug);
        var navLink = document.querySelector('.gallery-cat-nav a[href="#' + slug + '"]');
        if (!section || !navLink) return;

        var grid = section.querySelector('.gallery-grid');

        images.forEach(function (img) {
          var folder = cat.folder;
          var thumbJpg  = 'images/' + folder + '/' + img.id + '_' + img.tw + 'x' + img.th + '.jpg';
          var thumbWebp = 'images/' + folder + '/' + img.id + '_' + img.tw + 'x' + img.th + '.webp';
          var fullJpg   = 'images/' + folder + '/' + img.id + '_' + img.fw + 'x' + img.fh + '.jpg';
          var dataSize  = img.fw + 'x' + img.fh;
          var alt = cat.alt;

          var figure = document.createElement('figure');
          var link = document.createElement('a');
          link.href = './' + fullJpg;
          link.setAttribute('data-size', dataSize);

          if (img.webp) {
            var picture = document.createElement('picture');
            var source = document.createElement('source');
            source.srcset = './' + thumbWebp;
            source.type = 'image/webp';
            var imgEl = document.createElement('img');
            imgEl.src = './' + thumbJpg;
            imgEl.alt = alt;
            imgEl.loading = 'lazy';
            picture.appendChild(source);
            picture.appendChild(imgEl);
            link.appendChild(picture);
          } else {
            var imgEl = document.createElement('img');
            imgEl.src = './' + thumbJpg;
            imgEl.alt = alt;
            imgEl.loading = 'lazy';
            link.appendChild(imgEl);
          }

          var figcaption = document.createElement('figcaption');
          figcaption.textContent = alt;

          figure.appendChild(link);
          figure.appendChild(figcaption);
          grid.appendChild(figure);
        });

        section.hidden = false;
        navLink.hidden = false;
      });

      if (typeof initPhotoSwipeFromDOM === 'function') {
        initPhotoSwipeFromDOM('.my-gallery');
      }
    })
    .catch(function (err) {
      console.error('Gallery failed to load:', err);
    });

})();
