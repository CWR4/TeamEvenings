FROM php:7.2-fpm

RUN apt-get update && apt-get install -y --fix-missing \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmemcached-dev \
        zlib1g-dev \
        libzip-dev \
        libxslt-dev && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install zip && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-install -j$(nproc) xsl && \
    pecl install xdebug-2.6.0 && \
    apt-get update && \
    apt-get install -y --fix-missing \
        curl \
        libcurl4-openssl-dev \
        pkg-config \
        libssl-dev