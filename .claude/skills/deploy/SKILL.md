---
name: deploy
description: Deploy the Scheff's site to GoDaddy via SCP
context: fork
agent: Explore
allowed-tools: Bash(./deploy.sh), Bash(./deploy-images.sh), Bash(git status), Bash(git log *)
---

## Live repo state
- Status: !`git status --short`
- Recent commits: !`git log --oneline -5`

## Deployment workflow

**Which script to use:**
- `./deploy.sh` — full deploy; use when CSS, JS, PHP, or HTML changed
- `./deploy-images.sh` — images only; use after gallery uploads or hero image changes when no code changed

**Full deploy (`deploy.sh`):**
1. Runs `envsubst` on `index.html.tmpl` injecting `GOOGLE_MAPS_API_KEY` and `RECAPTCHA_SITE_KEY`
2. SCPs `deployed_site/` assets, PHP files, and the generated `index.html` to GoDaddy

**Image-only deploy (`deploy-images.sh`):**
1. SCPs only `deployed_site/images/` to GoDaddy

## One-time SSH setup

Add to `~/.ssh/config`:
```
Host godaddy_scheff
    HostName ssh.example.com
    User your_godaddy_username
    IdentityFile ~/.ssh/your_key
```
Then update `GODADDY_ALIAS` and `GODADDY_PATH` at the top of both `deploy.sh` and `deploy-images.sh`.

## ADMIN_PASSWORD in production

On GoDaddy, set `ADMIN_PASSWORD` via the hosting control panel or `.htaccess` `SetEnv`. Use the raw bcrypt hash — `$$`-escaping is only needed in docker-compose `.env` files, not on the server.
