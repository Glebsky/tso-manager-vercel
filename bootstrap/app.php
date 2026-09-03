<?php

declare(strict_types=1);

use App\Http\Middleware\RequestId;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Http\FlushEventsMiddleware;
use Sentry\Laravel\Http\SetRequestIpMiddleware;
use Sentry\Laravel\Http\SetRequestMiddleware;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        health: '/healthz',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vercel terminates TLS on its edge network.
        $middleware->trustProxies(at: '*');

        // Append security and Sentry middlewares
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->append(SetRequestMiddleware::class);
        $middleware->append(SetRequestIpMiddleware::class);
        $middleware->append(FlushEventsMiddleware::class);

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

        // Prepend RequestId to assign unique X-Request-ID
        $middleware->prepend(RequestId::class);

        // Public, read-only API: a generous fixed limit per IP.
        $middleware->throttleApi('120,1');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof ValidationException || $e instanceof HttpExceptionInterface) {
                    return null;
                }

                Log::error($e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'message' => 'Server error occurred. Please try again later.',
                    'code' => 500,
                ], 500);
            }

            return null;
        });
    })->create();

