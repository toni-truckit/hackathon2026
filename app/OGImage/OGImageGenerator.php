<?php

declare(strict_types=1);

namespace App\OGImage;

use App\OGImage\Contracts\HasOGImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class OGImageGenerator
{
    public function __construct(private OGImageRenderer $renderer) {}

    public function generate(Model&HasOGImage $model, bool $force = false): string
    {
        $disk = (string) config('og-image.disk');
        $path = $this->pathFor($model);

        if (! $force && Storage::disk($disk)->exists($path)) {
            return $path;
        }

        $lock = Cache::lock('og-image:'.$model->ogImageType().':'.$model->getKey(), 60);

        return $lock->block(30, function () use ($model, $path, $disk, $force): string {
            if (! $force && Storage::disk($disk)->exists($path)) {
                return $path;
            }

            $absolutePath = Storage::disk($disk)->path($path);

            File::ensureDirectoryExists(dirname($absolutePath));

            $this->renderer->render(
                view($model->ogImageView(), $model->ogImageData()->toArray())->render(),
                $absolutePath,
            );

            $this->pruneStale($model, $path, $disk);

            return $path;
        });
    }

    public function pathFor(Model&HasOGImage $model): string
    {
        return trim((string) config('og-image.directory'), '/').'/'.$model->ogImageRelativePath();
    }

    public function deleteFor(Model&HasOGImage $model): void
    {
        $disk = (string) config('og-image.disk');
        $directory = dirname($this->pathFor($model));

        if (! Storage::disk($disk)->exists($directory)) {
            return;
        }

        foreach ($this->staleFiles($model, $directory, $disk) as $file) {
            Storage::disk($disk)->delete($file);
        }
    }

    private function pruneStale(Model&HasOGImage $model, string $currentPath, string $disk): void
    {
        foreach ($this->staleFiles($model, dirname($currentPath), $disk) as $file) {
            if ($file !== $currentPath) {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function staleFiles(Model&HasOGImage $model, string $directory, string $disk): array
    {
        $prefix = $model->getRouteKey().'-';

        return array_values(array_filter(
            Storage::disk($disk)->files($directory),
            fn (string $file): bool => str_starts_with(basename($file), $prefix),
        ));
    }
}
