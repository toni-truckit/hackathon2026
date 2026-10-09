# Truckit partner quote MCP

Guideline for hackathon deliverable 2: an MCP server that turns the Truckit partner quote APIs into tools for **furniture**, **cars**, and **motorcycles**.

Loop slices and status live in `/Users/truckit/loop-plans/mcp`. Do not record loop status in this file.

## Goal

A guest can ask Claude, Cursor, or ChatGPT for a real Truckit price. The host calls this app. This app calls the partner API. The tool returns the partner payload. It never invents a price.

Landing page already exists in this repo. These stay out of this work:

- AI plugin
- PRD and tech write-up beyond this file
- Media and social drafts
- Book-now, job list, cancel, and provider tools
- User OAuth on the MCP route

## Current app

- Laravel 13, PHP 8.3, Pest, Pint
- No `laravel/mcp` in `composer.json`
- No `routes/ai.php`
- No `app/Mcp`
- Public MCP path is already named in `config/truckit-connect.php`: `TRUCKIT_MCP_URL` defaults to `https://mcp.truckit.net/mcp`

## Endpoints

From `truckit-partner-apis prod` Postman collection.

| Step | Method | URL |
| --- | --- | --- |
| Token | POST | `https://auth-au.truckit.net/oauth2/token` |
| Furniture | POST | `https://secure-api.truckit.net/get-quote/furniture` |
| Cars | POST | `https://secure-api.truckit.net/get-quote/cars` |
| Motorcycles | POST | `https://secure-api.truckit.net/get-quote/motorcycles` |

Token body (`application/x-www-form-urlencoded`):

- `grant_type` = `client_credentials`
- `client_id`
- `client_secret`
- `scope` = `partner-api/get-quote partner-api/book-now partner-api/manage`

Quote headers:

- `Authorization: Bearer {access_token}`
- `x-api-key`
- `x-customer-id` (same value as `client_id`)
- `x-customer-secret` (same value as `client_secret`)
- `Accept: application/json`
- `Content-Type: application/json`

The collection has no sample response body. Tools return the JSON the API sends. Do not assume field names such as `price` or `gst` until a real response is captured.

## Flow

```text
Host  -->  POST /mcp  -->  quote tool
                              |
                              +--> cache miss: POST /oauth2/token
                              |
                              +--> POST /get-quote/{category}
                              |
                              +--> text + structured JSON back to host
```

Partner auth stays on the server. The MCP route does not ask the end user to log in. Throttle the route.

A quote call stores a partner `reference`, so the tools are open-world and not read-only. They are not destructive.

## Secrets

Put credentials only in local `.env`. `.env.example` lists empty names. Do not commit the Postman `client_id`, `client_secret`, or `x-api-key`.

## Config

Add `truckit_partner` to `config/services.php`. Read env only inside that config file.

| Config key | Env | Default |
| --- | --- | --- |
| `base_url` | `TRUCKIT_PARTNER_BASE_URL` | `https://secure-api.truckit.net` |
| `auth_url` | `TRUCKIT_PARTNER_AUTH_URL` | `https://auth-au.truckit.net/oauth2/token` |
| `client_id` | `TRUCKIT_PARTNER_CLIENT_ID` | none |
| `client_secret` | `TRUCKIT_PARTNER_CLIENT_SECRET` | none |
| `api_key` | `TRUCKIT_PARTNER_API_KEY` | none |
| `scope` | `TRUCKIT_PARTNER_SCOPE` | `partner-api/get-quote partner-api/book-now partner-api/manage` |

## Files to add or change

| File | Role |
| --- | --- |
| `composer.json` / `composer.lock` | `laravel/mcp` |
| `routes/ai.php` | `Mcp::web('/mcp', TruckitServer::class)` |
| `config/services.php` | `truckit_partner` |
| `.env.example` | empty env names |
| `app/Services/Partner/PartnerQuoteClient.php` | token + quote HTTP |
| `app/Mcp/Servers/TruckitServer.php` | three tools, no `ToolSearch` |
| `app/Mcp/Tools/GetFurnitureQuote.php` | furniture schema + handle |
| `app/Mcp/Tools/GetCarQuote.php` | car schema + handle |
| `app/Mcp/Tools/GetMotorcycleQuote.php` | motorcycle schema + handle |
| `tests/Feature/PartnerQuoteMcpTest.php` | Pest, `Http::fake` |

## Install

```shell
composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes --no-interaction
php artisan make:mcp-server TruckitServer --no-interaction
php artisan make:mcp-tool GetFurnitureQuote --no-interaction
php artisan make:mcp-tool GetCarQuote --no-interaction
php artisan make:mcp-tool GetMotorcycleQuote --no-interaction
```

Confirm tool generator flags with `php artisan list` before running them. If `make:mcp-tool` is absent, write the tool classes by hand using the Laravel MCP `Tool` stub.

Register the route:

```php
use App\Mcp\Servers\TruckitServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', TruckitServer::class)
    ->middleware('throttle:60,1');
```

