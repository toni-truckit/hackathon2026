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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ManageFrontSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Landing page';

    protected static ?string $title = 'Landing page';

    protected static string $settings = FrontSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    /**
     * @throws Exception
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Landing')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Header')
                            ->icon(Heroicon::OutlinedBars3)
                            ->schema([
                                Section::make('Top bar')
                                    ->description('Sticky bar on the landing page.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('menu_label')
                                            ->label('Menu button')
                                            ->helperText('Read aloud on the mobile menu button.')
                                            ->required(),
                                        TextInput::make('cta_label')
                                            ->label('Button')
                                            ->required(),
                                        TextInput::make('cta_href')
                                            ->label('Button link')
                                            ->helperText('Anchor like #setup, or a full link. Also used on the closing banner.')
                                            ->required()
                                            ->columnSpanFull(),
                                        Repeater::make('nav')
                                            ->label('Menu links')
                                            ->addActionLabel('Add link')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                            ->schema([
                                                TextInput::make('label')->required(),
                                                TextInput::make('href')->label('Link')->required(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Hero')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->schema([
                                Section::make('Opening')
                                    ->description('Left side of the first screen.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('badge_label')
                                            ->label('Badge')
                                            ->required(),
                                        TextInput::make('badge_text')
                                            ->label('Badge line')
                                            ->required(),
                                        Textarea::make('hero_title')
                                            ->label('Heading')
                                            ->helperText('Each line break shows on the page.')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('hero_body')
                                            ->label('Intro')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('secondary_cta_label')
                                            ->label('Second button')
                                            ->required(),
                                        TextInput::make('secondary_cta_href')
                                            ->label('Second button link')
                                            ->required(),
                                        TextInput::make('hero_footnote')
                                            ->label('Note under buttons')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Chat')
                            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                            ->schema([
                                Section::make('Sample chat')
                                    ->description('Card on the right of the first screen. Price sits inside the reply.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('chat_title')
                                            ->label('Window title')
                                            ->required(),
                                        TextInput::make('chat_badge')
                                            ->label('Status badge')
                                            ->required(),
                                        Textarea::make('chat_user_message')
                                            ->label('Visitor message')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('chat_status')
                                            ->label('Status line')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('chat_reply_before')
                                            ->label('Reply before price')
                                            ->required(),
                                        TextInput::make('chat_price')
                                            ->label('Price')
                                            ->required(),
                                        Textarea::make('chat_reply_after')
                                            ->label('Reply after price')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('chat_book_label')
                                            ->label('Book button')
                                            ->required(),
                                        TextInput::make('chat_domain')
                                            ->label('Site name')
                                            ->required(),
                                    ]),
                            ]),
                        Tab::make('How it works')
                            ->icon(Heroicon::OutlinedQueueList)
                            ->schema([
                                Section::make('Steps')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('how_label')
                                            ->label('Eyebrow')
                                            ->required(),
                                        TextInput::make('how_heading')
                                            ->label('Heading')
                                            ->required(),
                                        Repeater::make('steps')
                                            ->addActionLabel('Add step')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->schema([
                                                TextInput::make('index')
                                                    ->label('Number')
                                                    ->required(),
                                                TextInput::make('title')
                                                    ->required(),
                                                Textarea::make('body')
                                                    ->required()
                                                    ->rows(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Try asking')
                            ->icon(Heroicon::OutlinedClipboardDocument)
                            ->schema([
                                Section::make('Example prompts')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('try_label')
                                            ->label('Eyebrow')
                                            ->required(),
                                        TextInput::make('try_heading')
                                            ->label('Heading')
                                            ->required(),
                                        TextInput::make('copy_label')
                                            ->label('Copy button')
                                            ->required(),
                                        TextInput::make('copied_label')
                                            ->label('Copied button')
                                            ->required(),
                                        Repeater::make('prompts')
                                            ->addActionLabel('Add prompt')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                                            ->schema([
                                                Textarea::make('text')
                                                    ->label('Prompt')
                                                    ->required()
                                                    ->rows(2),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Why Truckit')
                            ->icon(Heroicon::OutlinedCheckBadge)
                            ->schema([
                                Section::make('Reasons')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('why_label')
                                            ->label('Eyebrow')
                                            ->required(),
                                        TextInput::make('why_heading')
                                            ->label('Heading')
                                            ->required(),
                                        Repeater::make('why_points')
                                            ->label('Points')
                                            ->addActionLabel('Add point')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('body')->required()->rows(2),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Set up')
                            ->icon(Heroicon::OutlinedWrenchScrewdriver)
                            ->schema([
                                Section::make('Connector steps')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('setup_label')
                                            ->label('Eyebrow')
                                            ->required(),
                                        TextInput::make('setup_heading')
                                            ->label('Heading')
                                            ->required(),
                                        Textarea::make('setup_body')
                                            ->label('Intro')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('connector_url')
                                            ->label('Connector address')
                                            ->helperText('Shown on any step that turns on Show address.')
                                            ->required()
                                            ->columnSpanFull(),
                                        Repeater::make('setup_steps')
                                            ->label('Steps')
                                            ->addActionLabel('Add step')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['text'] ?? null)
                                            ->schema([
                                                TextInput::make('number')
                                                    ->required(),
                                                Toggle::make('show_url')
                                                    ->label('Show address'),
                                                Textarea::make('text')
                                                    ->required()
                                                    ->rows(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Close')
                            ->icon(Heroicon::OutlinedFlag)
                            ->schema([
                                Section::make('Questions')
                                    ->description('Headings only. Question text lives in FAQs.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('questions_label')
                                            ->label('Eyebrow')
                                            ->required(),
                                        TextInput::make('questions_heading')
                                            ->label('Heading')
                                            ->required(),
                                        TextInput::make('questions_empty')
                                            ->label('Empty message')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Closing banner')
                                    ->schema([
                                        Textarea::make('cta_heading')
                                            ->label('Heading')
                                            ->helperText('Button label and link come from the Header tab.')
                                            ->required()
                                            ->rows(2),
                                    ]),
                                Section::make('Footer')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('footer_brand')
                                            ->label('Brand')
                                            ->required(),
                                        TextInput::make('footer_tagline')
                                            ->label('Tagline')
                                            ->required(),
                                        Textarea::make('footer_disclaimer')
                                            ->label('Disclaimer')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
