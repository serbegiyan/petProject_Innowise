# ==============================================================================
# SECURITY WARNING: LOCAL ENVIRONMENT ONLY
# ==============================================================================
# This Dockerfile and the associated docker-compose configuration mount the 
# Docker socket (/var/run/docker.sock) inside the Jenkins container.
#
# 1. This configuration is strictly intended for local development and learning.
# 2. DO NOT use this setup, image, or docker-compose file in production/staging.
# 3. Exposing the Docker socket grants container processes root-level privileges 
#    over the host system, creating a critical security vulnerability.
# ==============================================================================
FROM php:8.5-fpm

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
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl sockets bcmath \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} laravel && \
    useradd -u ${UID} -g laravel -m laravel

WORKDIR /var/www/html

USER laravel