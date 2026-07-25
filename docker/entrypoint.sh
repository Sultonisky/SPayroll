#!/bin/sh
set -e

# ---------------------------------------------------------------
# Generate APP_KEY if not set (needed on first deploy)
# ---------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Generating a temporary key..."
    php artisan key:generate --force
fi

# ---------------------------------------------------------------
# Cache configuration, routes, and views for performance
# ---------------------------------------------------------------
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ---------------------------------------------------------------
# Run migrations (always safe with --force in production)
# ---------------------------------------------------------------
echo "Running migrations..."
php artisan migrate --force

# ---------------------------------------------------------------
# Run seeders only on first deploy (when DB is empty)
# Set DB_SEED=true in Railway environment variables to seed once,
# then remove it — it will NOT re-seed automatically on next deploy.
# ---------------------------------------------------------------
if [ "$DB_SEED" = "true" ]; then
    echo "DB_SEED=true detected. Running seeders..."
    php artisan db:seed --force
    echo "Seeding complete. Remove DB_SEED from env vars to skip next time."
fi

# ---------------------------------------------------------------
# Link storage
# ---------------------------------------------------------------
php artisan storage:link --force || true

# ---------------------------------------------------------------
# Start Supervisor (manages nginx + php-fpm)
# ---------------------------------------------------------------
exec supervisord -c /etc/supervisord.conf
