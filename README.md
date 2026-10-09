# TruckIt Connect

**Truckit Connector for Claude** — this repository hosts TruckIt’s public web surface (Filament CMS, marketing pages) and the **Laravel MCP server** that lets Claude and other MCP clients quote, post, compare, book, and track freight and removals in Australia. Users get **instant Book Now prices** where the pricing model allows; bookings hand off to **secure checkout on truckit.net** (Claude’s directory does not allow in-chat payment). The same MCP endpoint is intended for Claude’s Connectors Directory and ChatGPT in the same release.

Full product and engineering spec for agents and developers: [`.ai/guidelines/project-description.md`](.ai/guidelines/project-description.md).

## MCP server (Laravel)

| Item | Detail |
|------|--------|
| **Stack** | [Laravel MCP](https://laravel.com/docs/mcp) in this app (not a separate TypeScript service) |
| **Production URL (target)** | `https://mcp.truckit.net/mcp` (Streamable HTTP) |
| **Local** | Same app as the site; use `APP_URL` once MCP routes are published |

**MVP tools (8):**

- `get_instant_quote` — Book Now price and quote card
- `get_provider_profile` — Provider public profile card
- `list_quotes` — Provider quote comparison on a job
- `get_job_status` — Booking / delivery status card
- `list_my_jobs` — Signed-in customer’s jobs
- `post_job` — List on marketplace when instant price is not available
- `create_booking` — Booking + truckit.net checkout link
- `cancel_job` — Cancel unpaid jobs only

**Dependencies outside this repo:** Auth Service (OAuth 2.0 + PKCE), Truckit platform API, Book Now / matching services. MCP tool implementations call those systems; they are not fully replaceable by local DB seeds alone.

**When MCP is implemented locally:**

```bash
composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes
php artisan mcp:inspector <server-name>
```

Use Claude **Settings → Connectors → custom connector** with your local MCP URL for closed beta before directory submission.

## Requirements

- PHP 8.3 with extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `intl`, `gd`, `imagick`, `pcntl`
- Composer 2
- Bun or Node 20+ (Vite)
- MySQL for local app data (sessions, cache, queue use the database by default)

Optional for OG image generation: Chromium/Chrome and Node on the host, or set `OG_IMAGE_*` paths in `.env`.

## First-time setup

```bash
cd truckit-mcp

cp .env.example .env
composer install
bun install   # or: npm install

php artisan key:generate
```

Create the application database (name matches `DB_DATABASE` in `.env`, default `truckit_mcp`):

```sql
CREATE DATABASE truckit_mcp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
php artisan storage:link
```

### Environment

| Purpose | Connection / vars |
|--------|-------------------|
| This app (migrations, Filament, sessions) | `DB_*` — default connection `mysql` |
| business-api database (read-only usage) | `BUSINESS_API_DB_*` → `database.connections.business_api` |
| Monolith database | `MONOLITH_DB_*` → `database.connections.monolith` |

Copy `BUSINESS_API_DB_*` and `MONOLITH_DB_*` from the `truckit-for-business-api` repo `.env` when using the same hosts. Laravel code uses `DB::connection('business_api')` or `DB::connection('monolith')`.

Set `APP_URL` to the URL you use in the browser (e.g. `http://localhost:8000`).

## Run locally (development)

All-in-one (HTTP server, queue, logs, Vite):

```bash
composer run dev
```

Or run pieces separately:

```bash
php artisan serve
php artisan queue:listen --tries=1
bun run dev
```

| URL | |
|-----|---|
| Site | `APP_URL` (default `http://localhost:8000`) |
| Filament admin | `/admin` |
| Health | `/up` |

## Tests

```bash
composer test
# or
php artisan test --compact
```

## Docker

Requires Docker Compose v2.

### Local (bind-mounted code)

```bash
docker compose up -d --build
```

Open [http://localhost:8080](http://localhost:8080). Entrypoint copies `.env.example` → `.env` when missing, generates `APP_KEY`, runs `migrate --seed` when `DOCKER_BOOTSTRAP_DB=true` (default in compose), and keeps compiled views/cache in Docker volumes (`mcp-storage-framework`, `mcp-bootstrap-cache`) so host `php artisan` paths cannot break the container.

Use `APP_URL=http://localhost:8080` in `.env` (default in `.env.example`). Change `APP_PORT` only if 8080 is taken.

- Site: [http://localhost:8080](http://localhost:8080)
- Migrate (once DB reachable): `docker compose exec app php artisan migrate --seed`
- Optional one-shot migrate on boot: `RUN_MIGRATIONS=true` in `.env`

MySQL on the host: use `DB_HOST=host.docker.internal` in `.env`.

### business-api / monolith MySQL in Docker

Start `truckit-for-business-api` compose first, then:

```bash
docker compose -f docker-compose.yml -f docker-compose.external-db.yml up -d --build
```

In `.env`, set `DB_HOST`, `BUSINESS_API_DB_HOST`, and `MONOLITH_DB_HOST` to the MySQL service name (e.g. `db2`). If the external network name differs, set `BUSINESS_API_NETWORK` (default `truckit-for-business-api_app-network`).

### Prod-shaped image

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

Add `docker-compose.external-db.yml` when the app container must reach shared MySQL on a Docker network.
