# Eventra — Luxury Wedding & Event Management Dashboard

Eventra is a Laravel 11 + MongoDB Atlas event planning dashboard with role-based access for planners, vendors, and guests. It includes authentication, vendor discovery, event CRUD, guest RSVP management, budgets, bookings, timelines, task boards, gallery uploads, seeded demo data, and a premium animated Blade/Tailwind UI.

## Stack

- Laravel 11
- MongoDB Atlas via `mongodb/laravel-mongodb`
- Blade + Tailwind CSS
- Alpine.js, GSAP, Lenis, Chart.js, AOS, Lucide icons
- Vite build pipeline

## Setup

```bash
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
npm run build
php artisan serve --host=127.0.0.1 --port=8001
```

The `.env` is already configured for the supplied Atlas cluster:

```env
DB_CONNECTION=mongodb
DB_DATABASE=eventra
DB_DSN="mongodb+srv://USERNAME:PASSWORD@eventmanager.rbewk9i.mongodb.net/eventra?retryWrites=true&w=majority&appName=EventManager"
```

## Demo Accounts

- Planner: `planner@eventra.test` / `password`
- Vendor: `vendor@eventra.test` / `password`
- Guest: `guest@eventra.test` / `password`

## Main Routes

- `/` landing page
- `/login`, `/register`, `/reset-password`
- `/dashboard`
- `/events`
- `/vendors`
- `/favorites`
- `/events/{id}/guests`
- `/budget/{eventId}`
- `/events/{id}/bookings`
- `/events/{id}/bookings/timeline`
- `/events/{id}/tasks`
- `/events/{id}/gallery`
- `/rsvp/{token}` public RSVP portal
- `/timeline/shared/{token}` public stakeholder timeline

## Seeded Data

`php artisan migrate:fresh --seed` creates:

- 3 demo users across planner/vendor/guest roles
- 110 realistic vendors with categories, prices, ratings, locations, images, reviews, and availability data
- 3 sample events with budgets, expenses, bookings, tasks, galleries, and timeline share tokens
- 190+ guests with RSVP statuses, dietary preferences, plus-one counts, seats, and public RSVP tokens

## Verification

The current build was verified with:

```bash
php artisan route:list
php artisan view:cache
npm run build
php artisan test
```
