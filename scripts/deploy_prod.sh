#!/bin/bash
set -e

echo "Production deployment started ..."

php artisan down

composer install  --no-interaction --prefer-dist --optimize-autoloader
composer dump-autoload

# Clear and cache config
php artisan config:cache
php artisan config:clear

# Clear the old cache
php artisan clear-compiled
php artisan optimize
php artisan migrate --force

php artisan up

echo "Deployment finished!"