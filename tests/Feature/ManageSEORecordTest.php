<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Enums\UserRole;
use App\Filament\Resources\StaticPages\Pages\ManageStaticPageSEO;
use App\Models\StaticPage;
use App\Models\User;
use Livewire\Livewire;

it('persists seo overrides against the model morph columns', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Developer]);

    $this->actingAs($admin);

    $page = StaticPage::query()->create([
        'title' => 'SEO managed page',
        'description' => 'Description',
        'slug' => 'seo-managed',
        'name' => null,
        'type' => PageType::ContentPage,
        'tags' => [],
        'content' => '<p>Body</p>',
    ]);

    Livewire::test(ManageStaticPageSEO::class, ['record' => $page->slug])
        ->fillForm(['meta_title' => 'Custom SEO title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->refresh()->seo?->meta_title)->toBe('Custom SEO title');
});
