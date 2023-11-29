FROM php:7.4-fpm-alpine3.16

RUN apk add git curl zip postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY conf/* $PHP_INI_DIR/conf.d/
