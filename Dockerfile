# --- Stage 1: Base PHP image with extensions ---
FROM php:8.3-fpm-alpine AS base

# Install system dependencies for PHP extensions
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    icu-libs \
    pkgconfig

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd intl bcmath


# --- Stage 2: PHP Dependencies ---
FROM base AS vendor

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-scripts --no-dev --prefer-dist --optimize-autoloader


# --- Stage 3: Frontend Assets ---
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm install && npm run build


# --- Stage 4: Final Production Image ---
FROM base

WORKDIR /var/www/html

# Install runtime dependencies
RUN apk add --no-cache \
    nginx \
    supervisor

# Create temp dirs nginx needs (avoids permission errors when not running as root)
RUN mkdir -p /tmp/client_body /tmp/fastcgi /tmp/proxy /tmp/scgi /tmp/uwsgi

# Copy supervisord config only — nginx.conf is generated at runtime by entrypoint
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-railway.conf

# Copy application code
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

# Set permissions — 777 ensures any user (www-data, root, etc) can write
RUN chmod -R 777 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Railway sets PORT at runtime; document default
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
