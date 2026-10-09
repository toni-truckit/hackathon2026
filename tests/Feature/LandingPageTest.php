<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\Faq;
use App\Models\StaticPage;
use Livewire\Livewire;

beforeEach(function (): void {
    StaticPage::query()->create([
        'title' => 'Moving something big? Just ask Claude.',
        'description' => 'Landing page.',
        'slug' => 'landing-page',
        'name' => null,
        'type' => PageType::LandingPage,
        'tags' => [],
    ]);

    Faq::query()->create([
        'question' => 'Is it free?',
        'answer' => 'Getting a price in Claude is free and you only pay when you book on truckit.net.',
        'is_active' => true,
        'sort_order' => 1,
    ]);
});

it('renders the claude landing above the fold', function (): void {
    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toContain('Just ask Claude.')
        ->and($content)->toContain('Three steps, one question')
        ->and($content)->toContain('Copy a question, paste it into Claude')
        ->and($content)->toContain('id="how-it-works"')
        ->and($content)->toContain('id="try-asking"')
        ->and($content)->toContain('id="setup"')
        ->and($content)->toContain('"@type":"FAQPage"')
        ->and($content)->not->toContain('Contact us');
});

it('copies a sample prompt with alpine', function (): void {
    Livewire::test('landing.try-asking')
        ->assertSee('navigator.clipboard.writeText')
        ->assertSee('Copy');
});

it('toggles a landing faq inside the island', function (): void {
    $faq = Faq::query()->where('question', 'Is it free?')->firstOrFail();

    Livewire::test('landing.faq')
        ->assertSee('Is it free?')
        ->call('toggle', $faq->id)
        ->assertSet('open', $faq->id)
        ->call('toggle', $faq->id)
        ->assertSet('open', null);
});
