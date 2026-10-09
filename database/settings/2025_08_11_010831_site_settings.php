<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.name', 'TruckIt Connect');
        $this->migrator->add('site.description', 'Get an instant Truckit price for your move, right in the chat. Then book it on Truckit in a few taps.');
        $this->migrator->add('site.logo', '');
        $this->migrator->add('site.favicon', '');
        $this->migrator->add('site.og_image', '');
        $this->migrator->add('site.header_scripts', '');
        $this->migrator->add('site.footer_scripts', '');
        $this->migrator->add('site.robots_txt', '');
    }
};
