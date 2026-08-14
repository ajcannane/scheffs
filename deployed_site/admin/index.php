<?php
// ── Session hardening ─────────────────────────────────────────────────────────
const SESSION_TIMEOUT = 4 * 3600; // seconds of inactivity before logout

session_set_cookie_params([
    'lifetime' => 0,          // expire when browser closes
    'path'     => '/admin/',
    'secure'   => true,       // HTTPS only
    'httponly' => true,       // no JS access
    'samesite' => 'Lax',
]);

// Store sessions in a private directory so they aren't co-mingled with other
// tenants' sessions in /tmp (relevant on GoDaddy shared hosting).
// Falls back to the PHP default if the directory isn't writable (Docker dev).
$_sdir = __DIR__ . '/data';
if (!is_dir($_sdir)) @mkdir($_sdir, 0700, true);
if (is_writable($_sdir)) session_save_path($_sdir);
unset($_sdir);

session_start();

// Expire idle sessions
if (!empty($_SESSION['admin_auth'])) {
    if (time() - ($_SESSION['last_active'] ?? 0) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF'] . '?expired=1');
        exit;
    }
    $_SESSION['last_active'] = time();
}

// ── Config ───────────────────────────────────────────────────────────────────
const VALID_CATEGORIES = [
    'kitchens' => 'Kitchen',
    'benchtop' => 'Benchtop',
    'doors'    => 'DoorsAndDrawers',
    'pantry'   => 'Pantry',
    'vanity'   => 'Vanity',
    'wallunit' => 'WallUnit',
    'wardrobe' => 'Wardrobe',
    'workshop' => 'Workshop',
];
const CAT_LABELS = [
    'kitchens' => 'Kitchens',
    'benchtop' => 'Benchtops',
    'doors'    => 'Doors & Drawers',
    'pantry'   => 'Pantry',
    'vanity'   => 'Vanity',
    'wallunit' => 'Wall Units',
    'wardrobe' => 'Wardrobes',
    'workshop' => 'Workshop',
];
const THUMB_MAX  = 320;   // px
const FULL_MAX   = 960;   // px
const HERO_MAX   = 1920;  // px
const MANIFEST   = __DIR__ . '/../images/gallery-manifest.json';
const IMAGE_BASE = __DIR__ . '/../images';
const HERO_IMAGE = IMAGE_BASE . '/hero.jpg';

// ── Helpers ───────────────────────────────────────────────────────────────────
function adminPassword(): string {
    return (string) getenv('ADMIN_PASSWORD');
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_auth']);
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('CSRF mismatch.');
    }
}

function readManifest(): array {
    if (!file_exists(MANIFEST)) return array_fill_keys(array_keys(VALID_CATEGORIES), []);
    $data = json_decode(file_get_contents(MANIFEST), true) ?? [];
    foreach (array_keys(VALID_CATEGORIES) as $slug) {
        if (!isset($data[$slug])) $data[$slug] = [];
    }
    return $data;
}

function writeManifest(array $data): void {
    $tmp = MANIFEST . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    rename($tmp, MANIFEST);
}

function nextId(): string {
    $max = 0;
    foreach (VALID_CATEGORIES as $folder) {
        $dir = IMAGE_BASE . '/' . $folder;
        if (!is_dir($dir)) continue;
        foreach (glob($dir . '/*.jpg') as $file) {
            if (preg_match('/^(\d+)_/', basename($file), $m)) {
                $max = max($max, (int) $m[1]);
            }
        }
    }
    return (string) ($max + 1);
}

function scaleImage(GdImage $src, int $srcW, int $srcH, int $maxPx): array {
    if ($srcW <= $maxPx && $srcH <= $maxPx) {
        return [$src, $srcW, $srcH];
    }
    $ratio = $srcW > $srcH ? $maxPx / $srcW : $maxPx / $srcH;
    $dstW  = (int) round($srcW * $ratio);
    $dstH  = (int) round($srcH * $ratio);
    $dst   = imagecreatetruecolor($dstW, $dstH);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
    return [$dst, $dstW, $dstH];
}

function saveJpeg(GdImage $img, string $path): void {
    imagejpeg($img, $path, 88);
}

function saveWebp(GdImage $img, string $path): void {
    if (function_exists('imagewebp')) {
        imagewebp($img, $path, 85);
    }
}

// ── Brute-force protection ────────────────────────────────────────────────────
// Lock files live in /tmp (always writable by the web server; not web-accessible)
function lockFile(string $ip): string {
    return sys_get_temp_dir() . '/scheffs_lk_' . hash('sha256', $ip) . '.json';
}

