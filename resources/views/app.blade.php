@php
    $locale = app()->getLocale();
    $baseUrl = rtrim(config('app.url', 'https://tso-market.vercel.app'), '/');
    $currentUrl = url()->current();

    $seoConfig = [
        'ru' => [
            'title' => 'TSO Market Analytics — Цены, спрос и арбитраж The Settlers Online',
            'description' => 'Актуальные торговые цены, история цен и спроса, аналитика рынка и поиск прибыльных арбитражных сделок в The Settlers Online (TSO) в реальном времени.',
            'keywords' => 'the settlers online, tso, tso market, settlers online рынок, цены tso, арбитраж tso, аналитика settlers online, калькулятор tso, торговля tso, история цен tso',
            'og_locale' => 'ru_RU',
            'og_locale_alternates' => ['uk_UA', 'en_US'],
        ],
        'uk' => [
            'title' => 'TSO Market Analytics — Ціни, попит та арбітраж The Settlers Online',
            'description' => 'Актуальні торгові ціни, історія попиту, аналітика ринку та арбітражні можливості в The Settlers Online (TSO) у реальному часі.',
            'keywords' => 'the settlers online, tso, tso market, settlers online ринок, ціни tso, арбітраж tso, аналітика settlers online, калькулятор tso, торгівля tso, історія цін tso',
            'og_locale' => 'uk_UA',
            'og_locale_alternates' => ['ru_RU', 'en_US'],
        ],
        'en' => [
            'title' => 'TSO Market Analytics — Trade Prices, Demand & Arbitrage for The Settlers Online',
            'description' => 'Real-time trade prices, historical demand charts, popular items, and profitable trade arbitrage loops for The Settlers Online (TSO).',
            'keywords' => 'the settlers online, tso market, settlers online trade prices, tso analytics, settlers online economy, tso arbitrage, tso market history, tso calculator',
            'og_locale' => 'en_US',
            'og_locale_alternates' => ['ru_RU', 'uk_UA'],
        ],
    ];

    $currentSeo = $seoConfig[$locale] ?? $seoConfig['en'];
    $canonicalUrl = $locale === 'en' ? $baseUrl . '/' : $baseUrl . '/?lang=' . $locale;
    $ogImage = $baseUrl . '/android-chrome-512x512.png';
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebApplication',
                '@id' => $baseUrl . '#webapp',
                'name' => 'TSO Market Analytics',
                'alternateName' => 'The Settlers Online Market Analytics & Arbitrage Portal',
                'url' => $baseUrl,
                'description' => $currentSeo['description'],
                'applicationCategory' => 'GameApplication, BusinessApplication, UtilitiesApplication',
                'operatingSystem' => 'All',
                'browserRequirements' => 'Requires JavaScript',
                'inLanguage' => ['en', 'ru', 'uk'],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'USD',
                ],
                'screenshot' => $ogImage,
            ],
            [
                '@type' => 'WebSite',
                '@id' => $baseUrl . '#website',
                'name' => 'TSO Market Analytics',
                'url' => $baseUrl,
                'description' => 'Real-time trade prices, historical demand, and arbitrage analytics for The Settlers Online.',
                'inLanguage' => ['en', 'ru', 'uk'],
            ],
            [
                '@type' => 'FAQPage',
                '@id' => $baseUrl . '#faq',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name' => $locale === 'ru' ? 'Что такое TSO Market Analytics?' : ($locale === 'uk' ? 'Що таке TSO Market Analytics?' : 'What is TSO Market Analytics?'),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $locale === 'ru' ? 'TSO Market Analytics — это бесплатный сервис мониторинга цен, спроса и арбитража для игры The Settlers Online в реальном времени.' : ($locale === 'uk' ? 'TSO Market Analytics — це безкоштовний сервіс моніторингу цін, попиту та арбітражу для гри The Settlers Online у реальному часі.' : 'TSO Market Analytics is a free real-time trade price, demand history, and arbitrage analytics tool for The Settlers Online.'),
                        ],
                    ],
                    [
                        '@type' => 'Question',
                        'name' => $locale === 'ru' ? 'Как рассчитывается арбитраж торговли в TSO?' : ($locale === 'uk' ? 'Як розраховується арбітраж торгівлі в TSO?' : 'How does TSO market arbitrage work?'),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $locale === 'ru' ? 'Алгоритм анализирует цепочки прямых и обратных обменов (A -> B -> A и A -> B -> C -> A), выявляя выгодные ценовые дисбалансы между товарами.' : ($locale === 'uk' ? 'Алгоритм аналізує ланцюжки прямого та зворотного обміну (A -> B -> A та A -> B -> C -> A), виявляючи вигідні цінові дисбаланси між товарами.' : 'The algorithm analyzes 2-step and 3-step trade exchange loops to identify profitable price imbalances across market items.'),
                        ],
                    ],
                ],
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Search Console Verification -->
    <meta name="google-site-verification" content="3zfiir-_Gwcyr_0ib9dhYTGhjAuQkaI1yjTE1PKG5oI" />

    <!-- SEO Meta Tags -->
    <title>{{ $currentSeo['title'] }}</title>
    <meta name="description" content="{{ $currentSeo['description'] }}">
    <meta name="keywords" content="{{ $currentSeo['keywords'] }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="TSO Market Analytics">

    <!-- Canonical & Multilingual Hreflang -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/">
    <link rel="alternate" hreflang="en" href="{{ $baseUrl }}/?lang=en">
    <link rel="alternate" hreflang="ru" href="{{ $baseUrl }}/?lang=ru">
    <link rel="alternate" hreflang="uk" href="{{ $baseUrl }}/?lang=uk">

    <!-- Open Graph (Facebook, Telegram, Discord, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="TSO Market Analytics">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $currentSeo['title'] }}">
    <meta property="og:description" content="{{ $currentSeo['description'] }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:alt" content="TSO Market Analytics Logo">
    <meta property="og:locale" content="{{ $currentSeo['og_locale'] }}">
    @foreach ($currentSeo['og_locale_alternates'] as $altLocale)
        <meta property="og:locale:alternate" content="{{ $altLocale }}">
    @endforeach

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $currentSeo['title'] }}">
    <meta name="twitter:description" content="{{ $currentSeo['description'] }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Theme & Color Scheme -->
    <meta name="theme-color" content="#020617">
    <meta name="color-scheme" content="dark">

    <!-- Mobile & PWA Support -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TSO Market Analytics">
    <meta name="msapplication-TileColor" content="#020617">
    <meta name="msapplication-navbutton-color" content="#020617">

    <!-- Favicons & Manifest -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- Structured Data (Schema.org JSON-LD) -->
    <script type="application/ld+json">
    {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <script>
        window.__APP_LOCALE__ = @json($locale);
        window.__MARKET_CACHE_STRATEGY__ = @json(config('market.cache_strategy', 'bulk'));
        window.__MARKET_COMBAT_SIMULATOR_URL__ = @json(config('market.combat_simulator_url'));
    </script>
    @vite(['resources/js/app.js'])
</head>
<body class="h-full bg-dark-950 font-sans text-white antialiased">
    <div id="app" class="h-full">
        {{-- Semantic crawlable HTML for search engines, replaced upon Vue hydration --}}
        <div class="sr-only">
            <h1>{{ $currentSeo['title'] }}</h1>
            <p>{{ $currentSeo['description'] }}</p>
            <section>
                <h2>The Settlers Online Market Analytics &amp; Trading Tools</h2>
                <ul>
                    <li><strong>Live Market Prices:</strong> Real-time pricing for resources, buffs, adventures, and buildings in The Settlers Online.</li>
                    <li><strong>Historical Demand &amp; Volume:</strong> Dynamic candlestick charts and weighted averages for 1d, 7d, 30d, 1y periods.</li>
                    <li><strong>Arbitrage Opportunities:</strong> Automated discovery of multi-step exchange profit loops (A &rarr; B &rarr; A and A &rarr; B &rarr; C &rarr; A).</li>
                    <li><strong>International Servers:</strong> Live analytics across German, Russian, French, Polish, US, and Global game worlds.</li>
                </ul>
            </section>
        </div>
        <noscript>
            <div style="padding: 2rem; text-align: center; color: #ffffff; background-color: #020617; font-family: ui-sans-serif, system-ui, sans-serif;">
                <h1>{{ $currentSeo['title'] }}</h1>
                <p>{{ $currentSeo['description'] }}</p>
                <p style="margin-top: 1rem; color: #94a3b8;">Please enable JavaScript to use the interactive TSO Market Analytics portal.</p>
            </div>
        </noscript>
    </div>
</body>
</html>
