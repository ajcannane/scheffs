#!/bin/sh
set -e

envsubst '${GOOGLE_MAPS_API_KEY} ${RECAPTCHA_SITE_KEY}' \
  < /var/www/html/index.html.tmpl > /var/www/html/index.html

envsubst '${GOOGLE_MAPS_API_KEY}' \
  < /var/www/html/gallery.html.tmpl > /var/www/html/gallery.html

exec apache2-foreground
