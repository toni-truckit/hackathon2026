<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Enums\UserRole;
use App\Filament\Clusters\Settings\Pages\ManageFrontSettings;
use App\Models\StaticPage;
use App\Models\User;
use App\Settings\FrontSettings;
use Livewire\Livewire;

beforeEach(function (): void {
    StaticPage::query()->create([
        'title' => 'Home',
        'description' => 'Landing page.',
        'slug' => 'landing-page',
        'name' => null,
        'type' => PageType::LandingPage,
        'tags' => [],
    ]);
});

it('saves front copy from the settings page', function (): void {
    $this->actingAs(User::factory()->create(['role' => UserRole::Developer]));

    Livewire::test(ManageFrontSettings::class)
        ->fillForm([
            'chat_price' => '$12.00 incl. GST',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(FrontSettings::class)->chat_price)->toBe('$12.00 incl. GST');
});

it('renders saved chat copy on the landing page', function (): void {
    $front = app(FrontSettings::class);
    $front->chat_price = '$12.00 incl. GST';
    $front->chat_user_message = 'Move a piano from Adelaide.';
    $front->save();

    $content = (string) $this->get(route('landing-page'))->getContent();

    expect($content)->toContain('$12.00 incl. GST')
        ->and($content)->toContain('Move a piano from Adelaide.');
});
