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
                    Freight marketplace
                </div>

                <h1 class="mb-6 text-5xl font-black tracking-tight md:text-6xl xl:text-7xl text-neutral-900 leading-[1.1]">
                    {{ $settings->name }}
                </h1>

                <p class="max-w-2xl mx-auto lg:mx-0 mb-10 font-medium text-neutral-500 md:text-lg lg:text-xl leading-relaxed">
                    {{ $settings->description }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ route('contact.view') }}" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white rounded-2xl bg-primary shadow-lg shadow-primary/30 hover:bg-primary-500 hover:-translate-y-1 transition-all duration-300" wire:navigate.hover>
                        Contact us
                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 order-1 lg:order-2">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-linear-to-r from-primary to-primary-300 rounded-4xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>

                    <div class="relative bg-white rounded-4xl p-2 border border-neutral-100 shadow-2xl">
                        <img
                            src="https://hamrocdn.com/mhykONU_Vi2F"
                            alt="{{ $settings->name }} preview"
                            width="3328"
                            height="2006"
                            fetchpriority="high"
                            decoding="async"
                            class="rounded-3xl w-full h-auto object-cover transform transition duration-500 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
