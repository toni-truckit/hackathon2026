<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\FrontSettings;
use BackedEnum;
use Exception;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ManageFrontSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Front';

    protected static ?string $title = 'Front';

    protected static string $settings = FrontSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Header')
                    ->schema([
                        TextInput::make('menu_label')->required(),
                        TextInput::make('cta_label')->label('Button')->required(),
                        TextInput::make('cta_href')->label('Button link')->required(),
                        Repeater::make('nav')
                            ->schema([
                                TextInput::make('label')->required(),
                                TextInput::make('href')->label('Link')->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Hero')
                    ->schema([
                        TextInput::make('badge_label')->required(),
                        TextInput::make('badge_text')->required(),
                        Textarea::make('hero_title')->required()->rows(3)->helperText('Each line is a new line on the page.')->columnSpanFull(),
                        Textarea::make('hero_body')->required()->rows(3)->columnSpanFull(),
                        TextInput::make('secondary_cta_label')->label('Second button')->required(),
                        TextInput::make('secondary_cta_href')->label('Second button link')->required(),
                        TextInput::make('hero_footnote')->required()->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Chat')
                    ->schema([
                        TextInput::make('chat_title')->label('Window title')->required(),
                        TextInput::make('chat_badge')->required(),
                        Textarea::make('chat_user_message')->label('User message')->required()->columnSpanFull(),
                        TextInput::make('chat_status')->required()->columnSpanFull(),
                        TextInput::make('chat_reply_before')->label('Reply before price')->required(),
                        TextInput::make('chat_price')->label('Price')->required(),
                        Textarea::make('chat_reply_after')->label('Reply after price')->required()->columnSpanFull(),
                        TextInput::make('chat_book_label')->label('Book button')->required(),
                        TextInput::make('chat_domain')->required(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('How it works')
                    ->schema([
                        TextInput::make('how_label')->required(),
                        TextInput::make('how_heading')->required(),
                        Repeater::make('steps')
                            ->schema([
                                TextInput::make('index')->required(),
                                TextInput::make('title')->required(),
                                Textarea::make('body')->required()->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Try asking')
                    ->schema([
                        TextInput::make('try_label')->required(),
                        TextInput::make('try_heading')->required(),
                        TextInput::make('copy_label')->required(),
                        TextInput::make('copied_label')->required(),
                        Repeater::make('prompts')
                            ->schema([
                                Textarea::make('text')->required(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Why Truckit')
                    ->schema([
                        TextInput::make('why_label')->required(),
                        TextInput::make('why_heading')->required(),
                        Repeater::make('why_points')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('body')->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Set up')
                    ->schema([
                        TextInput::make('setup_label')->required(),
                        TextInput::make('setup_heading')->required(),
                        Textarea::make('setup_body')->required()->columnSpanFull(),
                        TextInput::make('connector_url')->label('Connector address')->required()->columnSpanFull(),
                        Repeater::make('setup_steps')
                            ->schema([
                                TextInput::make('number')->required(),
                                Textarea::make('text')->required(),
                                Toggle::make('show_url')->label('Show connector address'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Questions and close')
                    ->schema([
                        TextInput::make('questions_label')->required(),
                        TextInput::make('questions_heading')->required(),
                        TextInput::make('questions_empty')->required()->columnSpanFull(),
                        Textarea::make('cta_heading')->label('Closing heading')->required()->columnSpanFull(),
                        TextInput::make('footer_brand')->required(),
                        TextInput::make('footer_tagline')->required(),
                        Textarea::make('footer_disclaimer')->required()->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