Server attributes: name `Truckit`, version `1.0.0`. Instructions state that the server returns partner quote results for furniture, cars, and motorcycles, and that a missing price must be reported as the partner error.

## Partner client

`App\Services\Partner\PartnerQuoteClient`

- `quote(string $category, array $payload): array`
- `$category` is one of `furniture`, `cars`, `motorcycles`
- Cache the access token under a fixed key until `expires_in` minus a small buffer (60 seconds)
- Token request uses `Http::asForm()->post(config('services.truckit_partner.auth_url'), ...)`
- Quote request uses `Http::withToken($token)->withHeaders([...])->post($baseUrl.'/get-quote/'.$category, $payload)`
- Timeout 20 seconds
- 4xx and 5xx: throw an exception whose message is the partner error body, or a short status line when the body is empty
- Missing `client_id`, `client_secret`, or `api_key`: fail before the HTTP call with a message that names the missing config

Tools catch that exception and return `Response::error(...)`. They do not fill in a price.

## Tools

Each tool:

- `declare(strict_types=1);`
- `#[Description('...')]` says what the tool does. No instructions to the model.
- `#[IsOpenWorld]`
- No `#[IsReadOnly]`
- `handle()` validates, calls `PartnerQuoteClient`, returns `Response::make(Response::text($summary))->withStructuredContent($json)`
- `$summary` is one or two sentences. Include a price only when the partner JSON actually contains one. Always include `reference` when present.
- `schema(JsonSchema $schema): array` matches the tables below
- No `outputSchema` until a real response shape is known

### `get_furniture_quote`

`POST /get-quote/furniture`

Top level:

| Field | Required | Notes |
| --- | --- | --- |
| `reference` | yes | Caller reference string |
| `items` | yes | Array, min 1 |

Each item:

| Field | Required | Notes |
| --- | --- | --- |
| `id` | yes | SKU or item id |
| `category` | yes | Example: `Piano`, `Furniture` |
| `collect` | yes | Suburb or address string |
| `deliver` | yes | Suburb or address string |
| `description` | yes | |
| `length` | yes | Number, metres |
| `width` | yes | Number, metres |
| `height` | yes | Number, metres |
| `weight` | yes | Number, kilograms |
| `unsure_dimensions` | no | Boolean |
| `fragile` | no | Boolean |
| `blanket_wrap` | no | Boolean |
| `extra_details` | no | String |
| `quantity` | no | Integer, min 1 |
| `images` | no | Array of URL strings |

### `get_car_quote`

`POST /get-quote/cars`

Top level: `reference` (required), `items` (required, min 1).

Each item:

| Field | Required | Notes |
| --- | --- | --- |
| `id` | yes | |
| `collect` | yes | |
| `deliver` | yes | |
| `description` | yes | |
| `make` | yes | |
| `model` | yes | |
| `year` | yes | String, example `2019` |
| `body_type` | yes | Example `SUV` |
| `modifications` | yes | Boolean |
| `drivable` | yes | Boolean |
| `empty` | yes | Boolean |

### `get_motorcycle_quote`

`POST /get-quote/motorcycles`

Top level: `reference` (required), `items` (required, min 1).

Each item:

| Field | Required | Notes |
| --- | --- | --- |
| `id` | yes | |
| `collect` | yes | |
| `deliver` | yes | |
| `make` | yes | |
| `model` | yes | |
| `type` | yes | Example `Road` |

## Validation messages

Use `$request->validate()` inside `handle()`. Messages name the fix.

- Missing `reference`: `Send a reference string for this quote.`
- Missing or empty `items`: `Send at least one item.`
- Missing `collect` or `deliver`: `Each item needs a collect address and a deliver address.`

Partner failure example: `Truckit could not price this quote: {partner message}`

## Tests

`tests/Feature/PartnerQuoteMcpTest.php`. Pest. `Http::fake` only. No calls to production.

1. Furniture tool posts `https://secure-api.truckit.net/get-quote/furniture` with bearer token and the three partner headers. Response `assertOk()` and `assertSee` of a price that exists only in the fake JSON.
2. Car tool posts `/get-quote/cars`.
3. Motorcycle tool posts `/get-quote/motorcycles`.
4. Partner `422` returns `assertHasErrors` and the response text does not contain a made-up dollar amount.
5. Furniture call without `collect` fails validation and does not hit the quote URL.

Fake token endpoint returns `access_token` and `expires_in`.

Invoke tools with `TruckitServer::tool(GetFurnitureQuote::class, $input)`.

After the tests pass:

```shell
php artisan test --compact tests/Feature/PartnerQuoteMcpTest.php
vendor/bin/pint --dirty --format agent
```

## Done when

- `POST /mcp` is registered
- The server advertises exactly three tools
- Each tool calls only its category URL
- Token is cached
- Secrets are env-only
- The five tests pass
- A price appears in tool output only when the partner JSON contains it
