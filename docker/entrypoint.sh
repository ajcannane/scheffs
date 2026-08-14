#!/bin/sh
set -e

envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < /var/www/html/index.html.tmpl > /var/www/html/index.html

# Dev bind-mount: host owns images/ as uid 1000, www-data runs as others.
# Make the directory and manifest writable so uploads work locally.
chmod -R o+w /var/www/html/images 2>/dev/null || true

exec apache2-foreground
