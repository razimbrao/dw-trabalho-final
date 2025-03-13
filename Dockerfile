ARG PHP_VERSION
FROM php:${PHP_VERSION} as app

## Diretório da aplicação
ARG APP_DIR=/var/www/app

RUN apt-get update -y && apt-get install -y --no-install-recommends \
    apt-utils \
    nano

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libbrotli-dev \
    git

RUN docker-php-ext-install sockets pdo pdo_pgsql pdo_mysql session xml zip iconv simplexml pcntl gd fileinfo

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR $APP_DIR
RUN cd $APP_DIR
RUN chown www-data:www-data $APP_DIR

COPY --chown=www-data:www-data . .
RUN rm -rf vendor

RUN composer install --no-interaction
RUN composer update --no-interaction

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

CMD ["tail", "-f", "/dev/null"]