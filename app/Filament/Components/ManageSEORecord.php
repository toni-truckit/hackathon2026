<?php

declare(strict_types=1);

namespace App\Filament\Components;

use AchyutN\LaravelSEO\Models\SEO;
use BackedEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ManageSEORecord extends EditRecord
{
    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $breadcrumb = 'SEO';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MagnifyingGlass;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record)->load('seo');
        $this->form->fill($this->record?->seo?->toArray() ?? []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema(SEOForm::schema());
    }

    public function getTitle(): string|Htmlable
    {
        return parent::getTitle().' SEO';
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        SEO::query()->updateOrCreate([
            'model_id' => $record->getKey(),
            'model_type' => $record->getMorphClass(),
        ], $data);

        return $record;
    }
}
