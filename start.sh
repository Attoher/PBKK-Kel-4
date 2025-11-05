#!/bin/bash
set -e

echo "🚀 Starting Laravel application with pypdfium2..."

# Activate Python virtual environment
echo "📦 Activating Python virtual environment..."
if [ -d "/tmp/venv" ]; then
    . /tmp/venv/bin/activate
    echo "✅ Virtual environment activated"
else
    echo "⚠️ Virtual environment not found, creating new one..."
    python -m venv /tmp/venv
    . /tmp/venv/bin/activate
    echo "📦 Installing Python packages..."
    pip install --no-cache-dir -r python/requirements.txt
    echo "✅ Python packages installed"
fi

# Verify Python packages
echo "🔍 Verifying Python packages..."
python -c "import requests; import pypdfium2; import PyPDF2; print('✅ All packages available')" || {
    echo "⚠️ Packages missing, reinstalling..."
    pip install --no-cache-dir -r python/requirements.txt
}

# Create .env file if Railway variables are not loaded
if [ ! -f .env ] || [ -z "$APP_KEY" ]; then
    echo "📝 Creating .env file from Railway variables..."
    
    # Determine APP_URL from Railway environment
    if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
        APP_URL_VALUE="https://${RAILWAY_PUBLIC_DOMAIN}"
    else
        APP_URL_VALUE="http://localhost:${PORT:-8080}"
    fi
    
    cat > .env <<EOF
APP_NAME="TA Format Checker ITS"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-base64:q36FLfYNrRgFaBXaPIgz02qRcyPISRIWjPR3ZxiStQI=}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL_VALUE}

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-info}

DB_CONNECTION=${DB_CONNECTION:-sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}

# Senopati (local ITS) - preferred model/endpoint
SENOPATI_BASE_URL=${SENOPATI_BASE_URL:-https://senopati.its.ac.id/senopati-lokal-dev/generate}
SENOPATI_MODEL=${SENOPATI_MODEL:-dolphin-mixtral:latest}

# OpenRouter (backup)
OPENROUTER_API_KEY=${OPENROUTER_API_KEY:-}
OPENROUTER_BASE_URL=${OPENROUTER_BASE_URL:-https://openrouter.ai/api/v1}
OPENROUTER_MODEL=${OPENROUTER_MODEL:-meta-llama/llama-3.2-3b-instruct:free}
EOF
    echo "✅ .env file created with APP_URL=${APP_URL_VALUE}"
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

# Cache config for production (skip route cache for flexibility)
echo "� Caching configuration..."
php artisan config:cache
php artisan view:cache

# Start server
echo "✅ Starting PHP server on port ${PORT:-8080}..."
echo "📍 Server will be available at http://0.0.0.0:${PORT:-8080}"
php -S 0.0.0.0:${PORT:-8080} server.php
