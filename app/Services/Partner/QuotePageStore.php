<?php

declare(strict_types=1);

namespace App\Services\Partner;

use Illuminate\Support\Facades\Cache;

/**
 * Keeps a copy of each partner quote so /quote/{reference} can show it.
 * The reference is Truckit's own id for the quote, so it can't be guessed.
 */
final class QuotePageStore
{
    private const TTL_HOURS = 72;

    /**
     * @param  array<string, mixed>  $quote  the partner response
     */
    public function remember(array $quote): ?string
    {
        $reference = $quote['reference'] ?? null;

        if (! is_string($reference) || ! $this->validReference($reference)) {
            return null;
        }

        Cache::put($this->key($reference), $quote, now()->addHours(self::TTL_HOURS));

        return route('quote.show', ['reference' => $reference]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $reference): ?array
    {
        $quote = Cache::get($this->key($reference));

        return is_array($quote) ? $quote : null;
    }

    private function validReference(string $reference): bool
    {
        return preg_match('/^[A-Za-z0-9-]{8,64}$/', $reference) === 1;
    }

    private function key(string $reference): string
    {
        return 'truckit.quote_page.'.$reference;
    }
}
