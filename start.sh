#!/bin/sh
set -e

echo "🚀 Starting Come&Fix deployment..."

# Set default PORT if not provided
export PORT=${PORT:-8080}

# Get the application directory
APP_DIR=$(pwd)

echo "🔐 Setting storage permissions..."
# Ensure storage and bootstrap/cache are writable
chmod -R 775 storage bootstrap/cache
chown -R nobody:nogroup storage bootstrap/cache 2>/dev/null || true

echo "📝 Running migrations..."
php artisan migrate --force

echo "🔗 Creating storage link..."
# Remove existing link if it exists, then create fresh one
rm -f public/storage
php artisan storage:link

echo "⚡ Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔧 Configuring nginx for port $PORT..."
# Create log directory to suppress nginx warnings
mkdir -p /var/log/nginx 2>/dev/null || true
# Replace PORT placeholder in nginx config
sed "s/listen 8080;/listen $PORT;/g" "$APP_DIR/nginx.conf" > /tmp/nginx.conf

echo "🚀 Starting PHP-FPM..."
php-fpm -y "$APP_DIR/php-fpm.conf" &
PHP_FPM_PID=$!

# Wait a moment for PHP-FPM to start
sleep 2

# Check if PHP-FPM is running
if ! kill -0 $PHP_FPM_PID 2>/dev/null; then
    echo "❌ PHP-FPM failed to start"
    exit 1
fi

echo "✅ PHP-FPM started successfully (PID: $PHP_FPM_PID)"
echo "🌐 Starting nginx on port $PORT..."

# Start nginx in foreground
exec nginx -c /tmp/nginx.conf -g 'daemon off;'

