# Local shared-host integration evidence

Date: 8 November 2026 (Asia/Karachi).
Twitter baseline: `ba52e05`; portal baseline: `f124b38`. Evidence refers to uncommitted local adaptation changes, not the baseline commits alone. Nothing from this integration has been pushed, deployed or configured in GitHub environments.

## Approved dependency updates — latest verification

Mohsin explicitly approved compatible dependency patches on 8 November 2026. Both composer.json and package.json are unchanged when parsed as JSON compared with their baseline versions; no direct package or declared version range was added/changed. Composer and npm regenerated their lockfiles within those constraints. Selected versions: Laravel 12.29.0 -> 12.71.0; Fortify 1.30.0 -> 1.41.0; Sanctum 4.2.0 -> 4.3.3; Pest 4.1.0 -> 4.7.8; Axios 1.12.2 -> 1.21.0; Vite 7.1.5 -> 7.3.7; Tailwind 4.1.13 -> 4.3.5. Transitive packages also changed as required by the existing constraints; no root manifest range was widened.

Actual follow-up checks:

- `composer validate --strict` and `composer check-platform-reqs`: passed on Sail PHP 8.4.26.
- `composer audit`: no security vulnerability advisories found; structured after-audit contains zero advisories.
- `npm ci --ignore-scripts` from the regenerated lockfile, then `npm audit`: passed, zero vulnerabilities.
- Full `php artisan test --compact`: **80 passed / 297 assertions**.
- `node --test tests/js/feed-escaping.test.mjs`: **3 passed**.
- `npm run build`: passed with Vite 7.3.7. Local app restarted afterwards.
- Real local HTTP/session check after the restart: home 200, create post 201, list posts 200, delete temporary post 200 and new built script 200. Synthetic account only; the created post was removed.
- Changed-PHP Pint formatting and `git diff --check`: passed. The updated Laravel test lifecycle requires calling `parent::createApplication()`; the test harness now does that before its existing database checks and fixture setup.

Lockfile SHA-256 values for this uncommitted tested state:

- composer.lock: `cf4def6c6834e3ff489f63a03f15278850966e53e97fcf4c0ca16ff8e7300eea`
- package-lock.json: `18cd86dba504ec3040873d90aae5b69e603246adf793fe77143399971f863a8f`

Before audit, after audit and package diff JSON are in the planning workspace: `work/twitter-dependency-audit.json`, `work/twitter-dependency-audit-after.json`, `work/twitter-dependency-updates.json`. The earlier advisory counts below are the preserved pre-update finding and no longer describe the patched lockfiles. The deployment audit gate remains enabled. No push, workflow dispatch or hosted deployment occurred.

## Decisions and implemented boundaries

Mohsin approved checkout `C:\repos\demos\twitter-clone-demo` and explicitly chose adapting Twitter to the shared demo host. Production users belong to Main; Main has a new nullable username/avatar migration. Active Twitter migrations do not create or alter users. Historical standalone migrations are retained outside the active migration directory and excluded from deployment.

Twitter uses unprefixed shared_users and prefixed twitter_ owned tables, an explicit mysql connection for posts/tokens, a raw fixed unprefixed users foreign-key target, its own application key, file sessions and twitter_demo_session cookie. Main must deploy first. Twitter user deletion and two-factor enrolment are disabled. Password-only sign-in and existing Twitter tokens/sessions are blocked for accounts with Main's encrypted two-factor secret configured. No key copying or single sign-on is implemented. API fields are allow-listed; shared billing/authentication internals are not serialized. Feed and profile text are escaped before HTML insertion.

Local stacks remain independent MySQL volumes by project agreement. Production shares one schema. Local default portal and Twitter web sessions therefore do not automatically share account rows; the cross-application compatibility check below used Main's actual model against Twitter's isolated main-shaped schema. Tests use an explicitly disposable MySQL testing database and short tw_test_ prefix, with one shared PDO transaction across the two connection grammars.

## Executed checks

