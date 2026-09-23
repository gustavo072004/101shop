#!/bin/sh
set -eu
: "${APP_KEY:?Configure APP_KEY before starting}"
: "${DATABASE_URL:?Configure the PostgreSQL DATABASE_URL before starting}"
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
