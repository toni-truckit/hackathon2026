<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;

#[Name('get_car_quote')]
#[Title('Get Truckit car transport quote')]
#[Description('Requests a Truckit partner quote to transport a car between two Australian locations and returns the partner response.')]
#[IsOpenWorld]
class GetCarQuote extends PartnerQuoteTool
{
    protected function category(): string
    {
        return 'cars';
    }

    protected function itemRules(): array
    {
        return [
            'description' => ['required', 'string', 'max:200'],
            'make' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:60'],
            'year' => ['required', 'digits:4'],
            'body_type' => ['required', 'string', 'max:60'],
            'modifications' => ['required', 'boolean'],
            'drivable' => ['required', 'boolean'],
            'empty' => ['required', 'boolean'],
        ];
    }

    protected function itemProperties(JsonSchema $schema): array
    {
        return [
            'collect' => $schema->string()->description('Pickup suburb or address, e.g. "111 Eagle St, Brisbane City QLD 4000".')->required(),
            'deliver' => $schema->string()->description('Drop-off suburb or address, same format as collect.')->required(),
            'description' => $schema->string()->description('Short description, e.g. "2019 Suzuki Jimny GLX Manual".')->required(),
            'make' => $schema->string()->description('Manufacturer, e.g. Suzuki.')->required(),
            'model' => $schema->string()->description('Model, e.g. Jimny.')->required(),
            'year' => $schema->string()->description('Four-digit year, e.g. 2019.')->required(),
            'body_type' => $schema->string()->description('Body type, e.g. SUV.')->required(),
            'modifications' => $schema->boolean()->description('True when the car has modifications.')->required(),
            'drivable' => $schema->boolean()->description('True when the car can be driven.')->required(),
            'empty' => $schema->boolean()->description('True when the car will be empty of belongings.')->required(),
        ];
    }
}
