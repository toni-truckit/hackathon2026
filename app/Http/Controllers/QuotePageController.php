<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Partner\QuotePageStore;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;

final class QuotePageController extends Controller
{
    public function __invoke(string $reference, QuotePageStore $store): View
    {
        $quote = $store->find($reference);

        if ($quote === null) {
            return view('quote.show', ['state' => 'missing']);
        }

        $expires = $this->expires($quote['expires'] ?? null);

        if ($expires !== null && $expires->isPast()) {
            return view('quote.show', ['state' => 'expired']);
        }

        return view('quote.show', [
            'state' => 'ready',
            'total' => $this->price($quote['total'] ?? null),
            'expires' => $expires?->format('D j M, g:ia'),
            'listings' => collect($quote['listings'] ?? [])
                ->map(fn (array $listing): array => [
                    'collect' => (string) ($listing['collect'] ?? ''),
                    'deliver' => (string) ($listing['deliver'] ?? ''),
                    'price' => $this->price($listing['price'] ?? null),
                    'items' => collect($listing['items'] ?? [])
                        ->map(fn (array $item): string => (string) ($item['description'] ?? ''))
                        ->filter()
                        ->values()
                        ->all(),
                    'url' => $this->truckitUrl($listing['listing_url'] ?? null),
                ])
                ->all(),
        ]);
    }

    /** a single number for furniture, a min and max for cars and motorcycles */
    private function price(mixed $price): ?string
    {
        if (is_array($price) && isset($price['min'], $price['max'])) {
            return $this->money($price['min']).' to '.$this->money($price['max']);
        }

        return is_numeric($price) ? $this->money($price) : null;
    }

    private function money(mixed $amount): string
    {
        $value = (float) $amount;

        return '$'.number_format($value, $value === floor($value) ? 0 : 2);
    }

    private function expires(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return CarbonImmutable::parse($value)->setTimezone('Australia/Brisbane');
    }

    /** only ever link out to truckit.net, whatever the partner sent */
    private function truckitUrl(mixed $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $parts = parse_url($url);
        $host = mb_strtolower($parts['host'] ?? '');

        return ($parts['scheme'] ?? '') === 'https' && ($host === 'truckit.net' || str_ends_with($host, '.truckit.net'))
            ? $url
            : null;
    }
}
