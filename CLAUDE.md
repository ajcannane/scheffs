# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Scheff's Kitchens & Cabinets — a PHP/Apache website served via Docker. Site files live in `deployed_site/`, container config in `docker/`.

## Development Environment

```bash
docker-compose up          # Start web server (port 8084) + mailhog (port 8025)
docker-compose up --build  # Rebuild after Dockerfile changes
docker-compose up -d       # Pick up .env changes (restart does NOT re-read .env)
```

**`ADMIN_PASSWORD` gotcha:** must be a bcrypt hash with `$` doubled in `.env` (e.g. `$2y$` → `$$2y$$`) because docker-compose interpolates bare `$`. Generate:
```bash
docker exec redesign-website-web-1 php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
```

**Hero image gotcha:** CSS at `assets/css/main.css` references `/images/hero.jpg` as an absolute path — a relative `../images/` would resolve to `assets/images/` (wrong). Don't change it to relative.

## Architecture

**`deployed_site/`** — Apache document root (bind-mounted into container):
- `index.html.tmpl` — homepage; `entrypoint.sh` runs `envsubst` → `index.html` (gitignored)
- `gallery.php` — dynamic gallery; reads `images/gallery-manifest.json`, uses `getenv()` for API keys
- `contact-us.php` — standalone contact page with map, contact info, and enquiry modal
- `contact.php` — AJAX form POST handler (not a user-facing page)
- `admin/index.php` — password-protected gallery + hero image management panel
- `assets/css/main.css`, `assets/js/` — frontend assets
- `images/` — gallery images, manifest, and hero.jpg

**`docker/`**:
- `entrypoint.sh` — envsubst on template + `chmod -R o+w images/` (dev bind-mount workaround; not needed on GoDaddy where suPHP runs PHP as the file owner)
- `Dockerfile` — PHP/Apache + GD (WebP/JPEG) + msmtp + gettext-base
- `php.ini` — routes `mail()` → msmtp → mailhog in dev

## Environment Variables

Set in `.env`, injected via `docker-compose.yml`:

| Variable | Used by |
|---|---|
| `GOOGLE_MAPS_API_KEY` | `index.html.tmpl`, `gallery.php`, `contact-us.php` |
| `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` | `contact-us.php` (modal), `contact.php` |
| `ENQUIRY_EMAIL` | `contact.php` (recipient) |
| `ADMIN_PASSWORD` | `admin/index.php` (bcrypt hash, `$$`-escaped) |
| `RECAPTCHA_BYPASS` | Set `true` in dev to skip reCAPTCHA validation |

## Deployment

Run `/deploy` for the full deployment workflow and SSH setup instructions.
