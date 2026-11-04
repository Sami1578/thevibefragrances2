<?php
// ---- Database settings: edit these to match your hosting / XAMPP ----
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'thevibe_fragrances');

// ---- Auto-detect the folder the site lives in (works in sub-folders too) ----
$__base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (substr($__base, -6) === '/admin') {
    $__base = substr($__base, 0, -6);
}
define('BASE_URL', $__base);
define('ROOT_PATH', __DIR__);
