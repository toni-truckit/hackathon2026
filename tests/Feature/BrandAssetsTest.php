<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\Settings\SocialMediaSettings;

beforeEach(function (): void {
    StaticPage::query()->create([
        'title' => 'Example Site',
        'description' => 'Landing page.',
        'slug' => 'landing-page',
        'name' => null,
        'type' => PageType::LandingPage,
        'tags' => [],
    ]);
});

it('ships brand assets', function (): void {
    foreach (['favicon.svg', 'favicon.ico', 'apple-touch-icon.png', 'images/logo.png'] as $file) {
        expect(public_path($file))->toBeFile();
    }
});

it('links the favicon and touch icons', function (): void {
    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toContain('href="'.asset('favicon.svg').'"')
        ->and($content)->toContain('href="'.asset('apple-touch-icon.png').'"');
});

it('emits organization and website schema with social profiles', function (): void {
    $settings = app(SocialMediaSettings::class);
    $settings->linkedin = 'https://linkedin.com/company/example';
    $settings->facebook = 'https://facebook.com/example';
    $settings->save();

    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toContain('"@type":"Organization"')
        ->and($content)->toContain('"@type":"WebSite"')
        ->and($content)->not->toContain('"@type":"SearchAction"')
        ->and($content)->toContain(str_replace('/', '\/', 'https://linkedin.com/company/example'))
        ->and($content)->toContain(str_replace('/', '\/', 'https://facebook.com/example'));
});
