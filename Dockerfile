# =============================================================================
# Stage 1 — Frontend assets (Node.js)
# =============================================================================
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js .
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# =============================================================================
# Stage 2 — PHP dependencies (Composer)
# =============================================================================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# =============================================================================
# Stage 3 — Imagen de producción (PHP-FPM + Nginx + Supervisor)
# =============================================================================
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema y extensiones PHP
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        libxml2-dev \
        icu-dev \
    && apk add --no-cache \
        nginx \
        supervisor \
        freetype \
        libjpeg-turbo \
        libpng \
        libzip \
        libxml2 \
        icu-libs \
        oniguruma-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        intl \
        zip \
        gd \
        opcache \
    && apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www/html

# Copiar fuentes del proyecto (con vendor instalado por composer)
COPY --chown=www-data:www-data --from=vendor /app .

# Sobreescribir public/build con los assets compilados por Vite
COPY --chown=www-data:www-data --from=frontend /app/public/build ./public/build

# Crear directorios necesarios de storage y cache
RUN mkdir -p \
        storage/logs \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache/data \
        storage/app/public \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Archivos de configuración
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/php.ini        $PHP_INI_DIR/conf.d/app.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh      /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
