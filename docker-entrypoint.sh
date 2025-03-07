#!/bin/sh

echo "📦 Instalando dependencias de Laravel..."
composer install --no-dev --optimize-autoloader --no-interaction --no-progress
composer update --no-interaction --no-progress

echo "🔧 Instalando Livewire y Filament..."
composer require livewire/livewire filament/filament --no-interaction

echo "📄 Publicando assets..."
php artisan vendor:publish --tag=livewire:assets --force
php artisan vendor:publish --tag=filament-assets --force

echo "🎨 Compilando assets con Vite..."
npm install
npm run build

echo "📄 Ejecutando migraciones..."
php artisan migrate --force

echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🚀 Iniciando Nginx y PHP-FPM..."
service nginx start && php-fpm
