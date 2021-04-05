#!/bin/sh

set -e

flock -x ./vendor/composer-install.lock php /usr/local/bin/composer.phar install

chown -R www-data:www-data /var/www/app/var

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
