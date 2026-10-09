<?php

declare(strict_types=1);

use App\Settings\SiteSettings;
use Illuminate\Support\Facades\Route;

it('serves robots.txt from the site settings', function (): void {
    $settings = app(SiteSettings::class);
    $settings->robots_txt = "User-agent: *\nDisallow: /admin";
    $settings->save();

    $response = $this->get('/robots.txt');

    $response->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('text/plain')
        ->and($response->getContent())->toContain('User-agent: *')
        ->and($response->getContent())->toContain('Disallow: /admin')
        ->and($response->getContent())->toContain('Sitemap: '.url('/sitemap.xml'));
});

it('falls back to a valid robots policy when the setting is empty', function (): void {
    $settings = app(SiteSettings::class);
    $settings->robots_txt = '';
    $settings->save();

    $response = $this->get('/robots.txt');

    $response->assertOk();

    expect($response->getContent())->toContain('User-agent: *')
        ->and($response->getContent())->toContain('Sitemap: '.url('/sitemap.xml'));
});

it('does not duplicate the sitemap directive', function (): void {
    $settings = app(SiteSettings::class);
    $settings->robots_txt = "User-agent: *\nSitemap: ".url('/sitemap.xml');
    $settings->save();

    $content = (string) $this->get('/robots.txt')->getContent();

    expect(mb_substr_count($content, 'Sitemap:'))->toBe(1);
});

it('keeps the open graph image route registered', function (): void {
    expect(Route::has('og-image'))->toBeTrue();
});
