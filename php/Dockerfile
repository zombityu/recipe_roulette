FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    libicu-dev \
    libpq-dev \
    curl \
    unzip

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl

RUN apt-get install -y libzip-dev zip \
    && docker-php-ext-install zip

RUN apt-get update && \
    apt-get install -y \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN curl -LsS https://github.com/symfony/cli/releases/latest/download/symfony_linux_amd64.gz | gunzip -c > /usr/local/bin/symfony && chmod a+x /usr/local/bin/symfony

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html