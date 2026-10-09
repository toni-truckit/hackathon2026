<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update(
            'site.description',
            fn (string $description): string => 'Get an instant Truckit price for your move, right in the chat. Then book it on Truckit in a few taps.',
        );

        $this->migrator->update('site.robots_txt', fn (string $robots): string => implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /pulse',
            '',
            'Sitemap: '.config('app.url').'/sitemap.xml',
        ]));
    }
};
