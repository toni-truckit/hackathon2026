<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public function placeholder(): string
    {
        return '<div id="setup" class="landing-section scroll-mt-20" aria-hidden="true"></div>';
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }

};
?>

<section id="setup" class="landing-section scroll-mt-20 bg-landing-canvas-elevated">
    <div class="landing-container grid items-start gap-10 lg:grid-cols-2 lg:gap-14">
        <div>
            <p class="landing-label">{{ $this->front()->setup_label }}</p>
            <h2 class="landing-h2 mt-3">{{ $this->front()->setup_heading }}</h2>
            <p class="mt-4 max-w-md text-[15px] leading-relaxed text-landing-muted">{{ $this->front()->setup_body }}</p>
        </div>

        <ol class="rounded-2xl border border-white/8 bg-landing-surface p-2">
            @foreach ($this->front()->setup_steps as $step)
                <li wire:key="setup-{{ $step['number'] }}" class="flex gap-4 border-b border-white/6 px-4 py-4 last:border-b-0">
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-md border border-white/12 font-landing-mono text-xs text-[#b4bcd0]">{{ $step['number'] }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[15px] leading-6 text-landing-body">{{ $step['text'] }}</p>
                        @if ($step['show_url'])
                            <div class="mt-3 flex items-center justify-between gap-3 rounded-[10px] border border-[rgba(247,147,30,0.35)] bg-landing-canvas px-3 py-1.5">
                                <code class="truncate font-landing-mono text-sm text-landing-heading">{{ $this->front()->connector_url }}</code>
                                <button
                                    type="button"
                                    class="shrink-0 cursor-pointer rounded-md bg-white px-3.5 py-2 text-[13px] font-semibold text-landing-accent-brand"
                                    x-data="{ copied: false }"
                                    x-on:click="navigator.clipboard.writeText(@js($this->front()->connector_url)); copied = true; setTimeout(() => copied = false, 2000)"
                                >
                                    <span x-text="copied ? @js($this->front()->copied_label) : @js($this->front()->copy_label)">{{ $this->front()->copy_label }}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
