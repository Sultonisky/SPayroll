#!/bin/sh

# Railway injects PORT; default to 8080 to match Railway's expected port
PORT="${PORT:-8080}"

echo "==> Configuring nginx to listen on 0.0.0.0:${PORT}"

# Write nginx config directly — no template files, no envsubst, no sed
# This avoids any risk of stale files or variable substitution bugs
cat > /etc/nginx/nginx.conf << EOF
worker_processes auto;
error_log stderr warn;
pid /tmp/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    sendfile on;
    keepalive_timeout 65;

    # Write temp files to /tmp to avoid permission issues
    client_body_temp_path /tmp/client_body;
    fastcgi_temp_path /tmp/fastcgi;
    proxy_temp_path /tmp/proxy;
    scgi_temp_path /tmp/scgi;
    uwsgi_temp_path /tmp/uwsgi;

    server {
        listen ${PORT};
        server_name _;
        root /var/www/html/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        index index.php;
        charset utf-8;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_hide_header X-Powered-By;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
}
EOF

echo "==> Nginx config written for port ${PORT}"

# Run migrations FIRST (before caching config that depends on DB tables)
if [ "$APP_ENV" != "local" ]; then
    echo "==> Running migrations..."
    php artisan migrate --force || echo "migrate failed, continuing..."

    echo "==> Running seeds..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
fi

# Cache after migrate so DB tables exist
php artisan config:cache || echo "config:cache failed, continuing..."
php artisan route:cache  || echo "route:cache failed, continuing..."
php artisan view:cache   || echo "view:cache failed, continuing..."

# Link storage
php artisan storage:link --force || true

echo "==> Starting supervisord..."
exec supervisord -c /etc/supervisord.conf
