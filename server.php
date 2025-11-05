<?php

/**
 * Laravel development server routing script
 * This script helps PHP's built-in server handle Laravel routing correctly
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Don't do anything if it's a real file
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Otherwise, pass everything to index.php
require_once __DIR__.'/public/index.php';
