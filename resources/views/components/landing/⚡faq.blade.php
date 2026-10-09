<?php

declare(strict_types=1);

use App\Models\Faq;
use App\Settings\FrontSettings;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $open = null;

    public function placeholder(): string
    {
        return '<div id="questions" class="landing-section scroll-mt-20" aria-hidden="true"></div>';
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }

    public function toggle(int $id): void
    {
        $this->open = $this->open === $id ? null : $id;
    }

    /** @return Collection<int, Faq> */
    #[Computed]
    public function faqs(): Collection
    {
        return Faq::query()->active()->ordered()->limit(5)->get();
    }
};
?>

<section id="questions" class="landing-section scroll-mt-20 bg-landing-canvas-elevated">
    <div class="landing-container grid gap-10 lg:grid-cols-[16rem_1fr] lg:gap-16">
        <div>
            <p class="text-[13px] font-semibold text-white">{{ $this->front()->questions_label }}</p>
            <h2 class="landing-h2 mt-3">{{ $this->front()->questions_heading }}</h2>
        </div>

        @island(name: 'answers')
            <div class="border-t border-white/8">
                @forelse ($this->faqs as $faq)
                    <div wire:key="faq-{{ $faq->id }}" class="border-b border-white/8">
                        <h3>
                            <button
                                type="button"
                                class="flex w-full cursor-pointer items-center justify-between gap-4 py-5 text-left text-[17px] font-semibold text-landing-heading"
                                wire:click="toggle({{ $faq->id }})"
                                aria-expanded="{{ $open === $faq->id ? 'true' : 'false' }}"
                            >
                                {{ $faq->question }}
                                <span class="inline-block text-[22px] leading-none text-landing-faint {{ $open === $faq->id ? 'rotate-45' : '' }}" aria-hidden="true">+</span>
                            </button>
                        </h3>
                        @if ($open === $faq->id)
                            <div class="pb-5 text-[15px] leading-relaxed text-landing-muted">{!! $faq->answer !!}</div>
                        @endif
                    </div>
                @empty
                    <p class="py-6 text-sm text-landing-faint">{{ $this->front()->questions_empty }}</p>
                @endforelse
            </div>
        @endisland
    </div>
</section>
