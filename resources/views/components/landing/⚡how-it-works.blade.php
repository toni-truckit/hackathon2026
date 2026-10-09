<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }
};
?>

<section id="how-it-works" class="landing-section scroll-mt-20">
    <div class="landing-container">
        <p class="landing-label">{{ $this->front()->how_label }}</p>
        <h2 class="landing-h2 mt-3 max-w-xl">{{ $this->front()->how_heading }}</h2>

        <ol class="mt-12 grid gap-px overflow-hidden rounded-2xl border border-white/7 bg-white/7 md:grid-cols-3">
            @foreach ($this->front()->steps as $step)
                <li wire:key="step-{{ $step['index'] }}" class="bg-landing-surface-inset px-7 py-8">
                    <p class="font-landing-mono text-[13px] text-landing-accent-label">{{ $step['index'] }}</p>
                    <h3 class="mt-8 text-xl font-semibold tracking-tight text-landing-heading">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-[15px] leading-relaxed text-landing-muted">{{ $step['body'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
