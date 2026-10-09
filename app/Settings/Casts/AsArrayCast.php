<?php

declare(strict_types=1);

namespace App\Settings\Casts;

use Spatie\LaravelSettings\SettingsCasts\SettingsCast;

final class AsArrayCast implements SettingsCast
{
    /**
     * @return array<int|string, mixed>
     */
    public function get(mixed $payload): array
    {
        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<int|string, mixed>
     */
    public function set(mixed $payload): array
    {
        return is_array($payload) ? $payload : [];
    }
}
