<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PageType: string implements HasColor, HasLabel
{
    case LandingPage = 'landing_page';
    case IndexPage = 'index_page';
    case ContentPage = 'content_page';
    case PageWithForm = 'page_with_form';
    case Faq = 'faq';
    case Contact = 'contact';

    public function getColor(): array
    {
        return match ($this) {
            self::LandingPage => Color::Green,
            self::IndexPage => Color::Blue,
            self::ContentPage => Color::Gray,
            self::PageWithForm => Color::Yellow,
            self::Faq => Color::Orange,
            self::Contact => Color::Sky,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::LandingPage => 'Landing Page',
            self::IndexPage => 'Index Page',
            self::ContentPage => 'Content Page',
            self::PageWithForm => 'Page with Form',
            self::Faq => 'FAQ Page',
            self::Contact => 'Contact Page',
        };
    }
}
