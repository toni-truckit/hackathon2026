<?php

declare(strict_types=1);

use App\Mcp\Servers\TruckitServer;
use App\Mcp\Tools\GetCarQuote;
use App\Mcp\Tools\GetFurnitureQuote;
use App\Mcp\Tools\GetMotorcycleQuote;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Cache::flush();

    config([
        'services.truckit_partner.base_url' => 'https://partner.test',
        'services.truckit_partner.auth_url' => 'https://auth.test/oauth2/token',
        'services.truckit_partner.client_id' => 'cid',
        'services.truckit_partner.client_secret' => 'csecret',
        'services.truckit_partner.customer_id' => 'cust-id',
        'services.truckit_partner.customer_secret' => 'cust-secret',
        'services.truckit_partner.api_key' => 'akey',
    ]);
});

function fakePartner(string $quoteUrl, array|string $body = ['quote_total' => 777], int $status = 200): void
{
    Http::fake([
        'auth.test/*' => Http::response(['access_token' => 'tok123', 'expires_in' => 3600]),
        $quoteUrl => Http::response($body, $status),
    ]);
}

function furnitureInput(array $override = []): array
{
    return array_replace_recursive([
        'reference' => 'ref-1',
        'items' => [[
            'category' => 'Piano',
            'collect' => 'AITKENVALE, QLD, 4814',
            'deliver' => 'SURFERS PARADISE, QLD, 4217',
            'description' => 'Upright piano',
            'length' => 1,
            'width' => 1,
            'height' => 1,
            'weight' => 100,
        ]],
    ], $override);
}

it('quotes furniture with the bearer token and partner headers', function (): void {
    fakePartner('partner.test/get-quote/furniture');

    TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())
        ->assertOk()
        ->assertSee('777');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://partner.test/get-quote/furniture'
        && $request->hasHeader('Authorization', 'Bearer tok123')
        && $request->hasHeader('x-api-key', 'akey')
        && $request->hasHeader('x-customer-id', 'cust-id')
        && $request->hasHeader('x-customer-secret', 'cust-secret')
        && $request['reference'] === 'ref-1'
        && $request['items'][0]['id'] === 'item-1');
});

it('quotes a car', function (): void {
    fakePartner('partner.test/get-quote/cars', ['quote_total' => 1234]);

    TruckitServer::tool(GetCarQuote::class, [
        'items' => [[
            'collect' => '111 Eagle St, Brisbane City QLD 4000',
            'deliver' => '138 Oxford St, Darlinghurst NSW 2010',
            'description' => '2019 Suzuki Jimny GLX Manual',
            'make' => 'Suzuki',
            'model' => 'Jimny',
            'year' => '2019',
            'body_type' => 'SUV',
            'modifications' => true,
            'drivable' => false,
            'empty' => false,
        ]],
    ])->assertOk()->assertSee('1234');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://partner.test/get-quote/cars'
        && str_starts_with((string) $request['reference'], 'claude-'));
});

it('quotes a motorcycle', function (): void {
    fakePartner('partner.test/get-quote/motorcycles', ['quote_total' => 321]);

    TruckitServer::tool(GetMotorcycleQuote::class, [
        'items' => [[
            'collect' => '111 Eagle St, Brisbane City QLD 4000',
            'deliver' => '138 Oxford St, Darlinghurst NSW 2010',
            'make' => 'Suzuki',
            'model' => 'GSX1300R',
            'type' => 'Road',
        ]],
    ])->assertOk()->assertSee('321');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://partner.test/get-quote/motorcycles');
});

it('reports a partner error without inventing a price', function (): void {
    fakePartner('partner.test/get-quote/furniture', ['error' => ['message' => 'Delivery area not serviced.']], 422);

    $response = TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())
        ->assertHasErrors(['Truckit could not price this quote: Delivery area not serviced.']);

    $response->assertDontSee('$');
});

it('reports a not_available answer as an error', function (): void {
    fakePartner('partner.test/get-quote/furniture', ['status' => 'not_available', 'message' => 'Reference already exists. Duplicates not allowed.']);

    TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())
        ->assertHasErrors(['Reference already exists. Duplicates not allowed.']);
});

it('rejects a missing collect address before calling Truckit', function (): void {
    Http::fake();

    $input = furnitureInput();
    unset($input['items'][0]['collect']);

    TruckitServer::tool(GetFurnitureQuote::class, $input)
        ->assertHasErrors(['Each item needs a collect address and a deliver address.']);

    Http::assertNothingSent();
});

it('caches the partner token between quotes', function (): void {
    fakePartner('partner.test/get-quote/furniture');

    TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())->assertOk();
    TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())->assertOk();

    Http::assertSentCount(3);
});

it('names the missing config instead of calling Truckit', function (): void {
    Http::fake();
    config(['services.truckit_partner.api_key' => null]);

    TruckitServer::tool(GetFurnitureQuote::class, furnitureInput())
        ->assertHasErrors(['services.truckit_partner.api_key']);

    Http::assertNothingSent();
});

it('advertises exactly the three quote tools', function (): void {
    foreach ([GetFurnitureQuote::class => 'get_furniture_quote', GetCarQuote::class => 'get_car_quote', GetMotorcycleQuote::class => 'get_motorcycle_quote'] as $tool => $name) {
        TruckitServer::tool($tool, [])->assertName($name);
    }
});
