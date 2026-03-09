FROM php:8.5-fpm

# Установим системные зависимости
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Установим Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Создаём пользователя с UID/GID как у тебя на хосте
ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} laravel && \
    useradd -u ${UID} -g laravel -m laravel

WORKDIR /var/www/html

# Запускаем PHP-FPM от этого пользователя
USER laravel
