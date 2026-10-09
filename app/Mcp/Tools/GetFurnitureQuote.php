<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;

#[Name('get_furniture_quote')]
#[Title('Get Truckit furniture quote')]
#[Description('Requests a Truckit partner quote to move furniture between two Australian locations and returns the partner response.')]
#[IsOpenWorld]
class GetFurnitureQuote extends PartnerQuoteTool
{
    protected function category(): string
    {
        return 'furniture';
    }

    protected function itemRules(): array
    {
        return [
            'category' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:200'],
            'length' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'height' => ['required', 'numeric', 'gt:0'],
            'weight' => ['required', 'numeric', 'gt:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'unsure_dimensions' => ['nullable', 'boolean'],
            'fragile' => ['nullable', 'boolean'],
            'blanket_wrap' => ['nullable', 'boolean'],
            'extra_details' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['url'],
        ];
    }

    protected function itemProperties(JsonSchema $schema): array
    {
        return [
            'category' => $schema->string()->description('Kind of item, e.g. Piano or Furniture.')->required(),
            'collect' => $schema->string()->description('Pickup suburb or address, e.g. "AITKENVALE, QLD, 4814".')->required(),
            'deliver' => $schema->string()->description('Drop-off suburb or address, same format as collect.')->required(),
            'description' => $schema->string()->description('What the item is.')->required(),
            'length' => $schema->number()->description('Length of the item in metres, e.g. 1.5 (not centimetres).')->required(),
            'width' => $schema->number()->description('Width of the item in metres, e.g. 0.6.')->required(),
            'height' => $schema->number()->description('Height of the item in metres, e.g. 1.3.')->required(),
            'weight' => $schema->number()->description('Weight of the item in kilograms.')->required(),
            'quantity' => $schema->integer()->description('Number of identical items.'),
            'unsure_dimensions' => $schema->boolean()->description('True when the dimensions are estimates. Say so in the reply.'),
            'fragile' => $schema->boolean()->description('True only when the user said the item is fragile.'),
            'blanket_wrap' => $schema->boolean()->description('True when blanket wrapping is wanted.'),
            'extra_details' => $schema->string()->description('Anything else the carrier should know.'),
            'images' => $schema->array()->items($schema->string())->description('Image URLs of the item.'),
        ];
    }
}
