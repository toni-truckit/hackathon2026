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

    public function icon(int $index): string
    {
        return match ($index % 4) {
            0 => '<path d="M11 2.5 4.5 11H9l-1 6.5L15.5 8H11l0-5.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>',
            1 => '<path d="m10 2.8 1.7 3.5 3.8.5-2.8 2.7.7 3.8L10 11.5 6.6 13.3l.7-3.8L4.5 6.8l3.8-.5L10 2.8Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>',
            2 => '<path d="M10 2.8 16 5.2v4.6c0 3.2-2.4 5.4-6 6.4-3.6-1-6-3.2-6-6.4V5.2L10 2.8Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>',
            default => '<path d="M3 7.5h9.2L14.5 10H17v3.2h-1.4a1.8 1.8 0 0 1-3.2 0H8.2a1.8 1.8 0 0 1-3.2 0H3V7.5Zm2.4 5.7a.8.8 0 1 0 0-1.6.8.8 0 0 0 0 1.6Zm8.2 0a.8.8 0 1 0 0-1.6.8.8 0 0 0 0 1.6Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>',
        };
    }
};
?>

<section class="landing-section">
    <div class="landing-container">
        <p class="landing-label">{{ $this->front()->why_label }}</p>
        <h2 class="landing-h2 mt-3">{{ $this->front()->why_heading }}</h2>

        <ul class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->front()->why_points as $index => $point)
                <li wire:key="why-{{ $point['title'] }}" class="border-t border-white/10 pt-6">
                    <svg class="size-5 text-landing-accent-label" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        {!! $this->icon($index) !!}
                    </svg>
                    <h3 class="mt-4 text-base font-semibold text-landing-heading">{{ $point['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-landing-muted">{{ $point['body'] }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
