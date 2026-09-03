# TSO Public Market — Serverless витрина для Vercel

Высокопроизводительная read-only витрина публичного игрового маркета The Settlers Online (TSO): **Laravel 12 (API)** + **Vue 3 SPA**, оптимизированная для serverless-развертывания на **Vercel** (PHP-рантайм `vercel-php`).

Приложение **работает строго в режиме чтения** базы данных (Supabase / PostgreSQL). Вся синхронизация данных, AMF-клиент, аккаунты и фоновые задачи изолированы в основном приложении-сборщике, а данный проект обеспечивает быстрое, безопасное и кэшируемое отображение аналитики цен, спроса и арбитража.

Архитектура построена по стандартам **SOLID / DRY / KISS**, оснащена сквозным трейсингом, безопасными HTTP-заголовками и мониторингом ошибок через **Sentry**.

---

## 1. Возможности и функционал

- **Каталог и фильтрация по категориям (`item_kind`)**:
  - Поддержка 4 типов сущностей: `resource` (ресурсы), `buff` (усилители), `adventure` (приключения), `building` (здания).
  - Два режима отображения: классические зависимые выпадающие списки и визуальный браузер товаров с живым поиском.
- **Аналитика и графики**:
  - Графики истории цен (мин, макс, средняя взвешенная, тренд).
  - Графики спроса и объема торгов за различные периоды (`1d`, `7d`, `30d`, `1y`, `all`).
  - Анализ зеркальных пар (прямой и обратный обмен).
- **Арбитражные циклы**:
  - Автоматический поиск 2-шаговых ($A \to B \to A$) и 3-шаговых ($A \to B \to C \to A$) цепочек обмена с расчетом доходности.
- **Мультиязычность**:
  - Поддержка трех языков: **Українська (`uk`)**, **Русский (`ru`)**, **English (`en`)**.
  - Автоматическое распознавание игровых названий через `CompositeTradeableNameResolver` по секциям словарей (`RES`, `ADN`, `BUI`).
- **Многоуровневое кэширование**:
  - HTTP-кэш (`ETag`, `Cache-Control`, `X-Data-Version`).
  - Клиентский Stale-While-Revalidate (SWR) через `localStorage`.
  - Версионирование данных на уровне сервера через заголовок `X-Data-Version`.
- **Надежность и мониторинг**:
  - Интеграция с **Sentry** (`sentry/sentry-laravel`): сбор исключений API, трассировка запросов и логирование.
  - Сквозной идентификатор запроса (`RequestId` -> `X-Request-ID`).
  - Защитные заголовки (`SecurityHeadersMiddleware`): CSP, X-Frame-Options, nosniff, Referrer-Policy.

---

## 2. Публичные маршруты API и SPA

| Метод | Путь | Описание |
| --- | --- | --- |
| `GET` | `/` | Главная страница SPA публичного маркета |
| `GET` | `/healthz` | Healthcheck проверки доступности сервиса (200 OK) |
| `GET` | `/api/public/market/version` | Версия данных сервера (не кэшируется, для SWR) |
| `GET` | `/api/public/market/servers` | Список активных игровых миров |
| `GET` | `/api/public/market/goods` | Каталог доступных товаров (с фильтром `?kind=...`) |
| `GET` | `/api/public/market/targets` | Доступные цели обмена для выбранного товара |
| `GET` | `/api/public/market/popular` | Популярные предложения (валидация `MarketPopularRequest`) |
| `GET` | `/api/public/market/analytics` | Сводка или подробная аналитика торговой пары |
| `GET` | `/api/public/market/arbitrage` | Выгодные арбитражные циклы |
| `GET` | `/api/public/market/bulk` | Полный срез данных одним запросом (режим по умолчанию) |

---

## 3. Переменные окружения (`.env`)

Все параметры, необходимые для работы приложения локально и на Vercel:

| Переменная | Пример / Значение по умолчанию | Описание |
| --- | --- | --- |
| **Приложение** | | |
| `APP_NAME` | `"TSO Public Market"` | Название приложения |
| `APP_ENV` | `production` (на Vercel) / `local` | Окружение приложения |
| `APP_KEY` | `base64:...` | Ключ шифрования cookies (генерируется `php artisan key:generate`) |
| `APP_DEBUG` | `false` (на Vercel) / `true` | Режим отладки (на проде строго `false`) |
| `APP_URL` | `https://your-domain.vercel.app` | Базовый URL развернутого приложения |
| `APP_LOCALE` | `en` | Язык по умолчанию (`en`, `ru`, `uk`) |
| `APP_FALLBACK_LOCALE`| `en` | Резервный язык локализации |
| `APP_TIMEZONE` | `UTC` | Временная зона |
| **Sentry Мониторинг** | | |
| `SENTRY_LARAVEL_DSN` | `https://key@sentry.io/project-id` | DSN вашего проекта в Sentry для отправки ошибок |
| `SENTRY_TRACES_SAMPLE_RATE` | `0.2` | Процент трассировки транзакций (от `0.0` до `1.0`) |
| `SENTRY_LOG_LEVEL` | `error` | Минимальный уровень отправки логов в Sentry |
| **База данных (PostgreSQL / Supabase)** | | |
| `DB_CONNECTION` | `pgsql` | Драйвер БД |
| `DB_HOST` | `aws-0-...pooler.supabase.com` | **Важно для Vercel**: используйте connection pooler host (IPv4) |
| `DB_PORT` | `5432` или `6543` | Порт подключения (`6543` — transaction mode для serverless) |
| `DB_DATABASE` | `postgres` | Имя базы данных |
| `DB_USERNAME` | `postgres.<project-ref>` | Пользователь БД |
| `DB_PASSWORD` | `your-db-password` | Пароль БД |
| **Логирование** | | |
| `LOG_CHANNEL` | `stderr` (Vercel) / `stack` (локально) | Канал логирования по умолчанию |
| `LOG_STACK` | `stderr,sentry_logs` | Состав каналов в стеке |
| `LOG_LEVEL` | `error` (Vercel) / `debug` (локально) | Минимальный уровень логирования |
| **Serverless драйверы (без записи на диск)** | | |
| `CACHE_STORE` | `array` | Кэш в памяти (serverless lifecycle) |
| `CACHE_DRIVER` | `array` | Алиас драйвера кэша |
| `SESSION_DRIVER` | `cookie` | Сессии сохраняются в зашифрованных cookie клиента |
| `QUEUE_CONNECTION` | `sync` | Очереди не используются |
| **Настройки маркета** | | |
| `MARKET_DEFAULT_SERVER_ID` | `ru` | Игровой сервер, выбираемый по умолчанию |
| `MARKET_CACHE_STRATEGY` | `bulk` | Стратегия загрузки (`bulk` — один запрос, `granular` — раздельные) |
| **Пути Vercel** | | |
| `APP_STORAGE` | `/tmp/storage` | Единственная директория, доступная для записи в AWS Lambda / Vercel |
| `LARAVEL_STORAGE_PATH` | `/tmp/storage` | Путь хранилища Laravel |
| `VIEW_COMPILED_PATH` | `/tmp/storage/framework/views` | Путь скомпилированных Blade-шаблонов |

