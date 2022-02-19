FROM php:8.1-fpm-alpine

ADD ./php/www.conf /usr/local/etc/php-fpm.d/

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN mkdir -p /var/www/html

RUN chown laravel:laravel /var/www/html

WORKDIR /var/www/html

RUN set -ex \
  && apk --no-cache add \
  postgresql-dev

#RUN set -xe \
#    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
#    && pecl install -o -f redis  \
#    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini \
#    && rm -rf /usr/share/php \
#    && rm -rf /tmp/* \
#    && apk del  .phpize-deps

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql && docker-php-ext-enable pgsql
