<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\Faq;
use App\Models\StaticPage;
use App\Settings\SiteSettings;

beforeEach(function (): void {
    StaticPage::query()->create([
        'title' => 'About Us',
        'description' => 'Who we are.',
        'slug' => 'about-us',
        'name' => null,
        'type' => PageType::ContentPage,
        'tags' => [],
    ]);

    Faq::query()->create([
        'question' => 'What stack powers this site?',
        'answer' => 'Laravel, Livewire, and Filament.',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    Faq::query()->create([
        'question' => 'Is there a license?',
        'answer' => 'Yes, MIT licensed.',
        'is_active' => false,
        'sort_order' => 2,
    ]);
});

it('serves an llms file with pages, faqs and optional links', function (): void {
    $settings = app(SiteSettings::class);

    $response = $this->get('/llms.txt');

    $response->assertOk();

    $content = (string) $response->getContent();

    expect($response->headers->get('Content-Type'))->toContain('text/plain')
        ->and($content)->toContain('# '.$settings->name)
        ->and($content)->toContain('## Pages')
        ->and($content)->toContain('About Us')
        ->and($content)->toContain('## FAQs')
        ->and($content)->toContain('What stack powers this site?')
        ->and($content)->not->toContain('Is there a license?')
        ->and($content)->toContain('## Optional')
        ->and($content)->toContain(url('/sitemap.xml'))
        ->and($content)->toContain(url('/ai.txt'));
});

it('refreshes the llms file when content changes', function (): void {
    $this->get('/llms.txt');

    StaticPage::query()->where('slug', 'about-us')->firstOrFail()->update(['title' => 'Our Story']);

    expect((string) $this->get('/llms.txt')->getContent())->toContain('Our Story');
});
