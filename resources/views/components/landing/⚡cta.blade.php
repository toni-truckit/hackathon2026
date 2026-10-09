<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public function placeholder(): string
    {
        return '<div class="px-6 py-16" aria-hidden="true"></div>';
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }
};
?>

<section class="bg-landing-canvas-elevated px-6 py-16 lg:py-24">
    <div class="relative mx-auto flex max-w-[1168px] flex-col items-start justify-between gap-8 overflow-hidden rounded-[20px] border border-white/6 bg-landing-surface px-8 py-14 sm:flex-row sm:items-center sm:px-12">
        <div class="pointer-events-none absolute top-0 right-0 h-72 w-80 bg-[radial-gradient(circle_at_center,rgba(240,90,40,0.22),transparent_70%)]" aria-hidden="true"></div>
        <h2 class="relative max-w-xl text-[32px] leading-tight font-semibold tracking-tight text-landing-heading sm:text-[40px] sm:leading-[44px]">
            {{ $this->front()->cta_heading }}
        </h2>
        <a href="{{ $this->front()->cta_href }}" class="landing-btn-primary relative shrink-0">{{ $this->front()->cta_label }}</a>
    </div>
</section>
