<footer class="container-xl mt-16 mb-8">
    <div class="bg-white/70 backdrop-blur-md border border-neutral-200/60 rounded-[2.5rem] shadow-sm px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">

            <div class="max-w-xs">
                <a href="{{ route('landing-page') }}" class="group flex items-center space-x-2" wire:navigate.hover>
                    <span class="text-2xl font-black tracking-tight text-neutral-900 group-hover:text-primary transition-colors">
                        {{ $settings->name }}
                    </span>
                </a>
                <p class="mt-4 text-sm text-neutral-500 leading-relaxed">
                    {{ $settings->description }}
                </p>
            </div>

            <nav>
                <ul class="flex flex-wrap gap-x-8 gap-y-4 text-sm font-bold text-neutral-600">
                    <li>
                        <a href="{{ route('landing-page') }}"
                           class="transition-colors {{ request()->routeIs('landing-page') ? 'text-primary' : 'hover:text-primary' }}"
                           wire:navigate.hover
                        >
                            Home
                        </a>
                    </li>
                    @foreach(\App\Models\StaticPage::query()->withGlobalScope('contentPage', new \App\Models\Scopes\ContentPageOnly())->get() as $staticPage)
                        <li>
                            <a href="{{ $staticPage->getURLValue() }}"
                               wire:navigate.hover
                               class="transition-colors {{ request()->url() === $staticPage->getURLValue() ? 'text-primary' : 'hover:text-primary' }}">
                                {{ $staticPage->type === \App\Enums\PageType::Faq ? 'FAQ' : $staticPage->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        @php
            $profiles = array_filter([
                'LinkedIn' => $social->linkedin,
                'Facebook' => $social->facebook,
            ], filled(...));
        @endphp

        @if ($profiles !== [])
            <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2">
                <span class="text-[10px] uppercase tracking-widest font-bold text-neutral-400">Follow</span>

                @foreach ($profiles as $label => $url)
                    <a
                        href="{{ $url }}"
                        rel="me noopener"
                        target="_blank"
                        class="text-sm font-bold text-neutral-600 transition-colors hover:text-primary"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="my-8 h-px w-full bg-linear-to-r from-transparent via-neutral-200 to-transparent"></div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-sm text-neutral-500 font-medium">
                © {{ now()->format('Y') }}
                <span class="text-neutral-900">{{ $settings->name }}</span>.
                All Rights Reserved.
            </div>

            <div class="flex items-center gap-3">
                <span class="text-[10px] uppercase tracking-widest font-bold text-neutral-400">Built with</span>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-md bg-primary/10 text-primary text-xs font-bold border border-primary/20">Laravel {{ \Composer\InstalledVersions::getPrettyVersion('laravel/framework') }}</span>
                    <span class="px-2 py-1 rounded-md bg-neutral-100 text-neutral-600 text-xs font-bold border border-neutral-200">Filament {{ \Composer\InstalledVersions::getPrettyVersion('filament/filament') }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