---

## 4. Локальная разработка

### Требования
- PHP 8.2, 8.3, 8.4 или 8.5 (расширения: `pdo_pgsql`, `intl`, `mbstring`, `bcmath`)
- Composer 2.x
- Node.js 20+ и npm

### Установка и запуск

```bash
# 1. Установка PHP зависимостей
composer install

# 2. Установка JS зависимостей
npm install

# 3. Настройка окружения
cp .env.example .env
php artisan key:generate

# 4. (Опционально) Экспорт переводов для фронтенда
php artisan tso:lang:export-frontend

# 5. Запуск Vite dev-сервера (в отдельном окне)
npm run dev

# 6. Запуск сервера Laravel
php artisan serve
```

### Схема БД для локальной разработки
Для локального запуска с чистой базой данных используйте миграцию:
```bash
php artisan migrate
```
Файл `database/migrations/0001_01_01_000000_create_public_market_schema.php` создаёт все необходимые таблицы (`market_offers`, `market_history`, `market_server_connections`, `settings`) со всеми актуальными индексами и полями `item_kind`.
> **Внимание**: Против рабочей продакшн-базы миграции запускать **не нужно** — схема там управляется основным приложением, а публичный портал работает исключительно в режиме чтения.

---

## 5. Развертывание на Vercel

1. Импортируйте репозиторий в [Vercel](https://vercel.com).
2. Настройки проекта в Vercel:
   - **Framework Preset**: `Other`
   - **Build Command**: `npm run build`
   - **Output Directory**: `public`
3. Перейдите в **Project Settings → Environment Variables** и укажите переменные из таблицы выше (в частности, `APP_KEY`, параметры `DB_*`, и `SENTRY_LARAVEL_DSN`).
4. Нажмите **Deploy**. Время холодного старта функции составляет ~200–250 мс, тёплый отклик — единицы миллисекунд.

---

## 6. Структура проекта

```
├── api/
│   └── server.php                         # Serverless-обёртка для Vercel (/tmp storage, env)
├── app/
│   ├── Console/Commands/
│   │   └── LangFrontendExportCommand.php  # php artisan tso:lang:export-frontend
│   ├── Enums/
│   │   └── MarketItemKind.php             # Энум типов товаров (resource, buff, adventure, building)
│   ├── Http/
│   │   ├── Controllers/Market/            # 7 чистых read-only контроллеров маркета
│   │   ├── Middleware/                    # HttpCacheHeaders, RequestId, SecurityHeaders, SetLocale
│   │   ├── Requests/Market/               # Валидация входных параметров (MarketPopularRequest)
│   │   └── Resources/                     # Сериализаторы API (MarketOfferResource, etc.)
│   ├── Models/                            # 4 модели: MarketOffer, MarketHistory, MarketServerConnection, Setting
│   ├── Providers/                         # AppServiceProvider, MarketServiceProvider
│   └── Services/
│       ├── Lang/GameTranslationResolver   # Сервис поиска переводов игровых названий
│       ├── MarketCacheService.php         # Управление ETag, SWR и версионированием
│       └── Market/                        # Сервисы выборки, аналитики, арбитража и Tradeables
├── config/                                # Конфигурации (app, logging, market, sentry, database, etc.)
├── database/migrations/                   # 0001_01_01_000000_create_public_market_schema.php
├── lang/{en,ru,uk}/                       # Словари игровых названий и UI
├── resources/
│   ├── js/
│   │   ├── components/market/             # Компоненты графиков цен/спроса и таблиц
│   │   ├── lang/                          # Клиентский i18n + generated JSON
│   │   ├── views/PublicMarketAnalytics    # Основной Vue 3 SPA экран маркета
│   │   ├── App.vue, router.js, app.js     # Точка входа SPA
│   └── views/app.blade.php                # Blade shell
├── vercel.json                            # Конфигурация деплоя на Vercel (vercel-php@0.9.0)
└── vite.config.js                         # Конфигурация сборки ассетов Vite
```

