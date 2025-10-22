#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Activate Python virtual environment
echo "📦 Activating Python virtual environment..."
. /tmp/venv/bin/activate

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "❌ ERROR: APP_KEY is not set!"
    exit 1
fi

# Check storage permissions
echo "📁 Checking storage permissions..."
chmod -R 777 storage bootstrap/cache database

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists"

# Run migrations
echo "🗃️ Running database migrations..."
php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Start server
echo "✅ Starting PHP server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
