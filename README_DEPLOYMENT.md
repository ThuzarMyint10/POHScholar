# POH Scholarships Scheme — Laravel Edition — Deployment Guide

This is the Laravel 12 application for the POH Scholarships Scheme: the
public marketing site, the student application portal, and the staff
admin dashboard, all in one application backed by a single MySQL
database. Dependencies support PHP 8.4 and include the maintained
`sineld/voyager` v2.0 package for compatible Voyager functionality.

**Important — read this first:** this application was written in a
sandboxed environment with no access to Packagist or GitHub, so
`composer install` could never be run here. Every file has been checked
with `php -l` (syntax only), but the very first real test of the
application — installing dependencies and loading a page — has to happen
on your own machine or server, following the steps below. Budget some
time for that first run to turn up anything a static syntax check can't
catch.

---

## 1. What you need

- **PHP 8.4 or newer**, with these extensions enabled (all standard on
  most Windows PHP builds): `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`,
  `tokenizer`, `xml`, `ctype`, `curl`, `gd` (used by the PDF export
  library for rendering the applicant photo/logo).
- **MySQL 5.7+ / MariaDB 10.3+** (or later — anything current).
- **Composer 2.10 or newer** — used on the machine that has internet
  access, to download Laravel and its dependencies.
- **Node.js 20.12 or newer** and npm for rebuilding frontend assets.
- **IIS** with the **URL Rewrite** module
  (https://www.iis.net/downloads/microsoft/url-rewrite) and PHP wired up
  via FastCGI (the "PHP Manager for IIS" extension is the easiest way to
  set this up if you don't already have PHP running under IIS).

This guide assumes Windows + IIS, per the original request, but nothing
about the application itself is Windows-specific — it will run
identically on Linux/Apache/Nginx if that's ever more convenient.

---

## 2. Install dependencies

On any machine with internet access (your laptop, a build server — it
doesn't have to be the production server), from the project root:

```bash
composer install --no-dev --optimize-autoloader
```

This downloads Laravel itself and the handful of packages this project
depends on (`laravel/framework`, `barryvdh/laravel-dompdf` for the PDF
export feature, etc.) into a new `vendor/` folder. `--no-dev` skips the
testing tools you don't need in production.

If you'll be deploying by copying files to the server (rather than
running Composer directly on the server), copy the **entire project
folder including the newly-created `vendor/` folder** to the server.

---

## 3. Configure the environment

1. Copy `.env.example` to `.env`.
2. Fill in:
   - `APP_URL` — the real public URL of the site (e.g.
     `https://www.pohscholarships.org`). Several features (email links,
     `asset()` URLs) depend on this being correct.
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — a MySQL database and
     user you've created for this site (`DB_HOST`/`DB_PORT` if not on
     the same machine).
   - `MAIL_*` — leave `MAIL_MAILER=log` at first (emails are written to
     `storage/logs/laravel.log` instead of actually sending, which is
     useful for the first round of testing); switch to `smtp` and fill
     in your provider's details once you're ready to send real email.
   - `ADMIN_NOTIFICATION_EMAILS` — comma-separated list of staff emails
     notified whenever a student submits a full application.
3. Generate the application key (this is what Laravel uses to encrypt
   sessions and other sensitive data — every deployment needs its own):

   ```bash
   php artisan key:generate
   ```

---

## 4. Set up the database

With `.env` pointed at your empty MySQL database:

```bash
php artisan migrate
```

This creates every table (admin users, students, programs, scholars,
academic results, site settings, and so on — full parity with the
original app's schema, including the Section A–L application fields and
the 0–5 evaluation/rank scores).

Then seed the starting data (one default admin login, the initial
scholarship programme, a welcome news post, and all the editable site-
settings text — About/Mission copy, contact details, etc.):

```bash
php artisan db:seed
```

**The seeded admin login is `admin` / `ChangeMe123!` — log in once the
site is live and change this password immediately** (Admin → Admin
Users, or ask another administrator to reset it for you), or edit the
password before seeding by changing it in
`database/seeders/DatabaseSeeder.php`.

---

## 5. Link the public storage folder

Student photos, certificate/document PDFs, academic-result attachments,
and scholar photos are all stored under `storage/app/public` and served
through a symlink at `public/storage`. Create it with:

```bash
php artisan storage:link
```

If your hosting environment doesn't allow symlinks (some shared Windows
hosts don't), instead copy `storage/app/public` to `public/storage` and
re-copy it after every deploy — or ask your host to enable symlinks,
which is the better long-term fix.

---

## 6. Point IIS at the `public/` folder

This is the step people most often get wrong with Laravel: **the site's
document root must be the `public/` folder, not the project root.**
Everything outside `public/` (application code, `.env`, `vendor/`) must
NOT be web-accessible.

In IIS Manager:

1. Create a new website (or application) whose **Physical Path** points
   at `...\poh-scholarships-laravel\public` (not the project root).
2. Confirm the **URL Rewrite** module is installed, and confirm
   `public/web.config` (already included in this project) is present —
   it tells IIS to route every request through Laravel's front
   controller (`index.php`) except for real files (CSS, JS, images).
3. Confirm PHP is wired up as a FastCGI handler for `*.php` in that
   site (via "PHP Manager for IIS", or IIS's own Handler Mappings if you
   set up PHP manually).
4. In `php.ini` on the server, make sure `upload_max_filesize` and
   `post_max_size` are at least `10M` (documents/photos are validated at
   4–8MB in the app itself, but PHP's own limits are checked first and
   silently truncate uploads if too low).

---

## 7. File permissions

The web server's application pool identity needs **write access** to:

- `storage/` (and everything under it — logs, cached views, sessions,
  uploaded files)
- `bootstrap/cache/`

On Windows/IIS, grant the site's Application Pool Identity (e.g.
`IIS AppPool\YourSiteName`) Modify permissions on those two folders.

