#!/bin/sh
set -e

echo "==> Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creando symlink de storage..."
php artisan storage:link --quiet 2>/dev/null || true

echo "==> Iniciando servicios (php-fpm + nginx)..."
exec "$@"
