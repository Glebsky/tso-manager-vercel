<?php

/*
|--------------------------------------------------------------------------
| Vercel serverless entrypoint
|--------------------------------------------------------------------------
|
| Vercel functions have a read-only filesystem, only /tmp is writable.
| Everything Laravel would normally write into storage/ is redirected to
| /tmp before the framework boots. Sessions are cookie based, the cache is
| in-memory (array) and logs go to stderr, so nothing else touches disk.
|
*/

$appStorage = '/tmp/storage';

foreach ([
    $appStorage.'/framework/views',
    $appStorage.'/framework/cache/data',
    $appStorage.'/framework/sessions',
    $appStorage.'/framework/testing',
    $appStorage.'/logs',
] as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

$runtimeEnv = [
    'APP_STORAGE' => $appStorage,
    'LARAVEL_STORAGE_PATH' => $appStorage,
    'VIEW_COMPILED_PATH' => $appStorage.'/framework/views',
    'CACHE_STORE' => 'array',
    'CACHE_DRIVER' => 'array',
    'SESSION_DRIVER' => 'cookie',
    'LOG_CHANNEL' => 'stderr',
    'APP_CONFIG_CACHE' => $appStorage.'/config.php',
    'APP_EVENTS_CACHE' => $appStorage.'/events.php',
    'APP_PACKAGES_CACHE' => $appStorage.'/packages.php',
    'APP_ROUTES_CACHE' => $appStorage.'/routes.php',
    'APP_SERVICES_CACHE' => $appStorage.'/services.php',
];

foreach ($runtimeEnv as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../public/index.php';
