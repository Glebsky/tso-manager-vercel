# TSO Public Market — короткая версия для Vercel

Read-only витрина публичного игрового маркета: Laravel 12 (API) + Vue 3 SPA,
собранная под serverless-хостинг **Vercel** (PHP-рантайм `vercel-php`).

Приложение **только читает** базу данных. Здесь нет админки, авторизации,
задач/крона, синхронизации с игровыми серверами и AMF-клиента — данные в БД
наполняет основное (полное) приложение, а этот проект их лишь показывает.

---

## 1. Что осталось и что удалено

**Осталось (публичный маркет):**

- `GET /` — SPA публичного маркета (`resources/js/views/PublicMarketAnalytics.vue`);
- `GET /api/public/market/version` — версия данных (без кэша, для SWR-инвалидации);
- `GET /api/public/market/servers` — список игровых миров;
- `GET /api/public/market/goods` — каталог продаваемых товаров;
- `GET /api/public/market/targets` — во что можно обменять выбранный товар;
- `GET /api/public/market/popular` — популярные предложения;
- `GET /api/public/market/analytics` — обзор/аналитика пары (цены, спрос, история);
- `GET /api/public/market/arbitrage` — арбитражные циклы;
- `GET /api/public/market/bulk` — весь набор данных одним запросом (используется SPA по умолчанию);
- `GET /healthz` — проверка живости;
- HTTP-кэш (`ETag`, `Cache-Control`, `X-Data-Version`), кэш-сервис, локали `en/ru/uk`,
  игровые словари и переводы, графики/таблицы фронтенда.

**Удалено:**

- админка целиком (`/admin/*`, все Admin-контроллеры, вьюхи, роуты, `settingsApi`, `authApi`);
- авторизация и аккаунты (Laravel Sanctum, `User`/`Account`, middleware аутентификации, `auth()`);
- синхронизация маркета и работа с игровым сервером (AMF-клиент, `MarketSyncService`,
  `MarketServerService`, верификация серверов, ретраи, `market_sync_logs`);
- планировщик и задачи (`ScheduledTask`, консольные команды, cron, очереди, воркеры);
- логи ботов/операций, страницы настроек, всё, что писало в БД;
- Docker/Render-обвязка (`Dockerfile.render`, `render.yaml`, nginx-конфиги, entrypoint, crontab).

---

## 2. Как это работает на Vercel

Vercel не умеет запускать обычный PHP-FPM, поэтому:

- `api/server.php` — единственная serverless-функция. Она готовит окружение
  (пишущийся только `/tmp`, кэш в память, логи в stderr) и подключает
  `public/index.php` — стандартный фронт-контроллер Laravel;
- `vercel.json` направляет весь трафик на эту функцию, а статику (`public/build/*`,
  иконки, `robots.txt`, картинки) отдаёт файловой системой Vercel;
- фронтенд собирается Vite (`npm run build`) в `public/build` во время деплоя;
- диск read-only: сессии в cookie, кэш `array`, скомпилированные Blade-шаблоны и
  кэши конфигов — в `/tmp/storage` (см. `APP_*_CACHE`, `VIEW_COMPILED_PATH`).

### Деплой

1. Залейте содержимое архива в Git-репозиторий и импортируйте его в Vercel
   (**Framework Preset: Other**, Build Command `npm run build`, Output Directory `public`) —
   всё это уже прописано в `vercel.json`.
2. Проверьте переменные окружения (см. ниже). Значения из `vercel.json` можно
   (и лучше) перенести в **Project → Settings → Environment Variables** и удалить
   блок `env` из `vercel.json`.
3. Deploy. Первый холодный старт функции ~250 мс, тёплый — единицы мс.

`vercel-php@0.9.0` = PHP 8.5 с `pdo_pgsql`, `pgsql`, `intl`, `mbstring`, `zip`.
Если нужна другая версия: `0.8.0` → PHP 8.4, `0.7.x` → PHP 8.3, `0.6.x` → PHP 8.2.

---

## 3. Переменные окружения

