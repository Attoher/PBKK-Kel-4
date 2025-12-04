<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ClamAV Enabled
    |--------------------------------------------------------------------------
    |
    | Set to false to bypass scanning (useful for local dev without ClamAV).
    |
    */
    'enabled' => env('CLAMAV_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | ClamAV Binary Path
    |--------------------------------------------------------------------------
    |
    | Path to the clamscan binary.
    |
    */
    'path' => env('CLAMAV_PATH', 'clamscan'),
];
