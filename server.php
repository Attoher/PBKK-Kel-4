<?php

/**
 * Laravel development server routing script
 * This script helps PHP's built-in server handle Laravel routing correctly
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// SPECIAL HANDLING FOR PDF FILES - Force correct headers!
if (preg_match('/^\/pdfs\/(.+\.pdf)$/i', $uri, $matches)) {
    $file = __DIR__ . '/public/pdfs/' . $matches[1];
    
    if (file_exists($file) && is_file($file)) {
        // Force PDF headers
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        header('Cache-Control: public, must-revalidate, max-age=0');
        
        // Output file directly
        readfile($file);
        exit;
    }
}

// Don't do anything if it's a real file
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Otherwise, pass everything to index.php
require_once __DIR__.'/public/index.php';
