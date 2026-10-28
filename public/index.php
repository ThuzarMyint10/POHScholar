<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve an uploaded file directly if the built-in PHP dev server is being
// used and the request maps to an existing static file (mirrors what a
// real webserver - IIS, Apache, nginx - already does for you in
// production; this block is a no-op / never reached under IIS).
if (PHP_SAPI === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($uri !== '/' && file_exists(__DIR__.$uri)) {
        return false;
    }
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
