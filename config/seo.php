<?php

declare(strict_types=1);

use AchyutN\LaravelSEO\Models\SEO;

return [
    'model' => SEO::class,
    'sitemap' => '/sitemap.xml',
    'database' => config('database.default', 'mysql'),

    'site_name' => config('app.name'),

    'twitter' => [
        '@username' => env('SEO_TWITTER_USERNAME'),
    ],

    'robots' => [
        'default' => 'max-snippet:-1,max-image-preview:large,max-video-preview:-1',
        'force_default' => false,
    ],

    'title' => [
        'infer_title_from_url' => true,
        'suffix' => sprintf(' - %s', config('app.name')),
        'homepage_title' => null,
    ],
];
