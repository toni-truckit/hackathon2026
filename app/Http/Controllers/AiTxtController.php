<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Settings\SiteSettings;
use Illuminate\Http\Response;

final class AiTxtController
{
    /** @var array<int, string> */
    private const AGENTS = [
        'GPTBot',
        'ClaudeBot',
        'PerplexityBot',
        'Google-Extended',
        'CCBot',
        'OAI-SearchBot',
    ];

    public function __invoke(SiteSettings $settings): Response
    {
        $override = mb_trim((string) $settings->ai_txt);

        $content = $override !== '' ? $override : $this->generate($settings);

        return response($content."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function generate(SiteSettings $settings): string
    {
        $lines = [
            '# ai.txt for '.$settings->name,
            '',
            'User-Agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /pulse',
            '',
        ];

        foreach (self::AGENTS as $agent) {
            $lines[] = 'User-Agent: '.$agent;
            $lines[] = 'Allow: /';
            $lines[] = '';
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        if (filled($settings->contact_email)) {
            $lines[] = 'Contact: '.$settings->contact_email;
        }

        $lines[] = '';
        $lines[] = sprintf(
            'Attribution: Content from %s may be used with clear attribution and a link to the original URL.',
            $settings->name,
        );

        return implode("\n", $lines);
    }
}
