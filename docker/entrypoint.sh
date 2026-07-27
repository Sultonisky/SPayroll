#!/bin/sh

# Use Railway's PORT env var, default to 80
export PORT="${PORT:-80}"

# Substitute PORT into nginx config
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Cache configuration, routes, and views (non-fatal)
php artisan config:cache || echo "config:cache failed, continuing..."
php artisan route:cache  || echo "route:cache failed, continuing..."
php artisan view:cache   || echo "view:cache failed, continuing..."

# Run migrations and seed
if [ "$APP_ENV" != "local" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "migrate failed, continuing..."

    echo "Running seeds..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
fi

# Link storage
php artisan storage:link --force || true

# Start Supervisor
exec supervisord -c /etc/supervisord.conf
