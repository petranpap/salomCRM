# Studio Kassandra

**Salon management, done right.**

A multi-tenant salon management platform built for Cyprus salons — appointments,
customers, staff, inventory, payments, and VAT-correct branded receipts, with SMS
reminders through Cyta's Web SMS API.

[![Tests](https://github.com/petranpap/salomCRM/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/petranpap/salomCRM/actions/workflows/ci-cd.yml)

## What it does

- **Appointments** — calendar-based booking with double-booking protection (a customer
  can't be booked twice at overlapping times), and hours that respect a midday closure
  (e.g. 09:00–13:30, then 16:00–18:30), for both salon opening hours and individual
  staff schedules.
- **Customers** — profiles, treatment history, SMS/email consent, VIP and behavior
  flags (auto-flagged on no-shows or lateness).
- **Staff** — accounts, roles (owner/staff), working hours, per-staff calendar colors.
  Owners can reset a staff member's password (forcing them to set their own on next
  login) and deactivate someone without destroying their appointment/treatment
  history. Login lockout after 3 failed attempts (30 minutes), resettable by a
  super-admin.
- **Products & inventory** — stock levels, low-stock indicators, and a direct
  "Sell Products" flow for walk-in retail sales with no appointment attached.
- **Payments & receipts** — cash/card/bank transfer, VAT breakdown (inclusive VAT,
  matching Cyprus VAT rules), a salon's own logo/brand colors, and a choice of three
  receipt layouts (Classic / Modern / Minimal) with a live preview before saving.
- **SMS reminders** — a pluggable driver architecture (`App\Services\Sms`) so adding a
  new gateway is one class; Cyta (Cyprus) ships today. Every send attempt is logged
  regardless of outcome, and a salon can send itself a real test message from Settings.
- **Z-Report** — end-of-day cash/card reconciliation; once closed, a day's payments
  and appointments are locked against further edits.
- **Onboarding wizard** — a new salon's owner is walked through salon details, VAT,
  opening hours, SMS, and receipt branding on first login. Skippable, and everything
  set there stays editable in Settings afterward.
- **Platform admin** — a super-admin manages every salon from one place, can create
  additional super-admins, and can reset a salon's onboarding to force it through
  setup again.

## Tech stack

- **Backend**: Laravel 11, PHP 8.2+, MySQL/MariaDB
- **Auth**: Laravel Sanctum (token-ready for a future mobile/API client)
- **Frontend**: Tailwind CSS (a small custom design system, not a component
  library), Alpine.js, FullCalendar, Flatpickr — built with Vite
- **PDF**: barryvdh/laravel-dompdf
- **Roles**: spatie/laravel-permission

## Getting started (local development)

```bash
git clone git@github.com:petranpap/salomCRM.git
cd salomCRM
composer install
npm install

cp .env.example .env
php artisan key:generate
# Edit .env: set DB_* to a real MySQL/MariaDB database

php artisan migrate
php artisan storage:link   # needed for salon logo uploads to display

npm run build               # or `npm run dev` while actively working on JS/CSS
php artisan serve
```

### First-time setup

There's no public sign-up — every account is created by an administrator.

1. **Create your own platform admin account:**
   ```bash
   php artisan make:superadmin
   ```
2. Log in, go to **Platform Admin → Salons → New Salon**, then **Staff → New Staff**
   to create that salon's owner account (pick a temporary password — they'll be
   asked to set their own on first login).
3. Hand the owner their temporary password. On first login they'll be walked
   through the onboarding wizard (salon details, VAT, opening hours, SMS, branding)
   before reaching the dashboard.

See `Fixes.md` for the full history of fixes and design decisions behind the current
behavior, and `docs/` for architecture and deployment notes.

## Running tests

```bash
php artisan test
```

Tests run against an in-memory SQLite database (see `phpunit.xml`) — no separate test
database setup needed.

## CI/CD

`.github/workflows/ci-cd.yml` runs the full test suite on every push and pull
request, and deploys to a VPS over SSH on a successful push to `main`. See
`docs/DEPLOY.md` for what that needs to be configured.

## License

Proprietary — all rights reserved.
