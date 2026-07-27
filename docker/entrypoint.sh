#!/bin/sh

# Use Railway's PORT env var, default to 80
PORT="${PORT:-80}"

# Generate nginx config from template using sed (safe - won't touch nginx $variables)
sed "s/NGINX_PORT/${PORT}/g" /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo "Nginx configured to listen on port ${PORT}"

# Run migrations FIRST (before caching config that depends on DB tables)
if [ "$APP_ENV" != "local" ]; then
    echo "Running migrations..."
    php artisan migrate --force || echo "migrate failed, continuing..."

    echo "Running seeds..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
fi

# Cache configuration, routes, and views (after migrate so DB tables exist)
php artisan config:cache || echo "config:cache failed, continuing..."
php artisan route:cache  || echo "route:cache failed, continuing..."
php artisan view:cache   || echo "view:cache failed, continuing..."

# Link storage
php artisan storage:link --force || true

# Start Supervisor
exec supervisord -c /etc/supervisord.conf
