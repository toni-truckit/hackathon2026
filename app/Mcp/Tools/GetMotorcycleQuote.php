<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;

#[Name('get_motorcycle_quote')]
#[Title('Get Truckit motorcycle quote')]
#[Description('Requests a Truckit partner quote to transport a motorcycle between two Australian locations and returns the partner response.')]
#[IsOpenWorld]
class GetMotorcycleQuote extends PartnerQuoteTool
{
    protected function category(): string
    {
        return 'motorcycles';
    }

    protected function itemRules(): array
    {
        return [
            'make' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:60'],
            'type' => ['required', 'string', 'max:60'],
        ];
    }

    protected function itemProperties(JsonSchema $schema): array
    {
        return [
            'collect' => $schema->string()->description('Pickup suburb or address.')->required(),
            'deliver' => $schema->string()->description('Drop-off suburb or address.')->required(),
            'make' => $schema->string()->description('Manufacturer, e.g. Suzuki.')->required(),
            'model' => $schema->string()->description('Model, e.g. GSX1300R.')->required(),
            'type' => $schema->string()->description('Motorcycle type, e.g. Road.')->required(),
        ];
    }
}
