# syntax=docker/dockerfile:1
#
# Coolify
# - Build pack: Dockerfile
# - Ports exposes: 8080
# - Health check path: /up
# - Persistent storage: /app/storage and /app/database
# - Mark APP_KEY as Runtime only
# - Set APP_URL, APP_ENV=production, APP_DEBUG=false at runtime

FROM composer:2 AS vendor

WORKDIR /app

ENV APP_ENV=local \
    COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-scripts \
        --no-autoloader

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev --no-scripts


FROM node:22-alpine AS assets

WORKDIR /app

ENV APP_ENV=local \
    NODE_ENV=development

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY . .
COPY --from=vendor /app/vendor ./vendor
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

WORKDIR /app

COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

RUN php artisan package:discover --ansi \
    && rm -rf /tmp/*

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

ENV SERVER_NAME=:8080 \
    CADDY_GLOBAL_OPTIONS="auto_https off"

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=45s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8080/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
