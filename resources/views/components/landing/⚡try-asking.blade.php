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

<section id="try-asking" class="landing-section scroll-mt-20 bg-landing-canvas-elevated">
    <div class="landing-container">
        <p class="landing-label">{{ $this->front()->try_label }}</p>
        <h2 class="landing-h2 mt-3 max-w-2xl">{{ $this->front()->try_heading }}</h2>

        <ul class="mt-10 overflow-hidden rounded-[14px] border border-white/8">
            @foreach ($this->front()->prompts as $index => $prompt)
                <li wire:key="prompt-{{ $index }}" class="flex items-center justify-between gap-4 border-b border-white/6 bg-landing-surface px-5 py-4 last:border-b-0">
                    <p class="text-[15px] leading-snug text-landing-body">{{ $prompt['text'] }}</p>
                    <button
                        type="button"
                        class="landing-btn-ghost"
                        x-data="{ copied: false }"
                        x-on:click="navigator.clipboard.writeText(@js($prompt['text'])); copied = true; setTimeout(() => copied = false, 2000)"
                    >
                        <span x-text="copied ? @js($this->front()->copied_label) : @js($this->front()->copy_label)">{{ $this->front()->copy_label }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</section>