function getLock(string $ip): array {
    $f = lockFile($ip);
    if (!file_exists($f)) return ['attempts' => 0, 'until' => 0];
    return json_decode(file_get_contents($f), true) ?: ['attempts' => 0, 'until' => 0];
}

function saveLock(string $ip, array $data): void {
    file_put_contents(lockFile($ip), json_encode($data), LOCK_EX);
}

function clearLock(string $ip): void {
    $f = lockFile($ip);
    if (file_exists($f)) @unlink($f);
}

// ── Auth ──────────────────────────────────────────────────────────────────────
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
        $lock = getLock($ip);

        if ($lock['until'] > time()) {
            $mins  = (int) ceil(($lock['until'] - time()) / 60);
            $error = "Too many failed attempts. Try again in {$mins} minute(s).";
        } else {
            sleep(1); // constant-time baseline — ~1 attempt/second regardless of password
            $pw       = $_POST['password'] ?? '';
            $expected = adminPassword();
            if ($expected !== '' && password_verify($pw, $expected)) {
                clearLock($ip);
                session_regenerate_id(true);
                $_SESSION['admin_auth']  = true;
                $_SESSION['last_active'] = time();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
            $attempts = $lock['attempts'] + 1;
            $until    = $attempts >= 5 ? time() + 900 : 0; // 15-minute lockout after 5 failures
            saveLock($ip, ['attempts' => $attempts, 'until' => $until]);
            $error = $until > 0
                ? 'Too many failed attempts. Locked for 15 minutes.'
                : 'Incorrect password.';
        }
    }

    if ($action === 'logout' && isLoggedIn()) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isLoggedIn() && $action === 'upload') {
        verifyCsrf();

        $slug   = $_POST['category'] ?? '';
        $folder = VALID_CATEGORIES[$slug] ?? null;

        if (!$folder) {
            $error = 'Invalid category.';
        } elseif (empty($_FILES['photo']['tmp_name'])) {
            $error = 'No file uploaded.';
        } else {
            $tmp  = $_FILES['photo']['tmp_name'];
            $fi   = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($fi, $tmp);
            finfo_close($fi);

            if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
                $error = 'Only JPEG or PNG files are accepted.';
            } else {
                $src = $mime === 'image/png' ? imagecreatefrompng($tmp) : imagecreatefromjpeg($tmp);
                if (!$src) {
                    $error = 'Could not read image file.';
                } else {
                    $srcW = imagesx($src);
                    $srcH = imagesy($src);

                    $id  = nextId();
                    $dir = IMAGE_BASE . '/' . $folder;
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    // Generate thumb (320w)
                    [$thumb, $tw, $th] = scaleImage($src, $srcW, $srcH, THUMB_MAX);
                    $thumbJpg  = "{$dir}/{$id}_{$tw}x{$th}.jpg";
                    $thumbWebp = "{$dir}/{$id}_{$tw}x{$th}.webp";
                    saveJpeg($thumb, $thumbJpg);
                    saveWebp($thumb, $thumbWebp);
                    if ($thumb !== $src) imagedestroy($thumb);

                    // Generate full (960w)
                    [$full, $fw, $fh] = scaleImage($src, $srcW, $srcH, FULL_MAX);
                    $fullJpg = "{$dir}/{$id}_{$fw}x{$fh}.jpg";
                    saveJpeg($full, $fullJpg);
                    if ($full !== $src) imagedestroy($full);
                    imagedestroy($src);

                    $webpOk = file_exists($thumbWebp);

                    $manifest = readManifest();
                    $manifest[$slug][] = [
                        'id'   => $id,
                        'tw'   => $tw,
                        'th'   => $th,
                        'fw'   => $fw,
                        'fh'   => $fh,
                        'webp' => $webpOk,
                    ];
                    writeManifest($manifest);

                    $successMsg = "Uploaded image #{$id} to " . CAT_LABELS[$slug] . '.';
                }
            }
        }
    }

    if (isLoggedIn() && $action === 'delete') {
        verifyCsrf();

        $slug   = $_POST['category'] ?? '';
        $id     = $_POST['id'] ?? '';
        $folder = VALID_CATEGORIES[$slug] ?? null;

        if ($folder && preg_match('/^\d+$/', $id)) {
            $manifest = readManifest();
            $entry = null;
            foreach ($manifest[$slug] as $e) {
                if ($e['id'] === $id) { $entry = $e; break; }
            }
            if ($entry) {
                $dir = IMAGE_BASE . '/' . $folder;
                foreach (glob("{$dir}/{$id}_*.jpg") as $f)  { @unlink($f); }
                foreach (glob("{$dir}/{$id}_*.webp") as $f) { @unlink($f); }
                $manifest[$slug] = array_values(
                    array_filter($manifest[$slug], fn($e) => $e['id'] !== $id)
                );
                writeManifest($manifest);
                $successMsg = "Deleted image #{$id} from " . CAT_LABELS[$slug] . '.';
            }
        }

        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=' . urlencode($slug));
        exit;
    }

    if (isLoggedIn() && $action === 'upload_hero') {
        verifyCsrf();

        if (empty($_FILES['hero_photo']['tmp_name'])) {
            $error = 'No file uploaded.';
        } else {
            $tmp  = $_FILES['hero_photo']['tmp_name'];
            $fi   = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($fi, $tmp);
            finfo_close($fi);

            if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
                $error = 'Only JPEG or PNG files are accepted.';
            } else {
                $src = $mime === 'image/png' ? imagecreatefrompng($tmp) : imagecreatefromjpeg($tmp);
                if (!$src) {
                    $error = 'Could not read image file.';
                } else {
                    [$out, ,] = scaleImage($src, imagesx($src), imagesy($src), HERO_MAX);
                    saveJpeg($out, HERO_IMAGE);
                    if ($out !== $src) imagedestroy($out);
                    imagedestroy($src);
                    $successMsg = 'Hero image updated. Hard-refresh the homepage to see it (Ctrl+Shift+R / Cmd+Shift+R).';
                }
            }
        }
    }
}

