@php
    $settings = app(\App\Settings\SiteSettings::class);
@endphp

<section class="container-xl relative overflow-hidden my-8 lg:my-12">
    <div class="bg-white/60 backdrop-blur-sm border border-neutral-200/60 rounded-[2.5rem] shadow-xl shadow-neutral-100/50">
        <div class="grid gap-12 lg:grid-cols-12 py-12 lg:py-20 px-6 lg:px-16 items-center">

            <div class="lg:col-span-7 order-2 lg:order-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Truckit in Claude
                </div>

                <h1 class="mb-6 text-4xl font-black tracking-tight md:text-5xl xl:text-6xl text-neutral-900 leading-[1.1]">
                    Moving something big?<br class="hidden sm:block" />
                    Just ask Claude.
                </h1>

                <p class="max-w-2xl mx-auto lg:mx-0 mb-10 font-medium text-neutral-500 md:text-lg lg:text-xl leading-relaxed">
                    {{ $settings->description }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="https://claude.ai/settings/connectors" rel="noopener noreferrer" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white rounded-2xl bg-primary shadow-lg shadow-primary/30 hover:bg-primary-500 hover:-translate-y-1 transition-all duration-300">
                        Add Truckit to Claude
                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="{{ route('page.view', 'about-us') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-neutral-700 rounded-2xl border border-neutral-200 bg-white hover:bg-neutral-50 transition-all duration-300" wire:navigate.hover>
                        See how it works
                    </a>
                </div>

                <p class="mt-6 text-sm text-neutral-400">
                    Free to get a price. No sign in needed.
                </p>
            </div>

            <div class="lg:col-span-5 order-1 lg:order-2">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-r from-primary to-primary-300 rounded-4xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

                    <div class="relative bg-neutral-900 rounded-4xl p-2 border border-neutral-800 shadow-2xl text-left">
                        <div class="rounded-3xl bg-neutral-950 p-4 space-y-4">
                            <div class="flex items-center justify-between text-xs text-neutral-500 px-1">
                                <span>Claude</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 px-2 py-0.5 text-neutral-300">
                                    <span class="size-1.5 rounded-sm bg-emerald-500"></span>
                                    Truckit Connect
                                </span>
                            </div>
                            <p class="text-sm text-neutral-200 bg-neutral-800/80 rounded-xl rounded-br-sm px-3 py-2 max-w-[90%] ml-auto">
                                How much to move a 3 seater couch and a fridge from New Farm to Byron Bay next Saturday?
                            </p>
                            <p class="text-xs text-neutral-500">Checked Truckit for a price</p>
                            <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-sm text-neutral-300 space-y-3">
                                <p>
                                    Truckit can do that for <strong class="text-white">$964.12 incl. GST</strong>, picked up and delivered next Saturday. That covers loading, transport and unloading by a rated Truckit provider.
                                </p>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-neutral-900">Book this on Truckit ↗</span>
                                    <span class="text-[11px] text-neutral-500 font-mono">truckit.net</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
