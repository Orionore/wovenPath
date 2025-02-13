FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    curl \
    gnupg \
    && docker-php-ext-install zip pdo pdo_pgsql

# Installation de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && node --version \
    && npm --version


# Activation et installation de pnpm via corepack
RUN npm install -g pnpm \
    && pnpm --version

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN chown -R www-data:www-data /var/www/html