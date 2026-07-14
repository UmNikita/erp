FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    librabbitmq-dev \
    libssl-dev \
    autoconf \
    g++ \
    make \
    pkg-config \
    && docker-php-ext-install pdo pdo_pgsql zip

RUN git clone --branch v2.2.0 https://github.com/php-amqp/php-amqp.git /tmp/amqp \
    && cd /tmp/amqp \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && docker-php-ext-enable amqp \
    && rm -rf /tmp/amqp

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
