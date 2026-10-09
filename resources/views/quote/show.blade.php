<!DOCTYPE html>
<html lang="en-AU">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>Your Truckit quote</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
        <style>
            :root {
                --canvas: #08090a;
                --surface: #0f1011;
                --inset: #0c0d0e;
                --heading: #f7f8f8;
                --body: #e6e7e9;
                --muted: #8a8f98;
                --accent: #f05a28;
                --accent-label: #ff7550;
                --line: rgba(255, 255, 255, 0.08);
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                background: var(--canvas);
                color: var(--body);
                font-family: Inter, system-ui, sans-serif;
                line-height: 1.5;
                -webkit-font-smoothing: antialiased;
            }
            header { border-bottom: 1px solid var(--line); }
            .bar, main { width: 100%; max-width: 640px; margin: 0 auto; padding-left: 16px; padding-right: 16px; }
            .bar { padding-top: 14px; padding-bottom: 14px; }
            .bar img { height: 32px; width: auto; display: block; }
            main { padding-top: 40px; padding-bottom: 64px; }
            .label { color: var(--accent-label); font-size: 13px; font-weight: 600; letter-spacing: -0.01em; margin: 0 0 8px; }
            h1 { color: var(--heading); font-size: 30px; line-height: 1.15; letter-spacing: -0.03em; font-weight: 600; margin: 0 0 12px; }
            .lede { color: var(--muted); margin: 0 0 28px; }
            .card { background: var(--surface); border: 1px solid var(--line); border-radius: 20px; padding: 24px; margin-bottom: 16px; }
            .route { color: var(--heading); font-weight: 600; margin: 0 0 4px; overflow-wrap: anywhere; }
            .arrow { color: var(--accent-label); }
            .items { color: var(--muted); font-size: 14px; margin: 0 0 20px; }
            .price { color: var(--heading); font-size: 36px; line-height: 1.1; letter-spacing: -0.03em; font-weight: 600; margin: 0 0 4px; }
            .price-note { color: var(--muted); font-size: 14px; margin: 0 0 20px; }
            .btn {
                display: inline-flex; align-items: center; justify-content: center;
                width: 100%; padding: 14px 20px; border-radius: 10px;
                background: #fff; color: var(--accent); font-weight: 600; font-size: 15px; text-decoration: none;
            }
            .btn:hover { background: rgba(255, 255, 255, 0.92); }
            .btn:focus-visible { outline: 2px solid var(--accent-label); outline-offset: 3px; }
            .total { display: flex; justify-content: space-between; align-items: baseline; gap: 16px; border-top: 1px solid var(--line); padding-top: 16px; margin-top: 8px; }
            .total strong { color: var(--heading); font-size: 22px; letter-spacing: -0.02em; }
            .fine { color: var(--muted); font-size: 14px; margin: 24px 0 0; }
            .fine p { margin: 0 0 8px; }
            .state { text-align: left; }
            .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            @media (min-width: 640px) { h1 { font-size: 38px; } .btn { width: auto; min-width: 240px; } }
            @media (prefers-reduced-motion: no-preference) { .btn { transition: background-color 0.2s; } }
        </style>
    </head>
    <body>
        <header>
            <div class="bar">
                <img src="{{ asset('images/logo.svg') }}" alt="Truckit" width="191" height="30">
            </div>
        </header>

        <main>
            @if ($state === 'ready')
                <p class="label">Your Truckit quote</p>
                <h1>Here's your price</h1>
                <p class="lede">
                    This is an instant estimate{{ $expires ? ', valid until '.$expires : '' }}.
                    It's a quote, not a booking.
                </p>

                @foreach ($listings as $listing)
                    <section class="card" aria-label="Quote {{ $loop->iteration }}">
                        <p class="route">
                            {{ $listing['collect'] }} <span class="arrow" aria-hidden="true">&rarr;</span><span class="sr-only"> to </span> {{ $listing['deliver'] }}
                        </p>
                        @if ($listing['items'] !== [])
                            <p class="items">{{ implode(', ', $listing['items']) }}</p>
                        @endif

                        @if ($listing['price'])
                            <p class="price">{{ $listing['price'] }}</p>
                            <p class="price-note">AUD, as quoted by Truckit</p>
                        @endif

                        @if ($listing['url'])
                            <a class="btn" href="{{ $listing['url'] }}" rel="noopener">Continue on Truckit</a>
                        @endif
                    </section>
                @endforeach

                @if ($total && count($listings) > 1)
                    <div class="total">
                        <span>Total for all quotes</span>
                        <strong>{{ $total }}</strong>
                    </div>
                @endif

                <div class="fine">
                    <p>On Truckit you create your account, check the details and pay. Nothing is booked until you do.</p>
                    <p>Availability, dates and any extras are confirmed on Truckit.</p>
                </div>
            @else
                <div class="state">
                    <p class="label">Your Truckit quote</p>
                    <h1>{{ $state === 'expired' ? 'This quote has expired' : "We can't find that quote" }}</h1>
                    <p class="lede">
                        {{ $state === 'expired'
                            ? 'Quotes only hold for a couple of days. Head back to your chat and ask for a fresh price.'
                            : 'The link may be mistyped, or the quote is no longer saved. Head back to your chat and ask for a fresh price.' }}
                    </p>
                </div>
            @endif
        </main>
    </body>
</html>
