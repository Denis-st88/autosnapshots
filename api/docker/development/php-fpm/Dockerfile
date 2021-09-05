FROM php:8.0.6-fpm-alpine

RUN apk update && apk add autoconf g++ make \
  && pecl install xdebug \
  && rm -rf /tmp/pear \
  && docker-php-ext-enable xdebug

RUN apk add postgresql-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app
