<nav
    x-data="{ mobileMenuOpen: false }"
    class="container-xl sticky top-4 z-50"
>
    @php
        use App\Enums\PageType;
        use App\Models\StaticPage;

        $staticPages = StaticPage::query()
                ->whereIn('type', [PageType::LandingPage, PageType::ContentPage, PageType::PageWithForm, PageType::Faq, PageType::Contact])
                ->get();
    @endphp
    <div
        class="bg-white/70 backdrop-blur-md border border-neutral-200/60 rounded-xl shadow-sm px-6 py-3 transition-all duration-300">
        <div class="flex flex-wrap items-center justify-between">

            <a href="{{ route('landing-page') }}" class="flex items-center group" wire:navigate.hover>
                <span
                    class="self-center text-2xl font-bold tracking-tight text-neutral-900 group-hover:text-primary transition-colors">
                    {{ $settings->name }}
                </span>
            </a>

            <div class="flex items-center md:order-2 space-x-2">
                <a
                    href="{{ route('contact.view') }}"
                    wire:navigate.hover
                    class="hidden sm:inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl shadow-md hover:bg-primary-500 hover:-translate-y-0.5 transition-all duration-200 active:scale-95"
                >
                    Contact
                </a>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="inline-flex items-center p-2 text-neutral-500 hover:bg-neutral-100 rounded-xl md:hidden transition-colors"
                >
                    <span class="sr-only">Toggle Menu</span>
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="hidden w-full md:flex md:w-auto md:order-1">
                <ul class="flex flex-row space-x-8 font-medium">
                    @foreach($staticPages as $staticPage)
                        <li>
                            <a
                                href="{{ $staticPage->getURLValue() }}"
                               wire:navigate.hover
                               wire:current.exact
                               class="relative py-2 text-sm transition-colors data-current:text-primary text-neutral-600 hover:text-primary group"
                            >
                                {{ $staticPage->type === PageType::Faq ? 'FAQ' : $staticPage->getTitleValue() }}
                                <span class="absolute inset-x-0 -bottom-1 h-0.5 bg-primary transform scale-x-0 transition-transform duration-200 group-hover:scale-x-100"></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden pt-4 pb-2"
            x-cloak
        >
            <div class="flex flex-col space-y-1 border-t border-neutral-100 pt-4">
                <a href="{{ route('landing-page') }}"
                   wire:navigate.hover
                   class="flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->routeIs('landing-page') ? 'bg-primary/10 text-primary' : 'text-neutral-600 hover:bg-neutral-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                </a>

                @foreach(\App\Models\StaticPage::query()->withGlobalScope('contentPage', new \App\Models\Scopes\ContentPageOnly())->get() as $staticPage)
                    <a href="{{ $staticPage->getURLValue() }}"
                       wire:navigate.hover
                       class="flex items-center px-4 py-3 text-base font-medium rounded-xl {{ request()->url() === $staticPage->getURLValue() ? 'bg-primary/10 text-primary' : 'text-neutral-600 hover:bg-neutral-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ $staticPage->type === PageType::Faq ? 'FAQ' : $staticPage->title }}
                    </a>
                @endforeach

                <div class="pt-4 px-4 sm:hidden">
                    <a href="{{ route('contact.view') }}"
                       wire:navigate.hover
                       class="block w-full text-center py-3 bg-primary text-white font-bold rounded-xl shadow-lg">
                        Contact
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
