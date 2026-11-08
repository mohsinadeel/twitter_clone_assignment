# Twitter working demonstration

A small Laravel application with registration, sign-in, profiles and a 280-character post feed. It includes authenticated JSON APIs and Pest tests. This is a synthetic working demonstration, not a client case study.

The current local adaptation targets the Engineering Demos shared-host arrangement. It has **not been pushed or deployed**. The existing public instance at https://mohsin-twitter.inceptionsol.com/ is unchanged.

## Runtime and local setup

Verified lockfile versions after the approved compatible updates: Laravel 12.71.0, Fortify 1.41.0, Sanctum 4.3.3 and Pest 4.7.8; frontend examples include Axios 1.21.0, Vite 7.3.7 and Tailwind 4.3.5. Both dependency manifests and their allowed version ranges are unchanged. The locked dependency set is verified on Sail PHP 8.4 / MySQL 8.4; do not infer PHP 8.2 compatibility from the root manifest alone. Fresh Composer and npm audits reported no known advisories on 8 November 2026; this is a dated result, not a permanent security guarantee.

Clone into the agreed parent directory while keeping this as its own repository:

```bash
git clone https://github.com/mohsinadeel/twitter_clone_assignment.git twitter-clone-demo
cd twitter-clone-demo
cp .env.example .env

docker run --rm -u "$(id -u):$(id -g)" \
  -v "$PWD:/var/www/html" -w /var/www/html \
  laravelsail/php84-composer:latest composer install --no-interaction

# Match WWWUSER / WWWGROUP in .env to your Linux or WSL user.
./vendor/bin/sail up -d --build
./vendor/bin/sail artisan key:generate

# Local-only stand-in for the schema Main owns on the shared host.
./vendor/bin/sail artisan db:seed --class=LocalSharedUsersSeeder
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm ci
./vendor/bin/sail npm run build
```

Use WSL2 for Sail commands on Windows. PowerShell can use `docker compose exec laravel.test ...` instead. Open http://localhost:8083. The local synthetic account is `twitter-demo@example.test` / `password`. Fixture seeders refuse production and databases other than `twitter_clone_assignment` or `testing`. They do not alter an existing users table; an incomplete existing schema requires Main's migrations.

The independent Compose project is `twitter-clone-demo`, with its own persistent MySQL volume. Default local stacks do not automatically share account rows across ports; production shares one schema, while local verification models that schema within this isolated database. MySQL and Vite do not expose host ports. The portal uses 8080, Stripe 8081, Task Management 8082 and Twitter 8083. `docker compose down` keeps the data; do not add `-v` unless intentionally removing this disposable local data.

## Shared production database

Main owns the unprefixed `users` table, its customer/authentication fields and its migration ledger. Main's new nullable `username` and `avatar` migration must run **before** this demo is deployed. Existing accounts can keep those fields null.

Twitter uses an unprefixed `shared_users` connection for credentials and profiles. Its regular MySQL connection uses `DB_TABLE_PREFIX=twitter_` and `DB_MIGRATIONS_TABLE=migrations`. The latter is a logical name: Laravel prefixes it, producing `twitter_migrations`. Posts, personal access tokens, password-reset tokens, cache and queue tables are demo-owned. Post foreign keys reference unprefixed shared users; related posts and Sanctum tokens explicitly use the prefixed connection.

The former standalone user migrations are retained under `database/legacy-standalone-migrations` as historical source and excluded from deployment. They are not discovered by normal migration commands. **Never run them against the shared database.** Moving the existing external host's users/posts into this schema is a separate, unexecuted data-migration task.

Each application has its own `APP_KEY`, file-session storage and session cookie. Twitter's cookie is `twitter_demo_session`. Shared credentials do not mean single sign-on. A self-service name/password change affects the shared account. Account deletion and two-factor management belong to Main.

### Authentication boundaries

