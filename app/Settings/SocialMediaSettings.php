<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class SocialMediaSettings extends Settings
{
    public ?string $linkedin;

    public ?string $facebook;

    public static function group(): string
    {
        return 'social-media';
    }
}
