#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Activate Python virtual environment
echo "📦 Activating Python virtual environment..."
. /tmp/venv/bin/activate

# Create .env file if Railway variables are not loaded
if [ ! -f .env ] || [ -z "$APP_KEY" ]; then
    echo "📝 Creating .env file from Railway variables..."
    cat > .env <<EOF
APP_NAME="TA Format Checker ITS"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-base64:q36FLfYNrRgFaBXaPIgz02qRcyPISRIWjPR3ZxiStQI=}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${RAILWAY_PUBLIC_DOMAIN:-https://pbkk-kel-4-production.up.railway.app}

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}

OPENROUTER_API_KEY=${OPENROUTER_API_KEY:-}
OPENROUTER_BASE_URL=${OPENROUTER_BASE_URL:-https://openrouter.ai/api/v1}
OPENROUTER_MODEL=${OPENROUTER_MODEL:-meta-llama/llama-3.2-3b-instruct:free}
EOF
    echo "✅ .env file created successfully"
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