- User/profile writes remain owner-scoped; post updates/deletes remain owner-scoped.
- Shared account deletion through Twitter is refused, including for the account owner.
- API serialization uses an explicit public-field allow-list; billing fields, admin flags, password hashes and two-factor secrets are not returned. The existing API continues to expose synthetic profile names/emails to signed-in demo users.
- Twitter cannot decrypt Main's two-factor secrets with its independent application key. Password-only API/browser sign-in and existing Twitter sessions/tokens are therefore refused for accounts with a two-factor secret configured. Twitter cannot enrol/disable two-factor authentication. Supporting those accounts requires a separate authentication-federation decision; do not copy Main's key into Twitter.
- Feed/profile text is escaped before HTML insertion. The ordinary Laravel session and CSRF protections still apply.

## Verification

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail node --test tests/js/feed-escaping.test.mjs
./vendor/bin/sail npm run build
```

Tests use only disposable MySQL `testing`, with `tw_test_` owned tables and an unprefixed shared-users fixture. The shorter test prefix avoids MySQL's identifier-length limit. Both connections are checked before schema setup. Tests share one PDO transaction across the two connection grammars so user/post foreign keys see factory records; they do not reset a shared production schema.

The local verification file records commands, baseline commit, observed results and limits. Actual hosted Apache/symlink behaviour, real production migration and external-host data migration remain unverified. Older tests described as unit tests also use Laravel and the test database; they are not all isolated unit tests.

## Manual GitHub Actions

The old push-triggered FTPS action is replaced locally with two manual workflows:

- **Twitter demo checks** bootstraps locked dependencies, builds Sail/MySQL and assets, runs integration formatting checks, Pest and dependency-free JavaScript tests.
- **Deploy Twitter demo** builds production dependencies/assets, refuses known dependency advisories, connects through OpenVPN and SSH, uploads the app, checks table ownership/session settings and Main's profile columns, runs only normal demo migrations, caches configuration/views and creates the public-directory symlink. It does not seed production or cache routes.

Deployment uses the `production` environment. Variables: `SSH_HOST`, `DEPLOY_PATH=/home6/inceptio/demo.mohsinadeel.dev/twitter-clone-demo`. Secrets: `OPENVPN_CONFIG`, `VPN_USERNAME`, `VPN_PASSWORD`, `SSH_PRIVATE_KEY`, `SSH_USERNAME`, `SSH_PORT`, `SSH_KNOWN_HOSTS`. Verify the server fingerprint independently before configuring known hosts. These values have not been configured or tested for this repository in this session.

Before deployment, create a private server `.env` with `APP_ENV=production`, `APP_DEBUG=false`, PHP 8.4, the existing shared MySQL host/database credentials and:

```dotenv
APP_URL=https://demo.mohsinadeel.dev/twitter-clone-demo
ASSET_URL=https://demo.mohsinadeel.dev/twitter-clone-demo
DB_CONNECTION=mysql
DB_TABLE_PREFIX=twitter_
DB_MIGRATIONS_TABLE=migrations
SESSION_DRIVER=file
SESSION_COOKIE=twitter_demo_session
SESSION_PATH=/twitter-clone-demo
SESSION_SECURE_COOKIE=true
PORTAL_URL=https://demo.mohsinadeel.dev/
```

Generate this application's own key once and preserve it. Never reuse Main's key. Main deploys first, followed by demos. The intended public symlink is `main/public/twitter-clone-demo -> ../../twitter-clone-demo/public`; only `public/` may be served. Give the PHP user write access to `storage/` and `bootstrap/cache/`.

Back up the shared database before release. Use `php artisan migrate --force`; **never run `migrate:fresh`, `db:wipe`, fixture seeders or automated tests on the shared production database**. The approved compatible lockfile updates pass the deployment audit checks locally. The workflow keeps those checks enabled and will refuse deployment if later advisories exceed its thresholds. Hosted deployment and its environment still need separate authorisation and verification.

See [local integration evidence](evidence/LOCAL-INTEGRATION.md).
