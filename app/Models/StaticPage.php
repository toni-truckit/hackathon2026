<?php

declare(strict_types=1);

namespace App\Models;

use AchyutN\LaravelHelpers\Models\MediaModel;
use AchyutN\LaravelHelpers\Traits\HasTheSlug;
use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\Breadcrumb;
use AchyutN\LaravelSEO\Models\SEO;
use AchyutN\LaravelSEO\Schemas\PageSchema;
use App\Enums\PageType;
use App\OGImage\Contracts\HasOGImage;
use App\Traits\InteractsWithOGImage;
use App\Traits\InteractsWithSEO;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Support\Period;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $content
 * @property string|null $name
 * @property PageType $type
 * @property array<array-key, mixed>|null $tags
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SEO|null $seo
 * @property-read string $url
 * @property-read Collection<int, View> $views
 * @property-read int|null $views_count
 *
 * @method static Builder<static>|StaticPage findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder<static>|StaticPage newModelQuery()
 * @method static Builder<static>|StaticPage newQuery()
 * @method static Builder<static>|StaticPage orderByUniqueViews(string $direction = 'desc', $period = null, ?string $collection = null, string $as = 'unique_views_count')
 * @method static Builder<static>|StaticPage orderByViews(string $direction = 'desc', ?Period $period = null, ?string $collection = null, bool $unique = false, string $as = 'views_count')
 * @method static Builder<static>|StaticPage query()
 * @method static Builder<static>|StaticPage whereContent($value)
 * @method static Builder<static>|StaticPage whereCreatedAt($value)
 * @method static Builder<static>|StaticPage whereDeletedAt($value)
 * @method static Builder<static>|StaticPage whereDescription($value)
 * @method static Builder<static>|StaticPage whereId($value)
 * @method static Builder<static>|StaticPage whereName($value)
 * @method static Builder<static>|StaticPage whereSlug($value)
 * @method static Builder<static>|StaticPage whereTags($value)
 * @method static Builder<static>|StaticPage whereTitle($value)
 * @method static Builder<static>|StaticPage whereType($value)
 * @method static Builder<static>|StaticPage whereUpdatedAt($value)
 * @method static Builder<static>|StaticPage withUniqueSlugConstraints(Model $model, string $attribute, array $config, string $slug)
 * @method static Builder<static>|StaticPage withViewsCount(?Period $period = null, ?string $collection = null, bool $unique = false, string $as = 'views_count')
 *
 * @mixin \Eloquent
 */
final class StaticPage extends MediaModel implements HasMarkup, HasOGImage, Viewable
{
    use HasTheSlug;
    use InteractsWithOGImage;
    use InteractsWithSEO;
    use InteractsWithViews;
    use PageSchema;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoType(): string
    {
        return 'website';
    }

    public function categoryValue(): string
    {
        return $this->type->getLabel();
    }

    public function authorValue(): ?string
    {
        /** @phpstan-var string|null */
        return config('app.name');
    }

    public function authorUrlValue(): string
    {
        return route('landing-page');
    }

    public function publisherValue(): ?string
    {
        return $this->getAuthorValue();
    }

    public function publisherUrlValue(): ?string
    {
        return $this->getAuthorUrlValue();
    }

    public function urlValue(): string
    {
        if ($this->type === PageType::LandingPage) {
            return route('landing-page');
        }

        if ($this->type === PageType::IndexPage) {
            return route('landing-page');
        }

        if ($this->type === PageType::Faq) {
            return route('faq.view');
        }

        if ($this->type === PageType::Contact) {
            return route('contact.view');
        }

        return route('page.view', $this);
    }

    public function imageValue(): ?string
    {
        return $this->ogImageUrl() ?? ($this->hasMedia('cover') ? $this->getLastMediaUrl('cover') : null);
    }

    public function ogCoverImageUrl(): ?string
    {
        return $this->hasMedia('cover') ? $this->getLastMediaUrl('cover') : null;
    }

    /** @return array<Breadcrumb> */
    public function breadcrumbs(): array
    {
        if ($this->type === PageType::LandingPage) {
            return [
                new Breadcrumb($this->getTitleValue(), $this->getURLValue()),
            ];
        }

        return [
            new Breadcrumb('Home', route('landing-page')),
            new Breadcrumb($this->getTitleValue(), $this->getURLValue()),
        ];
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => route('page.view', $this),
        );
    }

    protected function casts(): array
    {
        return [
            'type' => PageType::class,
            'tags' => 'array',
        ];
    }
}
