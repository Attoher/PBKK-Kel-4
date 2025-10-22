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
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${RAILWAY_PUBLIC_DOMAIN:-https://pbkk-kel-4-production.up.railway.app}

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-debug}

DB_CONNECTION=${DB_CONNECTION:-sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}

OPENROUTER_API_KEY=${OPENROUTER_API_KEY:-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}
OPENROUTER_BASE_URL=${OPENROUTER_BASE_URL:-https://openrouter.ai/api/v1}
OPENROUTER_MODEL=${OPENROUTER_MODEL:-meta-llama/llama-3.2-3b-instruct:free}
EOF
    echo "✅ .env file created successfully"
fi

# Test Python environment
echo "🐍 Testing Python environment..."
python --version || echo "⚠️ Python not found in PATH"
which python || echo "⚠️ Python binary not found"

# Test Python modules
echo "📦 Testing Python modules..."
python -c "import fitz; print('✓ PyMuPDF installed')" || echo "⚠️ PyMuPDF not found"
python -c "import PyPDF2; print('✓ PyPDF2 installed')" || echo "⚠️ PyPDF2 not found"
python -c "import openai; print('✓ openai installed')" || echo "⚠️ openai not found"

# Check storage permissions
echo "📁 Checking storage permissions..."
mkdir -p storage/app/uploads storage/app/chunks storage/app/public
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
