<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetCarQuote;
use App\Mcp\Tools\GetFurnitureQuote;
use App\Mcp\Tools\GetMotorcycleQuote;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Truckit')]
#[Version('1.0.0')]
#[Instructions('Returns Truckit partner quote results for furniture, cars and motorcycles moved between Australian locations. When Truckit returns no price, the tool reports the partner error instead.')]
class TruckitServer extends Server
{
    protected array $tools = [
        GetFurnitureQuote::class,
        GetCarQuote::class,
        GetMotorcycleQuote::class,
    ];
}
