<?php

declare(strict_types=1);

namespace App\Providers;

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Services\SitemapService as VendorSitemapService;
use App\Http\Controllers\AiTxtController;
use App\Http\Controllers\LlmsTxtController;
use App\Http\Controllers\RobotsController;
use App\Models\Faq;
use App\Models\StaticPage;
use App\Models\User;
use App\Services\SitemapService;
use App\Settings\SiteSettings;
use App\Settings\SocialMediaSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RalphJSmit\Laravel\SEO\Facades\SEOManager;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use RalphJSmit\Laravel\SEO\Support\TwitterCardTag;
use RalphJSmit\Laravel\SEO\TagCollection;

final class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VendorSitemapService::class, SitemapService::class);
    }

    public function boot(): void
    {
        Route::get('/robots.txt', RobotsController::class)->name('robots');
        Route::get('/llms.txt', LlmsTxtController::class)->name('llms');
        Route::get('/ai.txt', AiTxtController::class)->name('ai');

        $forgetLlmsCache = function (): void {
            Cache::forget('seo:llms-txt');
        };

        foreach ([StaticPage::class, User::class, Faq::class, SEO::class] as $model) {
            $model::saved($forgetLlmsCache);
            $model::deleted($forgetLlmsCache);
        }

        SEOManager::SEODataTransformer(function (SEOData $data): SEOData {
            $handle = $this->twitterHandle();

            if (filled($handle) && in_array($data->twitter_username, [null, '@'], true)) {
                $data->twitter_username = $handle;
            }

            $data->schema ??= SchemaCollection::make();
            $data->schema->add(fn (): array => $this->organizationSchema());
            $data->schema->add(fn (): array => $this->websiteSchema());

            return $data;
        });

        SEOManager::tagTransformer(function (TagCollection $tags): TagCollection {
            $handle = $this->twitterHandle();

            if (filled($handle)) {
                $tags->push(new TwitterCardTag('creator', $handle));
            }

            return $tags;
        });
    }

    private function twitterHandle(): ?string
    {
        $handle = config('seo.twitter.@username');

        if (blank($handle)) {
            return null;
        }

        return str_starts_with($handle, '@') ? $handle : '@'.$handle;
    }

    /**
     * @return array<string, mixed>
     */
    private function organizationSchema(): array
    {
        $logo = rescue(fn (): ?string => app(SiteSettings::class)->logo, null, false);

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => $this->siteName(),
            'url' => url('/'),
            'logo' => filled($logo) ? url('storage/'.$logo) : url('images/logo.png'),
            'sameAs' => $this->socialProfiles() ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => $this->siteName(),
            'url' => url('/'),
            'publisher' => ['@id' => url('/').'#organization'],
        ];
    }

    private function siteName(): string
    {
        $name = rescue(fn (): ?string => app(SiteSettings::class)->name, null, false);

        return filled($name) ? $name : (string) config('app.name');
    }

    /**
     * @return array<int, string>
     */
    private function socialProfiles(): array
    {
        $settings = rescue(fn (): ?SocialMediaSettings => app(SocialMediaSettings::class), null, false);

        if ($settings === null) {
            return [];
        }

        return array_values(array_filter([
            $settings->linkedin,
            $settings->facebook,
        ], filled(...)));
    }
}
