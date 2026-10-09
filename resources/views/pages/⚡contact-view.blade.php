<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\StaticPage;
use App\Settings\SiteSettings;
use Livewire\Component;

new class extends Component
{
    public ?StaticPage $staticPage = null;

    public ?string $email = null;

    public function mount(): void
    {
        $this->staticPage = StaticPage::query()
            ->where('type', PageType::Contact)
            ->first();

        $this->email = app(SiteSettings::class)->contact_email;
    }

    /** @return array<string, mixed> */
    public function contactSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => $this->staticPage?->title ?? 'Contact',
            'url' => route('contact.view'),
            'mainEntity' => array_filter([
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => $this->email,
                'url' => route('contact.view'),
            ]),
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
                    ['label' => 'Contact', 'url' => null],
                ]" />
            </div>

            <header class="mx-auto max-w-3xl text-center">
                <h1 class="text-4xl lg:text-5xl font-black tracking-tight text-neutral-900 leading-tight">
                    {{ $staticPage?->title ?? 'Contact' }}
                </h1>

                @if (filled($staticPage?->description))
                    <p class="mt-6 text-neutral-500 leading-relaxed">
                        {{ $staticPage->description }}
                    </p>
                @endif

                @if (filled($email))
                    <p class="mt-8">
                        <a
                            href="mailto:{{ $email }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary-500"
                        >
                            {{ $email }}
                        </a>
                    </p>
                @endif
            </header>
        </div>

        <div class="px-6 py-12 lg:px-16 lg:py-20">
            <article class="prose prose-neutral mx-auto w-full max-w-3xl prose-lg
                prose-headings:font-black prose-headings:tracking-tight prose-headings:text-neutral-900
                prose-a:text-primary prose-a:no-underline hover:prose-a:underline">
                {!! $staticPage?->content !!}
            </article>
        </div>
    </div>
</main>

@push('seo')
    @if ($staticPage)
        {!! seo()->for($staticPage) !!}
    @endif
    <script type="application/ld+json">{!! json_encode($this->contactSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
