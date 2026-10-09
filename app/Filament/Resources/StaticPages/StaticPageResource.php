<?php

declare(strict_types=1);

namespace App\Filament\Resources\StaticPages;

use App\Enums\PageType;
use App\Filament\Components\SEOAction;
use App\Filament\Resources\StaticPages\Pages\ManageStaticPages;
use App\Filament\Resources\StaticPages\Pages\ManageStaticPageSEO;
use App\Models\StaticPage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentDuplicate;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->required(),
                Select::make('type')
                    ->options(PageType::class)
                    ->default(PageType::ContentPage)
                    ->live()
                    ->required(),
                TextInput::make('name')
                    ->visible(fn (Get $get): bool => in_array($get('type'), [PageType::IndexPage, PageType::PageWithForm]))
                    ->columnSpanFull()
                    ->label('Route Name'),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->label('Cover Image')
                    ->collection('cover')
                    ->image()
                    ->required()
                    ->preserveFilenames()
                    ->imageEditor()
                    ->imageAspectRatio('4:3')
                    ->automaticallyCropImagesToAspectRatio()
                    ->openable()
                    ->previewable()
                    ->downloadable()
                    ->deletable()
                    ->rules([
                        'required',
                        'dimensions:ratio=4/3',
                    ]),
                TagsInput::make('tags')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->visible(fn (Get $get): bool => $get('type') === PageType::ContentPage),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns()
                    ->columnSpanFull()
                    ->components([
                        TextEntry::make('title')
                            ->prefixAction(
                                Action::make('preview')
                                    ->color(Color::Green)
                                    ->visible(fn (StaticPage $staticPage): bool => $staticPage->getURLValue() !== null)
                                    ->icon(Heroicon::ArrowTopRightOnSquare)
                                    ->url(fn (StaticPage $staticPage): ?string => $staticPage->getURLValue())
                                    ->openUrlInNewTab()
                            ),
                        TextEntry::make('type')
                            ->badge(),
                        TextEntry::make('tags')
                            ->badge(),
                        TextEntry::make('name')
                            ->columnSpanFull()
                            ->visible(fn (StaticPage $staticPage): bool => in_array($staticPage->type, [PageType::IndexPage, PageType::PageWithForm]))
                            ->label('Route Name'),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->markdown()
                            ->visible(fn (StaticPage $staticPage): bool => $staticPage->type === PageType::ContentPage)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->counts('views')
                    ->color(
                        fn (int $state): array => $state > 0 ? Color::Green : Color::Neutral
                    )
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                SEOAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStaticPages::route('/'),
            'seo' => ManageStaticPageSEO::route('/{record}/seo'),
        ];
    }
}
