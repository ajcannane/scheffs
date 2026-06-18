#!/usr/bin/env bash
set -euo pipefail

# Load env vars from .env
set -a; source .env; set +a

GODADDY_USER="your_ssh_user"
GODADDY_HOST="your_host"        # e.g. ssh.example.com or IP
GODADDY_PATH="/path/to/public_html"

# Generate final HTML from templates (same substitutions as entrypoint.sh)
envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < deployed_site/index.html.tmpl > deployed_site/index.html

envsubst '${GOOGLE_MAPS_API_KEY}' \
  < deployed_site/gallery.html.tmpl > deployed_site/gallery.html

# SCP everything except templates, .env, and local-only files
scp -r deployed_site/assets \
        deployed_site/images \
        deployed_site/recaptcha-master \
        deployed_site/contact.php \
        deployed_site/index.html \
        deployed_site/gallery.html \
        "${GODADDY_USER}@${GODADDY_HOST}:${GODADDY_PATH}/"

echo "Deploy complete."
