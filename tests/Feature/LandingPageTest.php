<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;

it('renders landing page when landing static page is missing', function (): void {
    expect(StaticPage::query()->where('type', PageType::LandingPage)->exists())->toBeFalse();

    $this->get(route('landing-page'))->assertSuccessful();
});
