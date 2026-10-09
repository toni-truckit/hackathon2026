<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Settings\Pages;

use App\Enums\UserRole;
use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\SocialMediaSettings;
use BackedEnum;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ManageSocialMedia extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string $settings = SocialMediaSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    public static function canAccess(): bool
    {
        return auth()->user()->role !== UserRole::User;
    }

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('linkedin')
                    ->label('LinkedIn')
                    ->prefix('https://www.linkedin.com/in/'),
                TextInput::make('facebook')
                    ->label('Facebook')
                    ->prefix('https://www.facebook.com/'),
            ]);
    }
}
