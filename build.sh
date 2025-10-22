#!/usr/bin/env bash
# Build script untuk Render.com

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing Python dependencies..."
pip install -r python/requirements.txt

echo "Setting up Laravel..."
php artisan config:cache
php artisan route:cache

echo "Creating storage directories..."
mkdir -p storage/app/private/uploads
mkdir -p storage/app/private/results
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "Build completed!"
