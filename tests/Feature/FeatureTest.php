<?php

declare(strict_types=1);

it('redirects to login page when filament is requested being guest', function (): void {
    $response = $this->get('/admin');

    $response->assertStatus(302);
    $response->assertRedirect('/admin/login');
});