---

## 8. Smoke-test the deployment

Once the above is done, visit the site and check, in order:

1. The homepage loads with the correct logo, hero text, and (once you've
   added one via the admin) a programme card.
2. `/admin/login` — log in with the seeded admin account.
3. `/student/register` — create a test student account, log in, fill in
   the Profile page (this exercises photo upload + the database write
   path end-to-end), and check `storage/logs/laravel.log` for any
   errors if something doesn't save.
4. Submit a test application and confirm the confirmation email appears
   in `storage/logs/laravel.log` (with `MAIL_MAILER=log`) — this proves
   the whole submit → email pipeline works before you switch on real
   SMTP.
5. As the admin, open the student's application, try the Evaluation /
   Mrs. Poh's Remarks / Section Ranks panels, and try Accept — this
   should flip the student to Approved, stamp `accepted_at`, open the
   first 6-month Academic Results period, and (per the mail log) attempt
   the acceptance email.
6. Try "Download PDF" on a submitted application — this exercises the
   `barryvdh/laravel-dompdf` package end-to-end; if it errors, check
   that the `gd` PHP extension is enabled.

---

## 9. Ongoing maintenance

- **Backups**: back up the MySQL database and the `storage/app/public`
  folder (uploaded photos/documents) regularly — they're the only state
  that isn't in source control.
- **Updates**: to deploy a code change, replace the application files
  (everything except `.env` and `storage/`), then run
  `php artisan migrate` (if there are new migrations) and
  `php artisan config:clear` (or just delete
  `bootstrap/cache/config.php` if it exists) to make sure cached config
  doesn't linger.
- **Switching on real email**: change `MAIL_MAILER=log` to
  `MAIL_MAILER=smtp` in `.env` and fill in the `MAIL_HOST` /
  `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` /
  `MAIL_ENCRYPTION` values your email provider gives you, then send a
  real test submission to confirm delivery.
- **Six-monthly academic-result reminders**: the "Send Reminders to All
  Overdue" button on the Student Applications page (and the per-student
  "Send Reminder" button) send reminder emails on demand — there is no
  background scheduler required. If you'd like this automated later
  (e.g. a weekly scheduled task), that would call
  `php artisan schedule:run` via Windows Task Scheduler, but it's not
  required for the app to function correctly today.

---

## 10. Google reCAPTCHA configuration

The application supports reCAPTCHA v2 checkbox/image challenges and reCAPTCHA v3 score responses. Set `RECAPTCHA_MODE=v2` for the visible “I'm not a robot” checkbox. For a v2 key, configure the production hostname in the Google reCAPTCHA console.

For v3 mode, configure `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` with a
matching key pair in `.env`, set `RECAPTCHA_MODE=v3`, then run
`php artisan config:clear`. For v3,
`RECAPTCHA_SCORE_THRESHOLD` defaults to `0.5`. `RECAPTCHA_EXPECTED_HOSTNAME`
can optionally restrict verification to one hostname; leave it empty if the
deployment uses multiple accepted hostnames.

Related files: `app/Rules/Recaptcha.php`, `config/services.php`, `.env.example`,
and the login/register Blade views under `resources/views/admin/auth/` and
`resources/views/student/auth/`.

## 11. What changed structurally vs. the original PHP app

This is a full rewrite, not a partial port — every public page, every
student-portal page, and every admin page has a Laravel equivalent with
matching fields, validation, and behavior (see below for the mapping).
A few implementation details changed for the better along the way,
worth knowing about:

- **One codebase, one `.env`, one database connection** — no more
  hand-rolled `db()`/`csrf_field()`/`site_url()` helper functions;
  everything goes through Laravel's Eloquent ORM, Blade templates, and
  named routes, which is what makes the app testable and maintainable
  going forward.
- **Two separate authentication guards** (`admin` and `student`) so
  admin and student sessions can never cross-contaminate — same
  guarantee as the original's separate session key design, just
  expressed through Laravel's own auth system instead of hand-rolled
  session checks.
- **The PDF export** now offers both the original "print via your
  browser" option AND a proper server-generated PDF download (via
  `barryvdh/laravel-dompdf`), so users aren't limited to a browser's
  print-to-PDF feature anymore.
- Every JSON-array form section (family members, academic/financial
  awards, employment records, co-curricular activities, work
  experience, referees, and the two evaluator "section rank" scores)
  is stored the same way as before — a JSON column on `students` — so
  the data model is unchanged; only the code that reads/writes it is
  new.

If anything here doesn't match production data when you go live (for
example, if the live database schema has drifted from the
`sql/schema.sql` this was built against), the safest path is: back up
production, run `php artisan migrate` against a COPY of production
first, and diff before touching the real database.
