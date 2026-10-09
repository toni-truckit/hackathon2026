<?php

declare(strict_types=1);

it('returns a redirection response', function (): void {
    $response = $this->get('/admin');

    $response->assertStatus(302);
});
