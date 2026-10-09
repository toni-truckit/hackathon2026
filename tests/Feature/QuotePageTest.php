<?php

declare(strict_types=1);

use App\Services\Partner\QuotePageStore;
use Illuminate\Support\Facades\Cache;

beforeEach(fn () => Cache::flush());

function savedQuote(array $override = []): string
{
    $quote = array_replace([
        'status' => 'success',
        'total' => 1066,
        'reference' => 'REF-AAAA-1111',
        'expires' => now()->addDay()->toIso8601String(),
        'listings' => [
            [
                'collect' => 'AITKENVALE, QLD, 4814',
                'deliver' => 'SURFERS PARADISE, QLD, 4217',
                'listing_url' => 'https://www.truckit.net/api/frontend/partner/listing?reference=L1',
                'price' => 611,
                'items' => [['description' => 'Upright piano']],
            ],
            [
                'collect' => 'NORTHCOTE, VIC, 3070',
                'deliver' => 'SURFERS PARADISE, QLD, 4217',
                'listing_url' => 'https://www.truckit.net/api/frontend/partner/listing?reference=L2',
                'price' => 455,
                'items' => [['description' => 'Two chairs']],
            ],
        ],
    ], $override);

    return (string) app(QuotePageStore::class)->remember($quote);
}

it('shows each listing with its price and a link to Truckit', function (): void {
    $url = savedQuote();

    $this->get($url)
        ->assertOk()
        ->assertSee('Upright piano')
        ->assertSee('$611')
        ->assertSee('$455')
        ->assertSee('$1,066')
        ->assertSee('https://www.truckit.net/api/frontend/partner/listing?reference=L1', false)
        ->assertSee('Nothing is booked until you do');
});

it('says so when the quote has expired', function (): void {
    $url = savedQuote(['expires' => now()->subHour()->toIso8601String()]);

    $this->get($url)
        ->assertOk()
        ->assertSee('This quote has expired')
        ->assertDontSee('Continue on Truckit');
});

it('says so when the quote is not saved', function (): void {
    $this->get(route('quote.show', ['reference' => 'NOT-SAVED-0000']))
        ->assertOk()
        ->assertSee("We can't find that quote")
        ->assertDontSee('Continue on Truckit');
});

it('never links anywhere except truckit.net', function (): void {
    $url = savedQuote(['listings' => [[
        'collect' => 'A',
        'deliver' => 'B',
        'listing_url' => 'https://evil.example/pay',
        'price' => 100,
        'items' => [],
    ]]]);

    $this->get($url)
        ->assertOk()
        ->assertDontSee('evil.example')
        ->assertDontSee('Continue on Truckit');
});

it('escapes anything the partner sends', function (): void {
    $url = savedQuote(['listings' => [[
        'collect' => '<script>alert(1)</script>',
        'deliver' => 'B',
        'listing_url' => 'https://www.truckit.net/x',
        'price' => 100,
        'items' => [],
    ]]]);

    $this->get($url)->assertDontSee('<script>alert(1)</script>', false);
});

it('does not save a quote with an odd reference', function (): void {
    expect(app(QuotePageStore::class)->remember(['reference' => '../../etc']))->toBeNull();
});
