<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Services\Partner\PartnerQuoteClient;
use App\Services\Partner\PartnerQuoteException;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

abstract class PartnerQuoteTool extends Tool
{
    public function __construct(private readonly PartnerQuoteClient $partner) {}

    /** furniture, cars or motorcycles: the last part of the partner URL */
    abstract protected function category(): string;

    /**
     * Rules for one item, without the items.* prefix.
     *
     * @return array<string, list<string>>
     */
    abstract protected function itemRules(): array;

    /**
     * @return array<string, Type>
     */
    abstract protected function itemProperties(JsonSchema $schema): array;

    final public function handle(Request $request): Response|ResponseFactory
    {
        $data = $request->validate($this->rules(), $this->messages());

        $payload = [
            'reference' => $data['reference'] ?? 'claude-'.Str::uuid(),
            'items' => collect($data['items'])
                ->values()
                ->map(fn (array $item, int $index): array => ['id' => 'item-'.($index + 1)] + $item)
                ->all(),
        ];

        try {
            $quote = $this->partner->quote($this->category(), $payload);
        } catch (PartnerQuoteException $e) {
            return Response::error('Truckit could not price this quote: '.$e->getMessage());
        }

        if (isset($quote['status']) && $quote['status'] !== 'success') {
            return Response::error('Truckit could not price this quote: '.($quote['message'] ?? $quote['status']));
        }

        $count = count($payload['items']);
        $summary = sprintf(
            'Truckit quote for %d %s (reference %s). The full partner response follows.',
            $count,
            Str::plural('item', $count),
            $payload['reference'],
        );

        return Response::make([
            Response::text($summary),
            Response::text((string) json_encode($quote, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)),
        ])->withStructuredContent($quote);
    }

    /**
     * @return array<string, Type>
     */
    final public function schema(JsonSchema $schema): array
    {
        return [
            'reference' => $schema->string()->description('Optional label for this quote. One is generated when left out.'),
            'items' => $schema->array()
                ->items($schema->object($this->itemProperties($schema)))
                ->min(1)
                ->description('The items to move.')
                ->required(),
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private function rules(): array
    {
        $rules = [
            'reference' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*' => ['array'],
            'items.*.collect' => ['required', 'string', 'max:200'],
            'items.*.deliver' => ['required', 'string', 'max:200'],
        ];

        foreach ($this->itemRules() as $field => $fieldRules) {
            $rules['items.*.'.$field] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'items.required' => 'Send at least one item.',
            'items.min' => 'Send at least one item.',
            'items.*.collect.required' => 'Each item needs a collect address and a deliver address.',
            'items.*.deliver.required' => 'Each item needs a collect address and a deliver address.',
        ];
    }
}
