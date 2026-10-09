<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\OGImageController;
use App\OGImage\BrowsershotRenderer;
use App\OGImage\OGImageRenderer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class OGImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OGImageRenderer::class, BrowsershotRenderer::class);
    }

    public function boot(): void
    {
        Route::get(
            sprintf('%s/{type}/{key}/{hash}.png', config('og-image.route.prefix')),
            OGImageController::class,
        )
            ->where('type', '[a-z0-9-]+')
            ->where('key', '[A-Za-z0-9-_]+')
            ->where('hash', '[a-f0-9]{8}')
            ->middleware(sprintf('throttle:%d,1', config('og-image.route.throttle')))
            ->name((string) config('og-image.route.name'));
    }
}
