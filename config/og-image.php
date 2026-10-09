<?php

declare(strict_types=1);

use App\Models\StaticPage;

return [

    /*
    |--------------------------------------------------------------------------
    | Enable OG Image Generation
    |--------------------------------------------------------------------------
    |
    | When disabled, models fall back to their uploaded OG image, cover media
    | and finally the configured default image. Useful for environments
    | without Node or a Chrome binary available.
    |
    */

    'enabled' => (bool) env('OG_IMAGE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | The prefix, route name and per-minute throttle applied to the public
    | image endpoint. Generated URLs embed a fingerprint, so responses
    | can be cached immutably.
    |
    */

    'route' => [
        'prefix' => 'open-graph',
        'name' => 'og-image',
        'throttle' => (int) env('OG_IMAGE_THROTTLE', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | OG Imageable Models
    |--------------------------------------------------------------------------
    |
    | Map of route aliases to models implementing the HasOGImage contract.
    | Add an entry here and use the InteractsWithOGImage trait on the model
    | to give it a dynamically generated social card.
    |
    */

    'models' => [
        'page' => StaticPage::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    |
    | The Blade view rendering the card. Individual models can override this
    | through their ogImageView() method.
    |
    */

    'view' => 'og.base',

    /*
    |--------------------------------------------------------------------------
    | Dimensions
    |--------------------------------------------------------------------------
    |
    | 1200x630 is the standard Open Graph size. A device scale factor of 2
    | renders the card in retina quality.
    |
    */

    'width' => 1200,
    'height' => 630,
    'device_scale_factor' => 2,

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | Generated images are stored on a private disk and streamed through the
    | controller, keeping them inaccessible unless requested directly.
    |
    */

    'disk' => 'local',
    'directory' => 'og-images',

    /*
    |--------------------------------------------------------------------------
    | Browser (Browsershot)
    |--------------------------------------------------------------------------
    |
    | Node and Chrome binary paths are required on servers where PHP-FPM does
    | not inherit the user's PATH. HTTPS errors are ignored outside of
    | production to support locally signed certificates (e.g. Herd).
    |
    */

    'browser' => [
        'node_binary' => env('OG_IMAGE_NODE_BINARY'),
        'npm_binary' => env('OG_IMAGE_NPM_BINARY'),
        'chrome_path' => env('OG_IMAGE_CHROME_PATH'),
        'timeout' => (int) env('OG_IMAGE_TIMEOUT', 120),
        'no_sandbox' => (bool) env('OG_IMAGE_NO_SANDBOX', true),
        'ignore_https_errors' => (bool) env('OG_IMAGE_IGNORE_HTTPS_ERRORS', env('APP_ENV', 'production') !== 'production'),
        'wait_until_network_idle' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Image
    |--------------------------------------------------------------------------
    |
    | Served (with a redirect) when rendering fails. Falls back to the site
    | settings OG image when left empty.
    |
    */

    'default_image' => env('OG_IMAGE_DEFAULT_IMAGE'),

];
