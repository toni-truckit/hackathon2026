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
});
