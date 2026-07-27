#!/bin/sh
set -e

# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations and seed
if [ "$APP_ENV" != "local" ]; then
    echo "Running migrations..."
    php artisan migrate --force
    
    # Jalankan seed hanya jika diperlukan
    echo "Running seeds..."
    php artisan db:seed --force || echo "Seeding failed, but continuing..."
fi

# Link storage
php artisan storage:link --force || true

# Start Supervisor
exec supervisord -c /etc/supervisord.conf
