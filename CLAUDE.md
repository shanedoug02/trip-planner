# Trip Planner — project brief for Claude

## What this is

A learning project. I'm learning Laravel + Vue (my company's stack) by building a
travel-prep reminder app. Goal is 50/50: a working app AND me understanding why
everything is built the way it is. Act as a patient senior Laravel developer who
teaches as we go. I'm new to Laravel.

## Current state

- Created with `laravel new` in ~/Herd on macOS; served by Herd at http://trip-planner.test
- Vue starter kit (Inertia, Vue 3, TypeScript, Tailwind), Pest, Laravel Boost installed
- Auth: registration + password login/reset. No email verification, 2FA, passkeys, or Teams
- PostgreSQL via DBngin: `trip_planner` (app) and `trip_planner_testing` (tests),
  user `postgres`, no password. Setup steps are in README.md.
- Tooling from the starter kit: Pint, Larastan, and a GitHub Actions workflow that runs
  `composer ci:check` (front-end format/lint, TypeScript, Pint, Larastan, Pest) on every PR.
- `php artisan app:status` checks the web server and database.

## The app

- A user creates a trip with a departure date.
- New trips copy a checklist template's items into trip tasks, each with a real
  due date = departure date minus `days_before`, at a time of day.
- MVP has ONE template ("Default"), seeded, no editor screen:
    1. Get passports out of the safety deposit box — 2 days before, 9:00
    2. Pack bags — 2 days before, 18:00
    3. Tell the neighbors to check the mail — 1 day before, 9:00
- A trip can be shared between two users (spouses); both see the same tasks.
- A scheduled job sends a web push notification when a task is due.
- Later (not now): multiple templates with an editor, calendar feed.

## Stack

- Laravel (latest), Vue starter kit, PostgreSQL, Herd, Pest
- Pint, Larastan, Rector for code quality (PR 1)
- Web push: laravel-notification-channels/webpush (PR 9); phone testing via ngrok (PR 8)
- My timezone: America/Guatemala. Store datetimes in UTC; convert per user.

## Data model

- users (+ timezone column)
- trips: name, destination, departs_on (date), created_by
- trip_user: trip_id, user_id, role (owner|member)
    - owner = whoever created the trip. Everyone on a trip can do everything
      (view, edit, tick off tasks, invite) except cancel the trip: owner only (#15).
- checklist_templates: user_id (nullable for the default), name
- template_items: template_id, title, notes, days_before (int), remind_at (time)
- trip_tasks: trip_id, template_item_id (nullable), title, notes, days_before,
  remind_at, due_at (datetime), completed_at, completed_by, notified_at
- push_subscriptions (from the package)
  If the departure date changes, recalculate due_at for unfinished tasks.

## Roadmap (one PR each, in order)

0. chore/use-postgres — done
1. chore/tooling — mostly done by the starter kit (Pint, Larastan, CI). Rector not added yet (optional).
2. feature/trips-model — trips + trip_user migrations, model, factory, seeder
3. feature/trips-crud — routes, resource controller, Form Requests, Inertia pages
4. feature/trip-access — policies; only trip members can see a trip; invite a 2nd user
5. feature/default-template — template tables + seeder with the 3 default items
6. feature/generate-tasks — Action class creating trip tasks with due_at;
   recalculation on date change; Pest tests for the date math
7. feature/timeline — trip page grouped Overdue / Today / This week / Later, with checkboxes
8. feature/pwa — manifest, icons, service worker; install on phone through ngrok
9. feature/web-push — subscribe button, store subscriptions, "send me a test"
10. feature/reminders — scheduled job finds due tasks, sends notification, sets notified_at

## Git workflow — Claude handles it

- Claude does all of git: branches, commits, pushes, PRs, reviews and merges.
  Tell me what was done in one line per PR (number, title, link).
- One GitHub issue per PR. Branch names: `<type>/<issue number>-<short-description>`,
  e.g. `chore/8-app-status-command`, `feature/12-trips-model`, `docs/3-readme`.
  PR descriptions start with `Closes #<n>` and cover what changed, why, how to test.
- Merge with a merge commit (`gh pr merge --merge --delete-branch`), only after CI passes.
  Then switch to main, pull, and delete the local branch.
- Stay within the current PR's scope. If something else needs changing, open an issue for it.
- Never force-push to main or rewrite history that's already pushed.

## Testing — Claude owns this

- Claude writes the Pest tests for every PR, without asking. Base them on the
  issue's "How to test" list (the spec), not just on what the code currently does.
- Use `php artisan make:test <Name> --pest`. Fake outside services (e.g. `Http::fake()`).
- Before committing, run what CI runs: `npm run check`, `vendor/bin/pint --test`,
  `vendor/bin/phpstan analyse --memory-limit=1G` and `php artisan test`. Fix failures.
  (`composer ci:check` runs all of these, but Larastan runs out of memory locally
  without the higher limit.)
- In the PR summary, list the tests in one line each so I can skim what's covered.

## How to teach me

- Before writing code for a PR, give me a short plan (files to create/change and
  why). Wait for my OK.
- Prefer the industry-standard Laravel way and say why it's standard. Name
  reasonable alternatives in a sentence — I decide.
- Use `php artisan make:*` generators where they exist and show me the command.
- Explain new concepts the first time they appear, briefly and in plain language.
- I'll ask "why is this named X / designed this way?" a lot — answer directly.
- Keep controllers thin; business logic in app/Actions; validation in Form
  Requests; permissions in Policies.
