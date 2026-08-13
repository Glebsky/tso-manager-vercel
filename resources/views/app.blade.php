<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Public market analytics: live offers, prices, demand and arbitrage loops.">
    <title>{{ config('app.name', 'TSO Public Market') }}</title>

    <!-- Favicons & Manifest -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/site.webmanifest">
    <script>
        window.__APP_LOCALE__ = @json(app()->getLocale());
        window.__MARKET_CACHE_STRATEGY__ = @json(config('market.cache_strategy', 'bulk'));
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-dark-950 font-sans text-white antialiased">
    <div id="app" class="h-full"></div>
</body>
</html>
