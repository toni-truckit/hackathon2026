<?php

declare(strict_types=1);

use App\Settings\SiteSettings;

it('serves a generated ai policy file', function (): void {
    $response = $this->get('/ai.txt');

    $response->assertOk();

    $content = (string) $response->getContent();

    expect($response->headers->get('Content-Type'))->toContain('text/plain')
        ->and($content)->toContain('GPTBot')
        ->and($content)->toContain('ClaudeBot')
        ->and($content)->toContain('PerplexityBot')
        ->and($content)->toContain('Google-Extended')
        ->and($content)->toContain('CCBot')
        ->and($content)->toContain('OAI-SearchBot')
        ->and($content)->toContain('Disallow: /admin')
        ->and($content)->toContain('Sitemap: '.url('/sitemap.xml'))
        ->and($content)->toContain('Attribution:');
});

it('includes the contact email and attribution in the generated policy', function (): void {
    $settings = app(SiteSettings::class);
    $settings->contact_email = 'agents@example.test';
    $settings->save();

    $content = (string) $this->get('/ai.txt')->getContent();

    expect($content)->toContain('Contact: agents@example.test')
        ->and($content)->toContain('Attribution:');
});

it('serves the ai policy from the settings override when present', function (): void {
    $settings = app(SiteSettings::class);
    $settings->ai_txt = "User-Agent: *\nDisallow: /private";
    $settings->save();

    $content = (string) $this->get('/ai.txt')->getContent();

    expect($content)->toContain('Disallow: /private')
        ->and($content)->not->toContain('GPTBot');
});
