<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Settings\SiteSettings;
use Illuminate\Http\Response;

final class RobotsController
{
    public function __invoke(SiteSettings $settings): Response
    {
        $content = mb_trim((string) $settings->robots_txt);

        if ($content === '') {
            $content = implode("\n", [
                'User-agent: *',
                'Allow: /',
                'Disallow: /admin',
                'Disallow: /pulse',
            ]);
        }

        if (! str_contains($content, 'Sitemap:')) {
            $content .= "\n\nSitemap: ".url('/sitemap.xml');
        }

        return response($content."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
