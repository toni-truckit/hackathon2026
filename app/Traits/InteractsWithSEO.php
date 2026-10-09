<?php

declare(strict_types=1);

namespace App\Traits;

use AchyutN\LaravelSEO\Data\Breadcrumb;
use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Traits\InteractsWithSEO as BaseInteractsWithSEO;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\Support\SEOData;

trait InteractsWithSEO
{
    use BaseInteractsWithSEO;

    /**
     * The Open Graph type used for the model.
     */
    public function seoType(): string
    {
        return 'article';
    }

    public function getDynamicSEOData(): SEOData
    {
        $resolvedSEO = $this->resolveSEO();

        /** @var SEO|null $seo */
        $seo = $this->seo;

        $schema = $this->buildDynamicSchema();

        if (count($this->breadcrumbs()) > 0) {
            $schema->addBreadcrumbs(function (BreadcrumbListSchema $breadcrumbs): void {
                $breadcrumbs->breadcrumbs = collect($this->breadcrumbs())
                    ->filter(fn ($breadcrumb): bool => $breadcrumb instanceof Breadcrumb)
                    ->mapWithKeys(fn (Breadcrumb $breadcrumb): array => $breadcrumb->toArray());
            });
        }

        return new SEOData(
            title: $resolvedSEO->title,
            description: $resolvedSEO->description,
            author: $resolvedSEO->author,
            image: $resolvedSEO->image,
            url: $resolvedSEO->url,
            published_time: $resolvedSEO->publishedAt,
            modified_time: $resolvedSEO->modifiedAt,
            section: $resolvedSEO->category,
            tags: $resolvedSEO->tags,
            schema: $schema,
            type: $this->seoType(),
            site_name: config('seo.site_name'),
            robots: $this->resolveRobots($seo),
            openGraphTitle: $seo?->og_title ?? $resolvedSEO->title,
        );
    }

    /**
     * Whether the model's page should be indexed by search engines.
     */
    public function seoShouldIndex(): bool
    {
        return true;
    }

    private function resolveRobots(?SEO $seo = null): string
    {
        if (! $this->seoShouldIndex() || app()->isLocal()) {
            return 'noindex, nofollow';
        }

        $directives = array_values(array_filter((array) ($seo?->robots ?? []), 'is_string'));
        $directives = $directives === [] ? ['index', 'follow'] : $directives;

        $robots = implode(', ', $directives);
        $default = mb_trim((string) config('seo.robots.default'));

        if (in_array('noindex', $directives, true) || $default === '' || str_contains($robots, $default)) {
            return $robots;
        }

        return $robots.', '.$default;
    }
}
