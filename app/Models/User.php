<?php

declare(strict_types=1);

namespace App\Models;

use AchyutN\LaravelHelpers\Traits\HasTheSlug;
use AchyutN\LaravelSEO\Contracts\HasMarkup;
use AchyutN\LaravelSEO\Data\Breadcrumb;
use AchyutN\LaravelSEO\Models\SEO;
use App\Enums\UserRole;
use App\Models\Scopes\LowerRoleOnly;
use App\OGImage\Contracts\HasOGImage;
use App\Traits\InteractsWithOGImage;
use App\Traits\InteractsWithSEO;
use Awcodes\Gravatar\Gravatar;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use RalphJSmit\Laravel\SEO\SchemaCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $job_title
 * @property string|null $bio
 * @property string|null $website
 * @property string $email
 * @property UserRole $role
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $avatar
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read SEO|null $seo
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereBio($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereJobTitle($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereSlug($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereWebsite($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ScopedBy(LowerRoleOnly::class)]
final class User extends Authenticatable implements FilamentUser, HasMarkup, HasOGImage
{
    use HasFactory;
    use HasTheSlug;
    use InteractsWithOGImage;
    use InteractsWithSEO;
    use Notifiable;
    use SoftDeletes;

    public string $sluggableColumn = 'name';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Update this method to control access to the Filament panel.
        // Here, we allow access only to users with the Developer or Admin role.
        return in_array($this->role, [
            UserRole::Developer,
            UserRole::Admin,
            UserRole::Writer,
        ]);
    }

    /** @returns array<int, UserRole> */
    public function lowerRoles(): array
    {
        return match (auth()->user()->role) {
            UserRole::Developer => [UserRole::Developer, UserRole::Admin, UserRole::Writer, UserRole::User],
            UserRole::Admin => [UserRole::Admin, UserRole::Writer, UserRole::User],
            UserRole::Writer => [UserRole::Writer, UserRole::User],
            UserRole::User => [UserRole::User],
        };
    }

    /** @returns bool */
    public function isLowerInRole(): bool
    {
        if (auth()->user()->role === UserRole::Developer) {
            return true;
        }

        return in_array($this->role, auth()->user()->lowerRoles());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function titleValue(): string
    {
        return $this->name;
    }

    public function descriptionValue(): ?string
    {
        return $this->bio;
    }

    public function authorValue(): string
    {
        return $this->name;
    }

    public function authorUrlValue(): string
    {
        return route('landing-page');
    }

    public function publisherValue(): ?string
    {
        /** @phpstan-var string|null */
        return config('app.name');
    }

    public function publisherUrlValue(): string
    {
        return route('landing-page');
    }

    public function urlValue(): string
    {
        return route('landing-page');
    }

    public function imageValue(): ?string
    {
        return $this->ogImageUrl() ?? $this->avatar;
    }

    public function ogAuthorAvatarUrl(): ?string
    {
        return $this->avatar;
    }

    public function seoType(): string
    {
        return 'profile';
    }

    public function seoShouldIndex(): bool
    {
        return false;
    }

    /** @return array<int, Breadcrumb> */
    public function breadcrumbs(): array
    {
        return [
            new Breadcrumb('Home', route('landing-page')),
            new Breadcrumb($this->name, $this->urlValue()),
        ];
    }

    public function buildSchema(SchemaCollection $schema): SchemaCollection
    {
        /** @var HasMarkup $this */
        $resolvedSEO = $this->resolveSEO();

        return $schema->add(fn (): array => [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'name' => $resolvedSEO->title,
            'description' => $resolvedSEO->description,
            'url' => $resolvedSEO->url,
            'inLanguage' => 'en',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $this->name,
                'url' => $resolvedSEO->url,
                'image' => $this->avatar,
                'jobTitle' => $this->job_title,
                'sameAs' => array_values(array_filter([$this->website])),
            ],
        ]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    protected function avatar(): Attribute
    {
        $gravatar = Gravatar::get(
            email: $this->email,
            size: 200,
            default: 'initials'
        );

        return Attribute::make(
            get: fn (): string => $gravatar,
        );
    }
}
