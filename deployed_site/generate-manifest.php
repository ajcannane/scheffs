<?php
// One-time manifest generator.
// Run via: docker exec <container> php /var/www/html/generate-manifest.php
// Delete this file from the server after running.
if (php_sapi_name() !== 'cli') { http_response_code(404); exit; }

$categories = [
    'kitchens' => 'Kitchen',
    'benchtop' => 'Benchtop',
    'doors'    => 'DoorsAndDrawers',
    'pantry'   => 'Pantry',
    'vanity'   => 'Vanity',
    'wallunit' => 'WallUnit',
    'wardrobe' => 'Wardrobe',
    'workshop' => 'Workshop',
];

$imageBase   = __DIR__ . '/images';
$manifest    = [];
$webpOk      = function_exists('imagewebp') && (imagetypes() & IMG_WEBP);

if (!$webpOk) {
    echo "WARNING: WebP not supported by this PHP/GD build. JPEG-only manifest will be generated.\n";
}

foreach ($categories as $slug => $folder) {
    $dir = $imageBase . '/' . $folder;
    $manifest[$slug] = [];

    if (!is_dir($dir)) {
        echo "WARNING: directory not found: $dir\n";
        continue;
    }

    // Group .jpg files by numeric ID
    $byId = [];
    foreach (glob($dir . '/*.jpg') as $file) {
        $base = basename($file, '.jpg');
        if (!preg_match('/^(\d+)_(\d+)x(\d+)$/', $base, $m)) continue;
        $id  = $m[1];
        $w   = (int) $m[2];
        $h   = (int) $m[3];
        $byId[$id][] = ['file' => $file, 'w' => $w, 'h' => $h, 'px' => $w * $h];
    }

    foreach ($byId as $id => $variants) {
        usort($variants, fn($a, $b) => $a['px'] - $b['px']);

        if (count($variants) < 2) {
            echo "WARNING: fewer than 2 variants for ID $id in $folder — skipping\n";
            continue;
        }

        // 2nd-smallest = display thumbnail (skips the 180x135 mini-thumb)
        $thumb = $variants[1];
        // Largest = full/lightbox
        $full  = end($variants);

        // Generate WebP for display thumbnail if not already present
        $webpPath = substr($thumb['file'], 0, -4) . '.webp';
        $webp = false;
        if ($webpOk) {
            if (!file_exists($webpPath)) {
                $img = @imagecreatefromjpeg($thumb['file']);
                if ($img) {
                    imagewebp($img, $webpPath, 85);
                    imagedestroy($img);
                    echo "WebP: " . basename($webpPath) . "\n";
                }
            }
            $webp = file_exists($webpPath);
        }

        $manifest[$slug][] = [
            'id'   => $id,
            'tw'   => $thumb['w'],
            'th'   => $thumb['h'],
            'fw'   => $full['w'],
            'fh'   => $full['h'],
            'webp' => $webp,
        ];
    }

    usort($manifest[$slug], fn($a, $b) => (int) $a['id'] - (int) $b['id']);
    echo "Category $slug: " . count($manifest[$slug]) . " images\n";
}

file_put_contents($imageBase . '/gallery-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo "\nDone. Manifest written to images/gallery-manifest.json\n";
