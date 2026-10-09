<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\OGImage\OGImageGenerator;
use Illuminate\Support\Facades\Storage;

it('renders a pixel perfect card with browsershot', function (): void {
    if (! env('RUN_OG_RENDER')) {
        $this->markTestSkipped('Set RUN_OG_RENDER=1 to run the real Browsershot render.');
    }

    Storage::fake('local');

    $page = StaticPage::query()->create([
        'title' => 'Rendering Open Graph images with Browsershot',
        'description' => 'A real render smoke test.',
        'slug' => 'og-render-test',
        'name' => null,
        'type' => PageType::ContentPage,
        'tags' => ['laravel'],
        'content' => '<p>Body</p>',
    ]);

    $path = app(OGImageGenerator::class)->generate($page, force: true);

    $absolutePath = Storage::disk('local')->path($path);

    expect(is_file($absolutePath))->toBeTrue();

    [$width, $height] = getimagesize($absolutePath);

    expect($width)->toBe(2400)
        ->and($height)->toBe(1260);
});
