<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
} elseif (file_exists($maintenance = __DIR__.'/../bebras_admin/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader & bootstrap application...
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
    (require_once __DIR__.'/../bootstrap/app.php')
        ->handleRequest(Request::capture());
} elseif (file_exists(__DIR__.'/../bebras_admin/vendor/autoload.php')) {
    require __DIR__.'/../bebras_admin/vendor/autoload.php';
    (require_once __DIR__.'/../bebras_admin/bootstrap/app.php')
        ->handleRequest(Request::capture());
} else {
    throw new RuntimeException('Unable to locate autoload.php. Please ensure Composer dependencies are installed.');
}

