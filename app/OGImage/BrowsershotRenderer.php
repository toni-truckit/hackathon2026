<?php

declare(strict_types=1);

namespace App\OGImage;

use Spatie\Browsershot\Browsershot;

final class BrowsershotRenderer implements OGImageRenderer
{
    public function render(string $html, string $outputPath): void
    {
        $browsershot = Browsershot::html($html)
            ->windowSize((int) config('og-image.width'), (int) config('og-image.height'))
            ->deviceScaleFactor((int) config('og-image.device_scale_factor'))
            ->timeout((int) config('og-image.browser.timeout'));

        if (config('og-image.browser.wait_until_network_idle')) {
            $browsershot->waitUntilNetworkIdle();
        }

        if (config('og-image.browser.no_sandbox')) {
            $browsershot->noSandbox();
        }

        if (config('og-image.browser.ignore_https_errors')) {
            $browsershot->ignoreHttpsErrors();
        }

        $binaries = [
            'node_binary' => 'setNodeBinary',
            'npm_binary' => 'setNpmBinary',
            'chrome_path' => 'setChromePath',
        ];

        foreach ($binaries as $key => $method) {
            $binary = config('og-image.browser.'.$key);

            if (filled($binary)) {
                $browsershot->{$method}($binary);
            }
        }

        $browsershot->save($outputPath);
    }
}
