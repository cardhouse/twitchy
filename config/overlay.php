<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Overlay Key
    |--------------------------------------------------------------------------
    |
    | This value is used to secure access to the overlay route. It prevents
    | accidental access to the overlay from unauthorized sources. The key
    | should match the OVERLAY_KEY environment variable.
    |
    */

    'key' => env('OVERLAY_KEY', 'local'),
];
