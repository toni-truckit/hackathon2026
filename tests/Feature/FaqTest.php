<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Enums\UserRole;
use App\Models\Faq;
use App\Models\StaticPage;
use App\Models\User;

beforeEach(function (): void {
    StaticPage::query()->create([
        'title' => 'Frequently Asked Questions',
        'description' => 'Answers to common questions about this site.',
        'slug' => 'faq',
        'name' => 'faq',
        'type' => PageType::Faq,
        'tags' => ['faq'],
        'content' => '<p>Intro</p>',
    ]);

    StaticPage::query()->create([
        'title' => 'Example Site',
        'description' => 'Landing page.',
        'slug' => 'landing-page',
        'name' => null,
        'type' => PageType::LandingPage,
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

it('renders the faq page with active questions only', function (): void {
    $response = $this->get(route('faq.view'));

    $response->assertOk();

    $content = (string) $response->getContent();

    expect($content)->toContain('What stack powers this site?')
        ->and($content)->toContain('Laravel, Livewire, and Filament.')
        ->and($content)->not->toContain('Is there a license?');
});

it('emits faq page schema on the faq page', function (): void {
    $content = (string) $this->get(route('faq.view'))->getContent();

    expect($content)->toContain('"@type":"FAQPage"')
        ->and($content)->toContain('"@type":"Question"')
        ->and($content)->toContain('"@type":"Answer"')
        ->and($content)->toContain('"name":"What stack powers this site?"')
        ->and($content)->toContain('rel="canonical" href="'.route('faq.view').'"');
});

it('renders faq questions as headings', function (): void {
    $content = (string) $this->get(route('faq.view'))->getContent();

    expect(mb_substr_count($content, '<h3'))->toBe(1);
});

it('includes the faq page in the sitemap', function (): void {
    $content = (string) $this->get('/sitemap.xml')->getContent();

    expect($content)->toContain(route('faq.view'));
});

it('emits faq schema on the homepage without rendering the faq section', function (): void {
    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toContain('"@type":"FAQPage"')
        ->and($content)->not->toContain('x-data="{ open: 0 }"')
        ->and($content)->not->toContain('View all FAQs');
});

it('labels the faq page as FAQ in the navigation', function (): void {
    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toMatch('/>\s*FAQ\s*<\/a>/')
        ->and($content)->toMatch('/>\s*FAQ\s*<span/')
        ->and($content)->not->toMatch('/>\s*Frequently Asked Questions\s*<\/a>/');
});

it('manages faqs in the admin panel', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Developer]);

    $this->actingAs($admin)->get('/admin/faqs')->assertOk();
});
