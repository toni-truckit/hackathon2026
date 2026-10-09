<?php

declare(strict_types=1);

namespace Tests\Support;

use App\OGImage\OGImageRenderer;

final class FakeOGImageRenderer implements OGImageRenderer
{
    public int $calls = 0;

    /** @var array<int, string> */
    public array $renderedHtml = [];

    public function render(string $html, string $outputPath): void
    {
        $this->calls++;
        $this->renderedHtml[] = $html;

        file_put_contents($outputPath, "\x89PNG\r\n\x1a\nfake-og-image");
    }
}
