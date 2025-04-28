#!/bin/bash
set -e

echo "Deployment started ..."

# Enter maintenance mode or return true
php artisan down

git submodule update --init --recursive

# Install composer dependencies
composer install  --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations
php artisan migrate

php artisan optimize:clear
php artisan config:clear

# Exit maintenance mode
php artisan up

echo "Deployment finished!"
