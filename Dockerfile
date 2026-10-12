ARG PHP_VERSION=7.3
FROM php:${PHP_VERSION}-cli

RUN pecl install pcov && docker-php-ext-enable pcov

RUN apt update && apt install -y git zip
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app
RUN git config --global --add safe.directory /app
