#!/bin/zsh
# Runs WP-CLI against the "cfhg" site in Local, using Local's own PHP and MySQL socket.
# Usage: scripts/wp-local.sh plugin list
# The Local site must be running (green "Site running" in the Local app).
LOCAL_APP_DATA="$HOME/Library/Application Support/Local"
SITE_ID="cpbtddpLD"
PHP="$LOCAL_APP_DATA/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
WPCLI="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar"
export PHPRC="$LOCAL_APP_DATA/run/$SITE_ID/conf/php"
exec "$PHP" -c "$PHPRC/php.ini" -d memory_limit=512M "$WPCLI" --path="$HOME/Local Sites/cfhg/app/public" "$@"
