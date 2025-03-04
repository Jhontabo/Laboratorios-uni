# Imagen base de PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql intl zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar código de la aplicación
COPY . .

RUN chmod -R 777 /var/www/vendor

# Instalar dependencias de Laravel
#RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini


# Ajustar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]
