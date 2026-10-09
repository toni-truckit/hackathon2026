<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public function placeholder(): string
    {
        return '<footer class="border-t border-white/6" aria-hidden="true"></footer>';
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }
};
?>

<footer class="border-t border-white/6 bg-landing-canvas-elevated">
    <div class="landing-container flex flex-col gap-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-[13px]">
            <span class="font-semibold text-landing-heading">{{ $this->front()->footer_brand }}</span>
            <span class="text-landing-faint"> · {{ $this->front()->footer_tagline }}</span>
        </p>
        <p class="text-[13px] text-landing-faint">{{ $this->front()->footer_disclaimer }}</p>
    </div>
</footer>
