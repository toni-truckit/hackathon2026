<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\OGImage\OGImageGenerator;
use App\OGImage\OGImageRenderer;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeOGImageRenderer;

beforeEach(function (): void {
    Storage::fake('local');

    $this->renderer = new FakeOGImageRenderer;
    app()->instance(OGImageRenderer::class, $this->renderer);

    config()->set('og-image.enabled', true);
    config()->set('og-image.default_image');

    $this->page = StaticPage::query()->create([
        'title' => 'Dynamic Open Graph Images',
        'description' => 'How to render social cards on demand.',
        'slug' => 'og-test',
        'name' => null,
        'type' => PageType::ContentPage,
        'tags' => ['laravel', 'seo'],
        'content' => '<p>Body</p>',
    ]);
});

it('serves a generated open graph image', function (): void {
    $url = $this->page->ogImageUrl();

    expect($url)->toBeString();

    $response = $this->get($url);

    $response->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('image/png')
        ->and($this->renderer->calls)->toBe(1);
});

it('renders the image only once across repeated requests', function (): void {
    $this->get($this->page->ogImageUrl())->assertOk();
    $this->get($this->page->ogImageUrl())->assertOk();

    expect($this->renderer->calls)->toBe(1);
});

it('redirects to the current url when the fingerprint is stale', function (): void {
    $response = $this->get(route('og-image', [
        'type' => 'page',
        'key' => $this->page->slug,
        'hash' => '00000000',
    ]));

    $response->assertRedirect($this->page->ogImageUrl());
});

it('returns 404 for an unknown model type', function (): void {
    $this->get('/open-graph/unknown/whatever/00000000.png')->assertNotFound();
});

it('returns 404 for a missing record', function (): void {
    $this->get(route('og-image', [
        'type' => 'page',
        'key' => 'does-not-exist',
        'hash' => '00000000',
    ]))->assertNotFound();
});

it('falls back to the default image when rendering fails', function (): void {
    app()->instance(OGImageRenderer::class, new class implements OGImageRenderer
    {
        public function render(string $html, string $outputPath): void
        {
            throw new RuntimeException('render failed');
        }
    });

    $this->get($this->page->ogImageUrl())->assertNotFound();

    config()->set('og-image.default_image', 'https://example.com/fallback.png');

    $this->get($this->page->ogImageUrl())->assertRedirect('https://example.com/fallback.png');
});

it('disables generation when the feature is turned off', function (): void {
    config()->set('og-image.enabled', false);

    expect($this->page->ogImageUrl())->toBeNull()
        ->and($this->page->imageValue())->toBeNull();

    $this->get(route('og-image', [
        'type' => 'page',
        'key' => $this->page->slug,
        'hash' => '00000000',
    ]))->assertNotFound();
});

it('exposes the generated image through seo resolution', function (): void {
    expect($this->page->resolveSEO()->image)->toBe($this->page->ogImageUrl());
});

it('changes the fingerprint when the model changes', function (): void {
    $before = $this->page->ogImageFingerprint();

    $this->page->update(['title' => 'An updated title']);

    expect($this->page->refresh()->ogImageFingerprint())->not->toBe($before);
});

it('prunes stale images after regeneration', function (): void {
    $old = app(OGImageGenerator::class)->generate($this->page);

    $this->page->update(['title' => 'A brand new title']);

    $new = app(OGImageGenerator::class)->generate($this->page->refresh());

    expect($new)->not->toBe($old)
        ->and(Storage::disk('local')->exists($old))->toBeFalse()
        ->and(Storage::disk('local')->exists($new))->toBeTrue();
});
