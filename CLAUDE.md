# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Scheffs Kitchens & Cabinets — an Astro + Tailwind CSS static site, built from `site/` and served via PHP/Apache Docker. The Astro project outputs to `deployed_site/`, which Apache serves on port 8084.

## Development Environment

Copy `.env.example` to `.env` and fill in the values, then:

```bash
# Node >= 18.14 required (see site/.nvmrc). With nvm:
#   export NVM_DIR="$HOME/.nvm" && . "$NVM_DIR/nvm.sh" && nvm use 20

# Build the Astro site (required before or instead of Docker for static pages)
cd site && npm install   # first time only
cd site && npm run build # outputs to ../deployed_site/

# Start web server (port 8084) + mailhog (port 8025)
docker-compose up
docker-compose up --build  # Rebuild after Dockerfile changes
```

For iterative frontend development, use the Astro dev server:

```bash
cd site && npm run dev   # Dev server at http://localhost:4321
                         # contact.php is proxied to http://localhost:8084
```

- Site (Docker): http://localhost:8084
- Astro dev server: http://localhost:4321
- Mailhog (captured emails): http://localhost:8025

## Architecture

**`site/`** — Astro project source (the new website):
- `src/pages/` — one `.astro` file per page (`index.astro`, `gallery.astro`)
- `src/components/` — reusable Astro components
- `src/layouts/BaseLayout.astro` — HTML shell
- `src/data/gallery.ts` — typed gallery image data (replaces 900-line HTML)
- `astro.config.mjs` — `outDir: '../deployed_site'`, `emptyOutDir: false`

**`deployed_site/`** — Apache web root (bind-mounted in Docker at `/var/www/html`):
- `index.html`, `gallery/index.html` — **GENERATED** by `npm run build` (do not edit manually)
- `_astro/` — **GENERATED** Astro bundle assets
- `.htaccess` — **GENERATED** (redirects `/gallery.html` → `/gallery/`)
- `contact.php` — **PRESERVED** PHP backend (never overwritten by Astro)
- `recaptcha-master/` — **PRESERVED** PHP ReCAPTCHA library
- `images/` — **PRESERVED** photo assets (never overwritten by Astro)

**`docker/`** — container configuration (unchanged):
- `Dockerfile` — PHP/Apache base, installs `msmtp` for mail relay
- `php.ini` — routes `mail()` calls through msmtp → mailhog in dev
- `html-as-php.conf` — Apache config enabling PHP in `.html` files (harmless, static HTML has no PHP tags)

**Contact form** (`deployed_site/contact.php`):
- ReCAPTCHA v2 validation (bypass in dev: set `RECAPTCHA_BYPASS=true` in `.env`)
- CSRF protection via origin/referer check
- Email sent via `mail()` → msmtp → mailhog
- Form fields: `name`, `surname`, `email`, `phone`, `message`, `g-recaptcha-response`

**Environment variables** (set in `.env`):
- `PUBLIC_GOOGLE_MAPS_API_KEY` — baked into static HTML at build time by Astro
- `PUBLIC_RECAPTCHA_SITE_KEY` — baked into static HTML at build time by Astro
- `RECAPTCHA_SECRET_KEY` — used only by `contact.php` at runtime (via docker-compose)
- `ENQUIRY_EMAIL` — contact form recipient (via docker-compose)
- `RECAPTCHA_BYPASS=true` — dev bypass for ReCAPTCHA in `contact.php` (in docker-compose)
