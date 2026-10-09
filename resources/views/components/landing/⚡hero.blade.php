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

<section id="top" class="relative -mt-16 scroll-mt-20 border-b border-white/6 pt-16">
    <div class="landing-hero-glow pointer-events-none absolute inset-x-0 -top-16 h-96" aria-hidden="true"></div>

    <div class="landing-container relative grid items-center gap-16 py-16 lg:grid-cols-2 lg:py-28">
        <div class="flex flex-col gap-4">
            <a href="{{ $this->front()->cta_href }}" class="inline-flex w-fit cursor-pointer items-center gap-2 rounded-full border border-white/10 bg-white/3 py-1.5 pr-3 pl-1.5 text-[13px] text-[#b4bcd0]">
                <span class="rounded-full bg-[rgba(247,147,30,0.15)] px-2 py-0.5 text-xs font-semibold text-landing-accent-label">{{ $this->front()->badge_label }}</span>
                {{ $this->front()->badge_text }}
                <span aria-hidden="true">→</span>
            </a>

            {{-- unslop-ignore: Figma 366:1239 hero title --}}
            <h1 class="landing-hero-title text-[40px] leading-[1.02] font-semibold tracking-[-0.035em] sm:text-5xl lg:text-[64px] lg:leading-[65px]">
                {!! nl2br(e($this->front()->hero_title)) !!}
            </h1>

            <p class="max-w-md text-lg leading-relaxed text-landing-muted">{{ $this->front()->hero_body }}</p>

            <div class="flex flex-wrap gap-3 pt-3">
                <a href="{{ $this->front()->cta_href }}" class="landing-btn-primary">{{ $this->front()->cta_label }}</a>
                <a href="{{ $this->front()->secondary_cta_href }}" class="landing-btn-secondary">{{ $this->front()->secondary_cta_label }}</a>
            </div>

            <p class="text-[13px] text-landing-faint">{{ $this->front()->hero_footnote }}</p>
        </div>

        <div class="flex w-full flex-col items-center">
            <img src="{{ asset('images/claude-you-re-here.png') }}" alt="You're here!" width="219" height="32" class="mb-3 h-8 w-auto">
            <div class="w-full rounded-2xl border border-white/10 bg-linear-to-b from-[#121314] to-landing-surface-inset p-px shadow-[0_30px_80px_-20px_rgba(0,0,0,0.8),0_0_120px_-40px_rgba(247,147,30,0.35)]">
            <div class="flex items-center justify-between border-b border-white/6 px-4 py-3">
                <div class="flex gap-1.5" aria-hidden="true">
                    <span class="size-2.5 rounded-[5px] bg-[#2a2b2e]"></span>
                    <span class="size-2.5 rounded-[5px] bg-[#2a2b2e]"></span>
                    <span class="size-2.5 rounded-[5px] bg-[#2a2b2e]"></span>
                </div>
                <p class="text-xs text-landing-muted">{{ $this->front()->chat_title }}</p>
                <p class="flex items-center gap-1.5 rounded-full border border-white/8 px-2 py-1 text-[11px] text-[#b4bcd0]">
                    <span class="size-1.5 rounded-sm bg-[#4cb782]" aria-hidden="true"></span>
                    {{ $this->front()->chat_badge }}
                </p>
            </div>

            <div class="flex flex-col gap-4 px-5 py-6">
                <div class="flex justify-end">
                    <p class="max-w-[90%] rounded-xl rounded-br-sm border border-white/6 bg-[#1c1d1f] px-4 py-3 text-sm leading-relaxed text-landing-body">
                        {!! nl2br(e($this->front()->chat_user_message)) !!}
                    </p>
                </div>

                <p class="flex items-center gap-2 font-landing-mono text-xs text-landing-faint">
                    <svg class="size-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M3 8.2 6.2 11.4 13 4.6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ $this->front()->chat_status }}
                </p>

                <div class="rounded-xl border border-white/8 bg-white/2 p-4">
                    <p class="text-sm leading-relaxed text-[#b4bcd0]">
                        {{ $this->front()->chat_reply_before }}
                        <strong class="font-semibold text-white">{{ $this->front()->chat_price }}</strong>{{ $this->front()->chat_reply_after }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-2 rounded-lg bg-landing-accent-brand px-3.5 py-2 text-[13px] font-semibold text-white">
                            {{ $this->front()->chat_book_label }}
                            <span aria-hidden="true">↗</span>
                        </span>
                        <span class="font-landing-mono text-[11px] text-landing-faint">{{ $this->front()->chat_domain }}</span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>
