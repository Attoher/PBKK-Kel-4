#!/bin/bash
set -e

echo "🚀 Starting Laravel application with pypdfium2..."

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
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${RAILWAY_PUBLIC_DOMAIN:-https://pbkk-kel-4-production.up.railway.app}

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION:-sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}

# Force HTTPS for Railway proxy
ASSET_URL=https://\${RAILWAY_PUBLIC_DOMAIN}
APP_FORCE_HTTPS=true

OPENROUTER_API_KEY=${OPENROUTER_API_KEY:-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}
# Senopati (local ITS) - preferred model/endpoint for on-prem usage
SENOPATI_BASE_URL=${SENOPATI_BASE_URL:-https://senopati.its.ac.id/senopati-lokal-dev/generate}
SENOPATI_MODEL=${SENOPATI_MODEL:-dolphin-mixtral:latest}
EOF
    echo "✅ .env file created successfully"
fi

# Test Python environment
echo "🐍 Testing Python environment..."
python --version || echo "⚠️ Python not found in PATH"
which python || echo "⚠️ Python binary not found"

# Test Python modules
echo "📦 Testing Python modules..."
echo "📦 Testing Python modules..."
python -c "import pypdfium2; print('✓ pypdfium2 installed')" 2>/dev/null || echo "⚠️ pypdfium2 not found"
python -c "import PyPDF2; print('✓ PyPDF2 installed')" 2>/dev/null || echo "⚠️ PyPDF2 not found"
python -c "import openai; print('✓ openai installed')" 2>/dev/null || echo "⚠️ openai not found"

# Check storage permissions
echo "📁 Checking storage permissions..."
mkdir -p storage/app/uploads storage/app/chunks storage/app/public
chmod -R 777 storage bootstrap/cache database

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "⚠️ Storage link already exists"

# Run migrations (ignore missing dev packages)
echo "🗃️ Running database migrations..."
php artisan migrate --force 2>&1 | grep -v "PailServiceProvider" || true

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache config for production
echo "📦 Caching configuration..."
php artisan config:cache

# Start server
echo "✅ Starting PHP server on port ${PORT:-8080}..."
php -S 0.0.0.0:${PORT:-8080} -t public public/index.php
