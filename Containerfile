FROM docker.io/library/composer:2 AS composer

FROM docker.io/library/php:8.5-fpm AS php-base

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    zip


FROM php-base AS composer-build

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock /app

RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts

COPY ./ /app

RUN composer dump-autoload --optimize --no-dev --no-interaction


FROM php-base AS runtime

COPY ./ /app
COPY --from=composer-build /app/vendor /app/vendor

RUN chgrp -R 0 /app/storage /app/bootstrap/cache && \
    chmod -R g=u /app/storage /app/bootstrap/cache 
