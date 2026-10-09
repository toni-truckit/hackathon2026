<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public ?string $copiedKey = null;

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }

    public function markCopied(string $key): void
    {
        $this->copiedKey = $key;
    }

    public function clearCopied(): void
    {
        $this->copiedKey = null;
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
                        wire:click='$js.copy(@js($prompt["text"]), @js((string) $index))'
                    >
                        {{ $copiedKey === (string) $index ? $this->front()->copied_label : $this->front()->copy_label }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</section>

@script
<script>
    $wire.$js.copy = (text, key) => {
        navigator.clipboard.writeText(text).then(() => {
            $wire.markCopied(key)
            setTimeout(() => $wire.clearCopied(), 2000)
        })
    }
</script>
@endscript
