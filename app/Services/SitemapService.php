<?php

declare(strict_types=1);

namespace App\Services;

use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Services\SEOService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\LazyCollection;

final class SitemapService
{
    public function __construct(
        public SEOService $service,
    ) {}

    public function toXML(): Response
    {
        /** @var LazyCollection<int, SEO> $seoModels */
        $seoModels = $this->getSEOEntries();

        $xml = [];

        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model instanceof Model || ! $this->shouldInclude($model, $seoModel)) {
                continue;
            }

            [
                'url' => $url,
                'imageUrl' => $imageUrl,
                'title' => $title,
                'description' => $description,
                'updatedAt' => $updatedAt,
            ] = $this->service->getModelValues($model);

            if ($url === null) {
                continue;
            }

            $xml[] = '<url>';
            $xml[] = '<loc>'.htmlspecialchars($url, ENT_XML1, 'UTF-8').'</loc>';
            if ($updatedAt instanceof Carbon) {
                $xml[] = '<lastmod>'.htmlspecialchars($updatedAt->toAtomString(), ENT_XML1, 'UTF-8').'</lastmod>';
            }

            if ($imageUrl) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>'.htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8').'</image:loc>';
                $xml[] = '<image:title>'.htmlspecialchars($title ?? '', ENT_XML1, 'UTF-8').'</image:title>';
                $xml[] = '<image:caption>'.htmlspecialchars($description ?? '', ENT_XML1, 'UTF-8').'</image:caption>';
                $xml[] = '</image:image>';
            }

            $xml[] = '</url>';
        }

        $xml[] = '</urlset>';

        return response(implode("\n", $xml))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function toTXT(): Response
    {
        /** @var LazyCollection<int, SEO> $seoModels */
        $seoModels = $this->getSEOEntries();

        $txt = [];

        foreach ($seoModels as $seoModel) {
            $model = $seoModel->model;

            if (! $model instanceof Model || ! $this->shouldInclude($model, $seoModel)) {
                continue;
            }

            /** @phpstan-var string|null $url */
            [
                'url' => $url,
            ] = $this->service->getModelValues($model);

            if ($url !== null) {
                $txt[] = $url;
            }
        }

        return response(implode("\n", $txt))
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    private function shouldInclude(Model $model, SEO $seo): bool
    {
        if (in_array('noindex', (array) $seo->robots, true)) {
            return false;
        }

        if (method_exists($model, 'seoShouldIndex')) {
            return (bool) $model->seoShouldIndex();
        }

        return true;
    }

    /** @return LazyCollection<int, SEO> */
    private function getSEOEntries(): LazyCollection
    {
        return SEO::query()
            ->with('model')
            ->orderBy('model_type')
            ->lazy();
    }
}
