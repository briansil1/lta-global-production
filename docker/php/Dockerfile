# PHP 8.1-FPM oficial con extensiones necesarias
FROM php:8.1-fpm

# Paquetes del sistema que necesitaremos para compilar extensiones
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    libjpeg62-turbo-dev libfreetype6-dev git unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensiones comunes
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       intl zip pdo pdo_mysql gd bcmath exif

# Opcional: opcache (recomendado en prod)
RUN docker-php-ext-install opcache

# Composer (global)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Config PHP
COPY php.ini /usr/local/etc/php/conf.d/zz-custom.ini

# Ajusta usuario dentro del contenedor para evitar permisos raros
ARG PUID=1000
ARG PGID=1000
RUN groupmod -o -g ${PGID} www-data && usermod -o -u ${PUID} -g ${PGID} www-data

WORKDIR /var/www/html
USER www-data