| Check | Starting conditions, action and expected result | Observed result | Boundary |
| --- | --- | --- | --- |
| Main-owned schema bootstrap | Run Main's real migrations in a temporary Sail PHP 8.4 process connected to Twitter's isolated MySQL twitter_clone_assignment database; expect shared schema before demo migrations | Main user/auth/customer/profile migrations succeeded; no production database connected | Local compatibility setup, not a production migration |
| Twitter migration/seed | Normal `docker compose exec -T laravel.test php artisan migrate --seed`; expect only owned tables under twitter_ plus synthetic local records | Migrations and synthetic seed succeeded; user seeding now restricted to local/testing disposable DBs | Historical external-host data not imported |
| PHP regressions | `docker compose exec -T laravel.test php artisan test --compact` | **80 tests / 297 assertions passed** after the local fixture and migration-ownership tests were added | Existing Pest tests include framework/database-backed so-called unit tests |
| Owned migration rollback | MigrationOwnershipTest rolls back owned migrations and reruns them around a synthetic shared user | Shared user/profile fields preserved; owned posts table removed/recreated; fixture bootstrap rejects production | Disposable testing DB only |
| Authentication/data boundaries | SharedUserIntegrationTest exercises sign-in, tokens, duplicate registration, ownership, two-factor refusals and nested generated URLs | Focused five-test run passed; included in final full suite | Full hosted browser and mail/reset flows not verified |
| Cross-app credentials | Read-only PHP process boots Main's real Laravel 13 model and Hash facade against the local shared-shaped schema | Main reads the synthetic Twitter user and accepts its password: both true | Does not claim shared default local web databases or SSO |
| Real local session flow | PowerShell HTTP client GET login, submit synthetic credentials with CSRF/session cookies, GET home, create/list/delete a temporary post | Home 200, create 201, feed 200, delete 200; temporary post removed; cookie twitter_demo_session | HTTP session check, not visual browser automation |
| Frontend escaping | `node --test tests/js/feed-escaping.test.mjs` | **3 checks passed** for text escaping and hostile feed/profile fields | No third-party test dependency added |
| Build/style | Sail `npm ci`, `npm run build`, `vendor/bin/pint --dirty`, integration-path `pint --test`, and diff checks | Locked installs, build, changed-PHP formatting and integration style checks passed | Reused an available Sail PHP 8.4 image locally; the checked-in Dockerfile was not freshly rebuilt |
| Portal regression/build | Main's nullable profile migration applied locally; `php artisan test --filter='DemoLandingPageTest|RegistrationTest'`; `npm run build` | **4 tests / 17 assertions passed**; main asset build passed | Other portal/Stripe features not retested |
| Parent exclusions | Parent git check-ignore plus tar listing using deploy-exclude with vendor/node_modules omitted to bound the check | Twitter, Task Management and Stripe checkouts excluded; explicit Tailwind source/Vite watcher exclusions present | No real upload archive sent |
| Workflows | Parse eight main/Stripe/task/Twitter workflow files and Bash-check 48 run/remote blocks; php -l on new release guards | Manual-only triggers and syntax checks passed | No GitHub CI/deploy dispatch; no VPN/SSH secret validation |

## Corrections made during verification

The legacy tracked .env.testing used SQLite and omitted MySQL host settings. It was converted to MySQL. The first longer testing prefix exceeded MySQL's token-index identifier limit; tw_test_ avoids it without changing the deployed standalone index definition. A local web process started before Composer had finished and exhausted restart attempts; it was restarted after bootstrap. The two intentionally removed account-deletion contracts now assert refusal and record preservation; all other old behaviour coverage remains. Normal schema migration, tests and the real HTTP flow subsequently passed as stated above.

## Pre-update audit record and retained release gate

Before the approved updates, the 2025 lockfiles produced Composer advisories affecting **15 PHP packages** and npm's summary of **12 package entries: 3 critical, 8 high, 1 moderate**. These counts are package/advisory-tool results, not a claim that every issue is exploitable in this browser app. Both runtime packages and development tools are involved. The full local audit JSON is in the project planning workspace at `work/twitter-dependency-audit.json`.

Those findings were resolved by the approved compatible updates recorded above. The new manual deployment workflow executes `composer audit --no-dev` and `npm audit --audit-level=high` before contacting the server, so future affected lockfiles cannot silently proceed to deployment.

Final local reachability: portal 8080, Stripe 8081, Task Management 8082 and Twitter 8083 all returned HTTP 200. The unchanged Stripe stack was started only for link review; no payment or external integration action was taken.

## Not run / outstanding

- Dependency updates and follow-up checks: completed locally as recorded above; no unqualified security claim is made.
- Fresh Docker image build from this repository's legacy Dockerfile: Not run; CI is prepared to build it.
- GitHub workflow execution, environment variables/secrets and pinned SSH host-key setup: Not run.
- Hosted shared-DB migration, symlink/Apache/subdirectory root behaviour, hosted credentials/CSRF/assets/feed: Not run.
- Existing external-host account/post migration: Not run; no data copied or deleted there.
- Main-encrypted two-factor sign-in via Twitter: intentionally unsupported and refused; authentication federation is separate work.
- Public repository naming still predates the demo naming rule; no remote rename was made.
