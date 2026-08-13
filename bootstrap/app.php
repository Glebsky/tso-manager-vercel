<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vercel terminates TLS on its edge network.
        $middleware->trustProxies(at: '*');

        // The SPA reads the locale cookie from JavaScript.
        $middleware->encryptCookies(except: [
            'app_locale',
        ]);

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->api(append: [
            SetLocale::class,
        ]);

        // Public, read-only API: a generous fixed limit per IP.
        $middleware->throttleApi('120,1');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
