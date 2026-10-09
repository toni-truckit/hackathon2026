<?php

declare(strict_types=1);

it('registers business_api and monolith mysql connections', function (): void {
    $connections = config('database.connections');

    expect($connections)->toHaveKeys(['business_api', 'monolith']);

    expect($connections['business_api']['driver'])->toBe('mysql')
        ->and($connections['business_api']['database'])->toBeString();

    expect($connections['monolith']['driver'])->toBe('mysql')
        ->and($connections['monolith']['database'])->toBeString();

    if (extension_loaded('pdo_mysql')) {
        expect($connections['monolith']['options'][PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT])->toBeFalse();
    }
});
