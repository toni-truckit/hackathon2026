<?php

declare(strict_types=1);

namespace App\Services\Partner;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class PartnerQuoteClient
{
    private const CATEGORIES = ['furniture', 'cars', 'motorcycles'];

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function quote(string $category, array $payload): array
    {
        if (! in_array($category, self::CATEGORIES, true)) {
            throw new PartnerQuoteException("Unknown quote category [{$category}].");
        }

        $this->assertConfigured();

        try {
            $response = $this->quoteRequest()->post($this->baseUrl().'/get-quote/'.$category, $payload);
        } catch (ConnectionException) {
            throw new PartnerQuoteException('Truckit did not respond. Try again in a moment.');
        }

        if ($response->failed()) {
            throw new PartnerQuoteException($this->errorMessage($response));
        }

        return (array) $response->json();
    }

    private function quoteRequest(): PendingRequest
    {
        return Http::timeout($this->timeout())
            ->acceptJson()
            ->asJson()
            ->withToken($this->accessToken())
            ->withHeaders([
                'x-api-key' => (string) config('services.truckit_partner.api_key'),
                'x-customer-id' => (string) config('services.truckit_partner.customer_id'),
                'x-customer-secret' => (string) config('services.truckit_partner.customer_secret'),
            ]);
    }

    private function accessToken(): string
    {
        $cacheKey = $this->tokenCacheKey();
        $cached = Cache::get($cacheKey);

        if (is_string($cached)) {
            return $cached;
        }

        try {
            $response = Http::asForm()
                ->timeout($this->timeout())
                ->acceptJson()
                ->withHeaders(['x-api-key' => (string) config('services.truckit_partner.api_key')])
                ->post((string) config('services.truckit_partner.auth_url'), [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('services.truckit_partner.client_id'),
                    'client_secret' => config('services.truckit_partner.client_secret'),
                    'scope' => config('services.truckit_partner.scope'),
                ]);
        } catch (ConnectionException) {
            throw new PartnerQuoteException('Truckit sign-in did not respond. Try again in a moment.');
        }

        $token = $response->json('access_token');

        if ($response->failed() || ! is_string($token)) {
            throw new PartnerQuoteException('Truckit refused the partner sign-in: '.$this->errorMessage($response));
        }

        // refresh a minute early so a token never expires mid-request
        $ttl = max(30, (int) $response->json('expires_in', 300) - 60);
        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }

    /** keyed per environment so switching from the mock to Truckit never reuses a stale token */
    private function tokenCacheKey(): string
    {
        return 'truckit.partner.token.'.md5(implode('|', [
            config('services.truckit_partner.auth_url'),
            config('services.truckit_partner.client_id'),
            config('services.truckit_partner.scope'),
        ]));
    }

    private function assertConfigured(): void
    {
        $missing = collect(['base_url', 'client_id', 'client_secret', 'customer_id', 'customer_secret', 'api_key'])
            ->filter(fn (string $key): bool => blank(config("services.truckit_partner.{$key}")))
            ->map(fn (string $key): string => 'services.truckit_partner.'.$key)
            ->values();

        if ($missing->isNotEmpty()) {
            throw new PartnerQuoteException('Truckit quotes are not configured. Missing: '.$missing->implode(', ').'.');
        }
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.truckit_partner.base_url'), '/');
    }

    private function timeout(): int
    {
        return (int) config('services.truckit_partner.timeout', 20);
    }

    private function errorMessage(Response $response): string
    {
        $message = $response->json('error.message') ?? $response->json('message');

        if (is_string($message) && $message !== '') {
            return $message;
        }

        $body = trim($response->body());

        return $body !== '' ? mb_substr($body, 0, 300) : "HTTP {$response->status()}";
    }
}
