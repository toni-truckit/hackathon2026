<?php

declare(strict_types=1);

return [

    'mcp_url' => env('TRUCKIT_MCP_URL', 'https://mcp.truckit.net/mcp'),

    'claude_directory_url' => env('TRUCKIT_CLAUDE_DIRECTORY_URL'),

    'sample_prompts' => [
        'How much to move a 2 bedroom apartment from Newtown to Wollongong on 21 December?',
        'What would it cost to ship 2 pallets from Sydney to Melbourne next week?',
        'Price to transport my car from Perth to Brisbane',
    ],

];
