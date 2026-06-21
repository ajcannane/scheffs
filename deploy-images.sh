#!/usr/bin/env bash
set -euo pipefail

GODADDY_ALIAS="godaddy_scheff"
GODADDY_PATH="public_html"

scp -r deployed_site/images "${GODADDY_ALIAS}:${GODADDY_PATH}/"

echo "Images deployed."
