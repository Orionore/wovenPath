FROM php:8.2-fpm

RUN apt-get update
RUN apt upgrade -y
RUN apt-get install -y \
    libfreetype6 libfreetype6-dev \
    libpng-dev \
    libturbojpeg-dev \
    libzip-dev \
    zip \
    libxml2-dev \
    libwebp-dev \
    curl libcurl4 libcurl4-gnutls-dev \
    libicu-dev \
    libmagickwand-dev libmagickcore-dev

RUN curl -sL https://deb.nodesource.com/setup_20.x -o nodesource_setup.sh
RUN bash nodesource_setup.sh
RUN apt-get install -y nodejs
RUN rm nodesource_setup.sh

RUN apt-get install -y supervisor bash unzip gnupg

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | APT_KEY_DONT_WARN_ON_DANGEROUS_USAGE=DontWarn apt-key add - && \
echo "deb http://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
apt-get update && \
apt-get install -y --no-install-recommends yarn


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install zip
RUN docker-php-ext-install curl
RUN docker-php-ext-install exif
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl
RUN docker-php-ext-configure pcntl --enable-pcntl
RUN docker-php-ext-install pcntl

RUN apt-get install -y \
    libjpeg-dev
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && docker-php-ext-install gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY ./config.ini /usr/local/etc/php/conf.d/config.ini
RUN chmod 755 /usr/local/etc/php/conf.d/config.ini

RUN usermod -u 1000 www-data

WORKDIR /var/www/app
