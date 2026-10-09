<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Faq active()
 * @method static FaqFactory factory($count = null, $state = [])
 * @method static Builder<static>|Faq newModelQuery()
 * @method static Builder<static>|Faq newQuery()
 * @method static Builder<static>|Faq ordered()
 * @method static Builder<static>|Faq query()
 * @method static Builder<static>|Faq whereAnswer($value)
 * @method static Builder<static>|Faq whereCreatedAt($value)
 * @method static Builder<static>|Faq whereId($value)
 * @method static Builder<static>|Faq whereIsActive($value)
 * @method static Builder<static>|Faq whereQuestion($value)
 * @method static Builder<static>|Faq whereSortOrder($value)
 * @method static Builder<static>|Faq whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Faq extends Model
{
    use HasFactory;

    /**
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param  Builder<static>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
