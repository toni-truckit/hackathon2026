<?php

declare(strict_types=1);

use App\Models\Faq;
use App\Models\StaticPage;

it('seeds seo friendly static pages', function (): void {
    $this->seed();

    $landing = StaticPage::query()->where('slug', 'landing-page')->firstOrFail();
    $about = StaticPage::query()->where('slug', 'about-us')->firstOrFail();

    foreach ([$landing, $about] as $page) {
        expect($page->seo?->meta_title)->toBeString()->not->toBeEmpty();

        $description = (string) $page->seo?->meta_description;

        expect($description)->toBeString()->not->toBeEmpty();
    }
});

it('refreshes seo values when seeded again', function (): void {
    $this->seed();

    $landing = StaticPage::query()->where('slug', 'landing-page')->firstOrFail();
    $landing->seo()->update(['meta_title' => 'Stale title']);

    $this->seed();

    expect($landing->refresh()->seo?->meta_title)->not->toBe('Stale title');
});

it('refreshes static page descriptions with current copy', function (): void {
    $this->seed();

    $about = StaticPage::query()->where('slug', 'about-us')->firstOrFail();

    expect($about->description)->toBeString()->not->toBeEmpty()
        ->and($about->content)->toContain('TruckIt');
});

it('seeds faq answers long enough for answer engines', function (): void {
    $this->seed();

    $answers = Faq::query()->pluck('answer');

    expect($answers)->toHaveCount(6);

    foreach ($answers as $answer) {
        $words = str_word_count(strip_tags((string) $answer));

        expect($words)->toBeGreaterThanOrEqual(20);
    }
});
