<?php

use App\Enums\PageType;
use App\Models\StaticPage;
use Livewire\Component;

new class extends Component {
    public StaticPage $staticPage;

    public function mount()
    {
        if (in_array($this->staticPage->type, [PageType::LandingPage, PageType::IndexPage], true)) {
            return redirect()->to($this->staticPage->getURLValue());
        }

        views($this->staticPage)->record();
    }
};
?>

<main class="container-xl relative my-12 antialiased">
    <div class="bg-white/70 backdrop-blur-md border border-neutral-200/60 rounded-[2.5rem] shadow-xs overflow-hidden">

        <div class="px-6 py-12 lg:py-20 border-b border-neutral-100/80">
            <div class="mx-auto max-w-6xl">
                <x-shared.breadcrumbs :items="collect($staticPage->breadcrumbs())->map(fn ($breadcrumb) => [
                    'label' => $breadcrumb->getLabel(),
                    'url' => $breadcrumb->getUrl(),
                ])->all()" />
            </div>

            <header class="max-w-6xl mx-auto text-center">
                <h1 class="text-4xl lg:text-6xl font-black tracking-tighter text-neutral-900 leading-tight">
                    {{ $staticPage->title }}
                </h1>
            </header>
        </div>

        <div class="px-6 py-12 lg:px-16 lg:py-20">
            <article class="mx-auto w-full max-w-6xl prose prose-neutral prose-lg lg:prose-xl
                    prose-headings:font-black prose-headings:tracking-tight prose-headings:text-neutral-900
                    prose-a:text-primary prose-a:font-bold prose-a:no-underline hover:prose-a:underline
                    prose-img:rounded-4xl prose-img:shadow-xl
                    prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:italic prose-blockquote:bg-neutral-50 prose-blockquote:py-1 prose-blockquote:px-8 prose-blockquote:rounded-r-2xl
                    prose-strong:text-neutral-900">

                {!! $staticPage->content !!}

            </article>
        </div>
    </div>
</main>

@push('seo')
    {!! seo()->for($staticPage) !!}
@endpush
