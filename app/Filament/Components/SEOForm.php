<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Illuminate\Database\Eloquent\Model;

final class SEOForm
{
    public static function schema(): array
    {
        return [
            Section::make('Meta')
                ->schema([
                    TextInput::make('meta_title')
                        ->maxLength(255),
                    Textarea::make('meta_description')
                        ->maxLength(255),
                    TagsInput::make('meta_keywords')
                        ->columnSpanFull(),
                ])
                ->columns(),
            Section::make('Open Graph')
                ->schema([
                    TextInput::make('og_title')
                        ->label('OG Title')
                        ->maxLength(255),
                    Textarea::make('og_description')
                        ->label('OG Description')
                        ->maxLength(255),
                    FileUpload::make('og_image')
                        ->label('OG Image')
                        ->image()
                        ->imageEditor()
                        ->imageCropAspectRatio('4:3')
                        ->openable()
                        ->previewable()
                        ->preserveFilenames()
                        ->downloadable()
                        ->deletable()
                        ->rules([
                            'required',
                            'dimensions:ratio=4/3',
                        ])
                        ->helperText('Leave empty to use the auto-generated social card'),
                    TextInput::make('og_url')
                        ->label('OG URL')
                        ->helperText('Leave empty to use default URL')
                        ->url(),
                    View::make('filament.components.og-image-preview')
                        ->visible(fn (?Model $record): bool => $record instanceof Model && method_exists($record, 'ogImageUrl'))
                        ->viewData(fn (?Model $record): array => [
                            'previewUrl' => $record instanceof Model && method_exists($record, 'ogImageUrl') ? $record->ogImageUrl() : null,
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(),
            Section::make('Advanced')
                ->schema([
                    TextInput::make('canonical')
                        ->helperText('Leave empty to use the current URL')
                        ->url(),
                    TagsInput::make('robots'),
                    TextInput::make('author'),
                    TextInput::make('publisher'),
                ])
                ->columns(),
        ];
    }
}
