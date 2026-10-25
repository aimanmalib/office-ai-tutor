<?php

declare(strict_types=1);

// Router for PHP's built-in server (development only) — mirrors the Apache rewrite used in production:
//   php -S localhost:8000 -t app/public dev/router.php
// Real files are served as-is; every other path goes to the front controller laman.php.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($uri !== '/' && is_file($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    return false;
}
chdir($_SERVER['DOCUMENT_ROOT']);
require $_SERVER['DOCUMENT_ROOT'] . '/laman.php';
return true;
