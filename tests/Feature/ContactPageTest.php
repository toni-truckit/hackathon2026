<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\Settings\SiteSettings;
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

    StaticPage::query()->create([
        'title' => 'Contact',
        'description' => 'Questions, feedback or partnership ideas welcome.',
        'slug' => 'contact',
        'name' => 'contact',
        'type' => PageType::Contact,
        'tags' => [],
        'content' => '<p>Get in touch.</p>',
    ]);

    $settings = app(SiteSettings::class);
    $settings->contact_email = 'hello@example.test';
    $settings->save();
});

it('renders the contact page with email and contact schema', function (): void {
    $content = (string) $this->get(route('contact.view'))->getContent();

    expect($content)->toContain('hello@example.test')
        ->and($content)->toContain('"@type":"ContactPage"')
        ->and($content)->toContain('"@type":"ContactPoint"')
        ->and($content)->toContain('rel="canonical" href="'.route('contact.view').'"');
});

it('shows the contact link in header and footer navigation', function (): void {
    $content = (string) $this->get(route('faq.view'))->getContent();

    expect(mb_substr_count($content, 'href="'.route('contact.view').'"'))->toBeGreaterThanOrEqual(2);
});

it('includes the contact page in the sitemap', function (): void {
    $content = (string) $this->get('/sitemap.xml')->getContent();

    expect($content)->toContain(route('contact.view'));
});

it('renders configured social profiles in the footer', function (): void {
    $social = app(SocialMediaSettings::class);
    $social->linkedin = 'https://linkedin.com/company/example';
    $social->facebook = 'https://facebook.com/example';
    $social->save();

    $content = (string) $this->get(route('contact.view'))->getContent();

    expect($content)->toContain('href="https://linkedin.com/company/example"')
        ->and($content)->toContain('href="https://facebook.com/example"')
        ->and($content)->toContain('rel="me noopener"');
});
