<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\StaticPage;
use App\Settings\SiteSettings;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class LlmsTxtController
{
    public function __invoke(SiteSettings $settings): Response
    {
        $content = Cache::remember('seo:llms-txt', now()->addHour(), function () use ($settings): string {
            $lines = [
                '# '.$settings->name,
                '',
                '> '.$settings->description,
                '',
                '## Pages',
                '',
            ];

            StaticPage::query()->orderBy('type')->get()->each(function (StaticPage $page) use (&$lines): void {
                $url = $page->getURLValue();

                if (blank($url)) {
                    return;
                }

                $lines[] = sprintf(
                    '- [%s](%s): %s',
                    $page->getTitleValue(),
                    $url,
                    Str::limit((string) $page->getDescriptionValue(), 140),
                );
            });

            $lines[] = '';
            $lines[] = '## FAQs';
            $lines[] = '';

            Faq::query()->active()->ordered()->get()->each(function (Faq $faq) use (&$lines): void {
                $lines[] = '**'.$faq->question.'**';
                $lines[] = strip_tags($faq->answer);
                $lines[] = '';
            });

            $lines[] = '## Optional';
            $lines[] = '';
            $lines[] = sprintf('- [Sitemap](%s): XML sitemap', url('/sitemap.xml'));
            $lines[] = sprintf('- [Sitemap (plain text)](%s): Plain text list of URLs', url('/sitemap.txt'));
            $lines[] = sprintf('- [robots.txt](%s): Crawler directives', route('robots'));
            $lines[] = sprintf('- [ai.txt](%s): AI usage and attribution policy', route('ai'));

            return implode("\n", $lines)."\n";
        });

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
