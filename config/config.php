<?php
// config/config.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');

// Change this for production:
define('APP_ENV', 'dev');

define('BASE_URL', '/araf_blog'); // if your folder name is /araf_blog in htdocs
define('APP_NAME', 'Araf Blog');

define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

// Simple error mode
if (APP_ENV === 'dev') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}