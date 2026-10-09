<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            use App\Settings\SiteSettings;
            $siteSettings = app(SiteSettings::class);
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        @if($siteSettings->favicon)
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/storage/'.$siteSettings->favicon) }}">
        @endif

        @stack('seo')

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link
            rel="preload"
            as="style"
            href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
            onload="this.onload=null;this.rel='stylesheet'"
        >
        <noscript>
            <link
                rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
            >
        </noscript>

        {!! $siteSettings->header_scripts !!}

        @vite(["resources/js/app.js", "resources/css/app.css"])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="text-white font-sans bg-neutral-100 container-xl">
        <x-shared.header />
        {{ $slot }}
        <x-shared.footer />

        @stack('scripts')
        @livewireScripts
        {!! $siteSettings->footer_scripts !!}
    </body>
</html>
