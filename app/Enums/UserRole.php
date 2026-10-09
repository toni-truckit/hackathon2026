<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum UserRole: string implements HasColor, HasIcon, HasLabel
{
    case Developer = 'developer';

    case Admin = 'admin';

    case Writer = 'writer';

    case User = 'user';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Developer => 'Developer',
            self::Admin => 'Admin',
            self::Writer => 'Writer',
            self::User => 'User',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Developer => Color::Red,
            self::Admin => Color::Blue,
            self::Writer => Color::Yellow,
            self::User => Color::Green,
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Developer => Heroicon::CodeBracket,
            self::Admin => Heroicon::ShieldCheck,
            self::Writer => Heroicon::PencilSquare,
            self::User => Heroicon::UserCircle,
        };
    }
}
