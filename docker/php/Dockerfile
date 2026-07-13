FROM php:8.4-fpm-alpine AS app

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    mysql-client \
    oniguruma-dev \
    unzip \
    zip \
    $PHPIZE_DEPS \
    && docker-php-ext-install bcmath intl mbstring pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize

COPY docker/php/entrypoint.sh /usr/local/bin/elixe-entrypoint
RUN chmod +x /usr/local/bin/elixe-entrypoint

ENTRYPOINT ["elixe-entrypoint"]

CMD ["php-fpm"]
