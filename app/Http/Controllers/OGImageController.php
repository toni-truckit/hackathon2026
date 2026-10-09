<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\OGImage\Contracts\HasOGImage;
use App\OGImage\OGImageGenerator;
use App\Settings\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class OGImageController
{
    public function __invoke(
        string $type,
        string $key,
        string $hash,
        OGImageGenerator $generator,
    ): Response {
        abort_unless((bool) config('og-image.enabled'), 404);

        /** @var class-string<Model>|null $modelClass */
        $modelClass = config('og-image.models.'.$type);

        abort_if($modelClass === null, 404);

        /** @var (Model&HasOGImage)|null $model */
        $model = $modelClass::query()
            ->where((new $modelClass)->getRouteKeyName(), $key)
            ->first();

        abort_if($model === null, 404);

        if (! hash_equals($model->ogImageFingerprint(), $hash)) {
            $current = $model->ogImageUrl();

            abort_if($current === null, 404);

            return redirect()->to($current, 301);
        }

        try {
            $path = $generator->generate($model);
        } catch (Throwable $exception) {
            report($exception);

            return $this->fallback();
        }

        $disk = Storage::disk((string) config('og-image.disk'));

        abort_unless($disk->exists($path), 404);

        $response = $disk->response($path, null, ['Content-Type' => 'image/png']);
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setImmutable();

        return $response;
    }

    private function fallback(): Response
    {
        $fallback = config('og-image.default_image');

        if (blank($fallback)) {
            $fallback = rescue(fn (): ?string => app(SiteSettings::class)->og_image, null, false);
            $fallback = filled($fallback)
                ? (preg_match('/^https?:\/\//', $fallback) ? $fallback : url('storage/'.$fallback))
                : null;
        }

        abort_if(blank($fallback), 404);

        return redirect()->to($fallback, 302);
    }
}
