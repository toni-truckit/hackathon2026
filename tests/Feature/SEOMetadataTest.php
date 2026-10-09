<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\Settings\SiteSettings;

beforeEach(function (): void {
    config()->set('seo.site_name', 'Example Site');
    config()->set('seo.twitter.@username', 'examplesite');

    $site = app(SiteSettings::class);
    $site->name = 'Example Site';
    $site->save();

    $this->page = StaticPage::query()->create([
        'title' => 'SEO Metadata',
        'description' => 'A description for metadata testing.',
        'slug' => 'seo-metadata',
        'name' => null,
        'type' => PageType::ContentPage,
        'tags' => [],
        'content' => '<p>Body</p>',
    ]);
});

it('resolves the page title for seo metadata', function (): void {
    $data = $this->page->getDynamicSEOData();

    expect($data->title)->toBe('SEO Metadata');
});

it('emits a single robots tag with vendor directives', function (): void {
    $content = (string) $this->get(route('page.view', $this->page))->getContent();

    expect(mb_substr_count($content, 'name="robots"'))->toBe(1)
        ->and($content)->toContain('index, follow, max-snippet:-1,max-image-preview:large,max-video-preview:-1');
});

it('marks pages as noindex outside of production', function (): void {
    $this->app['env'] = 'local';

    $content = (string) $this->get(route('page.view', $this->page))->getContent();

    expect(mb_substr_count($content, 'name="robots"'))->toBe(1)
        ->and($content)->toContain('content="noindex, nofollow"');
});

it('outputs website type for static pages', function (): void {
    expect($this->page->getDynamicSEOData()->type)->toBe('website');

    $content = (string) $this->get(route('page.view', $this->page))->getContent();

    expect($content)->toContain('property="og:type" content="website"');
});

it('emits the site name as og site name', function (): void {
    $content = (string) $this->get(route('page.view', $this->page))->getContent();

    expect($content)->toContain('property="og:site_name" content="Example Site"');
});

it('emits twitter site and creator tags from config', function (): void {
    $content = (string) $this->get(route('page.view', $this->page))->getContent();

    expect($content)->toContain('name="twitter:site" content="@examplesite"')
        ->and($content)->toContain('name="twitter:creator" content="@examplesite"');
});

it('respects stored robots and og title overrides', function (): void {
    $this->page->seo()->update([
        'robots' => ['noindex'],
        'og_title' => 'Custom OG Title',
    ]);

    $data = $this->page->refresh()->getDynamicSEOData();

    expect($data->robots)->toBe('noindex')
        ->and($data->openGraphTitle)->toBe('Custom OG Title');
});
