<?php

declare(strict_types=1);

namespace App\Traits;

use App\OGImage\Contracts\HasOGImage;
use App\OGImage\OGImageData;
use App\OGImage\OGImageGenerator;
use App\Settings\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

trait InteractsWithOGImage
{
    /**
     * The alias registered in config/og-image.php.
     */
    public function ogImageType(): string
    {
        $type = array_search(static::class, config('og-image.models', []), true);

        if ($type === false) {
            throw new RuntimeException(sprintf(
                'Model [%s] is not registered in config/og-image.php.',
                static::class,
            ));
        }

        return (string) $type;
    }

    public function ogImageView(): string
    {
        return (string) config('og-image.view');
    }

    public function ogCoverImageUrl(): ?string
    {
        return null;
    }

    public function ogAuthorAvatarUrl(): ?string
    {
        return null;
    }

    public function ogImageData(): OGImageData
    {
        return new OGImageData(
            title: $this->getTitleValue() ?? '',
            description: Str::limit(strip_tags((string) $this->getDescriptionValue()), 180),
            eyebrow: $this->getCategoryValue(),
            authorName: $this->getAuthorValue(),
            authorAvatarUrl: $this->ogAuthorAvatarUrl(),
            coverUrl: $this->ogAbsoluteUrl($this->ogCoverImageUrl()),
            url: $this->getURLValue(),
            publishedAt: $this->getPublishedAtValue()?->toFormattedDateString(),
            tags: array_values(array_filter((array) $this->getTagsValue(), 'is_string')),
            brandName: $this->ogBrandName(),
            brandLogoUrl: $this->ogAbsoluteUrl($this->ogBrandLogoPath()),
            domain: parse_url((string) config('app.url'), PHP_URL_HOST) ?: null,
        );
    }

    public function ogImageFingerprint(): string
    {
        return mb_substr(md5((string) json_encode([
            'model' => static::class,
            'key' => $this->getKey(),
            'modified' => $this->getModifiedAtValue()?->getTimestamp(),
            'data' => $this->ogImageData()->toArray(),
            'view' => $this->ogImageView(),
            'app' => config('app.url'),
        ])), 0, 8);
    }

    public function ogImageUrl(): ?string
    {
        if (! config('og-image.enabled')) {
            return null;
        }

        return route(config('og-image.route.name'), [
            'type' => $this->ogImageType(),
            'key' => $this->getRouteKey(),
            'hash' => $this->ogImageFingerprint(),
        ]);
    }

    public function ogImageRelativePath(): string
    {
        return sprintf('%s/%s-%s.png', $this->ogImageType(), $this->getRouteKey(), $this->ogImageFingerprint());
    }

    public function imageValue(): ?string
    {
        return $this->ogImageUrl();
    }

    protected static function bootInteractsWithOGImage(): void
    {
        static::deleted(function (Model $model): void {
            if ($model instanceof HasOGImage) {
                app(OGImageGenerator::class)->deleteFor($model);
            }
        });
    }

    protected function ogBrandName(): string
    {
        $name = rescue(fn (): ?string => app(SiteSettings::class)->name, null, false);

        return filled($name) ? $name : (string) config('app.name');
    }

    protected function ogBrandLogoPath(): ?string
    {
        $logo = rescue(fn (): ?string => app(SiteSettings::class)->logo, null, false);

        return filled($logo) ? 'storage/'.$logo : null;
    }

    protected function ogAbsoluteUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        return preg_match('/^https?:\/\//', $url) ? $url : url($url);
    }
}
