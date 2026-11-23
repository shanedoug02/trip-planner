# Trip Planner

A travel-prep reminder app. Create a trip with a departure date and it builds a checklist of tasks with real due dates (for example "Pack bags, 2 days before, 18:00"), then sends a push notification when each task is due. A trip can be shared between two people.

Built with Laravel, Vue (Inertia), PostgreSQL and Pest.

## Requirements

- PHP 8.3 or newer
- Composer
- Node 22 or newer
- PostgreSQL 18, for example through [DBngin](https://dbngin.com)
- [Laravel Herd](https://herd.laravel.com) to serve the app. Without Herd, use `php artisan serve` instead.

## Setup

1. Start Postgres and create the two databases: one for the app and one for the tests.

    ```bash
    psql -h 127.0.0.1 -U postgres -c "CREATE DATABASE trip_planner;"
    psql -h 127.0.0.1 -U postgres -c "CREATE DATABASE trip_planner_testing;"
    ```

    The defaults in `.env.example` expect user `postgres` with no password on `127.0.0.1:5432`. If your setup is different, change the `DB_*` values in `.env` after step 3.

2. Clone the repo into your Herd folder, so Herd serves it at http://trip-planner.test:

    ```bash
    cd ~/Herd
    git clone https://github.com/shanedoug02/trip-planner.git
    cd trip-planner
    ```

3. Install everything and set up the database:

    ```bash
    composer setup
    ```

    This installs the PHP and Node packages, creates `.env` from `.env.example`, generates the app key, runs the migrations and builds the front end. If it fails at the migrate step, the databases from step 1 don't exist yet.

4. Check that it worked:

    ```bash
    php artisan app:status
    ```

    Both lines should show ✅. Then open http://trip-planner.test.

## Daily use

- Start Herd and Postgres.
- `composer run dev` starts the queue worker, log viewer and Vite (which reloads the browser when front-end files change). Herd serves the app itself, so this doesn't start a second web server.
- `php artisan test` runs the test suite against `trip_planner_testing`.
- `php artisan app:status` checks the web server and database.

## Warning: don't cache config locally

Don't run `php artisan config:cache` on your own machine. With cached config, the tests ignore `phpunit.xml` and use your real `trip_planner` database, and `RefreshDatabase` wipes it before each test.

If it happens, clear the cache:

```bash
php artisan config:clear
```
