<?php

declare(strict_types=1);

use App\Enums\PageType;
use App\Models\Faq;
use App\Models\StaticPage;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::landing')] class extends Component
{
    public ?StaticPage $landingPage = null;

    public function mount(): void
    {
        $this->landingPage = StaticPage::query()
            ->whereType(PageType::LandingPage)
            ->first();

        if ($this->landingPage) {
            views($this->landingPage)->record();
        }
    }

    /** @return Collection<int, Faq> */
    #[Computed]
    public function faqs(): Collection
    {
        return Faq::query()->active()->ordered()->limit(5)->get();
    }

    /** @return array<string, mixed> */
    public function faqSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $this->faqs->map(fn (Faq $faq): array => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ])->values()->all(),
        ];
    }
};
?>

<div>
    <livewire:landing.header />
    <livewire:landing.hero />
    <livewire:landing.how-it-works />
    <livewire:landing.try-asking />
    <livewire:landing.why-truckit lazy.bundle />
    <livewire:landing.setup lazy.bundle />
    <livewire:landing.faq lazy.bundle />
    <livewire:landing.cta lazy.bundle />
    <livewire:landing.footer lazy.bundle />
</div>

@push('seo')
    @if ($landingPage)
        {!! seo()->for($landingPage) !!}
    @else
        {!! seo() !!}
    @endif
    @if ($this->faqs->isNotEmpty())
        <script type="application/ld+json">{!! json_encode($this->faqSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endpush
