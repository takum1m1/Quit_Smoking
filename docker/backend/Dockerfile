FROM php:8.4-fpm

# OS パッケージ & PHP 拡張
RUN apt-get update && apt-get install -y \
      libzip-dev zip unzip git libonig-dev libicu-dev libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 権限調整（root のままでも動くが、CI/CD を意識）
RUN addgroup --system app && adduser --system --group app \
    && chown -R app:app /var/www/html
USER app

# 本番環境向け最終 CMD（開発中は artisan serve で十分）
CMD ["sh", "-c", "\
    composer install --no-dev --optimize-autoloader && \
    php artisan key:generate --ansi && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000 && \
    php-fpm \
"]