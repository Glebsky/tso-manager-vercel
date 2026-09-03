<?php

declare(strict_types=1);

use App\Http\Controllers\Market\AnalyticsController;
use App\Http\Controllers\Market\ArbitrageController;
use App\Http\Controllers\Market\BulkController;
use App\Http\Controllers\Market\CatalogController;
use App\Http\Controllers\Market\PopularController;
use App\Http\Controllers\Market\PublicMarketSettingsController;
use App\Http\Controllers\Market\PublicServerController;
use App\Http\Controllers\Market\VersionController;
use App\Http\Middleware\HttpCacheHeaders;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public market API (read-only)
|--------------------------------------------------------------------------
|
| Every endpoint only reads from the database. /version is never cached so
| clients can detect new data; the rest is wrapped in HttpCacheHeaders
| (ETag + X-Data-Version + Cache-Control) so the CDN, the browser and the
| localStorage SWR layer can all revalidate cheaply.
|
*/

Route::prefix('public/market')->group(function (): void {
    Route::get('/version', VersionController::class);
    Route::get('/settings', PublicMarketSettingsController::class);

    Route::middleware(HttpCacheHeaders::class)->group(function (): void {
        Route::get('/servers', PublicServerController::class);
        Route::get('/goods', [CatalogController::class, 'goods']);
        Route::get('/targets', [CatalogController::class, 'targets']);
        Route::get('/popular', PopularController::class);
        Route::get('/analytics', AnalyticsController::class);
        Route::get('/arbitrage', ArbitrageController::class);
        Route::get('/bulk', BulkController::class);
    });
});
