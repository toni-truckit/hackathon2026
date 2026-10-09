<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class SEOAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('SEO');

        $this->icon(Heroicon::MagnifyingGlass);

        $this->url(function ($livewire, $record): string {
            /** @var resource $resource */
            $resource = resolve($livewire->getResource());

            return $resource->getUrl('seo', ['record' => $record]);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'seo';
    }
}
