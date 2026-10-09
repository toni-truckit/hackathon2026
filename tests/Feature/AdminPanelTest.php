<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Livewire\Livewire;

it('keeps the password when the edit form leaves it blank', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Developer]);

    $this->actingAs($admin);

    $user = User::factory()->create([
        'role' => UserRole::Writer,
        'password' => 'secret-password',
    ]);

    $hash = $user->password;

    Livewire::test(EditUser::class, ['record' => $user->slug])
        ->fillForm([
            'name' => 'Renamed Writer',
            'password' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->name)->toBe('Renamed Writer')
        ->and($user->password)->toBe($hash);
});
