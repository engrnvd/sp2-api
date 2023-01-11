FROM php:8.1-fpm as php

ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_ENABLE_CLI=0
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1
ENV PHP_OPCACHE_REVALIDATE_FREQ=1

RUN usermod -u 1000 www-data

RUN apt-get update -y \
    && apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx supervisor \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && docker-php-ext-install pdo pdo_mysql bcmath curl opcache \
    && pecl install -o -f redis && docker-php-ext-enable redis \
    &&  rm -rf /tmp/pear

WORKDIR /var/www

COPY --chown=www-data:www-data . .

COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap

ENTRYPOINT [ "docker/entrypoint.sh" ]
