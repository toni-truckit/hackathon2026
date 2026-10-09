<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ScheduleOption: string implements HasColor, HasLabel
{
    case Now = 'now';

    case Later = 'later';

    case Draft = 'draft';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Now => Color::Green,
            self::Later => Color::Blue,
            self::Draft => Color::Gray,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Now => 'Publish Now',
            self::Later => 'Schedule for Later',
            self::Draft => 'Save as Draft',
        };
    }
}