$activeTab  = $_GET['tab'] ?? 'kitchens';
if (!array_key_exists($activeTab, VALID_CATEGORIES)) $activeTab = 'kitchens';
$manifest   = isLoggedIn() ? readManifest() : [];
$csrf       = isLoggedIn() ? csrfToken() : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gallery Admin — Scheff's Kitchens</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; font-size: 15px; background: #1a1a1a; color: #e0e0e0; min-height: 100vh; }
    a { color: #c09060; }
    .page { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }

    /* Login */
    .login-wrap { display: flex; align-items: center; justify-content: center; min-height: 80vh; }
    .login-box { background: #252525; border: 1px solid #333; border-radius: 8px; padding: 2.5rem 2rem; width: 100%; max-width: 380px; }
    .login-box h1 { font-size: 1.3rem; margin-bottom: 1.5rem; }
    .field { margin-bottom: 1rem; }
    .field label { display: block; font-size: .85rem; color: #aaa; margin-bottom: .4rem; }
    .field input { width: 100%; padding: .6rem .8rem; background: #333; border: 1px solid #444; border-radius: 5px; color: #e0e0e0; font-size: 1rem; }
    .btn { display: inline-block; padding: .6rem 1.4rem; background: #c09060; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: .95rem; font-weight: 600; text-decoration: none; }
    .btn:hover { background: #d0a070; }
    .btn-danger { background: #a03030; }
    .btn-danger:hover { background: #c03030; }
    .btn-sm { padding: .35rem .8rem; font-size: .8rem; }
    .alert { padding: .7rem 1rem; border-radius: 5px; margin-bottom: 1.2rem; font-size: .9rem; }
    .alert-error { background: #4a1c1c; border: 1px solid #a03030; color: #f0a0a0; }
    .alert-success { background: #1c3a1c; border: 1px solid #3a9030; color: #a0e0a0; }

    /* Header */
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #333; }
    .admin-header h1 { font-size: 1.3rem; }

    /* Upload panel */
    .upload-panel { background: #252525; border: 1px solid #333; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; }
    .upload-panel h2 { font-size: 1rem; margin-bottom: 1rem; color: #aaa; text-transform: uppercase; letter-spacing: .05em; }
    .upload-row { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; }
    .upload-row .field { margin: 0; flex: 1; min-width: 180px; }
    .upload-row select { width: 100%; padding: .6rem .8rem; background: #333; border: 1px solid #444; border-radius: 5px; color: #e0e0e0; font-size: .95rem; }
    .upload-row input[type=file] { width: 100%; padding: .5rem .8rem; background: #333; border: 1px solid #444; border-radius: 5px; color: #e0e0e0; font-size: .9rem; }

    /* Category tabs */
    .tab-nav { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: 1.5rem; }
    .tab-nav a { padding: .5rem 1rem; border-radius: 5px; text-decoration: none; font-size: .9rem; border: 1px solid #444; color: #ccc; }
    .tab-nav a:hover { background: #333; }
    .tab-nav a.active { background: #c09060; color: #fff; border-color: #c09060; }

    /* Image grid */
    .img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
    .img-card { background: #252525; border: 1px solid #333; border-radius: 6px; overflow: hidden; }
    .img-card img { width: 100%; height: 135px; object-fit: cover; display: block; }
    .img-card-footer { padding: .5rem .6rem; display: flex; align-items: center; justify-content: space-between; }
    .img-card-id { font-size: .75rem; color: #888; }
    .empty { color: #666; font-style: italic; padding: 1rem 0; }

    @media (max-width: 600px) {
      .upload-row { flex-direction: column; }
      .img-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
    }
  </style>
</head>
<body>

<?php if (!isLoggedIn()): ?>

<div class="login-wrap">
  <div class="login-box">
    <h1>Gallery Admin</h1>
    <?php if (!empty($_GET['expired'])): ?>
    <div class="alert alert-error">Session expired. Please sign in again.</div>
    <?php elseif ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="action" value="login">
      <div class="field">
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" autofocus required>
      </div>
      <button type="submit" class="btn" style="width:100%;margin-top:.5rem">Sign in</button>
    </form>
  </div>
</div>

<?php else: ?>

<div class="page">
  <div class="admin-header">
    <h1>Gallery Admin</h1>
    <form method="post" style="display:inline">
      <input type="hidden" name="action" value="logout">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <button type="submit" class="btn btn-sm" style="background:#444">Log out</button>
    </form>
  </div>

  <?php if (!empty($error)): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($successMsg)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
  <?php endif; ?>

  <!-- Hero image -->
  <div class="upload-panel">
    <h2>Hero image</h2>
    <?php if (file_exists(HERO_IMAGE)): ?>
    <img src="../images/hero.jpg?v=<?= filemtime(HERO_IMAGE) ?>"
         alt="Current hero image"
         style="width:100%;max-height:180px;object-fit:cover;border-radius:4px;margin-bottom:.5rem;display:block">
    <p style="font-size:.8rem;color:#888;margin-bottom:1rem">Last updated: <?= date('j M Y, g:ia', filemtime(HERO_IMAGE)) ?></p>
    <?php else: ?>
    <p style="font-size:.8rem;color:#666;font-style:italic;margin-bottom:1rem">No hero image set.</p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="upload_hero">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="upload-row">
        <div class="field">
          <label for="hero_photo">Replace hero (JPEG or PNG, max 20 MB)</label>
          <input type="file" id="hero_photo" name="hero_photo" accept="image/jpeg,image/png" required>
        </div>
        <div><button type="submit" class="btn">Upload</button></div>
      </div>
    </form>
  </div>

  <!-- Gallery photos -->
  <div class="upload-panel">
    <h2>Add photo</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="upload">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="upload-row">
        <div class="field">
          <label for="cat">Category</label>
          <select id="cat" name="category">
            <?php foreach (CAT_LABELS as $slug => $label): ?>
            <option value="<?= $slug ?>" <?= $slug === $activeTab ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="photo">Photo (JPEG or PNG, max 20 MB)</label>
          <input type="file" id="photo" name="photo" accept="image/jpeg,image/png" required>
        </div>
        <div>
          <button type="submit" class="btn">Upload</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Category tabs -->
  <div class="tab-nav">
    <?php foreach (CAT_LABELS as $slug => $label): ?>
    <a href="?tab=<?= $slug ?>" class="<?= $slug === $activeTab ? 'active' : '' ?>"><?= htmlspecialchars($label) ?> (<?= count($manifest[$slug]) ?>)</a>
    <?php endforeach; ?>
  </div>

  <!-- Image grid for active category -->
  <?php
  $folder  = VALID_CATEGORIES[$activeTab];
  $images  = $manifest[$activeTab];
  ?>
  <?php if (empty($images)): ?>
  <p class="empty">No photos yet in this category.</p>
  <?php else: ?>
  <div class="img-grid">
    <?php foreach ($images as $img):
      $thumbPath = "../images/{$folder}/{$img['id']}_{$img['tw']}x{$img['th']}.jpg";
    ?>
    <div class="img-card">
      <img src="<?= htmlspecialchars($thumbPath) ?>" alt="Image #<?= $img['id'] ?>" loading="lazy">
      <div class="img-card-footer">
        <span class="img-card-id">#<?= $img['id'] ?> &nbsp; <?= $img['fw'] ?>&times;<?= $img['fh'] ?></span>
        <form method="post" onsubmit="return confirm('Delete image #<?= $img['id'] ?>?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="csrf" value="<?= $csrf ?>">
          <input type="hidden" name="category" value="<?= $activeTab ?>">
          <input type="hidden" name="id" value="<?= $img['id'] ?>">
          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<?php endif; ?>

</body>
</html>
