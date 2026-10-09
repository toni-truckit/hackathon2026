<?php

declare(strict_types=1);

namespace App\Filament\Commands\FileGenerators\Resources;

use App\Filament\Components\ManageSEORecord;
use Filament\Commands\FileGenerators\Concerns\CanGenerateGetHeaderActionsMethod;
use Filament\Commands\FileGenerators\Resources\Pages\Concerns\CanGenerateResourceProperty;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Nette\PhpGenerator\ClassType;

class ResourceSEOPageClassGenerator extends ClassGenerator
{
    use CanGenerateGetHeaderActionsMethod;
    use CanGenerateResourceProperty;

    /**
     * @param  class-string  $resourceFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $resourceFqn,
        protected bool $hasViewOperation,
        protected bool $isSoftDeletable,
    ) {}

    public function getNamespace(): string
    {
        return $this->extractNamespace($this->getFqn());
    }

    /**
     * @return array<string>
     */
    public function getImports(): array
    {
        $extends = $this->getExtends();
        $extendsBasename = class_basename($extends);

        return [
            $this->getResourceFqn(),
            ...(($extendsBasename === class_basename($this->getFqn())) ? [$extends => 'Base' . $extendsBasename] : [$extends]),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return ManageSEORecord::class;
    }

    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * @return class-string
     */
    public function getResourceFqn(): string
    {
        return $this->resourceFqn;
    }

    public function hasViewOperation(): bool
    {
        return $this->hasViewOperation;
    }

    public function isSoftDeletable(): bool
    {
        return $this->isSoftDeletable;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addResourcePropertyToClass($class);
    }
}
