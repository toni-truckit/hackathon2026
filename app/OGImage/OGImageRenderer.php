<?php

declare(strict_types=1);

namespace App\OGImage;

interface OGImageRenderer
{
    public function render(string $html, string $outputPath): void;
}
