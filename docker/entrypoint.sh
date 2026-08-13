#!/bin/sh
set -e

envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < /var/www/html/index.html.tmpl > /var/www/html/index.html

exec apache2-foreground
