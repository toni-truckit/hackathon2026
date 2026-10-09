<?php

declare(strict_types=1);

use App\Settings\FrontSettings;
use Livewire\Component;

new class extends Component
{
    public bool $menuOpen = false;

    public function toggleMenu(): void
    {
        $this->menuOpen = ! $this->menuOpen;
    }

    public function front(): FrontSettings
    {
        return app(FrontSettings::class);
    }
};
?>

<header class="sticky top-0 z-50 border-b border-white/6 bg-landing-canvas/80 backdrop-blur-md">
    <div class="landing-container flex h-16 items-center justify-between gap-4">
        <a href="{{ route('landing-page') }}" class="shrink-0 cursor-pointer" wire:navigate>
            <img src="{{ asset('images/logo.svg') }}" alt="{{ $this->front()->footer_brand }}" width="191" height="30" class="h-10 w-auto">
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="On this page">
            @foreach ($this->front()->nav as $item)
                <a wire:key="nav-{{ $item['href'] }}" href="{{ $item['href'] }}" class="cursor-pointer rounded-lg px-3 py-2 text-sm text-landing-muted hover:text-landing-heading">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ $this->front()->cta_href }}" class="landing-btn-primary ml-2 px-3.5 py-2 text-sm">{{ $this->front()->cta_label }}</a>
        </nav>

        <button
            type="button"
            class="cursor-pointer rounded-lg p-2 text-landing-muted md:hidden"
            wire:click="toggleMenu"
            aria-expanded="{{ $menuOpen ? 'true' : 'false' }}"
        >
            <span class="sr-only">{{ $this->front()->menu_label }}</span>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    @if ($menuOpen)
        <nav class="landing-container flex flex-col gap-1 pb-4 md:hidden" aria-label="On this page">
            @foreach ($this->front()->nav as $item)
                <a wire:key="nav-mobile-{{ $item['href'] }}" href="{{ $item['href'] }}" class="cursor-pointer rounded-lg px-3 py-3 text-landing-muted" wire:click="toggleMenu">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ $this->front()->cta_href }}" class="landing-btn-primary mt-2">{{ $this->front()->cta_label }}</a>
        </nav>
    @endif
</header>
