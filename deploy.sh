#!/usr/bin/env bash
set -euo pipefail

# Load env vars from .env
set -a; source .env; set +a

# SSH Alias Configuration
# Instead of using user@host format, define an SSH alias in ~/.ssh/config:
#
#   Host godaddy_scheff
#       HostName ssh.example.com
#       User your_godaddy_username
#       IdentityFile ~/.ssh/your_key
#
# Then reference the alias name below:
GODADDY_ALIAS="godaddy_scheff"
GODADDY_PATH="public_html"

# Substitute environment variables into .tmpl files to generate .html files
# This approach works on shared hosting that doesn't support PHP processing of .html files
envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < deployed_site/index.html.tmpl > deployed_site/index.html

envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < deployed_site/gallery.html.tmpl > deployed_site/gallery.html

# SCP everything except templates, partials, and .env
# _contact_modal.php is only used locally (dev); modal HTML is embedded in .html files for production
scp -r deployed_site/assets \
        deployed_site/recaptcha-master \
        deployed_site/contact.php \
        deployed_site/index.html \
        deployed_site/gallery.html \
        "${GODADDY_ALIAS}:${GODADDY_PATH}/"

echo "Deploy complete."
