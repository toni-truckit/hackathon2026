<?php

declare(strict_types=1);

namespace App\Filament\Resources\StaticPages\Pages;

use App\Filament\Components\ManageSEORecord;
use App\Filament\Resources\StaticPages\StaticPageResource;

final class ManageStaticPageSEO extends ManageSEORecord
{
    protected static string $resource = StaticPageResource::class;
}
