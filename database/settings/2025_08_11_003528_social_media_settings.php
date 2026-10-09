<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social-media.linkedin', '');
        $this->migrator->add('social-media.facebook', '');
        $this->migrator->add('social-media.instagram', '');
    }
};