| Переменная | Значение | Зачем |
| --- | --- | --- |
| `APP_KEY` | `base64:...` | обязателен (шифрование cookie). Сгенерировать: `php artisan key:generate --show` |
| `APP_ENV` / `APP_DEBUG` | `production` / `false` | прод-режим |
| `APP_URL` | адрес деплоя | ссылки/ассеты |
| `DB_CONNECTION` | `pgsql` | Postgres/Supabase |
| `DB_HOST` | `aws-0-...pooler.supabase.com` | **только пулер**; прямой `db.*.supabase.co` в Vercel не резолвится (IPv6) |
| `DB_PORT` | `5432` (session) или `6543` (transaction) | для serverless можно `6543` |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | из Supabase | доступ к данным |
| `CACHE_STORE`, `CACHE_DRIVER` | `array` | нет Redis/файлов |
| `SESSION_DRIVER` | `cookie` | нет записи на диск |
| `QUEUE_CONNECTION` | `sync` | очередей нет |
| `LOG_CHANNEL` | `stderr` | логи в Vercel Logs |
| `APP_STORAGE`, `LARAVEL_STORAGE_PATH` | `/tmp/storage` | единственный writable путь |
| `VIEW_COMPILED_PATH`, `APP_*_CACHE` | `/tmp/storage/...` | Blade и кэши конфигов |
| `MARKET_DEFAULT_SERVER_ID` | `ru` | мир по умолчанию |
| `MARKET_CACHE_STRATEGY` | `bulk` | `bulk` — один запрос на всё, `granular` — по эндпоинтам |

> **Важно про безопасность.** В `vercel.json` лежат `APP_KEY` и пароль Supabase,
> перенесённые из старой конфигурации (`render.zip`). Эти секреты уже были в
> архиве, поэтому их стоит **сменить** (новый `APP_KEY`, новый пароль БД) и
> хранить в настройках проекта Vercel, а не в репозитории.

---

## 4. Локальный запуск

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run dev            # в отдельном терминале
php artisan serve
```

Миграция `database/migrations/0001_01_01_000000_create_public_market_schema.php`
создаёт минимальную схему (`market_offers`, `market_history`,
`market_server_connections`, `settings`) для локальной/пустой БД.
**Против рабочей базы миграции запускать не нужно** — схему там держит основное
приложение, а этот проект только читает.

---

## 5. Картинки игры

SPA берёт иконки из `/images/...` (`buildings/`, `resources/`, `buffs/`, `units/`,
`adventures/`, формат `.webp`). В архивах их не было, поэтому положите папку
`public/images/**` в репозиторий — Vercel отдаст её как статику. Без файлов
интерфейс работает, но вместо иконок будет заглушка.

---

## 6. Если после деплоя ассеты отдают 404

Вариант «в лоб» (без `outputDirectory`) — замените в `vercel.json`:

```json
{
    "routes": [
        { "src": "/build/(.*)", "dest": "/public/build/$1" },
        { "src": "/images/(.*)", "dest": "/public/images/$1" },
        { "src": "/(favicon.svg|robots.txt|site.webmanifest)", "dest": "/public/$1" },
        { "src": "/(.*)", "dest": "/api/server.php" }
    ]
}
```

Если Vercel не соберёт зависимости PHP — закоммитьте локальный `vendor/`
(`composer install --no-dev -o`), он специально не исключён в `.vercelignore`.

---

## 7. Структура

```
api/server.php                     serverless-обёртка (env + /tmp storage)
bootstrap/app.php                  Laravel 12 bootstrap (routing, middleware)
bootstrap/providers.php            AppServiceProvider + MarketServiceProvider
public/index.php                   фронт-контроллер
routes/web.php                     SPA + /healthz
routes/api.php                     публичные эндпоинты маркета
app/Http/Controllers/Market/*      7 read-only контроллеров
app/Http/Middleware/*              HttpCacheHeaders, SetLocale
app/Models/*                       MarketOffer, MarketHistory, MarketServerConnection, Setting
app/Services/Market/*              выборки, аналитика, арбитраж, bulk
resources/js/*                     Vue SPA (единственная страница)
lang/{en,ru,uk}/*                  интерфейс + игровые названия
vercel.json                        конфиг деплоя
```
