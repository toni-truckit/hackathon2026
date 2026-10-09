<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\Faq;
use App\Models\StaticPage;
use Livewire\Component;

new class extends Component
{
    public ?StaticPage $staticPage = null;

    public function mount(): void
    {
        $this->staticPage = StaticPage::query()
            ->where('type', PageType::Faq)
            ->first();
    }

    /** @return array<int, Faq> */
    public function faqs(): array
    {
        return Faq::query()->active()->ordered()->get()->all();
    }

    /** @return array<string, mixed> */
    public function faqSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (Faq $faq): array => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ], $this->faqs()),
        ];
    }
};
?>

<main class="container-xl relative my-16 antialiased">
    <div class="bg-white/70 backdrop-blur-md border border-neutral-200/60 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="px-6 py-12 lg:py-16 border-b border-neutral-100/80">
            <div class="mx-auto max-w-3xl">
                <x-shared.breadcrumbs :items="[
                    ['label' => 'Home', 'url' => route('landing-page')],
                    ['label' => 'FAQ', 'url' => null],
                ]" />
            </div>

            <header class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl lg:text-5xl font-black tracking-tight text-neutral-900 leading-tight">
                    {{ $staticPage?->title ?? 'Frequently Asked Questions' }}
                </h1>

                @if (filled($staticPage?->description))
                    <p class="mt-6 text-neutral-500 leading-relaxed">
                        {{ $staticPage->description }}
                    </p>
                @endif
            </header>
        </div>

        <div class="px-6 py-12 lg:px-16 lg:py-20">
            <div class="mx-auto max-w-3xl space-y-4" x-data="{ open: 0 }">
                @forelse ($this->faqs() as $index => $faq)
                    <div class="overflow-hidden rounded-2xl border border-neutral-200/70 bg-white shadow-xs">
                        <h3>
                            <button
                                type="button"
                                x-on:click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                                x-bind:aria-expanded="open === {{ $index }}"
                            >
                                <span class="text-lg font-bold text-neutral-900">{{ $faq->question }}</span>
                                <svg
                                    class="h-5 w-5 shrink-0 text-primary transition-transform duration-200"
                                    x-bind:class="open === {{ $index }} ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </h3>

                        <div x-show="open === {{ $index }}" x-transition class="px-6 pb-6 leading-relaxed text-neutral-500">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                @empty
                    <p class="text-center text-neutral-400">No FAQs published yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</main>

@push('seo')
    @if ($staticPage)
        {!! seo()->for($staticPage) !!}
    @endif
    <script type="application/ld+json">{!! json_encode($this->faqSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
