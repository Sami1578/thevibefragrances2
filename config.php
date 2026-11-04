<?php
// ---- Database settings: edit these to match your hosting / XAMPP ----
define('DB_HOST', 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com');
define('DB_PORT', 4000); // Crucial for TiDB Cloud
define('DB_USER', '3Ygd7e55uHcnWC9.root');
define('DB_PASS', 'pGQwDfCO7inGMteR');
define('DB_NAME', 'perfume_db');

// ---- Auto-detect the folder the site lives in (works in sub-folders too) ----
$__base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (substr($__base, -6) === '/admin') {
    $__base = substr($__base, 0, -6);
}
define('BASE_URL', $__base);
define('ROOT_PATH', __DIR__);
