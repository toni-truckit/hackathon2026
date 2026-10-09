<?php

declare(strict_types=1);

it('ships docker stack files', function (): void {
    $paths = [
        'Dockerfile',
        'docker-compose.yml',
        'docker-compose.prod.yml',
        'docker-compose.external-db.yml',
        'docker-entrypoint.sh',
        'docker/nginx/default.conf',
        'docker/php/local.ini',
        'docker/supervisor/supervisord.conf',
    ];

    foreach ($paths as $path) {
        expect(base_path($path))->toBeReadableFile();
    }

    $compose = file_get_contents(base_path('docker-compose.yml'));

    expect($compose)
        ->toContain('mcp-storage-framework')
        ->toContain('mcp-bootstrap-cache')
        ->not->toMatch('/^\s+env_file:/m');
});
