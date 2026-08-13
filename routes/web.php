<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public market portal
|--------------------------------------------------------------------------
|
| The whole application is a single read-only page: the Vue SPA is served
| from "/" and talks to the /api/public/market/* endpoints.
|
*/

Route::get('/', fn () => view('app'))->name('public.market');

Route::get('/market/public', fn () => redirect('/'));
Route::get('/public/market', fn () => redirect('/'));

Route::get('/healthz', fn () => response('ok', 200));
