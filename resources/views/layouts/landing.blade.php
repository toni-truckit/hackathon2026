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
        @if ($siteSettings->favicon)
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/storage/'.$siteSettings->favicon) }}">
        @endif

        @stack('seo')

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        {{-- unslop-ignore: Figma 366:1203 specifies Inter for Claude landing --}}
        <link
            rel="preload"
            as="style"
            href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,600;1,14..32,400&display=swap"
            onload="this.onload=null;this.rel='stylesheet'"
        >
        <noscript>
            <link
                rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,600;1,14..32,400&display=swap"
            >
        </noscript>

        {!! $siteSettings->header_scripts !!}

        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="bg-landing-canvas font-landing text-landing-heading antialiased motion-reduce:transition-none">
        {{ $slot }}

        @stack('scripts')
        @livewireScripts
        {!! $siteSettings->footer_scripts !!}
    </body>
</html>
