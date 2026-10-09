@php
    $fontPath = public_path('fonts/josefin-sans.woff2');
    $fontSrc = is_file($fontPath) ? 'data:font/woff2;base64,'.base64_encode((string) file_get_contents($fontPath)) : null;

    $title = trim((string) ($title ?? ''));
    $titleClass = mb_strlen($title) > 80 ? 'is-xs' : (mb_strlen($title) > 48 ? 'is-sm' : 'is-md');
    $initial = mb_strtoupper(mb_substr((string) ($author_name ?: $brand_name ?: 'A'), 0, 1));
    $cover = filled($cover_url ?? null) ? $cover_url : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @if ($fontSrc)
        @font-face {
            font-family: 'Josefin Sans';
            font-style: normal;
            font-weight: 100 700;
            font-display: block;
            src: url({{ $fontSrc }}) format('woff2');
        }
        @endif

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            width: 1200px;
            height: 630px;
            overflow: hidden;
            position: relative;
            background: #0a0a0b;
            color: #fafafa;
            font-family: 'Josefin Sans', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .glow {
            position: absolute;
            width: 720px;
            height: 720px;
            left: -240px;
            bottom: -420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(252, 106, 62, .38), transparent 65%);
        }

        .glow-top {
            position: absolute;
            width: 480px;
            height: 480px;
            right: 240px;
            top: -320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(252, 106, 62, .18), transparent 68%);
        }

        .grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .035) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .cover {
            position: absolute;
            top: 0;
            right: 0;
            width: 440px;
            height: 630px;
            overflow: hidden;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: saturate(1.05);
        }

        .cover::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #0a0a0b 0%, rgba(10, 10, 11, .6) 34%, rgba(10, 10, 11, .12) 100%);
        }

        .content {
            position: relative;
            z-index: 10;
            height: 100%;
            width: 100%;
            padding: 60px 72px;
            display: flex;
            flex-direction: column;
        }

        body.has-cover .content { width: 800px; }

        .top {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .eyebrow {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(252, 106, 62, .14);
            border: 1px solid rgba(252, 106, 62, .35);
            color: #ffb199;
            font-size: 19px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .brand {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            height: 40px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .brand-name {
            font-size: 24px;
            font-weight: 600;
            color: #e5e5e5;
            letter-spacing: .02em;
        }

        .middle {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 28px 0;
        }

        .title {
            font-weight: 700;
            color: #ffffff;
            line-height: 1.06;
            letter-spacing: -.01em;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .title.is-md { font-size: 76px; -webkit-line-clamp: 3; }
        .title.is-sm { font-size: 60px; -webkit-line-clamp: 4; }
        .title.is-xs { font-size: 48px; -webkit-line-clamp: 5; }

        .description {
            margin-top: 26px;
            max-width: 880px;
            font-size: 27px;
            font-weight: 300;
            line-height: 1.42;
            color: #a3a3a3;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding-top: 26px;
        }

        .author {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-fallback {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fc6a3e;
            color: #0a0a0b;
            font-size: 24px;
            font-weight: 700;
        }

        .author-name {
            display: block;
            font-size: 22px;
            font-weight: 600;
            color: #f5f5f5;
        }

        .published {
            display: block;
            margin-top: 2px;
            font-size: 19px;
            color: #8a8a8a;
        }

        .domain {
            font-size: 22px;
            color: #8a8a8a;
            letter-spacing: .02em;
        }
    </style>
</head>
<body class="{{ $cover ? 'has-cover' : '' }}">
    <div class="glow"></div>
    <div class="glow-top"></div>
    <div class="grid"></div>

    @if ($cover)
        <div class="cover">
            <img src="{{ $cover }}" alt="">
        </div>
    @endif

    <div class="content">
        <div class="top">
            @if (filled($eyebrow ?? null))
                <span class="eyebrow">{{ $eyebrow }}</span>
            @endif

            <span class="brand">
                @if (filled($brand_logo_url ?? null))
                    <img src="{{ $brand_logo_url }}" alt="">
                @else
                    <span class="brand-name">{{ $brand_name }}</span>
                @endif
            </span>
        </div>

        <div class="middle">
            <h1 class="title {{ $titleClass }}">{{ $title }}</h1>

            @if (filled($description ?? null))
                <p class="description">{{ $description }}</p>
            @endif
        </div>

        <div class="footer">
            <div class="author">
                @if (filled($author_avatar_url ?? null))
                    <img class="avatar" src="{{ $author_avatar_url }}" alt="">
                @else
                    <span class="avatar-fallback">{{ $initial }}</span>
                @endif

                <span>
                    @if (filled($author_name ?? null))
                        <span class="author-name">{{ $author_name }}</span>
                    @endif
                    @if (filled($published_at ?? null))
                        <span class="published">{{ $published_at }}</span>
                    @endif
                </span>
            </div>

            <span class="domain">{{ $domain }}</span>
        </div>
    </div>
</body>
</html>
