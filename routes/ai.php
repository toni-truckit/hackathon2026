<?php

declare(strict_types=1);

use App\Mcp\Servers\TruckitServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', TruckitServer::class)->middleware('throttle:60,1');
