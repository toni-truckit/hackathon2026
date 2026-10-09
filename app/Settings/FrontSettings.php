<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Casts\AsArrayCast;
use Spatie\LaravelSettings\Settings;

final class FrontSettings extends Settings
{
    public string $menu_label;

    public string $cta_label;

    public string $cta_href;

    public array $nav;

    public string $badge_label;

    public string $badge_text;

    public string $hero_title;

    public string $hero_body;

    public string $secondary_cta_label;

    public string $secondary_cta_href;

    public string $hero_footnote;

    public string $chat_title;

    public string $chat_badge;

    public string $chat_user_message;

    public string $chat_status;

    public string $chat_reply_before;

    public string $chat_price;

    public string $chat_reply_after;

    public string $chat_book_label;

    public string $chat_domain;

    public string $how_label;

    public string $how_heading;

    public array $steps;

    public string $try_label;

    public string $try_heading;

    public string $copy_label;

    public string $copied_label;

    public array $prompts;

    public string $why_label;

    public string $why_heading;

    public array $why_points;

    public string $setup_label;

    public string $setup_heading;

    public string $setup_body;

    public string $connector_url;

    public array $setup_steps;

    public string $questions_label;

    public string $questions_heading;

    public string $questions_empty;

    public string $cta_heading;

    public string $footer_brand;

    public string $footer_tagline;

    public string $footer_disclaimer;

    public static function group(): string
    {
        return 'front';
    }

    /**
     * @return array<string, class-string>
     */
    public static function casts(): array
    {
        return [
            'nav' => AsArrayCast::class,
            'steps' => AsArrayCast::class,
            'prompts' => AsArrayCast::class,
            'why_points' => AsArrayCast::class,
            'setup_steps' => AsArrayCast::class,
        ];
    }
}
