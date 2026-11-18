#!/bin/sh

set -eu

mkdir -p \
    bootstrap/cache \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chown -R www-data:www-data bootstrap/cache storage

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force
php artisan migrate --force

exec supervisord --nodaemon --configuration /etc/supervisor/conf.d/supervisord.conf

