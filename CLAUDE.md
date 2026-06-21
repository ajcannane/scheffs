# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Scheffs Kitchens & Cabinets — a PHP/Apache static-ish website served via Docker. The live site files live in `deployed_site/`, and the container is configured in `docker/`.

## Development Environment

Copy `.env.example` to `.env` and fill in the values, then:

```bash
docker-compose up        # Start web server (port 8084) + mailhog (port 8025)
docker-compose up --build  # Rebuild after Dockerfile changes
```

- Site: http://localhost:8084
- Mailhog (captured emails): http://localhost:8025

## Architecture

**`deployed_site/`** — all website content served by Apache inside the container. `index.html.tmpl` and `gallery.html.tmpl` contain `${VAR}` placeholders for environment variables; the container entrypoint runs `envsubst` at startup to produce the final `.html` files (gitignored).

**`docker/`** — container configuration:
- `Dockerfile` — PHP/Apache base, installs `msmtp` for mail relay and `gettext-base` for `envsubst`
- `entrypoint.sh` — substitutes env vars into HTML templates at container startup
- `php.ini` — routes `mail()` calls through msmtp → mailhog in dev

**Contact form** (`deployed_site/contact.php`):
- ReCAPTCHA v2 validation (bypass in dev: set `RECAPTCHA_BYPASS=true` in `.env`)
- CSRF protection via origin/referer check
- Email sent via `mail()` → msmtp → mailhog

**Environment variables** (set in `.env`, injected via `docker-compose.yml`):
- `GOOGLE_MAPS_API_KEY` — embedded in `gallery.html` and `index.html`
- `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` — contact form
- `ENQUIRY_EMAIL` — contact form recipient

## Deployment

Deploy to GoDaddy hosting via SCP using either script below.

**Setup (one-time):**
1. Add an SSH alias to `~/.ssh/config`:
   ```
   Host godaddy_scheff
       HostName ssh.example.com
       User your_godaddy_username
       IdentityFile ~/.ssh/your_key
   ```
2. Update `GODADDY_ALIAS` and `GODADDY_PATH` in both `deploy.sh` and `deploy-images.sh`
3. Ensure `.env` is populated with all required variables (see Environment variables above)

**`deploy.sh`** — full site deployment:
1. Runs `envsubst` on HTML templates to inject environment variables
2. SCPs assets, PHP, and generated HTML to the remote server
3. Run: `./deploy.sh`

**`deploy-images.sh`** — image-only deployment (faster for image changes):
1. SCPs only the `images/` folder to the remote server
2. Useful for quick image updates without re-uploading CSS/JS
3. Run: `./deploy-images.sh`
