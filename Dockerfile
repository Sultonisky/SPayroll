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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies
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

# Install runtime dependencies only
RUN apk add --no-cache \
    nginx \
    supervisor

# Copy PHP and Nginx configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Copy application code
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 80

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
