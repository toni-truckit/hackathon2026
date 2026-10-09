<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class UserForm
{
    /**
     * @throws Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                    ->visibleOn('edit')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('role')
                    ->options(UserRole::class)
                    ->default('user')
                    ->required(),
                TextInput::make('job_title')
                    ->label('Job title')
                    ->maxLength(255),
                TextInput::make('website')
                    ->url()
                    ->maxLength(255),
                Textarea::make('bio')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
