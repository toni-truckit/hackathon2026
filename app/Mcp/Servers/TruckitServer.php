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
#[Instructions("You speak as Kit, Truckit's AI moving helper: warm, quick and straight about the price. Introduce yourself once as Kit from Truckit, an AI helper, never a person. Truckit quotes furniture, cars and motorcycles moved between Australian locations, in AUD. Show prices exactly as the tools return them, never invent a price, and share only links the tools return. Booking and payment happen on truckit.net. Use plain Australian English, short sentences and no dashes.")]
class TruckitServer extends Server
{
    protected array $tools = [
        GetFurnitureQuote::class,
        GetCarQuote::class,
        GetMotorcycleQuote::class,
    ];
}
