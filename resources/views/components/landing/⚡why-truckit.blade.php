<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public function placeholder(): string
    {
        return '<div class="landing-section" aria-hidden="true"></div>';
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }
};
?>

<section class="landing-section">
    <div class="landing-container">
        <p class="landing-label">{{ $this->front()->why_label }}</p>
        <h2 class="landing-h2 mt-3">{{ $this->front()->why_heading }}</h2>

        <ul class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->front()->why_points as $point)
                <li wire:key="why-{{ $point['title'] }}" class="border-t border-white/10 pt-6">
                    <svg class="size-5 text-landing-accent-label" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M4 10.5 8 14.5 16 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="mt-4 text-base font-semibold text-landing-heading">{{ $point['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-landing-muted">{{ $point['body'] }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
