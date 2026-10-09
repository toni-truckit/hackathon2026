<?php

declare(strict_types=1);

namespace App\Filament\Commands\FileGenerators\Resources;

use App\Filament\Components\SEOAction;
use App\Traits\HasSEODetails;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator as BaseResourceTableClassGenerator;
use ReflectionClass;
use ReflectionException;

class ResourceTableClassGenerator extends BaseResourceTableClassGenerator
{
    public function getTableActions(): array
    {
        $actions = parent::getTableActions();

        if (! $this->modelHasSEO()) {
            return $actions;
        }

        $action = SEOAction::class;
        $this->importUnlessPartial($action);
        $actions[] = $action;

        return $actions;
    }

    private function modelHasSEO(): bool
    {
        try {
            $reflectionClass = new ReflectionClass($this->modelFqn);

            return in_array(HasSEODetails::class, $reflectionClass->getTraitNames(), true);
        } catch (ReflectionException $reflectionException) {
            return false;
        }
    }
}
