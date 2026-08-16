# syntax=docker/dockerfile:1
#
# Coolify
# - Build pack: Dockerfile
# - Ports exposes: 8080
# - Health check path: /up
# - Persistent storage: /app/storage and /app/database
# - Set APP_KEY, APP_URL, APP_ENV=production, APP_DEBUG=false in Coolify env

FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY . .
RUN npm run build


FROM dunglas/frankenphp:1-php8.4-alpine

RUN apk add --no-cache curl \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
    && install-php-extensions \
        intl \
        bcmath \
        pcntl \
        zip \
        gd \
        exif \
        pdo_mysql \
        pdo_pgsql \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-scripts \
        --no-autoloader

COPY . /app
COPY --from=assets /app/public/build /app/public/build

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && php artisan package:discover --ansi \
    && rm -rf /root/.composer /tmp/*

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

ENV SERVER_NAME=:8080 \
    CADDY_GLOBAL_OPTIONS="auto_https off" \
    APP_ENV=production \
    APP_DEBUG=false

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=45s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8080/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
